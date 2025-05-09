<?php

use App\Http\Controllers\VitrineController;
use Illuminate\Support\Facades\Route;

Route::get('/', function(){
    return redirect()->route('home');
});
Route::get('/home', function(){
    return VitrineController::index(1);
})->name('home');
Route::get('/about', function(){
    return VitrineController::index(2);
})->name('about');
Route::get('/event', function(){
    return VitrineController::index(3);
})->name('event');
Route::get('/hours', function(){
    return VitrineController::index(4);
})->name('hours');
Route::get('/menu', function(){
    return VitrineController::index(5);
})->name('menu');
Route::get('/book', function(){
    return VitrineController::index(6);
})->name('book');
Route::get('/rating', function(){
    return VitrineController::index(7);
})->name('rating');
Route::get('/contact', function(){
    return VitrineController::index(8);
})->name('contact');


Route::get('/langue/{langue_id}', function($langue_id){
    session(['locale' => $langue_id]);
    return redirect()->back();
})->name('langue');