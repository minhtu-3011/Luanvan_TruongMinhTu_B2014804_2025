<?php

use App\Http\Controllers\Ajax\LocationController;
use App\Http\Middleware\AuthenticateMiddleware;
use App\Http\Middleware\LoginMiddleware;
use Illuminate\Routing\RouteGroup;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\AuthController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\SlideController;
use App\Http\Controllers\Ajax\SlideController as AjaxSlideController;
use App\Http\Controllers\Ajax\DashboardController as AjaxDashboardController;
use App\Http\Controllers\Ajax\AttributeController as AjaxAttributeController;
use App\Http\Controllers\Ajax\MenuController as AjaxMenuController;
use App\Http\Controllers\Backend\SystemController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\MenuController;
use App\Http\Controllers\Backend\UserCatalogueController;
use App\Http\Controllers\Backend\PostCatalogueController;
use App\Http\Controllers\Backend\PostController;
use App\Http\Controllers\Backend\LanguageController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\GenerateController;
use App\Http\Controllers\Backend\ProductCatalogueController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\AttributeCatalogueController;
use App\Http\Controllers\Backend\AttributeController;
//@@useController@@





Route::get('/', [AuthController::class, 'index'])->name('auth.admin')
    ->middleware(LoginMiddleware::class);
Route::get('admin', [AuthController::class, 'index'])->name('auth.admin')
    ->middleware(LoginMiddleware::class);
Route::get('logout', [AuthController::class, 'logout'])->name('auth.logout');
Route::post('login', [AuthController::class, 'login'])->name('auth.login')
    ->middleware(LoginMiddleware::class);;


Route::group(['middleware' => ['admin', 'locale', 'backend_default_locale']], function () {
    Route::get('dashboard/index', [DashboardController::class, 'index'])->name('dashboard.index')
        ->middleware(AuthenticateMiddleware::class);


    // USER
    Route::group(['prefix' => 'user'], function () {
        Route::get('index', [UserController::class, 'index'])->name('user.index')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('create', [UserController::class, 'create'])->name('user.create')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('store', [UserController::class, 'store'])->name('user.store')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/edit', [UserController::class, 'edit'])->where(['id' => '[0-9]+'])->name('user.edit')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('{id}/update', [UserController::class, 'update'])->where(['id' => '[0-9]+'])->name('user.update')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/delete', [UserController::class, 'delete'])->where(['id' => '[0-9]+'])->name('user.delete')
            ->middleware(AuthenticateMiddleware::class);
        Route::delete('{id}/destroy', [UserController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('user.destroy')
            ->middleware(AuthenticateMiddleware::class);
    });


    Route::group(['prefix' => 'user/catalogue'], function () {
        Route::get('index', [UserCatalogueController::class, 'index'])->name('user.catalogue.index')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('create', [UserCatalogueController::class, 'create'])->name('user.catalogue.create')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('store', [UserCatalogueController::class, 'store'])->name('user.catalogue.store')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/edit', [UserCatalogueController::class, 'edit'])->where(['id' => '[0-9]+'])->name('user.catalogue.edit')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('permission', [UserCatalogueController::class, 'permission'])->name('user.catalogue.permission')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('{id}/update', [UserCatalogueController::class, 'update'])->where(['id' => '[0-9]+'])->name('user.catalogue.update')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/delete', [UserCatalogueController::class, 'delete'])->where(['id' => '[0-9]+'])->name('user.catalogue.delete')
            ->middleware(AuthenticateMiddleware::class);
        Route::delete('{id}/destroy', [UserCatalogueController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('user.catalogue.destroy')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('updatePermission', [UserCatalogueController::class, 'updatePermission'])->name('user.catalogue.updatePermission')
            ->middleware(AuthenticateMiddleware::class);
    });




    Route::group(['prefix' => 'language'], function () {
        Route::get('index', [LanguageController::class, 'index'])->name('language.index')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('create', [LanguageController::class, 'create'])->name('language.create')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('store', [LanguageController::class, 'store'])->name('language.store')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/edit', [LanguageController::class, 'edit'])->where(['id' => '[0-9]+'])->name('language.edit')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('{id}/update', [LanguageController::class, 'update'])->where(['id' => '[0-9]+'])->name('language.update')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/delete', [LanguageController::class, 'delete'])->where(['id' => '[0-9]+'])->name('language.delete')
            ->middleware(AuthenticateMiddleware::class);
        Route::delete('{id}/destroy', [LanguageController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('language.destroy')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/switch', [LanguageController::class, 'switchBackendLanguage'])->where(['id' => '[0-9]+'])->name('language.switch')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/{languageId}/{model}/translate', [LanguageController::class, 'translate'])->where(['id' => '[0-9]+', 'languageId' => '[0-9]+'])->name('language.translate')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('storeTranslate', [LanguageController::class, 'storeTranslate'])->name('language.storeTranslate')
            ->middleware(AuthenticateMiddleware::class);
    });

    Route::group(['prefix' => 'generate'], function () {
        Route::get('index', [GenerateController::class, 'index'])->name('generate.index')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('create', [GenerateController::class, 'create'])->name('generate.create')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('store', [GenerateController::class, 'store'])->name('generate.store')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/edit', [GenerateController::class, 'edit'])->where(['id' => '[0-9]+'])->name('generate.edit')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('{id}/update', [GenerateController::class, 'update'])->where(['id' => '[0-9]+'])->name('generate.update')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/delete', [GenerateController::class, 'delete'])->where(['id' => '[0-9]+'])->name('generate.delete')
            ->middleware(AuthenticateMiddleware::class);
        Route::delete('{id}/destroy', [GenerateController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('generate.destroy')
            ->middleware(AuthenticateMiddleware::class);
    });

    Route::group(['prefix' => 'system'], function () {
        Route::get('index', [SystemController::class, 'index'])->name('system.index');
        Route::post('store', [SystemController::class, 'store'])->name('system.store');
        Route::get('{languageId}/translate', [SystemController::class, 'translate'])->where(['id' => '[0-9]+'])->name('system.translate');
        Route::post('{languageId}/saveTranslate', [SystemController::class, 'saveTranslate'])->where(['id' => '[0-9]+'])->name('system.save.translate');
    });


    Route::group(['prefix' => 'menu'], function () {
        Route::get('index', [MenuController::class, 'index'])->name('menu.index')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('create', [MenuController::class, 'create'])->name('menu.create')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('store', [MenuController::class, 'store'])->name('menu.store')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/edit', [MenuController::class, 'edit'])->where(['id' => '[0-9]+'])->name('menu.edit')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/editMenu', [MenuController::class, 'editMenu'])->where(['id' => '[0-9]+'])->name('menu.editMenu')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('{id}/update', [MenuController::class, 'update'])->where(['id' => '[0-9]+'])->name('menu.update')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/delete', [MenuController::class, 'delete'])->where(['id' => '[0-9]+'])->name('menu.delete')
            ->middleware(AuthenticateMiddleware::class);
        Route::delete('{id}/destroy', [MenuController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('menu.destroy')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/children', [MenuController::class, 'children'])->where(['id' => '[0-9]+'])->name('menu.children')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('{id}/saveChildren', [MenuController::class, 'saveChildren'])->where(['id' => '[0-9]+'])->name('menu.save.children')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{languageId}/{id}/translate', [MenuController::class, 'translate'])->where(['languageId' => '[0-9]+', 'id' => '[0-9]+'])->name('menu.translate')
            ->middleware(AuthenticateMiddleware::class);;
        Route::post('{languageId}/saveTranslate', [MenuController::class, 'saveTranslate'])->where(['languageId' => '[0-9]+'])->name('menu.translate.save')
            ->middleware(AuthenticateMiddleware::class);;
    });

    Route::group(['prefix' => 'post/catalogue'], function () {
        Route::get('index', [PostCatalogueController::class, 'index'])->name('post.catalogue.index')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('create', [PostCatalogueController::class, 'create'])->name('post.catalogue.create')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('store', [PostCatalogueController::class, 'store'])->name('post.catalogue.store')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/edit', [PostCatalogueController::class, 'edit'])->where(['id' => '[0-9]+'])->name('post.catalogue.edit')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('{id}/update', [PostCatalogueController::class, 'update'])->where(['id' => '[0-9]+'])->name('post.catalogue.update')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/delete', [PostCatalogueController::class, 'delete'])->where(['id' => '[0-9]+'])->name('post.catalogue.delete')
            ->middleware(AuthenticateMiddleware::class);
        Route::delete('{id}/destroy', [PostCatalogueController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('post.catalogue.destroy')
            ->middleware(AuthenticateMiddleware::class);
    });


    Route::group(['prefix' => 'slide'], function () {
        Route::get('index', [SlideController::class, 'index'])->name('slide.index')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('create', [SlideController::class, 'create'])->name('slide.create')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('store', [SlideController::class, 'store'])->name('slide.store')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/edit', [SlideController::class, 'edit'])->where(['id' => '[0-9]+'])->name('slide.edit')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('{id}/update', [SlideController::class, 'update'])->where(['id' => '[0-9]+'])->name('slide.update')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/delete', [SlideController::class, 'delete'])->where(['id' => '[0-9]+'])->name('slide.delete')
            ->middleware(AuthenticateMiddleware::class);
        Route::delete('{id}/destroy', [SlideController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('slide.destroy')
            ->middleware(AuthenticateMiddleware::class);
    });



    Route::group(['prefix' => 'post'], function () {
        Route::get('index', [PostController::class, 'index'])->name('post.index')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('create', [PostController::class, 'create'])->name('post.create')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('store', [PostController::class, 'store'])->name('post.store')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/edit', [PostController::class, 'edit'])->where(['id' => '[0-9]+'])->name('post.edit')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('{id}/update', [PostController::class, 'update'])->where(['id' => '[0-9]+'])->name('post.update')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/delete', [PostController::class, 'delete'])->where(['id' => '[0-9]+'])->name('post.delete')
            ->middleware(AuthenticateMiddleware::class);
        Route::delete('{id}/destroy', [PostController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('post.destroy')
            ->middleware(AuthenticateMiddleware::class);
    });



    Route::group(['prefix' => 'product/catalogue'], function () {
        Route::get('index', [ProductCatalogueController::class, 'index'])->name('product.catalogue.index')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('create', [ProductCatalogueController::class, 'create'])->name('product.catalogue.create')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('store', [ProductCatalogueController::class, 'store'])->name('product.catalogue.store')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/edit', [ProductCatalogueController::class, 'edit'])->where(['id' => '[0-9]+'])->name('product.catalogue.edit')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('{id}/update', [ProductCatalogueController::class, 'update'])->where(['id' => '[0-9]+'])->name('product.catalogue.update')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/delete', [ProductCatalogueController::class, 'delete'])->where(['id' => '[0-9]+'])->name('product.catalogue.delete')
            ->middleware(AuthenticateMiddleware::class);
        Route::delete('{id}/destroy', [ProductCatalogueController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('product.catalogue.destroy')
            ->middleware(AuthenticateMiddleware::class);
    });


    Route::group(['prefix' => 'product'], function () {
        Route::get('index', [ProductController::class, 'index'])->name('product.index')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('create', [ProductController::class, 'create'])->name('product.create')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('store', [ProductController::class, 'store'])->name('product.store')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/edit', [ProductController::class, 'edit'])->where(['id' => '[0-9]+'])->name('product.edit')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('{id}/update', [ProductController::class, 'update'])->where(['id' => '[0-9]+'])->name('product.update')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/delete', [ProductController::class, 'delete'])->where(['id' => '[0-9]+'])->name('product.delete')
            ->middleware(AuthenticateMiddleware::class);
        Route::delete('{id}/destroy', [ProductController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('product.destroy')
            ->middleware(AuthenticateMiddleware::class);
    });


    Route::group(['prefix' => 'attribute/catalogue'], function () {
        Route::get('index', [AttributeCatalogueController::class, 'index'])->name('attribute.catalogue.index')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('create', [AttributeCatalogueController::class, 'create'])->name('attribute.catalogue.create')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('store', [AttributeCatalogueController::class, 'store'])->name('attribute.catalogue.store')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/edit', [AttributeCatalogueController::class, 'edit'])->where(['id' => '[0-9]+'])->name('attribute.catalogue.edit')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('{id}/update', [AttributeCatalogueController::class, 'update'])->where(['id' => '[0-9]+'])->name('attribute.catalogue.update')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/delete', [AttributeCatalogueController::class, 'delete'])->where(['id' => '[0-9]+'])->name('attribute.catalogue.delete')
            ->middleware(AuthenticateMiddleware::class);
        Route::delete('{id}/destroy', [AttributeCatalogueController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('attribute.catalogue.destroy')
            ->middleware(AuthenticateMiddleware::class);
    });


    Route::group(['prefix' => 'attribute'], function () {
        Route::get('index', [AttributeController::class, 'index'])->name('attribute.index')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('create', [AttributeController::class, 'create'])->name('attribute.create')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('store', [AttributeController::class, 'store'])->name('attribute.store')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/edit', [AttributeController::class, 'edit'])->where(['id' => '[0-9]+'])->name('attribute.edit')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('{id}/update', [AttributeController::class, 'update'])->where(['id' => '[0-9]+'])->name('attribute.update')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/delete', [AttributeController::class, 'delete'])->where(['id' => '[0-9]+'])->name('attribute.delete')
            ->middleware(AuthenticateMiddleware::class);
        Route::delete('{id}/destroy', [AttributeController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('attribute.destroy')
            ->middleware(AuthenticateMiddleware::class);
    });

    //@@new-module@@














    Route::group(['prefix' => 'permission'], function () {
        Route::get('index', [PermissionController::class, 'index'])->name('permission.index')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('create', [PermissionController::class, 'create'])->name('permission.create')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('store', [PermissionController::class, 'store'])->name('permission.store')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/edit', [PermissionController::class, 'edit'])->where(['id' => '[0-9]+'])->name('permission.edit')
            ->middleware(AuthenticateMiddleware::class);
        Route::post('{id}/update', [PermissionController::class, 'update'])->where(['id' => '[0-9]+'])->name('permission.update')
            ->middleware(AuthenticateMiddleware::class);
        Route::get('{id}/delete', [PermissionController::class, 'delete'])->where(['id' => '[0-9]+'])->name('permission.delete')
            ->middleware(AuthenticateMiddleware::class);
        Route::delete('{id}/destroy', [PermissionController::class, 'destroy'])->where(['id' => '[0-9]+'])->name('permission.destroy')
            ->middleware(AuthenticateMiddleware::class);
    });


    // Route::get('user/update', [UserController::class,'update'])->name('user.index')
    // ->middleware(AuthenticateMiddleware::class);
    // Route::get('user/del', [UserController::class,'del'])->name('user.index')
    // ->middleware(AuthenticateMiddleware::class);

    //AJAX
    Route::get('ajax/location/getLocation', [LocationController::class, 'getLocation'])->name('ajax.location.index')
        ->middleware(AuthenticateMiddleware::class);
    Route::post('ajax/dashboard/changeStatus', [AjaxDashboardController::class, 'changeStatus'])->name('ajax.dashboard.changeStatus')
        ->middleware(AuthenticateMiddleware::class);
    Route::post('ajax/dashboard/changeStatusAll', [AjaxDashboardController::class, 'changeStatusAll'])->name('ajax.dashboard.changeStatusAll')
        ->middleware(AuthenticateMiddleware::class);
    Route::get('ajax/dashboard/getMenu', [AjaxDashboardController::class, 'getMenu'])->name('ajax.dashboard.getMenu')
        ->middleware(AuthenticateMiddleware::class);
    Route::get('ajax/attribute/getAttribute', [AjaxAttributeController::class, 'getAttribute'])->name('ajax.attribute.getAttribute')
        ->middleware(AuthenticateMiddleware::class);
    Route::get('ajax/attribute/loadAttribute', [AjaxAttributeController::class, 'loadAttribute'])->name('ajax.attribute.loadAttribute')
        ->middleware(AuthenticateMiddleware::class);
    Route::post('ajax/menu/createCatalogue', [AjaxMenuController::class, 'createCatalogue'])->name('ajax.menu.createCatalogue')
        ->middleware(AuthenticateMiddleware::class);
    Route::post('ajax/menu/drag', [AjaxMenuController::class, 'drag'])->name('ajax.menu.drag')
        ->middleware(AuthenticateMiddleware::class);
    Route::post('ajax/menu/deleteMenu', [AjaxMenuController::class, 'deleteMenu'])->name('ajax.menu.deleteMenu')
        ->middleware(AuthenticateMiddleware::class);;
    Route::post('ajax/slide/order', [AjaxSlideController::class, 'order'])->name('ajax.slide.order')
        ->middleware(AuthenticateMiddleware::class);;
});
