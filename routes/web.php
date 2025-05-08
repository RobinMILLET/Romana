<?php

use App\Http\Controllers\VitrineController;
use Illuminate\Support\Facades\Route;

Route::get('/', function(){
    return redirect()->route('vitrine', [1]);
})->name('home');
Route::get('/vitrine/{page_id}', function($page_id){
    return VitrineController::index($page_id);
})->name('vitrine');


Route::get('/langue/{langue_id}', function($langue_id){
    session(['locale' => $langue_id]);
    return redirect()->back();
})->name('langue');