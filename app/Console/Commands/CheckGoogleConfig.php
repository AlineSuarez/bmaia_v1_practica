<?php
// Script para verificar la configuración de Google OAuth
namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckGoogleConfig extends Command
{
    protected $signature = 'google:check-config';
    protected $description = 'Verifica la configuración de Google OAuth';

    public function handle()
    {
        $this->info('=== VERIFICACIÓN DE CONFIGURACIÓN DE GOOGLE ===');
        $this->line('');

        // Client ID
        $clientId = config('services.google.client_id');
        $this->line('GOOGLE_CLIENT_ID:');
        if ($clientId) {
            $preview = substr($clientId, 0, 20) . '...';
            $this->info("  ✓ Configurado: {$preview}");
        } else {
            $this->error('  ✗ NO configurado');
        }
        $this->line('');

        // Client Secret
        $clientSecret = config('services.google.client_secret');
        $this->line('GOOGLE_CLIENT_SECRET:');
        if ($clientSecret) {
            $preview = substr($clientSecret, 0, 10) . '...';
            $this->info("  ✓ Configurado: {$preview}");
        } else {
            $this->error('  ✗ NO configurado');
        }
        $this->line('');

        // Redirect URL
        $redirect = config('services.google.redirect');
        $this->line('GOOGLE_REDIRECT_URL:');
        if ($redirect) {
            $this->info("  ✓ {$redirect}");
        } else {
            $this->error('  ✗ NO configurado');
        }
        $this->line('');

        // URLs de callback esperadas
        $this->line('=== URLS DE CALLBACK ESPERADAS ===');
        $this->line('');
        
        $loginCallback = url('/auth/google/callback');
        $calendarCallback = url('/auth/google-calendar/callback');
        
        $this->line('Para Login:');
        $this->info("  {$loginCallback}");
        $this->line('');
        
        $this->line('Para Google Calendar:');
        $this->info("  {$calendarCallback}");
        $this->line('');

        $this->warn('IMPORTANTE: Ambas URLs deben estar registradas en:');
        $this->line('https://console.cloud.google.com/apis/credentials');
        $this->line('');

        // Verificar rutas
        $this->line('=== VERIFICACIÓN DE RUTAS ===');
        $this->line('');
        
        $routes = [
            'auth.google' => 'GET|HEAD /auth/google',
            'auth.google.calendar' => 'GET|HEAD /auth/google-calendar',
            'auth.google.calendar.callback' => 'GET|HEAD /auth/google-calendar/callback',
        ];

        foreach ($routes as $name => $expected) {
            $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName($name);
            if ($route) {
                $this->info("  ✓ {$name}");
                $this->line("    {$route->uri()}");
            } else {
                $this->error("  ✗ {$name} NO registrada");
            }
        }
        
        $this->line('');
        $this->line('=== CHECKLIST ===');
        $this->line('');
        $this->line('[ ] Client ID configurado en .env');
        $this->line('[ ] Client Secret configurado en .env');
        $this->line('[ ] Redirect URL configurado en .env');
        $this->line('[ ] Ambas URLs de callback registradas en Google Console');
        $this->line('[ ] Google Calendar API habilitada');
        $this->line('[ ] Scope calendar agregado en OAuth Consent Screen');
        $this->line('');

        return 0;
    }
}
