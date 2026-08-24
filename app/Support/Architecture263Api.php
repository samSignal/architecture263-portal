<?php

namespace App\Support;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Architecture263Api
{
    public function login(string $username, string $password, string $deviceName = 'portal'): Response
    {
        $client = Http::baseUrl(config('services.architecture263.base_url'))
            ->acceptJson()
            ->asJson()
            ->timeout(config('services.architecture263.timeout'));

        $payload = [
            'username' => $username,
            'password' => $password,
            'device_name' => $deviceName,
        ];

        $paths = [
            '/api/auth/login',
            '/api/login',
            '/login',
        ];

        $lastResponse = null;
        foreach ($paths as $path) {
            $response = $client->post($path, $payload);

            if ($response->status() === 404 || $response->status() === 405) {
                $lastResponse = $response;
                continue;
            }

            return $response;
        }

        return $lastResponse ?? $client->post('/api/auth/login', $payload);
    }

    public function register(array $data): Response
    {
        return Http::baseUrl(config('services.architecture263.base_url'))
            ->acceptJson()
            ->asJson()
            ->timeout(config('services.architecture263.timeout'))
            ->post('/api/auth/register', $data);
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

    public function listArchitects(string $token, ?string $query = null): Response
    {
        return Http::baseUrl(config('services.architecture263.base_url'))
            ->acceptJson()
            ->asJson()
            ->timeout(config('services.architecture263.timeout'))
            ->withToken($token)
            ->get('/api/architects', array_filter(['q' => $query]));
    }

    public function getArchitect(string $token, int $id): Response
    {
        return Http::baseUrl(config('services.architecture263.base_url'))
            ->acceptJson()
            ->asJson()
            ->timeout(config('services.architecture263.timeout'))
            ->withToken($token)
            ->get("/api/architects/{$id}");
    }

    public function listEngagements(string $token): Response
    {
        return Http::baseUrl(config('services.architecture263.base_url'))
            ->acceptJson()
            ->asJson()
            ->timeout(config('services.architecture263.timeout'))
            ->withToken($token)
            ->get('/api/engagements');
    }

    public function getEngagement(string $token, int $id): Response
    {
        return Http::baseUrl(config('services.architecture263.base_url'))
            ->acceptJson()
            ->asJson()
            ->timeout(config('services.architecture263.timeout'))
            ->withToken($token)
            ->get("/api/engagements/{$id}");
    }

    public function createEngagement(string $token, int $architectId): Response
    {
        return Http::baseUrl(config('services.architecture263.base_url'))
            ->acceptJson()
            ->asJson()
            ->timeout(config('services.architecture263.timeout'))
            ->withToken($token)
            ->post('/api/engagements', ['architect_id' => $architectId]);
    }

    public function approveEngagement(string $token, int $id): Response
    {
        return Http::baseUrl(config('services.architecture263.base_url'))
            ->acceptJson()
            ->asJson()
            ->timeout(config('services.architecture263.timeout'))
            ->withToken($token)
            ->post("/api/engagements/{$id}/approve");
    }

    /**
     * Architect declares they've purchased the ACZ Blue Book (Conditions of
     * Engagement & Scale of Fees) — a standing credential, not something
     * bought per engagement. Admin approves it from the admin backend.
     */
    public function requestBlueBook(string $token): Response
    {
        return Http::baseUrl(config('services.architecture263.base_url'))
            ->acceptJson()
            ->asJson()
            ->timeout(config('services.architecture263.timeout'))
            ->withToken($token)
            ->post('/api/architect/blue-book/request');
    }

    public function signContract(string $token, int $id): Response
    {
        return Http::baseUrl(config('services.architecture263.base_url'))
            ->acceptJson()
            ->asJson()
            ->timeout(config('services.architecture263.timeout'))
            ->withToken($token)
            ->post("/api/engagements/{$id}/contract/sign");
    }

    public function submitPlanApplication(string $token, array $data, ?\Illuminate\Http\UploadedFile $drawings = null): Response
    {
        $request = Http::baseUrl(config('services.architecture263.base_url'))
            ->acceptJson()
            ->timeout(config('services.architecture263.timeout'))
            ->withToken($token);

        if ($drawings) {
            $request = $request->attach(
                'drawings',
                file_get_contents($drawings->getRealPath()),
                $drawings->getClientOriginalName()
            );
        }

        return $request->post('/api/plan-applications', $data);
    }

    public function downloadDrawings(string $token, int $id): Response
    {
        return Http::baseUrl(config('services.architecture263.base_url'))
            ->timeout(config('services.architecture263.timeout'))
            ->withToken($token)
            ->get("/api/plan-applications/{$id}/drawings");
    }

    public function previewDrawings(string $token, int $id): Response
    {
        return Http::baseUrl(config('services.architecture263.base_url'))
            ->timeout(config('services.architecture263.timeout'))
            ->withToken($token)
            ->get("/api/plan-applications/{$id}/drawings", ['preview' => 1]);
    }

    public function downloadDrawingVersion(string $token, int $id, int $version): Response
    {
        return Http::baseUrl(config('services.architecture263.base_url'))
            ->timeout(config('services.architecture263.timeout'))
            ->withToken($token)
            ->get("/api/plan-applications/{$id}/drawings/{$version}");
    }

    public function storeMarkup(string $token, int $id, array $data): Response
    {
        return Http::baseUrl(config('services.architecture263.base_url'))
            ->acceptJson()
            ->asJson()
            ->timeout(config('services.architecture263.timeout'))
            ->withToken($token)
            ->post("/api/plan-applications/{$id}/markups", $data);
    }

    public function deleteMarkup(string $token, int $id, int $markupId): Response
    {
        return Http::baseUrl(config('services.architecture263.base_url'))
            ->acceptJson()
            ->timeout(config('services.architecture263.timeout'))
            ->withToken($token)
            ->delete("/api/plan-applications/{$id}/markups/{$markupId}");
    }

    /**
     * Save-and-continue: persist whatever fields have been completed so
     * far as a durable draft on the admin backend (not just PHP session).
     */
    public function saveDraftPlanApplication(string $token, array $data): Response
    {
        return Http::baseUrl(config('services.architecture263.base_url'))
            ->acceptJson()
            ->asJson()
            ->timeout(config('services.architecture263.timeout'))
            ->withToken($token)
            ->post('/api/plan-applications/draft', $data);
    }

    public function getDraftPlanApplication(string $token, int $engagementId): Response
    {
        return Http::baseUrl(config('services.architecture263.base_url'))
            ->acceptJson()
            ->asJson()
            ->timeout(config('services.architecture263.timeout'))
            ->withToken($token)
            ->get("/api/plan-applications/draft/{$engagementId}");
    }

    public function finalizePlanApplication(string $token, int $id, array $data, ?\Illuminate\Http\UploadedFile $drawings = null): Response
    {
        $request = Http::baseUrl(config('services.architecture263.base_url'))
            ->acceptJson()
            ->timeout(config('services.architecture263.timeout'))
            ->withToken($token);

        if ($drawings) {
            $request = $request->attach(
                'drawings',
                file_get_contents($drawings->getRealPath()),
                $drawings->getClientOriginalName()
            );
        }

        return $request->post("/api/plan-applications/{$id}/finalize", $data);
    }

    public function resubmitPlanApplication(string $token, int $id, array $data, ?\Illuminate\Http\UploadedFile $drawings = null): Response
    {
        $request = Http::baseUrl(config('services.architecture263.base_url'))
            ->acceptJson()
            ->timeout(config('services.architecture263.timeout'))
            ->withToken($token);

        if ($drawings) {
            $request = $request->attach(
                'drawings',
                file_get_contents($drawings->getRealPath()),
                $drawings->getClientOriginalName()
            );
        }

        return $request->post("/api/plan-applications/{$id}/resubmit", $data);
    }

    public function listPlanApplications(string $token): Response
    {
        return Http::baseUrl(config('services.architecture263.base_url'))
            ->acceptJson()
            ->asJson()
            ->timeout(config('services.architecture263.timeout'))
            ->withToken($token)
            ->get('/api/plan-applications');
    }

    public function getPlanApplication(string $token, int $id): Response
    {
        return Http::baseUrl(config('services.architecture263.base_url'))
            ->acceptJson()
            ->asJson()
            ->timeout(config('services.architecture263.timeout'))
            ->withToken($token)
            ->get("/api/plan-applications/{$id}");
    }

    public function addPlanApplicationComment(string $token, int $id, string $body): Response
    {
        return Http::baseUrl(config('services.architecture263.base_url'))
            ->acceptJson()
            ->asJson()
            ->timeout(config('services.architecture263.timeout'))
            ->withToken($token)
            ->post("/api/plan-applications/{$id}/comments", ['body' => $body]);
    }

    public function decidePlanApplication(string $token, int $id, string $decision, ?string $comment = null): Response
    {
        return Http::baseUrl(config('services.architecture263.base_url'))
            ->acceptJson()
            ->asJson()
            ->timeout(config('services.architecture263.timeout'))
            ->withToken($token)
            ->post("/api/plan-applications/{$id}/decide", array_filter([
                'decision' => $decision,
                'comment' => $comment,
            ]));
    }
}
