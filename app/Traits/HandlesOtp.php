<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait HandlesOtp
{

    /**
     * Génère un OTP à 6 chiffres
     *
     * @return string
     */
    private function generateOtp(): string
    {
        return str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Stocke un OTP en cache avec expiration
     *
     * @param string $userId
     * @param string $otp
     * @param int $minutes
     * @return void
     */
    private function storeOtp(string $userId, string $otp, int $minutes = 5): void
    {
        $cacheKey = 'otp_phone_' . $userId;
        Cache::put($cacheKey, $otp, now()->addMinutes($minutes));
    }

    /**
     * Valide un OTP pour un utilisateur
     *
     * @param string $userId
     * @param string $otp
     * @return bool
     */
    private function validateOtp(string $userId, string $otp): bool
    {
        $cacheKey = 'otp_phone_' . $userId;
        $cachedOtp = Cache::get($cacheKey);

        return $cachedOtp && $cachedOtp === $otp;
    }

    /**
     * Supprime un OTP du cache
     *
     * @param string $userId
     * @return void
     */
    private function clearOtp(string $userId): void
    {
        $cacheKey = 'otp_phone_' . $userId;
        Cache::forget($cacheKey);
    }
}