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

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => 'localization', 'prefix' => Session::get('locale')], function() {
    
    // Auth::routes();
    // Authentication Routes...
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Auth\LoginController@login');
    Route::post('logout', 'Auth\LoginController@logout')->name('logout');
    // Registration Routes...
    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'Auth\RegisterController@register');
    // Password Reset Routes...
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');
    //Send Mail valid Account
    Route::get('/user/verify/{confirmation_code}', 'Auth\RegisterController@verifyUser');
    //I18n
    Route::post('/lang', [
        'as' => 'switchLang',
        'uses' => 'LangController@postLang',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Backend
    |--------------------------------------------------------------------------|
    */
    Route::prefix('admin')->namespace('Back')->group(function () {

        Route::middleware('admin')->group(function () {
            //Home
            Route::name('admin')->get('/', 'AdminController@adminDashboard');
            //Users
            Route::resource('users', 'UserController', ['only' => [
                'index', 'edit', 'update', 'destroy'
            ]]);
            // Topics
            Route::resource('topics', 'TopicController', ['except' => ['show', 'create', 'edit'] ]);
            //Tags
            Route::resource('tags', 'TagController', ['except' => ['show', 'create', 'edit'] ]);
            // posts
            Route::name('posts.active')->put('posts/active/{post}/{status?}', 'PostController@updateActive')->middleware('can:manage,post');
            Route::resource('posts', 'PostController', ['except' => ['show'] ]);
        });

    });

});
