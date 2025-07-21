<?php

namespace App\Http\Controllers;

class TraductionController extends Controller
{
    private static string $NEWLINE = "\n";
    private static $KEY = ["§[¤", "¤]§"];
    private static function key(string $id)
        { return TraductionController::$KEY[0].$id.TraductionController::$KEY[1]; }
    private static $SECTION = ["@section", "@endsection"];
    private static $SURROUND = [
        ["@push", "@endpush"],
        ["@php", "@endphp"],
        ["{!!", "!!}"],
        ["{{", "}}"],
        ["<script", "</script>"],
        ["<style", "</style>"],
        ["<", ">"],
    ];
    private static $IGNORE_LINE = [
        "@if", "@elseif", "@else", "@endif",
        "@foreach", "@endforeach",
        "@for", "@endfor",
        "@section", "@endsection",
        "@extends", "@include"
    ];
    private static $IGNORE = [
        "@csrf", "&nbsp;", "&lt;", "&gt;", "\r\n", "\n"
    ];

    public static function translate(string $text, string $lang = "en") {
        // Séparer le contenu des métadonnées HTML avec @section
        [$before, $content, $after] = TraductionController::section($text);
        $glossary = []; // Glossaire des remplacements à ne pas traduire
        // Lignes à ignorer si elles débutent par $ignore
        foreach (TraductionController::$IGNORE_LINE as $ignore) {
            $content = TraductionController::ignore_line(
                $content, $glossary, $ignore, TraductionController::$NEWLINE);
        }
        // Contenu à ignorer si entouré par $start et $end
        foreach (TraductionController::$SURROUND as [$start, $end]) {
            $content = TraductionController::surround(
                $content, $glossary, $start, $end);
        }
        // Expressions individuelles à ingorer (exact match)
        foreach (TraductionController::$IGNORE as $ignore) {
            $content = TraductionController::ignore(
                $content, $glossary, $ignore);
        }
        // Séparation du langage traductible et des ID de glossaire
        // Réduits aussi l'espacement pour baisser la conso des API
        [$notranslate, $translate] = TraductionController::content($content);
        foreach ($translate as &$txt) { // Traduire le contenu
            $txt = TraductionController::translator($txt, $lang);
        }
        // Remplacer les éléments traduits et le glossaire
        $output = TraductionController::reconstruct($notranslate, $translate, $glossary);
        return $before.$output.$after; // Reformer le texte et renvoyer
    }

    public static function section(string $text) {
        // Si il y a exactement 1 début et 1 fin de section (sinon passer)
        if (substr_count($text, TraductionController::$SECTION[0]) == 1 &&
            substr_count($text, TraductionController::$SECTION[1]) == 1) {
            // Séparer le texte par lignes
            $lines = explode(TraductionController::$NEWLINE, $text);
            // Stage définis l'étape relative à la section :
            // 0 avant, 1 pendant, 2 après
            $stage = 0; $before = []; $content = []; $after = [];
            foreach ($lines as $line) { // Itération à travers les lignes
                if ($stage == 0) { // Si on est au stage 0
                    $before[] = $line; // On ajoute à $before car c'est inclusif
                    // (on veut que @section soit dans $before et pas $content)
                    // Et enfin, on vérifie si la ligne est le début de section
                    if (str_contains($line, TraductionController::$SECTION[0])) $stage += 1;
                }
                else if ($stage == 1) { // Si on est au stage 1
                    // On veut que @endsection soit dans $after et pas $content, donc vérif avant
                    if (str_contains($line, TraductionController::$SECTION[1])) $stage += 1;
                    else $content[] = $line; // Sinon, on ajoute dans $content
                }
                // $stage peut passer de 1 à 2 au if précédant, donc on utilise pas else
                if ($stage == 2) $after[] = $line; // Le reste
            }
            // Retourner le texte rafistolé en 3 parties
            return [implode(TraductionController::$NEWLINE, $before),
                implode(TraductionController::$NEWLINE, $content),
                implode(TraductionController::$NEWLINE, $after)];
        }
        return ["", $text, ""]; // S'il n'y a pas lieu, $before et $after sont vide
    }

    public static function ignore_line(string $text, array &$glossary,
            string $ignore, string $newline) {
        $lines = explode($newline, $text); // Diviser le texte en lignes
        foreach ($lines as &$line) { // Itération sur les lignes
            // Si la ligne commence par $ignore (sans conpter espaces blancs)
            if (str_starts_with(trim($line), $ignore)) {
                // On créer la clé avec le nombre actuel d'entrées dans le glossaire
                $key = TraductionController::key(count($glossary));
                $glossary[] = $line; // On met à jour le glossaire
                $line = $key; // On remplace la ligne (par référence)
            }
        }
        return implode($newline, $lines); // Rafistolage et renvoi
    }

    public static function surround(string $text, array &$glossary,
            string $start, string $end) {
        while (true) { // Boucle infinie (la sortie est par retour)
            // On commence par une profondeur de 1, car on assume qu'il y a lieu de boucler
            $depth = 1; $start_pos = strpos($text, $start);
            // Si c'est faux (pas de $start présent), alors on aborte
            if ($start_pos === false) return $text;
            $current_pos = $start_pos; // On incrémente le position actuelle
            while ($depth != 0) { // Tant que nb($start) != nb($end)
                // Trouver le prochain $start et $end
                $next_start = strpos($text, $start, $current_pos + 1);
                $next_end = strpos($text, $end, $current_pos + 1);
                if (!$next_end) return $text; // S'il n'y pas de fin, on aborte
                // S'il y a un $start et qu'il est avant $end
                // On incrémente la profondeur et on avance l'index
                if ($next_start !== false && $next_start < $next_end) {
                    $depth += 1; $current_pos = $next_start;
                }
                // S'il n'y a pas de $start (tout court ou aant $end)
                // On décrémente la profondeur et on avance l'index
                else {
                    $depth -= 1; $current_pos = $next_end;
                }
            }
            // Quand on arrive à un équilibrium de profondeur (nb($start) == nb($end))
            $current_pos += strlen($end); // On ajoute la longueur de $end car on veut l'inclure
            // On créer la clé avec le nombre actuel d'entrées dans le glossaire
            $key = TraductionController::key(count($glossary));
            // On met à jour le glossaire avec le contenu entre $start et $end
            $glossary[] = substr($text, $start_pos, $current_pos - $start_pos);
            // On remplace le texte original par la clé de glossaire
            $text = substr($text, 0, $start_pos).$key.substr($text, $current_pos);
        }
    }

    public static function ignore(string $text, array &$glossary, string $ignore) {
        while (true) { // Boucle infinie brisée par retour anticipé
            $pos = strpos($text, $ignore); // Trouver le premier caractère ignoré
            if ($pos === false) return $text; // S'il n'existe pas, retour
            // On créer la clé avec le nombre actuel d'entrées dans le glossaire
            $key = TraductionController::key(count($glossary));
            $glossary[] = $ignore; // On met à jour le glossaire
            // On remplace le texte original par la clé de glossaire
            $text = substr($text, 0, $pos).$key.substr($text, $pos + strlen($ignore));
        }
    }

    public static function content(string $text) {
        // On initialise les clés pour faciliter leur utilisation
        [$key1, $key2] = TraductionController::$KEY;
        $trimmed = trim($text); // On retire l'espace blanc en début et fin

        // Si le texte ne commence pas par un ID (sans compter espaces)
        $has_start = !str_starts_with($trimmed, $key1);
        // On sauvegarde le début dans sa propre variable
        $start = $has_start ? substr($text, 0, strpos($text, $key1)) : '';
        // Et on le retire du texte principal
        if ($has_start) $text = substr($text, strpos($text, $key1));

        // Si le texte ne finis pas par un ID (sans compter espaces)
        $has_end = !str_ends_with($trimmed, $key2);
        // On sauvegarde la fin dans sa propre variable
        $end = $has_end ? substr($text, strrpos($text, $key2, -1) + strlen($key2)) : '';
        // Et on la retire du texte principal
        if ($has_end) $text = substr($text, 0, strrpos($text, $key2, -1) + strlen($key2));

        // On échappe les clés pour les utiliser dans le regex
        $key1 = preg_quote($key1); $key2 = preg_quote($key2); 
        $matches = []; // L'équivalent du glossaire mais pour capturer les match regex
        $output = preg_replace_callback( // Applique le callback de la fonction pour chaque match

            // Le regex suivant sert à rechercher, remplacer et retourner le contenu de $text
            // Il doit capturer les éléments tradutibles entre les ID de glossaire,
            // Sans modifier les clés d'ID ou les espaces autours.
            '/('.$key2.')(\s*)((?:(?!'.$key1.'|'.$key2.').)*?\S)(\s*)('.$key1.')/',
            // Il est divisé en 3 parties :
            // 1 et 3: /('.$key2.')(\s*) et (\s*)('.$key1.')/ définissent le début et la fin du match
            // Détectant les clés de glossaire autour et acceptant des espaces en plus
            // Dans le callback, il donnent dans l'ordre match[1], 2, 4 et 5
            // ((?:(?!'.$key1.'|'.$key2.').)*?\S) capture le contenu en propre
            // (?:(?!'.$key1.'|'.$key2.').)*? permet d'éviter de capturer un ID entre deux autres ID
            // Et \S est utilisé pour les caractères !espace

            function ($match) use (&$matches) { // Callback avec remplissage par référence de $matches
                $matches[] = $match[3]; // Ajouter le troisième match au glossaire
                // Reconstruire le reste pour mettre à jour le texte
                // en remplaçant le match par le clé de glossaire X
                return $match[1].$match[2].TraductionController::key('X').$match[4].$match[5];
            },
            $text
        );
        if ($has_start) { // Si il y a du texte au début
            $output = TraductionController::key('X').$output; // On ajoute un ID X
            array_unshift($matches, $start); // Et le traductible au début de $matches
        }
        if ($has_end) { // Si il y a du texte à la fin
            $output = $output.TraductionController::key('X'); // On ajoute un ID X
            $matches[] = $end; // Et le traductible à la fin de $matches
        }
        return [$output, $matches];
    }

    public static function translator(string $text, string $lang) {
        return $text; // TODO:
    }

    public static function reconstruct(string $notranslate, array $translated, array $glossary) {
        $key = TraductionController::key('X'); // Init la clé pour facilité d'utilisation
        foreach ($translated as $translation) { // Pour chaque traduction
            // Elles sont enregistrées dans l'ordre, on peut donc les restituer dans l'ordre
            $pos = strpos($notranslate, $key); // Ainsi la première clé 'X'
            // Correspond à la première traduction du tableau
            $notranslate = substr_replace($notranslate, $translation, $pos, strlen($key));
        }
        // L'élément du glossaire le plus englobant est le dernier,
        // Il faut donc parcourir l'array à l'envers (avec l'$id = index)
        for ($element = end($glossary); ($id = key($glossary)) !== null; $element = prev($glossary)) {
            $key = TraductionController::key($id); // On définis la clé à remplacer
             // Elle est forcément présente, il faut juste la trouver et la remplacer
            $notranslate = str_replace($key, $element, $notranslate);
        }
        return $notranslate;
    }
}
