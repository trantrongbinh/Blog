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

/*
|--------------------------------------------------------------------------
| Frontend
|--------------------------------------------------------------------------|
*/

Route::group(['middleware' => 'localization', 'prefix' => Session::get('locale')], function() {
    // Home
    Route::name('home')->get('/', 'Front\PostController@index');
    Route::name('home.post.list')->post('home/{type}', 'Front\PostController@getPostByType');
    //Topic
    Route::name('topic')->get('topic/{topic}', 'Front\PostController@topic');
    // Posts and comments
    Route::prefix('posts')->namespace('Front')->group(function () {
        Route::name('posts.display')->get('{slug}', 'PostController@show');
        Route::name('posts.tag')->get('tag/{tag}', 'PostController@tag');
        Route::name('posts.comments.store')->post('{post}/comments', 'CommentController@store');
        Route::name('posts.comments.comments.store')->post('{post}/comments/{comment}/comments', 'CommentController@store');
        Route::name('posts.comments')->get('{post}/comments/{page}', 'CommentController@comments');
        Route::name('posts.questions')->post('questions/{type}', 'PostController@question');
        Route::name('home.posts.create')->get('front/create', 'PostController@create');
        Route::name('home.posts.store')->post('front/create', 'PostController@store');

        Route::name('posts.search')->get('', 'PostController@search');
    });
    //User Following , Rating
    Route::group(['middleware' => ['auth']], function () {
        Route::post('/follow/{user}', 'Front\FollowController@follow');
        Route::delete('/unfollow/{user}', 'Front\FollowController@unfollow');
        Route::name('posts.rate')->put('posts/rate/{post}', 'Front\RateController@rate');
    });
    //Delete comment, post
    Route::resource('posts', 'Front\PostController', [
        'only' => ['edit', 'update', 'destroy'],
        'names' => ['edit' => 'front.post.edit', 'update' => 'front.post.update', 'destroy' => 'front.posts.destroy'],
    ]);
    Route::resource('comments', 'Front\CommentController', [
        'only' => ['destroy'],
        'names' => ['destroy' => 'front.comments.destroy'],
    ]);
    Route::name('comment.update')->put('front/commet/update/{coment}', 'Front\CommentController@update');
    //User
    Route::resource('users', 'Front\UserController', [
        'only' => ['index', 'show', 'update'],
        'names' => ['index' => 'front.user.index', 'show' => 'front.user.show', 'update' => 'front.user.update'],
    ]);

    Route::name('front.user.avata')->post('front/user/avata/{user}', 'Front\UserController@avata');

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
