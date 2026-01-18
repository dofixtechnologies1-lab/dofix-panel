<?php

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

use Illuminate\Support\Facades\Route;
use Modules\CategoryManagement\Http\Controllers\Web\Admin\CategoryController;
use Modules\CategoryManagement\Http\Controllers\Web\Admin\ExtraController;

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Web\Admin', 'middleware' => ['admin']], function () {

    Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
        Route::any('create', 'CategoryController@create')->name('create');
        Route::post('store', 'CategoryController@store')->name('store');
        Route::get('edit/{id}', 'CategoryController@edit')->name('edit');
        Route::put('update/{id}', 'CategoryController@update')->name('update');
        Route::any('status-update/{id}', 'CategoryController@statusUpdate')->name('status-update');
        Route::any('featured-update/{id}', 'CategoryController@featuredUpdate')->name('featured-update');
        Route::any('top_rated/{id}', 'CategoryController@top_ratedUpdate')->name('top_rated-update');
        Route::any('quick_repair/{id}', 'CategoryController@quick_repairUpdate')->name('quick_repair-update');
        Route::delete('delete/{id}', 'CategoryController@destroy')->name('delete');
        Route::get('childes', 'CategoryController@childes');
        Route::get('ajax-childes/{id}', 'CategoryController@ajaxChildes')->name('ajax-childes');
        Route::get('ajax-childes-only/{id}', 'CategoryController@ajaxChildesOnly')->name('ajax-childes-only');
        Route::get('download', 'CategoryController@download')->name('download');
    });

    Route::group(['prefix' => 'sub-category', 'as' => 'sub-category.'], function () {
        Route::any('create', 'SubCategoryController@create')->name('create');
        Route::post('store', 'SubCategoryController@store')->name('store');
        Route::get('edit/{id}', 'SubCategoryController@edit')->name('edit');
        Route::put('update/{id}', 'SubCategoryController@update')->name('update');
        Route::any('status-update/{id}', 'SubCategoryController@statusUpdate')->name('status-update');
        Route::delete('delete/{id}', 'SubCategoryController@destroy')->name('delete');
        Route::get('download', 'SubCategoryController@download')->name('download');
    });


    Route::group(['prefix' => 'extra', 'as' => 'extra.'], function () {
        
        Route::get('create', [ExtraController::class,'create'])->name('create');
        Route::post('store', [ExtraController::class,'store'])->name('store');
        Route::get('edit/{id}', [ExtraController::class,'edit'])->name('edit');
        Route::post('update', [ExtraController::class,'update'])->name('update');
        Route::delete('delete/{id}', [ExtraController::class,'destroy'])->name('delete');
        Route::get('download', [ExtraController::class,'download'])->name('download');
        Route::any('status-update/{id}', [ExtraController::class,'statusUpdate'])->name('status-update');

    });

    // Route::group(['prefix' => 'home-category-slider', 'as' => 'home-category-slider.'], function () {
        
    //     Route::get('/', [CategoryController::class,'categoryHomeCreate'])->name('categoryHomeCreate');
    //     Route::post('store', [CategoryController::class,'categoryHomeStore'])->name('categoryHomeStore');
    // });


});
