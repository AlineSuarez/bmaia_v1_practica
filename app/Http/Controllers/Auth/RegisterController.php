<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Mail;
use App\Mail\WelcomeMail;
use Illuminate\Http\Request;
use SendGrid;
use Illuminate\Support\Facades\View;
use Exception;
use App\Models\Preference;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */

    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => \Hash::make($data['password']),
        ]);
        Preference::firstOrCreate(
            ['user_id' => $user->id],
            [
                'language' => 'es_CL',
                'date_format' => 'DD/MM/YYYY',
                'theme' => 'light',
                'voice_preference' => 'female_1',
                'default_view' => 'home',
                'voice_match' => false,
                'calendar_email' => false,
                'calendar_push' => false,
                'reminder_time' => 15,
            ]
        );

        // === Asignar prueba gratuita al usuario recién registrado ===
        \App\Models\Payment::create([
            'user_id' => $user->id,
            'transaction_id' => 'trial-' . uniqid(),
            'status' => 'paid',
            'amount' => 0,
            'plan' => 'drone',
        ]);
        $user->fecha_vencimiento = now()->addDays(16);
        $user->save();

        // Renderiza la vista Blade como HTML
        $htmlContent = View::make('emails.welcome', ['user' => $user])->render();
        // Construir el correo
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("soporte@bmaia.cl", "B-MaiA");
        $email->setSubject("¡Bienvenido a B-MaiA, {$user->name}!");
        $email->addTo($user->email, $user->name);
        $email->addContent(
            "text/plain",
            "¡Bienvenido a B-MaiA, {$user->name}!"
        );
        $email->addContent(
            "text/html",
            $htmlContent
        );

        // Envía el correo usando la API de SendGrid
        $sendgrid = new SendGrid(config('services.sendgrid.api_key'));
        try {
            $response = $sendgrid->send($email);
            \Log::info('SendGrid welcome response', [
                'status' => $response->statusCode(),
                'body' => $response->body(),
                'headers' => $response->headers(),
            ]);
        } catch (Exception $e) {
            \Log::error('SendGrid welcome error: ' . $e->getMessage());
        }

        return $user;
    }
}