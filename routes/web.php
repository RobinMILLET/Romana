<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompteController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TraductionController;
use App\Http\Controllers\VitrineController;
use App\Models\Categorie;
use App\Models\Constante;
use App\Models\Langue;
use App\Models\Reservation;
use App\Models\Statut;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

// --------------------
//   ROUTES PUBLIQUES
// --------------------


// ------ TEST ------

Route::get('/test', function(){
    //
})->name('test');


// ------ PAGES ------

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

Route::get('/menu/{id?}', function($id = null){
    $menus = Categorie::whereNull('categorie_idparent')->orderBy('categorie_ordre')->get();
    if ($id === null) $id = $menus->first()->categorie_id;
    return view(
        'Public.Pages.menu',
        ['id' => $id, 'menus' => $menus]
    );
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


// ---- RÉSERVATION ----

Route::get('/display', function() {
    $reservation = session('reservation');
    if ($reservation === null) return redirect()->route('book');
    // L'affichage de la réservation est une vitrine personnalisée
    return VitrineController::index(reservation: $reservation);
})->name('display');


// ---------------------
//    ROUTES PRIVÉES
// ---------------------


function adminRoute(int|string|array $perm = null) {
    $personnel = AuthController::current(); // Récupérer le personnel
    // S'il n'existe pas, rediriger vers /login
    if ($personnel === null) return redirect()->route('admin.login');
    // S'il doit changer son mot de passe, rediriger vers la page bloquante
    if ($personnel->personnel_mdp_change === null) return redirect()->route('admin.mdp');
    // S'il ne peut pas y accéder à cause de ses permission, renvoyer en arrière
    if (!AuthController::requirePerm($perm, $personnel)) return redirect()->back()
        ->withErrors(["503" => "Forbidden"]);
    return null;
}


// ------- ADMIN -------

Route::get('/admin', function(){
    if ($r = adminRoute()) return $r;
    return redirect()->route('admin.dashboard');
})->name('admin');

Route::get('/admin/login', function(){
    $personnel = AuthController::current();
    if ($personnel) return redirect()->route('admin');
    return view('Private.Pages.login');
})->name('admin.login');

Route::get('/admin/dashboard', function() {
    if ($r = adminRoute()) return $r;
    return view('Private.Pages.dashboard');
})->name('admin.dashboard');

Route::get('/admin/reservations/more', function() {
    Session::put('more', true);
    return redirect()->route('admin.reservations');
})->name('admin.reservations.more');

Route::get('/admin/reservations/', function() {
    if ($r = adminRoute()) return $r;
    // Déterminer si plus de réservations doivent être chargées
    $more = session('more', false);
    Session::forget('more');
    $reservations = Reservation::orderBy('reservation_horaire');
    if (!$more) {
        $limit_past = (new DateTime("-1 week"))->format("Y-m-d H:i:s");
        $limit_futur = (new DateTime("+1 month"))->format("Y-m-d H:i:s");
        $reservations->where('reservation_horaire', '>', $limit_past)
                ->where('reservation_horaire', '<', $limit_futur);
    }
    $statuts = Statut::orderBy('statut_id')->get();
    foreach ($statuts as $statut) {
        $statut->nb = (clone $reservations)->where('statut_id', $statut->statut_id)->count();
    }
    return view('Private.Pages.reservations',
        [
            "reservations" => $reservations->get(),
            "diff" => Reservation::count() - $reservations->count(),
            "statuts" => $statuts
        ]);
})->name('admin.reservations');

Route::get('/admin/mdp', function(){
    $personnel = AuthController::current();
    if (!$personnel || $personnel->personnel_mdp_change !== null) return redirect()->route('admin');
    return view('Private.Pages.mdp');
})->name('admin.mdp');


// --------------------
//     API PUBLIQUE
// --------------------


// ------ LANGUE ------

Route::get('/api/lang/{langue_id}', function($langue_id){
    $lang = Langue::find($langue_id); // Trouver la langue
    if ($lang) session(['locale' => $lang]); // Si trouvée, mettre à jour la session
    return redirect()->back(); // Redirection en arrière
})->name('api.lang');


// -- PLACES LIBRES --

Route::get('/api/free/{nb?}/{date?}', function($nb = null, $date = null) {
    // Limiter nb dans le backend (doit aussi être limité en front-end)
    if ($nb && (int) $nb > Constante::key('réservation_personnes_max'))
        throw new Exception("Nb $nb cannot be higher than Const 'réservation_personnes_max'");

    if ($date === null) {
        [$start, $end] = PlanningController::bornesTZ($nb);
        return array_keys(PlanningController::calendrier($nb, $start, $end));
    }
    return array_map(
        // 'YYYY-MM-DD hh:mm:ss' -> 'hh:mm:ss'
        fn($x) => explode(" ", $x)[1],
        array_column(PlanningController::crenaux(
            Date::createFromFormat("Y-m-d", $date),
            null, $nb, [1, 2, 3, 4, 7]), 'datetime'));
})->name('api.free');


// --- FORMULAIRES ---

Route::post('/api/book',
    [ReservationController::class, 'reserver']
)->name('api.book');

Route::post('/api/find',
    [ReservationController::class, 'trouver']
)->name('api.find');

Route::post('/api/modifinfo',
    [ReservationController::class, 'modifinfo']
)->name('api.modifinfo');

Route::post('/api/modifhoraire',
    [ReservationController::class, 'modifhoraire']
)->name('api.modifhoraire');

Route::post('/api/annulation',
    [ReservationController::class, 'annulation']
)->name('api.annulation');


// --------------------
//      API PRIVÉE
// --------------------


Route::post('/api/login',
    [AuthController::class, 'login']
)->name('api.login');

Route::get('/api/logout', function(){
    AuthController::logout();
    return redirect()->route('admin.login');
})->name('api.logout');

Route::post('/api/mdp',
    [CompteController::class, 'changeMdp']
)->name('api.mdp');