<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\Architecture263Api;
use App\Support\PortalCookie;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request by creating the client
     * account on the admin backend (System 1 owns all domain data)
     * and logging the portal in with the token it returns.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, Architecture263Api $api): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            $response = $api->register($validated);
        } catch (\Exception $e) {
            Log::error('Portal registration exception', ['message' => $e->getMessage()]);
            throw ValidationException::withMessages([
                'username' => ['Unable to connect to the registration server.'],
            ]);
        }

        if ($response->failed()) {
            Log::error('Portal registration failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'username' => $validated['username'],
            ]);

            $errors = $response->json('errors');
            if (is_array($errors)) {
                throw ValidationException::withMessages($errors);
            }

            throw ValidationException::withMessages([
                'username' => [$response->json('message') ?? 'Registration failed.'],
            ]);
        }

        $token = $response->json('access_token') ?? $response->json('token');

        $cookie = PortalCookie::issue($token);

        return redirect()->route('engagements.index')->withCookie($cookie);
    }
}
