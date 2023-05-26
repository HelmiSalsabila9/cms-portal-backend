<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $menus = Menu::oldest()->get();

        return new MenuResource(true, 'List Data menus', $menus);
    }
}
