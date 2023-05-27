<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $tags = Tag::latest()->get();

        return new TagResource(true, 'List Data Tag', $tags);
    }

    /**
     * show
     *
     * @param  mixed $slug
     * @return void
     */
    public function show($slug) 
    {
        $tags = Tag::with('posts.tags', 'posts.category', 'posts.comments')->where('slug', $slug)->first();

        if ($tags) {
            return new TagResource(true, 'List Data Posts By Tag', $tags);
        }
        return new TagResource(false, 'Data Tag Tidak Ditemukan.', null);
    }

    /**
     * footer
     *
     * @param  mixed $slug
     * @return void
     */
    public function footer()
    {
        $tags = Tag::latest()->take(12)->get();

        return new TagResource(true, 'List Data Tag', $tags);
    }
}
