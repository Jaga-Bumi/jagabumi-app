<?php

namespace App\Http\Controllers;

use App\Http\Requests\Article\CreateUpdateArticleRequest;
use App\Models\Article;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\User;
use DOMDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    private function deleteImageFile($src)
    {
        $relativePath = str_replace('/storage/', 'public/', $src);
        if (Storage::exists($relativePath)) {
            Storage::delete($relativePath);
        }
    }

    private function deleteArticleImages($htmlContent)
    {
        $dom = new DOMDocument();
        $dom->loadHTML($htmlContent, 9);
        $images = $dom->getElementsByTagName('img');

        foreach ($images as $img) {
            $this->deleteImageFile($img->getAttribute('src'));
        }
    }

    public function create(){
        return view('pages.articles.create');
    }

    public function store(CreateUpdateArticleRequest $request){
        if ($request->org_id) {
            $isMember = OrganizationMember::where('organization_id', $request->org_id)
                ->where('user_id', Auth::id())
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
            $slug = $originalSlug . '-' . $counter++;
        }

        $article = Article::create([
            'slug' => $slug,
            'title' => $request->title,
            'body' => $text,
            'thumbnail' => $thumbnailName,
            'user_id' => Auth::id(),
            'org_id' => $request->org_id,
        ]);

        return redirect('/articles');
    }

    public function update(CreateUpdateArticleRequest $request, $id)
    {
        $article = Article::findOrFail($id);

        if ($article->user_id !== Auth::id()) {
            return response()->json(['error' => 'You do not have permission to update this article'], 403);
        }

        if ($request->org_id && $request->org_id !== $article->org_id) {
            $isMember = OrganizationMember::where('organization_id', $request->org_id)
                ->where('user_id', Auth::id())
                ->exists();
            
            if (!$isMember) {
                return response()->json(['error' => 'User does not belong to the specified organization'], 403);
            }
        }

        // Get old images
        $oldDom = new DOMDocument();
        $oldDom->loadHTML($article->body, 9);
        $oldImages = $oldDom->getElementsByTagName('img');
        $oldSrcs = [];
        foreach ($oldImages as $img) {
            $oldSrcs[] = $img->getAttribute('src');
        }

        // Process new content
        $text = $request->body;
        $dom = new DOMDocument();
        $dom->loadHTML($text, 9);
        $images = $dom->getElementsByTagName('img');
        $newSrcs = [];

        foreach ($images as $key => $img) {
            $src = $img->getAttribute('src');
            if (strpos($src, 'data:image/') === 0) {
                $data = base64_decode(explode(',', explode(';', $src)[1])[1]);
                $imageName = "TextImage/" . Str::uuid() . $key . '.png';
                Storage::put('/public/ArticleStorage/' . $imageName, $data);
                $srcPath = "/storage/ArticleStorage/" . $imageName;
                $img->removeAttribute('src');
                $img->setAttribute('src', $srcPath);
                $newSrcs[] = $srcPath;
            } else {
                $newSrcs[] = $src;
            }
        }

        $text = $dom->saveHTML();

        // Delete unused images
        foreach ($oldSrcs as $oldSrc) {
            if (!in_array($oldSrc, $newSrcs)) {
                $this->deleteImageFile($oldSrc);
            }
        }

        // Handle thumbnail update
        if ($request->hasFile('thumbnail')) {
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
                $slug = $originalSlug . '-' . $counter++;
            }
            $article->slug = $slug;
        }

        $article->update([
            'title' => $request->title,
            'body' => $text,
            'org_id' => $request->org_id,
        ]);

        return redirect('/dashboard');
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);

        if ($article->user_id !== Auth::id()) {
            return response()->json(['error' => 'You do not have permission to delete this article'], 403);
        }

        $this->deleteArticleImages($article->body);

        if ($article->thumbnail && Storage::exists('public/ArticleStorage/Thumbnail/' . $article->thumbnail)) {
            Storage::delete('public/ArticleStorage/Thumbnail/' . $article->thumbnail);
        }

        $article->delete();

        return redirect('/dashboard');
    }

    public function getAll()
    {
        $query = Article::query()
            ->where('is_deleted', false)
            ->with(['user:id,name,handle,avatar_url', 'organization:id,name,handle,logo_img']);

        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('body', 'like', '%' . $search . '%');
            });
        }

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
            default:
                $query->latest();
                break;
        }

        $articles = $query->paginate(6);

        return view('pages.articles.index', compact('articles'));
    }

    public function getOne($id){

        $article = Article::findOrFail($id);

        $writer = User::findOrFail($article->user_id);

        return view('pages.articles.single')->with([
            'article' => $article,
            'writer' => $writer
        ]);

    }

    public function edit(Article $article){

        return view('pages.articles.edit', compact('article'));

    }
}
