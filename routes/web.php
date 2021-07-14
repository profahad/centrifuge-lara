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


Route::get('/decrypt', function () {
    $cipher = "sNhOc3BPjvoBiAKvOXfFNFJxWjKoaFmNUi78VMWIEVw=";
    $method = "YWVzLTI1Ni1lY2I=";
    $key = "MjZrb3pRYUt3UnVOSjI0dDI2a296UWFLd1J1TkoyNHQ=";
    return openssl_decrypt(
    base64_decode($cipher),
    base64_decode($method),
    base64_decode($key),
    OPENSSL_RAW_DATA
    );
});



Route::get('/extractor', function () {
    $url = "http://www.youtube.com/watch?v=LP81U91IwEQ&feature=relate";
    parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars );
    return $my_array_of_vars['v']; 
});