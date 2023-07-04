<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Menu;
use App\Models\Post;
use App\Models\Slider;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $posts = Post::count();
        $tags = Tag::count();
        $comments = Comment::count();
        $categories = Category::count();
        $menus = Menu::count();
        $sliders = Slider::count();
        $users = User::count();

        return response()->json([
            'success' => true,
            'message' => 'List Jumlah Data Keseluruhan',
            'data' => [
                'posts' => $posts,
                'tags' => $tags,
                'comments' => $comments,
                'categories' => $categories,
                'menus' => $menus,
                'sliders' => $sliders,
                'users' => $users,
            ],
        ], 200);
    }
}
