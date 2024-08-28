<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'api/admin', 'namespace' => 'Admin\Api', 'as' => 'api.admin.', 'middleware' => ['api']], function () {
    Route::post('login', 'LoginController@loginApi');
    Route::post('register', 'LoginController@register');

    Route::group(['middleware' => ['admin_authenticated:sanctum']], function () {

        //products routes
        Route::post('products-list', 'ProductController@getProductsList');
        Route::post('products', 'ProductController@SaveProduct');
        Route::post('product/{id}', 'ProductController@getProductById');
        Route::put('products/{id}', 'ProductController@EditProduct');
        Route::delete('products/{id}', 'ProductController@DeleteProduct');
        Route::post('get-products', 'ProductController@getProducts');

        Route::post('/admin-profile-update', 'HomeController@updateAdminProfile');
        
        Route::post('site-settings', 'SiteSettingsController@getSiteSettings');
        Route::post('update-settings/{id}', 'SiteSettingsController@updateSettings');
        Route::post('password-update', 'SiteSettingsController@passwordReset');
        Route::post('admin-details', 'DashboardController@getAdminDetails');

        
        //dashboard
        Route::post('admin-list', 'AdminController@index');
        Route::post('admin-save', 'AdminController@saveAdmin');
        Route::post('admin-edit/{id}', 'AdminController@editAdmin');
        Route::post('admin-update/{id}', 'AdminController@updateAdmin');
        Route::post('admin-details/{id}', 'AdminController@detailAdmin');
        // permissions crud
        Route::post('store-role', 'PermissionsController@storeRole');
        Route::post('edit-role/{id}', 'PermissionsController@editRoles');
        Route::post('update-role/{id}', 'PermissionsController@updateRoles');
        Route::post('delete-role/{id}', 'PermissionsController@deleteRoles');
        Route::post('update-permission/{id}', 'PermissionsController@updatePermissions');
        Route::post('roles-list', 'PermissionsController@index');
        Route::post('get-permissions/{id}', 'PermissionsController@getPermissions');
        Route::post('update-pmerissions/{id}', 'PermissionsController@savePermissions');
        Route::post('roles', 'AdminController@getRoles');
    });
});
