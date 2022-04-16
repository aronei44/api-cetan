<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use App\Http\Requests\ImageRequest;
use App\Http\Resources\ImageResource;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function index(Request $request)
    {
        return ImageResource::collection(Image::where('user_id', $request->user()->id)->get());
    }
    public function store(ImageRequest $request)
    {
        try {
            $image = $request->image->store('','google');
            $url = Storage::disk('google')->url($image);
            foreach(Image::where('user_id', $request->user()->id)->get() as $image){
                $image->update([
                    'primary'=>false,
                ]);
            }
            $image = Image::create([
                'image_url'=>$url,
                'user_id'=>$request->user()->id,
            ]);
            return ImageResource::collection(Image::where('user_id', $request->user()->id)->get());
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>'Something went wrong',
            ],500);
        }
    }
    public function update(Request $request, Image $image)
    {
        try {
            if($image->user_id !== $request->user()->id){
                return response()->json([
                    'message'=>'You are not authorized to delete this image',
                ],401);
            }
            foreach(Image::where('user_id', $request->user()->id)->get() as $img){
                $img->update([
                    'primary'=>false,
                ]);
            }
            $image->update([
                'primary'=>true,
            ]);
            return ImageResource::collection(Image::where('user_id', $request->user()->id)->get());
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>'Something went wrong',
            ],500);
        }
    }
    public function destroy(Request $request, Image $image)
    {
        try {
            if($image->user_id !== $request->user()->id){
                return response()->json([
                    'message'=>'You are not authorized to delete this image',
                ],401);
            }
            $image->delete();
            return ImageResource::collection(Image::where('user_id', $request->user()->id)->get());
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>'Something went wrong',
            ],500);
        }
    }
}
