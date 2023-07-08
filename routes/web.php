<?php

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

Route::get('/', function () {
    return view('welcome');
});


Route::resource('videos', '\App\Http\Controllers\VideoController');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('/delete-video/{video_id?}', array(
    'as' => 'delete-video',
    'middleware' => 'auth',
    'uses' => '\App\Http\Controllers\VideoController@delete'
));

Route::name('print')->get('/imprimir', '\App\Http\Controllers\GeneradorController@imprimir');


Route::get('/editar-video/{video_id}', array(
    'as' => 'videoEdit',
    'middleware' => 'auth',
    'uses' => '\App\Http\Controllers\VideoController@edit'
 ));

 
 Route::post('/update-video/{video_id}',array(
    'as' => 'updateVideo',
    'middleware' => 'auth',
    'uses' => '\App\Http\Controllers\VideoController@update'
 ));

 Route::get('/video-file/{filename}', array(
    'as' => 'fileVideo',
    'uses' => '\App\Http\Controllers\VideoController@getVideo'
 ));
 
 