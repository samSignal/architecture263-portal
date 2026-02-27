<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestApi extends Command
{
    protected $signature = 'test:api {username} {password}';
    protected $description = 'Test API connection with credentials';

    public function handle()
    {
        $url = config('services.architecture263.base_url');
        $this->info("Attempting to connect to {$url}...");

        try {
            $response = Http::baseUrl($url)
                ->timeout(10)
                ->post('/api/auth/login', [
                    'username' => $this->argument('username'),
                    'password' => $this->argument('password'),
                    'device_name' => 'portal_cli_test'
                ]);
            
            $this->info("Status Code: " . $response->status());
            $this->info("Response Body: " . $response->body());
        } catch (\Exception $e) {
            $this->error("Exception: " . $e->getMessage());
        }
    }
}
