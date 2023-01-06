<?php

use Eftersom\Larafeed\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

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

Route::get('/', 'Eftersom\Larafeed\Http\Controllers\FeedController@home')->name('feed-home');

Route::get('/user/{user}', 'Eftersom\Larafeed\Http\Controllers\FeedController@showUserFeedAll')->name('feed-user');
Route::get('/user/{user}/feed/show/{slug}', 'Eftersom\Larafeed\Http\Controllers\FeedController@showUserFeed')->name('feed-user-show');

Route::middleware([Authenticate::class])->group(function () {
    Route::post('/feed/remove', 'Eftersom\Larafeed\Http\Controllers\FeedController@detatchUserFeed')->name('feed-user-remove');
    Route::get('/feed', 'Eftersom\Larafeed\Http\Controllers\FeedController@showAll')->name('feed-show-all');
    Route::get('/feed/show/{slug}', 'Eftersom\Larafeed\Http\Controllers\FeedController@show')->name('feed-show');
    Route::post('/feed/search', 'Eftersom\Larafeed\Http\Controllers\FeedController@feedSearch')->name('feed-search');
});