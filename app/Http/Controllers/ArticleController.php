<?php

namespace App\Http\Controllers;

use App\Http\Requests\Article\StoreArticleRequest;
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

    public function store(StoreArticleRequest $request){

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
}
