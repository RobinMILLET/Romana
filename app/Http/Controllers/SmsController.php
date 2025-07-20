<?php

namespace App\Http\Controllers;

use App\Models\Langue;
use Twilio\Rest\Client as TwilioClient;

class SmsController extends Controller
{
    public static function validation(string $num) {
        $lang_id = session("locale", Langue::find(0))->langue_id;

        return match ($lang_id) {
            0 => "Votre numéro de réservation La Romana est '$num'", // Français
            2 => "Su número de reserva de La Romana es '$num'", // Espagnol
            3 => "Ihre La Romana-Buchungsnummer ist '$num'", // Allemand
            4 => "Il tuo numero di prenotazione La Romana è '$num'", // Italien
            5 => "O seu número de reserva da La Romana é '$num'", // Portugais
            6 => "Ваш номер бронирования La Romana: '$num'", // Russe
            7 => "您的La Romana预订号码是：'$num'", // Chinois
            8 => "La Romanaの予約番号は「$num"."」です", // Japonais
            9 => "La Romana 예약 번호는 '$num'입니다", // Coréen
            10 => "رقم الحجز الخاص بك في La Romana هو '$num'", // Arabe
            default => // Autres langues, dont le 1 (Anglais) (en fallback)
                "Your La Romana booking number is '$num'"
        };
    }

    public static function send(string $content, string $target) {
        $twilio = new TwilioClient( // Créer le client
            env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
        
        // Si mis dans le .env, envoyer à ce numéro (pour test)
        if (env('TWILIO_DUMMY')) $target = env('TWILIO_DUMMY');

        // Ajouter automatiquement le '+' si pas inclus
        if (!str_starts_with($target, '+')) $target = '+'.$target;

        $twilio->messages->create(
            $target,
            [
                // Numéro alloué sur twilio
                'from' => env('TWILIO_PHONE_NUMBER'),
                'body' => $content,
            ]
        );
    }
}