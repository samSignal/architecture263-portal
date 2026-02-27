<?php

namespace App\Support;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Architecture263Api
{
    public function login(string $username, string $password, string $deviceName = 'portal'): Response
    {
        return Http::baseUrl(config('services.architecture263.base_url'))
            ->acceptJson()
            ->asJson()
            ->timeout(config('services.architecture263.timeout'))
            ->post('/api/auth/login', [
                'username' => $username,
                'password' => $password,
                'device_name' => $deviceName,
            ]);
    }

    public function getUser(string $token): Response
    {
        return Http::baseUrl(config('services.architecture263.base_url'))
            ->acceptJson()
            ->asJson()
            ->timeout(config('services.architecture263.timeout'))
            ->withToken($token)
            ->get('/api/auth/me');
    }

    public function logout(string $token): Response
    {
        return Http::baseUrl(config('services.architecture263.base_url'))
            ->acceptJson()
            ->asJson()
            ->timeout(config('services.architecture263.timeout'))
            ->withToken($token)
            ->post('/api/auth/logout');
    }

    public function submitPlanApplication(string $token, array $data): Response
    {
        return Http::baseUrl(config('services.architecture263.base_url'))
            ->acceptJson()
            ->asJson()
            ->timeout(config('services.architecture263.timeout'))
            ->withToken($token)
            ->post('/api/plan-applications', $data);
    }
}
