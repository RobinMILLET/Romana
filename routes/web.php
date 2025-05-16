<?php

use App\Http\Controllers\VitrineController;
use App\Models\Langue;
use Illuminate\Support\Facades\Route;

Route::get('/test', function(){
    //
})->name('test');

Route::get('/', function(){
    return VitrineController::index(1);
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


Route::get('/api/lang/{langue_id}', function($langue_id){
    $lang = Langue::find($langue_id); // Trouver la langue
    if ($lang) session(['locale' => $lang]); // Si trouvée, mettre à jour la session
    return redirect()->back(); // Redirection en arrière
})->name('api.lang');

Route::get('/api/free/{nb?}/{date?}', function($nb = null, $date = null) {
    // NB null : Donne les jours disponibles
    // DATE null : Donne les jours disponibles pour NB personnes
    // else : PlanningController::tableau(start: $date, filter: $nb);
})->name('api.free');

Route::post('/api/book', function(){
    //
})->name('api.book');