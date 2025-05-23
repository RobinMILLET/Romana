<?php

namespace App\Http\Controllers;

use App\Models\Constante;
use App\Models\Reservation;
use DateInterval;
use DateTime;
use DB;
use Exception;
use Illuminate\Http\Request;
use Log;

class ReservationController extends Controller
{
    public function reserver(Request $request) {
        // Validation du corps POST
        // Si appalé depuis l'UI, il n'est normalement pas possible de fail
        // Néanmoins on veut éviter les modifications devtools
        // Et on pourrait voir pour adapter ce code et autoriser des appels API
        $request->validate([
            'lastname' => ['required','string','max:256'],
            'firstname' => ['required','string','max:256'],
            'phone' => ['required','string','max:17',
                'regex:/^((\+\d{1,2}[\-_\. ]?[1-9])|(0[1-9]))([\-_\. ]?\d{2}){4}$/'],
            'amount' => ['required','integer','min:1',
                'max:'.strval(Constante::key('reservation_personnes_max'))],
            'date' => ['required','string','size:10','regex:/^\d{4}-\d{2}-\d{2}$/'],
            'time' => ['required','string','size:8','regex:/^\d{2}:\d{2}:\d{2}$/'],
            'other' => ['nullable','string','max:512'],
        ]);
        
        // L'entrée téléphone accepte des séparateurs ; Pas la BDD
        $phone = str_replace(['-','_','.',' '], '', $request->phone);
        // On veut finir avec un format ^[0-9]{11}$
        if (str_starts_with($phone, '0')) $phone = '33'.substr($phone, 1);
        else if (str_starts_with($phone, '+')) $phone = substr($phone, 1);
        
        // Récupérer les bornes min et max de réservation
        [$early, $late] = PlanningController::bornesTZ($request->amount);
        // Créer l'objet DateTime depuis l'entrée date et time
        $dt = DateTime::createFromFormat("Y-m-dH:i:s", $request->date.$request->time);
        // S'assurer qu'il est dans les bornes
        if ($dt < $early || $late < $dt) return redirect()->back()->withErrors(
            ['SlotTaken' => 'The requested date and time are not available.']);
        
        // Ici on récupère uniquement les créneaux interessés...
        $crenaux = PlanningController::crenaux($dt, $dt, $request->amount);
        // ... en qualité de double-check/validation de la disponibilité
        if (!$crenaux || count($crenaux) == 0 || array_search($dt->format("Y-m-d H:i:s"),
            array_column($crenaux, 'datetime')) === false) return redirect()->back()->withErrors(
                ['SlotTaken' => 'The requested date and time are not available.']);
        
        // Moins de 10 minutes dans le future : statut 3 "En cours"
        if ($dt < (PlanningController::modTZ())->add(new DateInterval("PT10M"))) $statut = 3;
        // Moins de 45 minutes dans le future : statut 2 "En approche"
        else if ($dt < (PlanningController::modTZ())->add(new DateInterval("PT45M"))) $statut = 2;
        else $statut = 1; // Le reste : statut 1 "En attente"
        
        $data = [
            "statut_id" => $statut,
            // On créer un numéro de demande unique et non-séquentiel
            "reservation_num" => ReservationController::numReservation(),
            // On formate 'LASTNAME' et 'Firstname'
            "reservation_nom" => strtoupper($request->lastname),
            "reservation_prenom" => ucfirst(strtolower($request->firstname)),
            "reservation_telephone" => $phone,
            "reservation_personnes" => $request->amount,
            "reservation_commentaire" => $request->other,
            // Enrigistrement dans la base en TZ-naïve
            "reservation_horaire" => $dt->format("Y-m-d H:i:s")
        ];
               
        DB::beginTransaction();
        try {
            $reservation = Reservation::create($data);           
            DB::commit();
            dd($reservation);
            // TODO: redirect
        }
        catch (Exception $e) {
            DB::rollBack();
            Log::error("Unnexpected error in ReservationController::reserver() : ", [$e]);
            return redirect()->back()->withErrors(
                ['SQL' => 'Unnexpected database error.']);
        }
    }

    public static function numReservation(int $len = 8) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        do { // Tant que le code n'est pas unique, on régénère
            $code = ''; 
            for ($i = 0; $i < $len; $i++) {
                // random_int() est anti-entropique (non pas que l'on stocke des données militaires)
                $code .= $characters[random_int(0, strlen($characters) - 1)];
            }
            // Techniquement, cette boucle n'a pas de fail-safe de sortie,
            // et pourrait tourner à l'infini si ele ne trouve pas un code libre.
            // Néanmoins, le code a 36^8 = 2B 821Md possibilités, je pense qu'on est bon
        } while (Reservation::where('reservation_num', $code)->exists());
        return $code;
    }
}
