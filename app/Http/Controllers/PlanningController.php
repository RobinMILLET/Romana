<?php

namespace App\Http\Controllers;

use App\Models\Constante;
use App\Models\Fermeture;
use App\Models\Horaire;
use App\Models\Planning;
use App\Models\Reservation;
use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;

class PlanningController extends Controller
{
    public static function modTZ(DateTime $dt_utc = null) {
        // Par défaut le DT est maintenant
        if ($dt_utc === null) $dt_utc = new DateTime();
        $new_dt = clone $dt_utc; // Éviter les modifications par &ref
        // On donne un UTC, on veut UTC->TZ->UTC
        $new_dt->setTimezone(new DateTimeZone('UTC'));
        // La différence de TZ est déterminée par constante -> offset en integer...
        $offset = (new DateTime('now', new DateTimeZone(Constante::key("fuseau_horaire"))))->getOffset();
        $new_dt->modify("+$offset seconds"); // ...que l'on applique à notre objet
        // Et enfin retourner la date TZ-naïve
        return DateTime::createFromFormat('Y-m-d H:i:s', $new_dt->format('Y-m-d H:i:s'));
    }

    public static function bornesTZ(int $nb = null) {
        $avance = Constante::interval('réservation_temps_min');
        // Créer les bornes pour la résolution du planning
        $early = (PlanningController::modTZ())->add($avance);
        // Déterminer si l'avance est simple ou multiplicative
        if ($nb && Constante::key('avance_multiplicative'))
            // Et l'appliquer en utilisant $nb
            for ($i=1 ; $i<$nb ; $i++) $early->add($avance);
        $late = (PlanningController::modTZ())->add(Constante::interval('réservation_temps_max'));
        return array($early, $late); // On retourne les deux
        // [$early, $late] = PlanningController::bornesTZ($nb?);
    }

    public static function nextPlanning(DateTime $time, int $couverts = Null) {
        // Obtenir les plannings futures (exclusif) dans l'ordre
        $plannings = Planning::orderBy('planning_debut')
            ->where('planning_debut', '>', $time->format("Y-m-d H:i:s"))->get();
        // Pas de restriction de couverts ; on renvoie le 1er
        if ($couverts === Null) return $plannings->first()->planning_debut;
        // Si le couvert n'est pas 0, on peut simplement filter avec planning_couverts
        if ($couverts !== 0) return $plannings->firstWhere('planning_couverts', $couverts)->planning_debut;
        foreach ($plannings as $planning) { // Si le couvert est 0 :
            // Les plannings avec couverts == 0 sont filtrés hors de la table;
            // On doit donc prendre le prochain planning pas immédiatement suivis d'un autre
            if (!Planning::find($planning->planning_fin))
                // À noter qu'on renvoie la date et non un objet Planning
                return DateTime::createFromFormat("Y-m-d H:i:s", $planning->planning_fin);
        }
        return Null;
    }

    public static function changePlanning(DateTime $start, DateTime $end, $function = null) {
        // Par défaut, retire le planning entre $start et $end
        if ($function == null) $function = fn($x) => 0;
        $start_str = $start->format("Y-m-d H:i:s") ; $end_str = $end->format("Y-m-d H:i:s");
        // On s'assure qu'il n'y ait pas de milisecondes
        $start = DateTime::createFromFormat("Y-m-d H:i:s", $start_str);
        $end = DateTime::createFromFormat("Y-m-d H:i:s", $end_str);
        // $start doit être plus tôt que $end
        if ($start >= $end) throw new Exception("Start $start_str must be earlier than end $end_str.");
        
        // Si le début est avant $end ET la fin est après $start
        // Donc cherche tous les plannings qui sont partiellement dans l'interval,
        // même si ils ne sont pas entièrement contenus dedans
        $plannings = Planning::where("planning_debut", "<", $end_str)
            ->where("planning_fin", ">", $start_str)
            ->orderBy('planning_debut')->get();

        $first = $plannings->first();
        // Si le premier élément est au travers de $start, il faut le séparer en 2
        if ($first && DateTime::createFromFormat("Y-m-d H:i:s", $first->planning_debut) < $start) {
            if ($first->planning_couverts != $function($first->planning_couverts)) {
                // On créer le nouveau planning PUIS maj l'ancien PUIS ajoute le nouveau
                // pour éviter les contraintes de PK sur planning_debut
                $new = new Planning([
                    "planning_debut" => $first->planning_debut,
                    "planning_fin" => $start_str,
                    "planning_couverts" => $first->planning_couverts
                ]);
                // Notez que $new ne sera pas ajouté à $plannings, ce qui est bien
                $first->planning_debut = $start_str;
                $first->save(); $new->save();
            }
        }

        $last = $plannings->last();
        // Si le dernier élément est au travers de $end, il faut le séparer en 2
        if ($last && DateTime::createFromFormat("Y-m-d H:i:s", $last->planning_fin) > $end) {
            if ($last->planning_couverts != $function($last->planning_couverts)) {
                // On créer le nouveau planning PUIS maj l'ancien PUIS ajoute le nouveau
                // pour éviter les contraintes de PK sur planning_debut
                $new = Planning::create([
                    "planning_debut" => $end_str,
                    "planning_fin" => $last->planning_fin,
                    "planning_couverts" => $last->planning_couverts
                ]);
                // Notez que $new ne sera pas ajouté à $plannings, ce qui est bien
                $last->planning_fin = $end_str;
                $last->save(); $new->save();
            }
        }

        // Ici, on cherche à remplir l'espace au début et à la fin si défaut > 0
        $default = $function(0);
        if ($default > 0) {
            if ($plannings->count() == 0) {
                // Si il n'y a pas d'éléments dans $plannings,
                // on créer juste un élément qui va de $start à $end
                Planning::create([
                    "planning_debut" => $start_str,
                    "planning_fin" => $end_str,
                    "planning_couverts" => $default
                ]);
                return;
            }
            // Si le premier élément ne commence pas au début,
            // on ajoute un élément juste avant, qui ne fera pas partie de $planning
            if (DateTime::createFromFormat("Y-m-d H:i:s", $plannings[0]->planning_debut) > $start) {
                Planning::create([
                    "planning_debut" => $start_str,
                    "planning_fin" => $plannings[0]->planning_debut,
                    "planning_couverts" => $default
                ]);
            }
            // Si le dernier élément ne va pas jusqu'à la fin,
            // on ajoute un élément juste après, qui ne fera pas partie de $planning
            $l = $plannings->count()-1;
            if (DateTime::createFromFormat("Y-m-d H:i:s", $plannings[$l]->planning_fin) < $end) {
                Planning::create([
                    "planning_debut" =>  $plannings[$l]->planning_fin,
                    "planning_fin" => $end_str,
                    "planning_couverts" => $default
                ]);
            }
        }

        // Pour chaque planning, on applique la fonction $func pour modifier planning_couverts
        for ($i = 0; $i < $plannings->count(); $i ++) {
            $plannings[$i]->planning_couverts = $function($plannings[$i]->planning_couverts);
            $plannings[$i]->save();
            // Si on n'est pas au dernier index et que défaut > 0,
            // il ne doit pas y avoir de vide entre deux éléments
            if ($i < $plannings->count()-1 && $default > 0 &&
                    $plannings[$i]->planning_fin != $plannings[$i+1]->planning_debut) {
                // Donc si les deux éléments ne sont pas consécutifs,
                // on en créer un 3e pour remplir l'espace
                Planning::create([
                    "planning_debut" => $plannings[$i]->planning_fin,
                    "planning_fin" => $plannings[$i+1]->planning_debut,
                    "planning_couverts" => $default
                ]);
            }
        }
    }

    public static function nettoiePlanning() {
        // On vide la BdD de plages vides ou négatives
        // Les plages vides sont retirées pour économiser de l'espace au coût de la logique,
        // les négatifs ne sont pas interdits par les CHECK pour autoriser les éléments temporaires
        Planning::where("planning_couverts", "<=", 0)->delete();
        // On veut ensuite fusionner les plannings consécutifs ayant le même nombre de couverts disponibles
        $plannings = Planning::orderBy("planning_debut")->get(); $cursor = 0;
        // Pour cela, on compare n et n+1 sur début/fin et couverts
        while ($cursor < $plannings->count()-1) {
            if ($plannings[$cursor]->planning_couverts == $plannings[$cursor+1]->planning_couverts &&
                    $plannings[$cursor]->planning_fin == $plannings[$cursor+1]->planning_debut) {
                // S'ils sont égaux et consécutifs, on étend le 1er et supprime le 2nd
                $plannings[$cursor]->planning_fin = $plannings[$cursor+1]->planning_fin;
                $plannings[$cursor+1]->delete();
                $plannings[$cursor]->save();
                // Et il faut mettre à jour la liste des plannings
                $plannings = Planning::orderBy("planning_debut")->get();
            }
            else $cursor += 1;
        }
    }

    public static function generePlanning(DateTime $start, DateTime $end) {
        $start_str = $start->format("Y-m-d H:i:s") ; $end_str = $end->format("Y-m-d H:i:s");
        // On s'assure qu'il n'y ait pas de milisecondes
        $start = DateTime::createFromFormat("Y-m-d H:i:s", $start_str);
        $end = DateTime::createFromFormat("Y-m-d H:i:s", $end_str);
        // $start doit être plus tôt que $end
        if ($start >= $end) { throw new Exception("Start $start_str must be earlier than end $end_str."); }

        // Comme $func => 0 par défaut, ceci écrase la plage de planning dans l'interval
        PlanningController::changePlanning($start, $end);
        PlanningController::nettoiePlanning();

        // On obtiens les horaires et les fermetures utiles à notre planning
        $horaires = Horaire::orderBy("horaire_couverts")->get();
        $fermetures = Fermeture::where("fermeture_debut", "<", $end_str)
            ->where("fermeture_fin", ">", $start_str)
            ->orderBy("fermeture_couverts", "desc")->get();

        // On itère dans l'interval de temps, jour par jour
        $date = clone $start;
        while ($date <= $end) {
            $jsem = ((int) $date->format('N'))-1; // Jour de la semaine, de 0 à 6
            $jour = (int) $date->format('j'); // Jour dans le mois, de 1 à 31
            $mois = ((int) $date->format('n'))-1; // Mois dans l'année, de 0 à 11

            foreach ($horaires as $horaire) { // Chaque horaire qui...
                // ...est dans horaire_date (inclusif)
                if ((int) $horaire->horaire_date_debut > $jour) continue;
                if ((int) $horaire->horaire_date_fin < $jour) continue;
                if (!$horaire->listeJours()[$jsem]) continue; // ...affecte ce jour de la semaine
                if (!$horaire->listeMois()[$mois]) continue; // ...affecte ce mois de l'année

                // Il faut faire attention aux milisecondes si elles existent
                // Et on créer la plage horaire qui utilise la $date avec le temps de l'horaire
                $from = clone $date; $to = clone $date;
                [$h, $m, $s] = explode(':', explode('.', $horaire->horaire_temps_debut)[0]);
                $from->setTime($h, $m, $s);
                [$h, $m, $s] = explode(':', explode('.', $horaire->horaire_temps_fin)[0]);
                $to->setTime($h, $m, $s);

                // Vérification de la plage horaire relative au temps, puis on applique
                if ($from < $start || $to > $end) continue;
                PlanningController::changePlanning($from, $to,
                    fn($x) => (int) $horaire->horaire_couverts);
            }
            // Incrémentation de la $date d'1 jour
            $date->modify('+1 day');
        }

        foreach ($fermetures as $fermeture) {
            // Si la fermeture dépasse l'interval autorisé, on modifie les bornes
            $from = max($start, DateTime::createFromFormat("Y-m-d H:i:s", $fermeture->fermeture_debut));
            $to = min($end, DateTime::createFromFormat("Y-m-d H:i:s", $fermeture->fermeture_fin));
            // Les nombres positifs sont un maximum, les négatifs sont soustraits relativement
            if ($fermeture->fermeture_couverts >= 0) {
                $func = fn($x) => min($x, $fermeture->fermeture_couverts); }
            else $func = fn($x) => $x + $fermeture->fermeture_couverts;
            PlanningController::changePlanning($from, $to, $func); // On applique
        }

        PlanningController::nettoiePlanning();
    }

    public static function comptePlacesPrises(
            DateTime $datetime, DateInterval $duree, array|int $filter = null) {
        // Retourne le nombre de place prises par les réservations à un moment donné,
        // sachant que une réservation bloque pendant $duree
        $reservations = Reservation::whereBetween("reservation_horaire", [
            date_sub(clone $datetime, $duree)->format("Y-m-d H:i:s"),
            $datetime->format("Y-m-d H:i:s")]);
        if ($filter !== null) { // Si le filtre existe
            // On s'assure que c'est un array
            if (!is_array($filter)) $filter = [$filter];
            // Et on l'utilise pour récupérer unqiuement certaines réservations
            $reservations = $reservations->whereIn('statut_id', $filter);
        }
        return $reservations->sum("reservation_personnes");
    }

    public static function crenaux(DateTime $start, DateTime $end = null,
            int|bool $filter_nb = null, array $filter_statut = null,
            DateInterval $interval = null) {
        if ($end === null) { // Si la fin n'est pas donnée,
            // on donne les disponibilités du jour entier
            $start = $start->setTime(0, 0, 0, 0);
            $new_end = date_add(clone $start, new DateInterval("P1D"));
            // Récupérer les bornes min et max de réservation
            [$early, $late] = PlanningController::bornesTZ(is_int($filter_nb) ? $filter_nb : null);
        }
        else $new_end = clone $end;
        
        $start_str = $start->format("Y-m-d H:i:s") ; $end_str = $new_end->format("Y-m-d H:i:s");
        // $start doit être plus tôt que ou égal à $end
        if ($start > $new_end) throw new Exception("Start $start_str must be earlier than end $end_str.");

        // Récupération des constantes depuis la base de données
        $duree = Constante::interval('duree_réservation', 'PTM');
        if ($interval === null) $interval = Constante::interval('interval_réservation', 'PTM');

        // On transforme les deux intervales en int (secondes)
        $interval_secs = date_create('@0')->add($interval)->getTimestamp();
        $duree_secs = date_create('@0')->add($duree)->getTimestamp();
        // On effectue une divisions pour savoir combien il faut de $interval pour un $duree
        $creneaux = (int) ceil($duree_secs / (float) $interval_secs);

        $array = []; // Initialiser la liste à créer
        // On itère entre $start et $end, créant une entrée dans la liste tous les $interval
        // On veut aller jusqu'à $end+$duree pour que les ["allow"] en fin de listes soient vrais
        for ($current = clone $start; 
             $current <= date_add(clone $new_end, $duree);
             $current = date_add($current, $interval))
        {
            // Empêcher de sélectionner un créneau hors bornes
            if ($end === null && $current < $early) continue;
            if ($end === null && $late < $current) continue;

            // Obtenir le planning qui gère $now
            $current_str = $current->format("Y-m-d H:i:s");
            
            $planning = Planning::where("planning_debut", "<=", $current_str)
                ->where("planning_fin", ">", $current_str)->get()->first();
            // S'il est nul (n'existe pas), cela veut dire que le restaurant est fermé (0)
            $max = $planning ? $planning->planning_couverts : 0;
            // Calculer le nombres de places réservées à l'instant $now
            $res = PlanningController::comptePlacesPrises($current, $duree, $filter_statut);
            // Enregistrer les données et les ajouter à la liste
            $array[] = [
                "datetime" => $current_str, "maximum" => $max,
                "booked" => $res, "free" => $max - $res, "allow" => 0
            ];
        }

        // Pour chaque créneau où on pourrait insérer une réservation
        for ($i = 0; $i < count($array) - $creneaux ; $i++) {
            // Ces créneaux sont ceux qui doivent être suffisament libre
            $slice = array_slice($array, $i, $creneaux);
            // On en extrait le nombre de places libres
            $values = array_column($slice, "free");
            // La disponibilité correspond à la valeur la plus petite
            $array[$i]["allow"] = min($values);
        }

        // Les ["allow"] ont étés créés, on s'assure de respecter l'interval $start - $end
        $array = array_slice($array, 0, -$creneaux);
        
        // Pas besoins de tronquer ; on rend le résultat
        if ($filter_nb === null || $filter_nb === false) return $array;

        // Supprimer les x,0,0,x au début
        while (count($array) && $array[0]['maximum'] == 0 &&
            $array[0]['booked'] == 0) array_shift($array);

        // Supprimer les x,0,0,x à la fin
        while (count($array) && end($array)['maximum'] == 0 &&
            end($array)['booked'] == 0) array_pop($array);

        // Pas besoins de filtrer ; on rend le résultat
        if ($filter_nb === true) return $array;

        $new_array = []; // Initialiser la liste à rendre
        foreach ($array as $element) {
            // Si égal ou supérieur au filtre, on garde
            if ($element["allow"] >= $filter_nb) $new_array[] = $element;
        }
        return $new_array;
    }

    public static function calendrier(int $filter = null,
            DateTime $start = null, DateTime $end = null) {
        if ($start) $start_str = $start->format("Y-m-d H:i:s");
        if ($end) $end_str = $end->format("Y-m-d H:i:s");
        if ($start && $end && $start >= $end) // $start doit être plus tôt que $end
            throw new Exception("Start $start_str must be earlier than end $end_str.");
            
        // Récupération des plannings
        $plannings = Planning::orderBy('planning_debut')->get();
        if ($start) $plannings = $plannings->where('planning_fin', '>', $start_str);
        if ($end) $plannings = $plannings->where('planning_debut', '<', $end_str);

        $jours = []; // Initialiser la liste à rendre
        foreach ($plannings as $planning) {
            // Couverts est donné en string, il faut le cast en int
            $couverts = (int) $planning->planning_couverts;
            // Si filtre donné, alors retourner uniquement les plannings qui sont >=
            if ($filter && $couverts < $filter) continue;
            
            // La clé est 'YYYY-mm-dd' (on prend la date sans l'heure)
            $debut = substr($planning->planning_debut, 0, 10);
            $fin = substr($planning->planning_fin, 0, 10);

            // Si (($début pas dans $jours OU nouveau max pour $jours[$début]) ET
            // ($start est null OU $debut est >= à $start)) ALORS ...
            if ((!array_key_exists($debut, $jours) || $couverts > $jours[$debut])
                && (!$start || DateTime::createFromFormat("Y-m-d", $debut) >= $start))
                    $jours[$debut] = $couverts;
                    
            // Même chose pour $end et $fin
            if ((!array_key_exists($fin, $jours) || $couverts > $jours[$fin])
                && (!$end || DateTime::createFromFormat("Y-m-d", $fin) <= $end))
                    $jours[$fin] = $couverts;
        }
        return $jours;
    }
}
