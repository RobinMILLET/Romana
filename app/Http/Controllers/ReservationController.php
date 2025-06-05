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
    private static string $NUM_CHARS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    public static function reserver(Request $request) {
        // Validation du corps POST
        // Si appellé depuis l'UI, il n'est normalement pas possible de fail
        // Néanmoins on veut éviter les modifications devtools
        // Et on pourrait voir pour adapter ce code et autoriser des appels API
        $override = AuthController::requirePerm('RESER');
        $request->validate([
            'lastname' => [$override ? 'nullable' : 'required', 'string', 'max:256'],
            'firstname' => [$override ? 'nullable' : 'required', 'string', 'max:256'],
            'phone' => ReservationController::validate_phone(null, $override),
            'amount' => ['required','integer','min:1'] + $override ? [] :
                ['max:'.strval(Constante::key('réservation_personnes_max'))],
            'date' => ['required','string','size:10','regex:/^\d{4}-\d{2}-\d{2}$/'],
            'time' => ['required','string','size:8','regex:/^\d{2}:\d{2}:\d{2}$/'],
            'other' => ['nullable','string','max:512']
        ]);
        
        // Récupérer les bornes min et max de réservation
        [$early, $late] = PlanningController::bornesTZ($request->amount);
        // Créer l'objet DateTime depuis l'entrée date et time
        $dt = DateTime::createFromFormat("Y-m-dH:i:s", $request->date.$request->time);
        
        if (!$override) { // Les checks suivants ne sont pas effectués si créé par le staff
            // S'assurer qu'il est dans les bornes
            if (!$override && ($dt < $early || $late < $dt)) return redirect()->back()->withErrors(
                ['SlotTaken' => 'The requested date and time are not available.']);
            
            // Ici on récupère uniquement les créneaux interessés...
            $crenaux = PlanningController::crenaux($dt, $dt, $request->amount);
            // ... en qualité de double-check/validation de la disponibilité
            if (!$crenaux || count($crenaux) == 0 || array_search($dt->format("Y-m-d H:i:s"),
                array_column($crenaux, 'datetime')) === false) return redirect()->back()->withErrors(
                    ['SlotTaken' => 'The requested date and time are not available.']);
        }
        
        $data = [
            "statut_id" => 1,
            // On créer un numéro de demande unique et non-séquentiel
            "reservation_num" => ReservationController::numReservation(),
            // On formate 'LASTNAME' et 'Firstname'
            "reservation_nom" => strtoupper($request->lastname ?? ''),
            "reservation_prenom" => ucfirst(strtolower($request->firstname ?? '')),
            "reservation_telephone" => ReservationController::validate_phone($request->phone),
            "reservation_personnes" => $request->amount,
            "reservation_commentaire" => $request->other,
            // Enregistrement dans la base en TZ-naïve
            "reservation_horaire" => $dt->format("Y-m-d H:i:s")
        ];
               
        DB::beginTransaction();
        try {
            $reservation = Reservation::create($data);
        }
        catch (Exception $e) {
            DB::rollBack();
            Log::error("Unnexpected error in ReservationController::reserver() : ", [$e]);
            return redirect()->back()->withErrors(
                ['SQL' => 'Unnexpected database error.']);
        }
        if (!$override && $reservation->reservation_telephone && Constante::key('sms_réservation')) {
            try {
                SmsController::send(
                    SmsController::validation($reservation->reservation_num),
                    $reservation->reservation_telephone
                );
            }
            catch (Exception $e) {
                DB::rollBack();
                return redirect()->back()->withErrors(
                    ['SMS' => 'Could not send confirmation message.']);
            }
        }
        DB::commit();
        return redirect()->route('display')
            ->with(['reservation' => $reservation, 'errors' => 'Success1']);
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

    public static function numReservation(int $len = 8, bool $unique = true) {
        do { // Tant que le code n'est pas unique, on régénère (sauf si $unique est false)
            $code = ''; 
            for ($i = 0; $i < $len; $i++) {
                // random_int() est anti-entropique (non pas que l'on stocke des données militaires)
                $code .= ReservationController::$NUM_CHARS[
                    random_int(0, strlen(ReservationController::$NUM_CHARS) - 1)];
            }
            // Techniquement, cette boucle n'a pas de fail-safe de sortie,
            // et pourrait tourner à l'infini si elle ne trouve pas un code libre.
            // Néanmoins, le code a 36^8 = 2B 821Md possibilités, je pense qu'on est bon
        } while ($unique && Reservation::where('reservation_num', $code)->exists());
        return $code;
    }

    public static function validate_phone(string $request_phone = null, bool $override = false) {
        // Sans argument, on retourne la validation pour le numéro de téléphone
        if ($request_phone === null) return [$override ? 'nullable' : 'required', 'string',
            'max:17', 'regex:/^((\+\d{1,2}[\-_\. ]?[1-9])|(0[1-9]))([\-_\. ]?\d{2}){4}$/'];
        if ($override && !$request_phone) return null;
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

    public static function restrictReservation(DateTime $limit, Reservation $reservation) {
        // NotFound : réservation pas trouvée
        if ($reservation === null) return redirect()->back()
            ->with(['errors' => 'NotFound']);
        // L'utilisateur connecté et autorisé peut ignorer ces contraintes
        if (AuthController::requirePerm('RESER')) return null;
        // Canceled : la réservation est annulée
        if ($reservation->statut_id == 6) return redirect()->back()
            ->with(['reservation' => $reservation, 'errors' => 'Cancelled']);
        // TooLate : trop tard pour effectuer l'action
        if (DateTime::createFromFormat("Y-m-d H:i:s", $reservation->reservation_horaire) < $limit)
            return redirect()->back()->with(['reservation' => $reservation, 'errors' => 'TooLate']);
        return null;
    }

    public static function modifinfo(Request $request) {
        $override = AuthController::requirePerm('RESER');
        $request->validate([
            'phone' => [$override?'nullable':'required','string','size:11','regex:/^[0-9]{11}$/'],
            'num' => ['required','string','size:8','regex:/^[0-9A-Za-z]{8}$/'],
            'lastname' => [$override?'nullable':'required','string','max:256'],
            'firstname' => [$override?'nullable':'required','string','max:256'],
            'other' => ['nullable','string','max:512']
        ]);
        
        $reservation = ReservationController::findReservation($request);
        $borne_min = PlanningController::bornesTZ($reservation->reservation_personnes)[0];
        if ($exit = ReservationController::restrictReservation($borne_min, $reservation)) return $exit;
        
        DB::beginTransaction();
        try {
            $reservation->reservation_nom = strtoupper($request->lastname ?? '');
            $reservation->reservation_prenom = ucfirst(strtolower($request->firstname ?? ''));
            $reservation->reservation_commentaire = $request->other;
            $reservation->save();
            DB::commit();
            return redirect()->route('display')->with(
                ['reservation' => $reservation, 'success' => 'Success2']);
        }
        catch (Exception $e) {
            DB::rollBack();
            Log::error("Unnexpected error in ReservationController::modifinfo() : ", [$e]);
            $reservation->refresh();
            return redirect()->route('display')->with(
                ['reservation' => $reservation, 'errors' => 'SQL']);
        }
    }

    public static function modifhoraire(Request $request) {
        $override = AuthController::requirePerm('RESER');
        $request->validate([
            'phone' => [$override?'nullable':'required','string','size:11','regex:/^[0-9]{11}$/'],
            'num' => ['required','string','size:8','regex:/^[0-9A-Za-z]{8}$/'],
            'amount' => ['required','integer','min:1'] + $override ? [] :
                ['max:'.strval(Constante::key('réservation_personnes_max'))],
            'date' => ['required','string','size:10','regex:/^\d{4}-\d{2}-\d{2}$/'],
            'time' => ['required','string','size:8','regex:/^\d{2}:\d{2}:\d{2}$/']
        ]);

        $reservation = ReservationController::findReservation($request);
        $borne_min = PlanningController::bornesTZ($reservation->reservation_personnes)[0];
        if ($exit = ReservationController::restrictReservation($borne_min, $reservation)) return $exit;

        // Récupérer les bornes min et max de réservation
        [$early, $late] = PlanningController::bornesTZ($request->amount);
        // Créer l'objet DateTime depuis l'entrée date et time
        $dt = DateTime::createFromFormat("Y-m-dH:i:s", $request->date.$request->time);

        if (!$override) { // Les checks suivants ne sont pas effectués si créé par le staff
            // S'assurer qu'il est dans les bornes
            if ($dt < $early || $late < $dt) return redirect()->back()->with(['errors' => 'SlotTaken']);
            
            // Ici on récupère uniquement les créneaux interessés...
            $crenaux = PlanningController::crenaux($dt, $dt, $request->amount);
            // ... en qualité de double-check/validation de la disponibilité
            if (!$crenaux || count($crenaux) == 0 || array_search($dt->format("Y-m-d H:i:s"),
                array_column($crenaux, 'datetime')) === false)
                    return redirect()->back()->with(['errors' => 'SlotTaken']);
        }

        DB::beginTransaction();
        try {
            $reservation->reservation_personnes = $request->amount;
            $reservation->reservation_horaire = $dt->format("Y-m-d H:i:s");
            $reservation->save();
            DB::commit();
            return redirect()->route('display')->with(
                ['reservation' => $reservation, 'success' => 'Success3']);
        }
        catch (Exception $e) {
            DB::rollBack();
            Log::error("Unnexpected error in ReservationController::modifhoraire() : ", [$e]);
            $reservation->refresh();
            return redirect()->route('display')->with(
                ['reservation' => $reservation, 'errors' => 'SQL']);
        }
    }

    public static function annulation(Request $request) {
        $override = AuthController::requirePerm('RESER');
        $request->validate([
            'phone' => [$override?'nullable':'required','string','size:11','regex:/^[0-9]{11}$/'],
            'num' => ['required','string','size:8','regex:/^[0-9A-Za-z]{8}$/']
        ]);
        
        $reservation = ReservationController::findReservation($request);
        $borne_min = PlanningController::modTZ(); // Annulation au plus tard
        if ($exit = ReservationController::restrictReservation($borne_min, $reservation)) return $exit;

        DB::beginTransaction();
        try {
            $reservation->statut_id = 6;
            $reservation->save();
            DB::commit();
            return redirect()->route('display')->with(
                ['reservation' => $reservation, 'success' => 'Success4']);
        }
        catch (Exception $e) {
            DB::rollBack();
            Log::error("Unnexpected error in ReservationController::annulation() : ", [$e]);
            $reservation->refresh();
            return redirect()->route('display')->with(
                ['reservation' => $reservation, 'errors' => 'SQL']);
        }
    }
}
