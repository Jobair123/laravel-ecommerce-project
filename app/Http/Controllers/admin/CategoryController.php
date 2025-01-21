<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::latest();

        if (!empty($request->get('keyword'))) {
            $categories = $categories->where('name', 'like', '%' . $request->get('keyword') . '%');
        }

        $categories = $categories->paginate(10);

        return view('admin.category.list', compact('categories'));
    }

    public function create()
    {
        return view('admin.category.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories'
        ]);

        if ($validator->passes()) {
            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();

            // Save image
            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id . '.' . $ext;
                $sPath = public_path('/temp/' . $tempImage->name);
                $dPath = public_path('/uploads/category/' . $newImageName);
                File::copy($sPath, $dPath);

                // Thumbnail
               

                $thumbPath = public_path('/uploads/category/thumb/' . $newImageName);
                $image = Image::read($sPath);
                $image->resize(450, 600, function ($constraint) {
                    $constraint->upsize();
                });
                
                $image->save($thumbPath);

                $category->image = $newImageName;
                $category->save();
            }

            $request->session()->flash('success', 'Category Added Successfully');
            return response()->json([
                'status' => true,
                'message' => 'Category Added Successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit($categoryId,Request $request)
    {
        
        $category = Category::find($categoryId);
        if(empty($category)){
            return redirect()->route('categories.index');
        }
        return view('admin.category.edit',compact('category'));
    }

    public function update($categoryId, Request $request)
    {   
        $category = Category::find($categoryId);
        if(empty($category)){
            $request->session()->flash('error','Category not found');
            
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => "Category not found!"
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$category->id.',id'
        ]);

        if ($validator->passes()) {
           
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();

            // Save image
            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);
                $oldImage = $category->image;

                $newImageName = $category->id . '-'. time().'.' . $ext;
                $sPath = public_path('/temp/' . $tempImage->name);
                $dPath = public_path('/uploads/category/' . $newImageName);
               

                File::copy($sPath, $dPath);

                // Thumbnail
               

                $thumbPath = public_path('/uploads/category/thumb/' . $newImageName);
                $image = Image::read($sPath);
                $image->resize(450, 600, function ($constraint) {
                    $constraint->upsize();
                });
                
               
                // Delete old image if exists 
                // if (File::exists($dPath)) { File::delete($dPath); }
                // if (File::exists($thumbPath)) { File::delete($thumbPath); }

                $image->save($thumbPath);
                
                $category->image = $newImageName;
                $category->save();
                
                File::delete(public_path('/uploads/category/thumb/' . $oldImage));
                File::delete(public_path('/uploads/category/' . $oldImage));


                
            }

            $request->session()->flash('success', 'Category updated successfully');
            return response()->json([
                'status' => true,
                'message' => 'Category updated successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    

    public function destroy($categoryId, Request $request)
    {
        $category = Category::find($categoryId);
        if(empty($category)){
            $request->session()->flash('error','Category not found');
            return response()->json([
                'status' => true,
                'message' => 'Category not found!'
            ]);
      
        }

        File::delete(public_path('/uploads/category/thumb/' . $category->image));
        File::delete(public_path('/uploads/category/' . $category->image));

        $category->delete();

        $request->session()->flash('success','Category deleted successfull');

        return response()->json([
            'status' => true,
            'message' => 'Category deleted successfull!'
        ]);

    }
}
