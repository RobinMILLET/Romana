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
    private static string $num_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    public static function reserver(Request $request) {
        // Validation du corps POST
        // Si appellé depuis l'UI, il n'est normalement pas possible de fail
        // Néanmoins on veut éviter les modifications devtools
        // Et on pourrait voir pour adapter ce code et autoriser des appels API
        $request->validate([
            'lastname' => ['required','string','max:256'],
            'firstname' => ['required','string','max:256'],
            'phone' => ReservationController::validate_phone(),
            'amount' => ['required','integer','min:1',
                'max:'.strval(Constante::key('reservation_personnes_max'))],
            'date' => ['required','string','size:10','regex:/^\d{4}-\d{2}-\d{2}$/'],
            'time' => ['required','string','size:8','regex:/^\d{2}:\d{2}:\d{2}$/'],
            'other' => ['nullable','string','max:512']
        ]);
        
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
            "reservation_telephone" => ReservationController::validate_phone($request->phone),
            "reservation_personnes" => $request->amount,
            "reservation_commentaire" => $request->other,
            // Enrigistrement dans la base en TZ-naïve
            "reservation_horaire" => $dt->format("Y-m-d H:i:s")
        ];
               
        DB::beginTransaction();
        try {
            $reservation = Reservation::create($data);           
            DB::commit();
            return redirect()->route('display')->with(['reservation' => $reservation]);
        }
        catch (Exception $e) {
            DB::rollBack();
            Log::error("Unnexpected error in ReservationController::reserver() : ", [$e]);
            return redirect()->back()->withErrors(
                ['SQL' => 'Unnexpected database error.']);
        }
    }

    public static function trouver(Request $request) {
        $request->validate([
            'phone' => ReservationController::validate_phone(),
            'num' => ['required','string','size:8','regex:/^[0-9A-Za-z]{8}$/']
        ]);

        $reservation = ReservationController::findReservation($request);
        if ($reservation === null) return redirect()->back()->withErrors(
            ['NotFound' => 'The reservation was not found.']);
        
        return redirect()->route('display')->with(['reservation' => $reservation]);
    }

    public static function numReservation(int $len = 8) {
        do { // Tant que le code n'est pas unique, on régénère
            $code = ''; 
            for ($i = 0; $i < $len; $i++) {
                // random_int() est anti-entropique (non pas que l'on stocke des données militaires)
                $code .= ReservationController::$num_chars[
                    random_int(0, strlen(ReservationController::$num_chars) - 1)];
            }
            // Techniquement, cette boucle n'a pas de fail-safe de sortie,
            // et pourrait tourner à l'infini si elle ne trouve pas un code libre.
            // Néanmoins, le code a 36^8 = 2B 821Md possibilités, je pense qu'on est bon
        } while (Reservation::where('reservation_num', $code)->exists());
        return $code;
    }

    public static function validate_phone(string $request_phone = null) {
        // Sans argument, on retourne la validation pour le numéro de téléphone
        if ($request_phone === null) return ['required','string','max:17',
            'regex:/^((\+\d{1,2}[\-_\. ]?[1-9])|(0[1-9]))([\-_\. ]?\d{2}){4}$/'];
        // L'entrée téléphone accepte des séparateurs ; Pas la BDD
        $phone = str_replace(['-','_','.',' '], '', $request_phone);
        // On veut finir avec un format ^[0-9]{11}$
        if (str_starts_with($phone, '0')) $phone = '33'.substr($phone, 1);
        else if (str_starts_with($phone, '+')) $phone = substr($phone, 1);
        return $phone;
    }

    public static function findReservation(Request $request) {
        // Formatter le téléphone pour s'accorder avec la BDD
        if ($request->phone === null || $request->phone == "") $phone = null;
        else $phone = ReservationController::validate_phone($request->phone);
        // Trouver la réservation correspondante
        return Reservation::where('reservation_num', strtoupper($request->num))
            ->where('reservation_telephone', $phone)->get()->first();
    }

    public static function restrictReservation(int $num, Reservation $reservation) {
        // NotFound : réservation pas trouvée
        if ($reservation === null) return redirect()->back()
            ->withErrors(['NotFound'.$num => 'The reservation was not found.']);
        // Canceled : la réservation est annulée
        if ($reservation->statut_id == 6) return redirect()->back()
            ->with(['reservation' => $reservation])
            ->withErrors(['Cancelled'.$num => 'This reservation is cancelled.']);
        // TooLate : trop tard pour effectuer l'action
        $limit = $num == 3 ? PlanningController::modTZ() : // 3: Annulation
            PlanningController::bornesTZ($reservation->reservation_personnes)[0];
        if (DateTime::createFromFormat("Y-m-d H:i:s", $reservation->reservation_horaire) < $limit)
            return redirect()->back()->with(['reservation' => $reservation])
                ->withErrors(['TooLate'.$num => 'Could not modify the reservation.']);
        return null;
    }

    public static function modifinfo(Request $request) {
        $request->validate([
            'phone' => ['required','string','size:11','regex:/^[0-9]{11}$/'],
            'num' => ['required','string','size:8','regex:/^[0-9A-Za-z]{8}$/'],
            'lastname' => ['required','string','max:256'],
            'firstname' => ['required','string','max:256'],
            'other' => ['nullable','string','max:512']
        ]);
        
        $reservation = ReservationController::findReservation($request);
        $exit = ReservationController::restrictReservation(1, $reservation);
        if ($exit) return $exit;
        
        DB::beginTransaction();
        try {
            $reservation->reservation_nom = strtoupper($request->lastname);
            $reservation->reservation_prenom = ucfirst(strtolower($request->firstname));
            $reservation->reservation_commentaire = $request->other;
            $reservation->save();
            DB::commit();
            return redirect()->route('display')->with(['reservation' => $reservation])
                ->withErrors(['Success1' => 'Reservation sucessfully modified']);
        }
        catch (Exception $e) {
            DB::rollBack();
            Log::error("Unnexpected error in ReservationController::modifinfo() : ", [$e]);
            $reservation->refresh();
            return redirect()->route('display')->with(['reservation' => $reservation])
                ->withErrors(['SQL1' => 'Unnexpected database error.']);
        }
    }

    public static function modifhoraire(Request $request) {
        $request->validate([
            'phone' => ['required','string','size:11','regex:/^[0-9]{11}$/'],
            'num' => ['required','string','size:8','regex:/^[0-9A-Za-z]{8}$/'],
            'amount' => ['required','integer','min:1',
                'max:'.strval(Constante::key('reservation_personnes_max'))],
            'date' => ['required','string','size:10','regex:/^\d{4}-\d{2}-\d{2}$/'],
            'time' => ['required','string','size:8','regex:/^\d{2}:\d{2}:\d{2}$/']
        ]);

        $reservation = ReservationController::findReservation($request);
        $exit = ReservationController::restrictReservation(2, $reservation);
        if ($exit) return $exit;

        // Récupérer les bornes min et max de réservation
        [$early, $late] = PlanningController::bornesTZ($request->amount);
        // Créer l'objet DateTime depuis l'entrée date et time
        $dt = DateTime::createFromFormat("Y-m-dH:i:s", $request->date.$request->time);
        // S'assurer qu'il est dans les bornes
        if ($dt < $early || $late < $dt) return redirect()->back()->withErrors(
            ['SlotTaken2' => 'The requested date and time are not available.']);
        
        // Ici on récupère uniquement les créneaux interessés...
        $crenaux = PlanningController::crenaux($dt, $dt, $request->amount);
        // ... en qualité de double-check/validation de la disponibilité
        if (!$crenaux || count($crenaux) == 0 || array_search($dt->format("Y-m-d H:i:s"),
            array_column($crenaux, 'datetime')) === false) return redirect()->back()->withErrors(
                ['SlotTaken2' => 'The requested date and time are not available.']);
        
        // Moins de 10 minutes dans le future : statut 3 "En cours"
        if ($dt < (PlanningController::modTZ())->add(new DateInterval("PT10M"))) $statut = 3;
        // Moins de 45 minutes dans le future : statut 2 "En approche"
        else if ($dt < (PlanningController::modTZ())->add(new DateInterval("PT45M"))) $statut = 2;
        else $statut = 1; // Le reste : statut 1 "En attente"

        DB::beginTransaction();
        try {
            $reservation->reservation_personnes = $request->amount;
            $reservation->reservation_horaire = $dt->format("Y-m-d H:i:s");
            $reservation->save();
            DB::commit();
            return redirect()->route('display')->with(['reservation' => $reservation])
                ->withErrors(['Success2' => 'Reservation sucessfully modified']);
        }
        catch (Exception $e) {
            DB::rollBack();
            Log::error("Unnexpected error in ReservationController::modifhoraire() : ", [$e]);
            $reservation->refresh();
            return redirect()->route('display')->with(['reservation' => $reservation])
                ->withErrors(['SQL2' => 'Unnexpected database error.']);
        }
    }

    public static function annulation(Request $request) {
        Log::info("1");
        $request->validate([
            'phone' => ['required','string','size:11','regex:/^[0-9]{11}$/'],
            'num' => ['required','string','size:8','regex:/^[0-9A-Za-z]{8}$/']
        ]);
        Log::info("2");
        $reservation = ReservationController::findReservation($request);
        $exit = ReservationController::restrictReservation(3, $reservation);
        if ($exit) return $exit;

        DB::beginTransaction();
        try {
            $reservation->statut_id = 6;
            $reservation->save();
            DB::commit();
            return redirect()->route('display')->with(['reservation' => $reservation])
                ->withErrors(['Success3' => 'Reservation sucessfully canceled']);
        }
        catch (Exception $e) {
            DB::rollBack();
            Log::error("Unnexpected error in ReservationController::annulation() : ", [$e]);
            $reservation->refresh();
            return redirect()->route('display')->with(['reservation' => $reservation])
                ->withErrors(['SQL3' => 'Unnexpected database error.']);
        }
    }
}
