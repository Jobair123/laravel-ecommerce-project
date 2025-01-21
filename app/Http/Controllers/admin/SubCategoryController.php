<?php

namespace App\Http\Controllers\admin;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function index(Request $request){
        $subcategories = SubCategory::select('sub_categories.*','categories.name as categoryName')
        ->latest('sub_categories.id')
        ->leftJoin('categories','categories.id','sub_categories.category_id');

        if (!empty($request->get('keyword'))) {
            $subcategories = $subcategories->where('sub_categories.name', 'like', '%' . $request->get('keyword') . '%');
            $subcategories = $subcategories->orwhere('categories.name', 'like', '%' . $request->get('keyword') . '%');
        }

        $subcategories = $subcategories->paginate(10);

        return view('admin.sub_category.list', compact('subcategories'));
    }
    public function create(){
        $categories = Category::orderBy('name','ASC')->get();
        return view('admin.sub_category.create',compact('categories'));
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
    'name' => 'required',
    'slug' => 'required | unique:sub_categories',
    'category' => 'required',
    'status' => 'required'
]);
    if($validator->passes()){
       $Subcategory = new SubCategory();
       $Subcategory->name = $request->name;
       $Subcategory->slug = $request->slug;
       $Subcategory->status = $request->status;
       $Subcategory->category_id = $request->category;
       $Subcategory->save();
       
       $request->session()->flash('success','Sub category created successfully');

       return response([
        'status' => true,
        'message' => 'Sub category created successfully'
    ]);

    }else{
        return response([
            'status' => false,
            'errors' => $validator->errors()
        ]);

    }
    }

    public function edit($id, Request $request){
        $subcategory = SubCategory::find($id);
        if(empty($subcategory)){
        $request->session()->flash('error','Record not found');
        return redirect()->route('sub-categories.index');
        }
        $categories = Category::orderBy('name','ASC')->get();
        return view('admin.sub_category.edit',compact('subcategory','categories'));
    }

    public function update($id, Request $request){
        $subcategory = SubCategory::find($id);
        if(empty($subcategory)){
            $request->session()->flash('error','Category not found');
            
            return response()->json([
                'status' => false,
                'notFound' => true
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:sub_categories,slug,'.$subcategory->id.',id',
            'category' => 'required',
            'status'=> 'required'
        ]);

        if ($validator->passes()) {
           
            $subcategory->name = $request->name;
            $subcategory->slug = $request->slug;
            $subcategory->status = $request->status;
            $subcategory->category_id = $request->category;
            $subcategory->save();
        }
        $request->session()->flash('success', 'Sub Category updated successfully');
        return response()->json([
            'status' => true,
            'message' => 'Sub Category updated successfully'
        ]);
    }
    public function destroy($id, Request $request){
        $subcategory = SubCategory::find($id);
        if(empty($subcategory)){
            $request->session()->flash('error','Category not found');
            return response()->json([
                'status' => true,
                'message' => 'Category not found!'
            ]);
        }
        $subcategory->delete();

        $request->session()->flash('success','Sub category delete successfull');
        return response()->json([
            'status' => true,
            'message' => 'Sub Category deleted successfull!'
        ]);
    }
}
