<?php

namespace App\Http\Controllers;

use App\Http\Requests\Article\CreateUpdateArticleRequest;
use App\Models\Article;
use App\Models\OrganizationMember;
use DOMDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function create(){
        return view('pages.articles.create');
    }

    public function store(CreateUpdateArticleRequest $request){

        if ($request->org_id) {
            $userId = Auth::id();
            
            $isMember = OrganizationMember::where('organization_id', $request->org_id)
                ->where('user_id', $userId)
                ->exists();
            
            if (!$isMember) {
                return response()->json(['error' => 'User does not belong to the specified organization'], 403);
            }
        }

        $text = $request->body;

        $dom = new DOMDocument();
        $dom->loadHTML($text, 9);

        $images = $dom->getElementsByTagName('img');

        foreach ($images as $key => $img) {
            if (strpos($img->getAttribute('src'),'data:image/') === 0){
                $data = base64_decode( explode(',',explode(';',$img->getAttribute('src'))[1])[1] );
                $imageName = "TextImage/" . Str::uuid() . $key.'.png';
                Storage::put( '/public/ArticleStorage/'. $imageName, $data );
                $srcPath = "/storage/ArticleStorage/" . $imageName; 
                $img->removeAttribute('src');
                $img->setAttribute('src',$srcPath);
            }
        }

        $text = $dom->saveHTML();

        $thumbnailPath = $request->file('thumbnail');

        $thumbnailName = Str::uuid() . '_' . str_replace(' ', '_', $thumbnailPath->getClientOriginalName());

        $thumbnailPath->storeAs('public/ArticleStorage/Thumbnail/' . $thumbnailName);

        $slug = Str::slug($request->title);
        $originalSlug = $slug;
        $counter = 1;
        
        while (Article::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $article = Article::create([
            'slug' => $slug,
            'title' => $request->title,
            'body' => $text,
            'thumbnail' => $thumbnailName,
            'user_id' => Auth::id(),
        ]);

        if ($request->org_id) {
            $article->org_id = $request->org_id;
        }
        
        $article->save();

        return response()->json(['message' => 'Article created successfully'], 201);

    }

    // $table->string('slug')->unique();
    // $table->string('title');
    // $table->longText('body');
    // $table->text('thumbnail');
    // $table->boolean('is_deleted')->default(false);
    // $table->foreignId('org_id')->nullable()->references('id')->on('organizations')->cascadeOnDelete();
    // $table->foreignId('user_id')->nullable()->references('id')->on('users')->cascadeOnDelete();

    public function update(CreateUpdateArticleRequest $request, $id)
    {
        $article = Article::findOrFail($id);

        // Check authorization - user must be the article owner
        if ($article->user_id !== Auth::id()) {
            return response()->json(['error' => 'You do not have permission to update this article'], 403);
        }

        // If org_id is being updated, verify user is member of that organization
        if ($request->org_id && $request->org_id !== $article->org_id) {
            $userId = Auth::id();
            
            $isMember = OrganizationMember::where('organization_id', $request->org_id)
                ->where('user_id', $userId)
                ->exists();
            
            if (!$isMember) {
                return response()->json(['error' => 'User does not belong to the specified organization'], 403);
            }
        }

        $text = $request->body;

        // Get old images to compare later
        $oldText = $article->body;
        $oldDom = new DOMDocument();
        $oldDom->loadHTML($oldText, 9);
        $oldImages = $oldDom->getElementsByTagName('img');
        $oldSrcs = [];
        foreach ($oldImages as $img) {
            $oldSrcs[] = $img->getAttribute('src');
        }

        // Process new content
        $dom = new DOMDocument();
        $dom->loadHTML($text, 9);
        $images = $dom->getElementsByTagName('img');
        $newSrcs = [];

        foreach ($images as $key => $img) {
            $src = $img->getAttribute('src');
            if (strpos($src, 'data:image/') === 0) {
                // New base64 image - upload it
                $data = base64_decode(explode(',', explode(';', $src)[1])[1]);
                $imageName = "TextImage/" . Str::uuid() . $key . '.png';
                Storage::put('/public/ArticleStorage/' . $imageName, $data);
                $srcPath = "/storage/ArticleStorage/" . $imageName;
                $img->removeAttribute('src');
                $img->setAttribute('src', $srcPath);
                $newSrcs[] = $srcPath;
            } else {
                // Existing image - keep track of it
                $newSrcs[] = $src;
            }
        }

        $text = $dom->saveHTML();

        // Delete old images that are no longer used
        foreach ($oldSrcs as $oldSrc) {
            if (!in_array($oldSrc, $newSrcs)) {
                $relativePath = str_replace('/storage/', 'public/', $oldSrc);
                if (Storage::exists($relativePath)) {
                    Storage::delete($relativePath);
                }
            }
        }

        // Handle thumbnail update
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($article->thumbnail && Storage::exists('public/ArticleStorage/Thumbnail/' . $article->thumbnail)) {
                Storage::delete('public/ArticleStorage/Thumbnail/' . $article->thumbnail);
            }

            $thumbnailPath = $request->file('thumbnail');
            $thumbnailName = Str::uuid() . '_' . str_replace(' ', '_', $thumbnailPath->getClientOriginalName());
            $thumbnailPath->storeAs('public/ArticleStorage/Thumbnail/' . $thumbnailName);
            $article->thumbnail = $thumbnailName;
        }

        // Update slug if title changed
        if ($request->title !== $article->title) {
            $slug = Str::slug($request->title);
            $originalSlug = $slug;
            $counter = 1;
            
            while (Article::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            $article->slug = $slug;
        }

        // Update article fields
        $article->title = $request->title;
        $article->body = $text;

        // Update org_id if provided
        if ($request->has('org_id')) {
            $article->org_id = $request->org_id;
        }

        $article->save();

        return response()->json(['message' => 'Article updated successfully'], 200);
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);

        // Check authorization - user must be the article owner
        if ($article->user_id !== Auth::id()) {
            return response()->json(['error' => 'You do not have permission to delete this article'], 403);
        }

        // Delete all images in article body
        $dom = new DOMDocument();
        $dom->loadHTML($article->body, 9);
        $images = $dom->getElementsByTagName('img');

        foreach ($images as $img) {
            $src = $img->getAttribute('src');
            $relativePath = str_replace('/storage/', 'public/', $src);
            if (Storage::exists($relativePath)) {
                Storage::delete($relativePath);
            }
        }

        // Delete thumbnail
        if ($article->thumbnail && Storage::exists('public/ArticleStorage/Thumbnail/' . $article->thumbnail)) {
            Storage::delete('public/ArticleStorage/Thumbnail/' . $article->thumbnail);
        }

        $article->delete();

        return response()->json(['message' => 'Article deleted successfully'], 200);
    }

    public function readAll(){

        $articles = Article::all();

        return response()->json(['article' => $articles], 200);

    }

    public function getAll()
    {
        $query = Article::query()
            ->where('is_deleted', false)
            ->with(['user:id,name,handle,avatar_url', 'organization:id,name,handle,logo_img']);

        // Live search by title or body content
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('body', 'like', '%' . $search . '%');
            });
        }

        // Sorting
        $sort = request('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $articles = $query->paginate(6);

        return view('pages.tests.articles', compact('articles'));
    }

    public function readOne($id){
        
        $article = Article::find($id);

        return response()->json(['article' => $article], 200);
    }
}
