<?php

use App\Http\Controllers\PlanningController;
use App\Http\Controllers\VitrineController;
use App\Models\Constante;
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

Route::get('/api/const', function(){
    // Les clés définies dans $keys seront donc accessible publiquement en lecture
    $keys = ["reservation_personnes_max"]; $output = [];
    // On charges ces constantes dans la sortie
    foreach ($keys as $key) $output[$key] = Constante::key($key);
    return $output; // Pour ensuite les envoyer en json
})->name('api.lang');

Route::get('/api/free/{nb?}/{date?}', function($nb = null, $date = null) {
    // Limiter nb dans le backend (doit aussi être limité en front-end)
    if ($nb && (int) $nb > Constante::key('reservation_personnes_max'))
        throw new Exception("Nb $nb cannot be higher than Const 'reservation_personnes_max'");

    if ($date === null) {
        // Déterminer si l'avance est simple ou multiplicative
        $avance = Constante::key('reservation_temps_min');
        // Créer les bornes pour la résolution du planning
        $start = (new DateTime())->add($avance);
        if ($nb && Constante::key('avance_multiplicative'))
           for ($i=1 ; $i<$nb ; $i++) $start->add($avance);
        $end = (new DateTime())->add(Constante::key('reservation_temps_max'));
        return array_keys(PlanningController::calendrier($nb, $start, $end));
    }
    return array_map(
        // 'YYYY-MM-DD hh:mm:ss' -> 'hh:mm:ss'
        fn($x) => explode(" ", $x)[1],
        array_column(PlanningController::crenaux(
            Date::createFromFormat("Y-m-d", $date),
            null, $nb), 'datetime'));
})->name('api.free');

Route::post('/api/book', function(){
    //
})->name('api.book');