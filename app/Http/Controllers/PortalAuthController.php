<?php

namespace App\Http\Controllers;

use App\Support\Architecture263Api;
use App\Support\PortalCookie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PortalAuthController extends Controller
{
    public function showLogin()
    {
        return view('portal.login');
    }

    public function login(Request $request, Architecture263Api $api)
    {
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        try {
            $response = $api->login($validated['username'], $validated['password']);
        } catch (\Exception $e) {
            Log::error('Portal login exception', ['message' => $e->getMessage()]);
            throw ValidationException::withMessages([
                'username' => ['Unable to connect to authentication server.'],
            ]);
        }

        if ($response->failed()) {
            Log::error('Portal login failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'username' => $validated['username']
            ]);

            $errorMessage = $response->json('message') ?? 'Invalid credentials or server error.';
            
            throw ValidationException::withMessages([
                'username' => [$errorMessage],
            ]);
        }

        $token = $response->json('access_token') ?? $response->json('token');

        $cookie = PortalCookie::issue($token);

        return redirect()->route('engagements.index')->withCookie($cookie);
    }

    public function logout(Request $request, Architecture263Api $api)
    {
        $token = $request->cookie('portal_token');

        if ($token) {
            // Best effort logout on System 1
            try {
                $api->logout($token);
            } catch (\Exception $e) {
                // Ignore cleanup errors
            }
        }

        // Forget the cookie
        $cookie = Cookie::forget('portal_token');

        return redirect()->route('portal.login')->withCookie($cookie);
    }

    public function dashboard(Request $request, Architecture263Api $api)
    {
        $token = $request->cookie('portal_token');
        if (! $token) {
            return redirect()->route('portal.login');
        }

        return redirect()->route('engagements.index');
    }
}
