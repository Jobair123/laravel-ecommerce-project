<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminloginController;
use  App\Http\Controllers\adminHomeController;
use  App\Http\Controllers\admin\CategoryController;
use  App\Http\Controllers\admin\SubCategoryController;
use  App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\TempImageController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ProductSubCategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
//Route::get('admin/login',[AdminloginController::class,'adminlogin']);
Route::group(['prefix' => 'admin'],function(){
    Route::group(['middleware' => 'admin.guest'],function(){
        Route::get('/login',[AdminloginController::class,'adminlogin'])->name('admin.login');
        Route::post('/authenticate',[AdminloginController::class,'authenticate'])->name('admin.authenticate');
    });
    Route::group(['middleware' => 'admin.auth'],function(){
        Route::get('/deshboard',[adminHomeController::class,'index'])->name('admin.deshboard');
        Route::get('/logout',[adminHomeController::class,'logout'])->name('admin.logout');
        //category routes
        Route::get('/categories',[CategoryController::class,'index'])->name('categories.index');
        Route::get('/categories/create',[CategoryController::class,'create'])->name('categories.create');
        Route::post('/categories/store',[CategoryController::class,'store'])->name('categories.store');

        Route::get('/categories/{id}/edit',[CategoryController::class,'edit'])->name('categories.edit');
        Route::put('/categories/{id}',[CategoryController::class,'update'])->name('categories.update');
        Route::delete('/categories/{id}',[CategoryController::class,'destroy'])->name('categories.delete');
        //temp_image
        Route::post('/upload-temp-image/store',[TempImageController::class,'create'])->name('temp-images.create');
        //sub category routes
        Route::get('/sub-categories/create',[SubCategoryController::class,'create'])->name('sub-categories.create');
        Route::post('/sub-categories/store',[SubCategoryController::class,'store'])->name('sub-categories.store');
        Route::get('/sub-categories',[SubCategoryController::class,'index'])->name('sub-categories.index');
        Route::get('/subcategories/{id}/edit',[SubCategoryController::class,'edit'])->name('sub-categories.edit');
        Route::put('/sub-categories/{id}',[SubCategoryController::class,'update'])->name('sub-categories.update');
        Route::delete('/sub-categories/{id}',[SubCategoryController::class,'destroy'])->name('sub-categories.delete');
       
        Route::get('/getSlug', function(Request $request){ 
            $slug = ''; 
            if(!empty($request->title)){ 
                $slug = Str::slug($request->title); 
            } return response()->json([ 'status' => true, 'slug' => $slug ]); 
        })->name('getSlug');

        //brands
        Route::get('/brands/create',[BrandController::class,'create'])->name('brands.create');
        Route::post('/brands/store',[BrandController::class,'store'])->name('brands.store');
        Route::get('/brands',[BrandController::class,'index'])->name('brands.index');
        Route::get('/brands/{id}/edit',[BrandController::class,'edit'])->name('brands.edit');
        Route::put('/brands/{id}',[BrandController::class,'update'])->name('brands.update');
        Route::delete('/brands/{id}',[BrandController::class,'destroy'])->name('brands.delete');

        //products
        Route::get('/products/create',[ProductController::class,'create'])->name('products.create');
        Route::get('/product-subcategories',[ProductSubCategoryController::class,'index'])->name('product-subcategories.index');
        Route::post('/products/store',[ProductController::class,'store'])->name('products.store');
        Route::get('/products',[ProductController::class,'index'])->name('products.index');
        Route::get('/products/{id}/edit',[ProductController::class,'edit'])->name('products.edit');








    });
});