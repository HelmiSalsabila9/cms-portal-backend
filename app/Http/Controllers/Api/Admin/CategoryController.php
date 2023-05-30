<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get category
        $categories = Category::when(request()->q, function($categories) {
            $categories = $categories->where('name', 'like', '%' . request()->q . '%');
        })->latest()->paginate(10);

        return new CategoryResource(true, 'List Data Categories', $categories);
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
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'name' => 'required|unique:categories',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Upload image
        $image = $request->file('image');
        $image->storeAs('public/categories', $image->hashName());

        // Create categori
        $category = Category::create([
            'image' => $image->hashName(),
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if ($category) {
            return new CategoryResource(true, 'Data Category Berhasil Disimpan.', $category);
        }

        return new CategoryResource(false, 'Data Category Gagal Disimpan.', null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::whereId($id)->first();

        if ($category) {
            return new CategoryResource(true, 'Detail Data Category.', $category);
        }

        return new CategoryResource(false, 'Detail Data Category Tidak Ditemukan.', null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|unique:categories,name,'.$category->id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //check image update
        if ($request->file('image')) {

            //remove old image
            Storage::disk('local')->delete('public/categories/'.basename($category->image));
        
            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/categories', $image->hashName());

            //update category with new image
            $category->update([
                'image'=> $image->hashName(),
                'name' => $request->name,
                'slug' => Str::slug($request->name, '-'),
            ]);

        }

        //update category without image
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if($category) {
            //return success with Api Resource
            return new CategoryResource(true, 'Data Category Berhasil Diupdate!', $category);
        }

        //return failed with Api Resource
        return new CategoryResource(false, 'Data Category Gagal Diupdate!', null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        // remove image
        Storage::disk('local')->delete('public/categories'. basename($category->image));

        if ($category->delete()) {
            return new CategoryResource(true, 'Data Category Berhasil Dihapus.', $category);
        }

        return new CategoryResource(false, 'Data Category Gagal Dihapus.', null);
    }
}
