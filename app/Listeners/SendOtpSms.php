<?php

namespace App\Listeners;

use App\Events\OtpRequested;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class SendOtpSms implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OtpRequested $event): void
    {
        try {
            // Configuration Twilio
            $sid = config('services.twilio.sid');
            $token = config('services.twilio.token');
            $from = config('services.twilio.from');

            // Créer le client Twilio
            $twilio = new Client($sid, $token);

            // Numéro de téléphone formaté (ajouter le préfixe international si nécessaire)
            $to = $this->formatPhoneNumber($event->numeroUser);

            // Message OTP
            $message = "Votre code OTP OM Pay est : {$event->otp}. Valide pendant 5 minutes.";

            // Envoyer le SMS
            $twilio->messages->create($to, [
                'from' => $from,
                'body' => $message
            ]);

            // Log de succès
            Log::info("OTP envoyé avec succès à {$to}", [
                'user_id' => $event->user->id,
                'numero_user' => $event->numeroUser
            ]);

        } catch (\Exception $e) {
            // Log d'erreur
            Log::error("Erreur lors de l'envoi de l'OTP", [
                'user_id' => $event->user->id,
                'numero_user' => $event->numeroUser,
                'error' => $e->getMessage()
            ]);

            // Relancer l'exception pour que le job soit marqué comme échoué
            throw $e;
        }
    }

    /**
     * Formater le numéro de téléphone
     */
    private function formatPhoneNumber(string $numero): string
    {
        // Supprimer tous les espaces et caractères non numériques
        $numero = preg_replace('/\D/', '', $numero);

        // Si le numéro commence par 77, 78, 76, 70 (Sénégal), ajouter +221
        if (preg_match('/^(77|78|76|70)/', $numero)) {
            return '+221' . $numero;
        }

        // Si le numéro ne commence pas par +, l'ajouter
        if (!str_starts_with($numero, '+')) {
            return '+' . $numero;
        }

        return $numero;
    }
}