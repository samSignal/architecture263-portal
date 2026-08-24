<?php

namespace App\Http\Controllers;

use App\Support\Architecture263Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ArchitectDirectoryController extends Controller
{
    public function index(Request $request, Architecture263Api $api)
    {
        $token = $request->cookie('portal_token');
        $query = $request->query('q');

        try {
            $response = $api->listArchitects($token, $query);
        } catch (\Exception $e) {
            Log::error('Architect directory exception', ['message' => $e->getMessage()]);

            return view('architects.index', ['architects' => [], 'query' => $query])
                ->with('error', 'Unable to reach the architects registry right now.');
        }

        if ($response->failed()) {
            return view('architects.index', ['architects' => [], 'query' => $query])
                ->with('error', 'Unable to load architects.');
        }

        return view('architects.index', [
            'architects' => $response->json('data', []),
            'query' => $query,
        ]);
    }

    public function show(Request $request, Architecture263Api $api, int $id)
    {
        $token = $request->cookie('portal_token');

        $response = $api->getArchitect($token, $id);

        if ($response->failed()) {
            return redirect()->route('architects.index')->with('error', 'Architect not found.');
        }

        return view('architects.show', ['architect' => $response->json()]);
    }
}
