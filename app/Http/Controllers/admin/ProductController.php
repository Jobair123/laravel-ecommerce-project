<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;
use App\Models\TempImage;

class ProductController extends Controller
{

public function index(Request $request){
    $products = Product::latest('id')->with('product_images');
    if (!empty($request->get('keyword'))) {
        $products = $products->where('name', 'like', '%' . $request->get('keyword') . '%');
    }
   $products  = $products->paginate();
    return view('admin.products.list',compact('products'));
    
    
}
public function edit(Request $request,$id){
        $data = [];
        $product = Product::find($id);
        $categories = Category::orderBy('name','ASC')->get();
        $subCategories = SubCategory::where('category_id',$product->category_id)->get();
        //dd($subCategories);
        $brands = Brand::orderBy('name','ASC')->get();
        $data['subCategories'] = $subCategories;
        $data['product'] = $product;
        $data['categories'] = $categories;
        $data['brands'] = $brands;
    
    if(empty($data)){
       //dd($products);
        return redirect()->route('products.index');
    }
   return view('admin.products.edit',$data);

}

    public function create(){
        $data = [];
        $categories = Category::orderBy('name','ASC')->get();
        $brands = Brand::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        return view('admin.products.create', $data);
        
    }

    public function store(Request $request){
   
    $rules = [ 'title' => 'required',
    'slug' => 'required|unique:products',
    'price' => 'required | numeric',
    'sku' => 'required|unique:products',
    'track_qty' => 'required|in:Yes,No',
    'category' => 'required|numeric',
    'is_featured' => 'required|in:Yes,No'
];
if(!empty($request->track_qty)&& $request->track_qty == 'Yes'){
    $rules['qty'] = 'required|numeric';
}

    $validator = Validator::make($request->all(),$rules);
    if($validator->passes()){
     $product = new Product;
     $product->name = $request->title;
     $product->slug = $request->slug;
     $product->description = $request->description;
     $product->price = $request->price;
     $product->compare_price = $request->compare_price;
     $product->sku = $request->sku;
     $product->barcode = $request->barcode;
     $product->track_qty = $request->track_qty;
     $product->qty = $request->qty;
     $product->status = $request->status;
     $product->category_id = $request->category;
     $product->sub_category_id = $request->sub_category;
     $product->brand_id = $request->brand;
     $product->is_featured = $request->is_featured;
     $product->save();

     // Save Gallery pics
     if(!empty($request->image_array)){
        foreach ($request->image_array as $temp_image_id){

       $tempImageinfo = TempImage::find($temp_image_id);
       $extarray = explode('.',$tempImageinfo->name);
       $ext = last($extarray);
            $productImage = new ProductImage();
            $productImage->product_id = $product->id;
            $productImage->image = 'NULL';
            $productImage->save();
            
            $imageName = $product->id.'-'.$productImage->id.'-'.time().'.'.$ext;

            $productImage->image = $imageName;
            $productImage->save();

            //Genarate Product Thumbnail

            //Large Image
            $sourcePath = public_path().'/temp/'.$tempImageinfo->name;
            $desPath = public_path().'/uploads/product/large/'.$imageName;
            $image = Image::read($sourcePath);
            $image->scale(width: 1400);
            $image->save($desPath);

              //Small Image
            
              $desPath = public_path().'/uploads/product/small/'.$imageName;
              $image = Image::read($sourcePath);
              $image = $image->resizeDown(300, 300); 
              $image->save($desPath);
              
            


              
        }

     }
     
    $request->session()->flash('success','Products added Successfull');
     return response()->json([
        'status' => true,
        'message' => 'Products added Successfull'
    ]);

    }else{
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ]);
    }


    }
}
