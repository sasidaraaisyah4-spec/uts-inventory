<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category; // <-- Pastikan baris ini ada di sini
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Data Kategori Mainan & Art Toys
        Category::create(['name' => 'Blind Box']);
        Category::create(['name' => 'Action Figure']);
        Category::create(['name' => 'Plushie']);
        Category::create(['name' => 'Accessories']);
        
        // 1. Akun Admin
        User::create([
            'name' => 'Admin Toko',
            'email' => 'admin@toko.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 2. Akun Staff / Kasir
        User::create([
            'name' => 'Staff Kasir',
            'email' => 'staff@toko.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        // 3. Akun Customer
        User::create([
            'name' => 'Sasi Customer',
            'email' => 'customer@toko.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);
    }
}