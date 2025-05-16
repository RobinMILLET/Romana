<?php

namespace App\Models;

use DateInterval;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class Constante extends Model
{
    protected $table = "constante";
    protected $primaryKey = 'constante_clef';
    protected $keyType = 'string';
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = [
        'constante_valeur'
    ];

    public static function key(string $key) {
        $key = strtolower($key); // La clef est toujours en minuscules
        $const = Constante::find($key); // Exception si elle n'est pas trouvée
        if (!$const) throw new InvalidArgumentException("Constant $key was not found.");
        return $const->valeur(); // On récupère la valeur
    }

    public static function interval(string $key, string $unit = '') {
        $key = strtolower($key); // La clef est toujours en minuscules
        $const = Constante::find($key); // Exception si elle n'est pas trouvée
        if (!$const) throw new InvalidArgumentException("Constant $key was not found.");
        $val = $const->valeur(); // On récupère la valeur
        switch ($const->constante_type) {
            case 'interval': return $val; // Bon type ; on retourne tout de suite
            case 'integer': // Si c'est un entier, on doit convertir avec $unit (qui doit être fournis)
                if (strlen($unit) < 2) throw new InvalidArgumentException("Unit $unit must be P[T]X");
                $before = substr($unit, 0, strlen($unit) - 1); // Les [1/2] premiers charactères
                $after = substr($unit, strlen($unit) - 1, 1); // Le dernier charactère
                // On construit l'objet DateInterval avec Px[Y|M|W|D] ou PTx[H|M|S]
                return new DateInterval($before.strval($val).$after);
            // Ni un interval ni un entier : inconvertible (on jette une erreur)
            default: throw new InvalidArgumentException("Constant $key cannot be converted.");
        }

    }

    public function valeur() {
        if ($this->constante_valeur) // On prend la valeur si elle existe
            return Constante::decode($this->constante_valeur, $this->constante_type);
        // Sinon, on utilise la valeur par défaut donnée par la base
        return Constante::decode($this->constante_defaut, $this->constante_type);
    }

    public static function decode(string $value, string $type) {
        switch ($type) { 
            case 'boolean': 
                if (array_search(strtolower($value), // les TRUE
                    ['true', 'yes', 'vrai', 'oui', 't', 'y', 'v', 'o'])) return true;
                if (array_search(strtolower($value), // les FALSE
                    ['false', 'faux', 'f', 'non', 'no', 'n'])) return false;
                return boolval($value); // Dernier cas, toute autre valeur
            case 'integer': return intval($value);
            case 'float': return floatval($value);
            case 'string': return strval($value);
            case 'binary': return $value;
            case 'json': return json_decode($value);
            case 'csv': return explode(',', $value);
            case 'date': return DateTime::createFromFormat("Y-m-d", $value);
            case 'time': // Sous format 'h:m:s[.x]'
                [$h, $m, $s] = explode(':', $value);
                return [(int)$h, (int)$m, (float)$s];
            case 'datetime': // Sous format 'Y-m-d H:i:s'
                return DateTime::createFromFormat("Y-m-d H:i:s", $value);
            case 'interval': return new DateInterval($value); // Format 'P[T]xX'
            default: throw new InvalidArgumentException("Type $type is not correct.");
        }
    }

    public function check(string $valeur = null) {
        // Valeur si existante ou valeur par défaut
        if ($valeur === null) $valeur = $this->constante_valeur ?? $this->constante_defaut;
        if (!$this->constante_check) return true; // check peut être null
        // Sinon, check est un csv
        $checks = explode(',', $this->constante_check);
        foreach ($checks as $check) { // On itère
            // ET on compare chaque test à la valeur donnée
            $result = Constante::checkOne($valeur, $check, $this->constante_type);
            if ($result) return $result; // En cas d'échec, checkOne retourne un message d'erreur
        }
        return true;
    }

    public static function checkOne(string $value_str, string $check, string $type) {
        $array = [ // [symbol => [message, fonction]]
            ">=" => [" supérieur ou égal à ", fn($a,$b) => $a>=$b],
            "<=" => [" inférieur ou égal à ", fn($a,$b) => $a<=$b],
            ">" => [" supérieur à ", fn($a,$b) => $a>$b],
            "<" => [" inférieur à ", fn($a,$b) => $a<$b],
            "!" => [" différent de ", fn($a,$b) => $a!=$b],
            "%" => [" multiple de ", fn($a,$b) => $b%$a==0],
        ]; // On itère dans le tableau
        foreach ($array as $symbol => [$msg, $fn]) {
            // Si le symbol correspond (attention à l'ordre)
            if (str_starts_with($check, $symbol)) {
                // La partie 'valeur' de test est après
                $test_str = substr($check, strlen($symbol));
                // On décode les valeurs à tester
                $test = Constante::decode($test_str, $type);
                $value = Constante::decode($value_str, $type);
                // Les valeurs intervales ne peuvent pas être comparées (inégalités)
                if ($type == 'interval') { // Donc on les convertis en secondes
                    $test = date_create('@0')->add($test)->getTimestamp();
                    $value = date_create('@0')->add($value)->getTimestamp();
                }
                // Si le test renvoie vrai, la valeur passe ; on renvoie null
                if ($fn($value, $test)) return null;
                // Sinon, on renvoie un message d'erreur pour l'utilisateur
                return $value_str.$msg.$test_str;
            }
        }
        return null;
    }
}
