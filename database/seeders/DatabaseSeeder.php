<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\RekeningAdmin;
use App\Models\RekeningPengguna;
use App\Models\User;
use Carbon\Carbon;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $bankBRI = Bank::create([
            'id' => (string) Str::uuid(),
            'nama' => 'BRI',
            'kode' => '002',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $bankMandiri = Bank::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Mandiri',
            'kode' => '008',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $bankBNI = Bank::create([
            'id' => (string) Str::uuid(),
            'nama' => 'BNI',
            'kode' => '009',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $bankBTN = Bank::create([
            'id' => (string) Str::uuid(),
            'nama' => 'BTN',
            'kode' => '200',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $bankBCA = Bank::create([
            'id' => (string) Str::uuid(),
            'nama' => 'BCA',
            'kode' => '014',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $bankPanin = Bank::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Panin',
            'kode' => '019',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        RekeningAdmin::create([
            'bank_id' => $bankBCA->id,
            'atas_nama' => 'PT BosCOD Indonesia',
            'nomor_rekening' => mt_rand(1000000000, 9999999999),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        RekeningAdmin::create([
            'bank_id' => $bankBNI->id,
            'atas_nama' => 'PT BosCOD Indonesia',
            'nomor_rekening' => mt_rand(1000000000, 9999999999),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        RekeningAdmin::create([
            'bank_id' => $bankBRI->id,
            'atas_nama' => 'PT BosCOD Indonesia',
            'nomor_rekening' => mt_rand(1000000000, 9999999999),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        RekeningAdmin::create([
            'bank_id' => $bankBTN->id,
            'atas_nama' => 'PT BosCOD Indonesia',
            'nomor_rekening' => mt_rand(1000000000, 9999999999),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        RekeningAdmin::create([
            'bank_id' => $bankMandiri->id,
            'atas_nama' => 'PT BosCOD Indonesia',
            'nomor_rekening' => mt_rand(1000000000, 9999999999),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        RekeningAdmin::create([
            'bank_id' => $bankPanin->id,
            'atas_nama' => 'PT BosCOD Indonesia',
            'nomor_rekening' => mt_rand(1000000000, 9999999999),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $user1 = User::create([
            'id' => (string) Str::uuid(),
            'username' => 'pengirim',
            'password' => bcrypt('pengirim'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        RekeningPengguna::create([
            'user_id' => $user1->id,
            'bank_id' => $bankBCA->id,
            'atas_nama' => 'Denny Vioz',
            'nomor_rekening' => mt_rand(1000000000, 9999999999),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        RekeningPengguna::create([
            'user_id' => $user1->id,
            'bank_id' => $bankBRI->id,
            'atas_nama' => 'Denny Vioz',
            'nomor_rekening' => mt_rand(1000000000, 9999999999),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $user2 = User::create([
            'id' => (string) Str::uuid(),
            'username' => 'penerima',
            'password' => bcrypt('penerima'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        RekeningPengguna::create([
            'user_id' => $user2->id,
            'bank_id' => $bankBNI->id,
            'atas_nama' => 'Putri Susiowati',
            'nomor_rekening' => mt_rand(1000000000, 9999999999),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
