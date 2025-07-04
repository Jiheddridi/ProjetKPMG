<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un utilisateur admin de test
        User::create([
            'name' => 'Admin Test',
            'email' => 'admin@cobit.local',
            'password' => Hash::make('password123'),
        ]);

        echo "Utilisateur admin créé avec succès !\n";
        echo "Email: admin@cobit.local\n";
        echo "Mot de passe: password123\n";
    }
}
