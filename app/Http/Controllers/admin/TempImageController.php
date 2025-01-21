<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TempImage;
use Intervention\Image\Laravel\Facades\Image;

class TempImageController extends Controller
{
    public function create(Request $request){
    $image = $request->image;
    if(!empty($image)){
        $ext = $image->getClientOriginalExtension();
        $newName = time().'.'.$ext;

        $tempImage = new TempImage();
        $tempImage->name = $newName;
        $tempImage->save();

        $image->move(public_path().'/temp',$newName);

        //Genarate Thumbnail

        $srcPath = public_path().'/temp/'.$newName;
        $destPath = public_path().'/temp/thumb/'.$newName;
        $image_1 = Image::read($srcPath);

        $image_1->resize(300,250);
        $image_1->save($destPath);
        

        return response()->json([
            'status'=> true,
            'image_id'=> $tempImage->id,
            'ImagePath'=> asset('temp/thumb/'.$newName),
            'message'=> 'Image uploaded successfully'
        ]);
    }

    }
}
