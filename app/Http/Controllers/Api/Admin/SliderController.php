<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Slider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SliderResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sliders = Slider::oldest()->paginate(5);

        return new SliderResource(true, 'List Data Sliders.', $sliders);
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
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2000'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Upload image
        $image = $request->file('image');
        $image->storeAs('public/sliders', $image->hashName());

        $slider = Slider::create([
            'image' => $image->hashName(),
        ]);

        if ($slider) {
            return new SliderResource(true, 'Data Sliders Berhasil Disimpan.', $slider);
        }

        return new SliderResource(false, 'Data Slider Gagal Disimpan.', null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $slide = Slider::whereId($id)->first();

        if ($slide) {
            return new SliderResource(true, 'Detail Data Slider', $slide);
        }

        return new SliderResource(false, 'Detail Data Slider Tidak Ditemukan', null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Slider $slider)
    {

        // Cek image
        if ($request->file('image')) {
            // Hapus image lama
            Storage::disk('local')->delete('public/sliders/' .basename($slider->image));

            // Kemudian tampung
            $image = $request->file('image');
            $image->storeAs('public/sliders', $image->hashName());

            $slider->update([
                'image' => $image->hashName(),
            ]);

            if ($slider) {
                return new SliderResource(true, 'Data Slider Berhasil Diupdate.', $slider);
            }

            return new SliderResource(false, 'Data Slider Gagal Diupdate.', null);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Slider $slider)
    {
        // Remove Image
        Storage::disk('local')->delete('public/sliders/' . basename($slider->image));

        if ($slider->delete()) {
            return new SliderResource(true, 'Data Sliders Berhasil Dihapus.', null);
        }

        return new SliderResource(false, 'Data Sliders Gagal Dihapus.', null);
    }
}
