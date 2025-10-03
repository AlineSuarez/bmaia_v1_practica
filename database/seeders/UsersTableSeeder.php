<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Mantener el usuario original (NO lo pisa si ya existe)
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password123'),
                'remember_token' => Str::random(10),
            ]
        );

        // 2) Usuarios adicionales (se crean o actualizan por email)
        $moreUsers = [
            [
                'name'              => 'Richard GE',
                'email'             => 'richard@bmaia.cl',
                'password'          => Hash::make('password123'),
                'plan'              => 'ge',
                'fecha_vencimiento' => Carbon::now()->addYears(5),
                'webpay_status'     => 'paid',
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Aline GE',
                'email'             => 'aline@bmaia.cl',
                'password'          => Hash::make('password123'),
                'plan'              => 'ge',
                'fecha_vencimiento' => Carbon::now()->addYear(),
                'webpay_status'     => 'paid',
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Nicolas GE',
                'email'             => 'nicolas@bmaia.cl',
                'password'          => Hash::make('password123'),
                'plan'              => 'ge',
                'fecha_vencimiento' => Carbon::now()->addYear(),
                'webpay_status'     => 'paid',
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Milenny GE',
                'email'             => 'milenny@bmaia.cl',
                'password'          => Hash::make('password123'),
                'plan'              => 'ge',
                'fecha_vencimiento' => Carbon::now()->addYear(),
                'webpay_status'     => 'paid',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($moreUsers as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }

        // 3) (Opcional) Crear varios de prueba con Factory
        //    Requiere tener definida la factory de User.
        // User::factory()->count(5)->create([
        //     'plan' => 'afc',
        //     'webpay_status' => 'paid',
        // ]);
        
        // 4) (Opcional) Roles con Spatie
        // if (class_exists(\Spatie\Permission\Models\Role::class)) {
        //     $role = \Spatie\Permission\Models\Role::findOrCreate('Super Admin');
        //     $admin = User::where('email', 'admin@bmaia.cl')->first();
        //     if ($admin && method_exists($admin, 'assignRole')) {
        //         $admin->assignRole($role);
        //     }
        // }
    }
}
