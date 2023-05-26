<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get Menus
        $menus = Menu::when(request()->q, function($menus) {
            $menus = $menus->where('name', 'like', '%' . request()->q . '%');
        })->oldest()->paginate(10);

        return new MenuResource(true, 'List Data Menu', $menus);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:menus',
            'url' => 'required|unique:menus',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Membuat menus
        $menu = Menu::create([
            'name' => $request->name,
            'url' => $request->url,
        ]);

        if ($menu) {
            return new MenuResource(true, 'Data Menu Berhasil Disimpan.', $menu);
        }

        return new MenuResource(false, 'Data Menu Gagal Disimpan.', null);
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $menu = Menu::whereId($id)->first();

        if($menu) {
            return new MenuResource(true, 'Detail Data Menu.', $menu);
        }

        return new MenuResource(false, 'Detail Data Menu Tidak Ditemukan.', null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Menu $menu)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:menus,name,'. $menu->id,
            'url' => 'required|unique:menus,url,'. $menu->id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update menu
        $menu->update([
            'name' => $request->name,
            'url' => $request->url,
        ]);

        if ($menu) {
            return new MenuResource(true, 'Data Menu Berhasil Diupdate.', $menu);
        }

        return new MenuResource(false, 'Data Menu Gagal Diupdate.', null);
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Menu $menu)
    {
        if($menu->delete()) {
            return new MenuResource(true, 'Data Menu Berhasil Dihapus.', null);
        }

        return new MenuResource(false, 'Data Menu Gagal Dihapus.', null);
    }
}
