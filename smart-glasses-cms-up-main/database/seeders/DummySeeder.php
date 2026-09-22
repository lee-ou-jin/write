<?php

namespace Database\Seeders;

use App\Models\EquipmentCompany;
use App\Models\Ship;
use App\Models\ShipsCompany;
use App\Models\SmartGlasses;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummySeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 일반유저 생성 및 선박 생성
        for ($i = 0; $i < 10; $i++) {
            $user = \App\Models\User::factory()->create();

            if ($user->name != 'jerry' && $user->name != 'xiso') {
                $roleAbleType = [ShipsCompany::class, EquipmentCompany::class][rand(0, 1)];
                $company = null;
                $role = null;

                if ($roleAbleType === ShipsCompany::class) {
                    $company = ShipsCompany::factory()->create();
                    Ship::factory()->create([
                        'ships_company_id' => $company->id,
                    ]);
                    $role = '선사';
                } elseif ($roleAbleType === EquipmentCompany::class) {
                    $company = EquipmentCompany::factory()->create();
                    $role = '업체';
                }
                $user->update([
                    'roleable_id' => $company->getKey(),
                    'roleable_type' => $roleAbleType,
                    'role' => $role,
                ]);
            }
        }

        $company = ShipsCompany::factory()->create([
            'name' => 'test company',
            'address' => 'test address',
            'phone' => '010-1234-5678',
            'email' => $user->email,
            'ceo' => $user->name
        ]);

        // 테스트용 user, ships_company, ship 생성
        $user = User::factory()->create([
            'name' => 'test',
            'email' => 'test@company.com',
            'password' => Hash::make('amuz1234'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'roleable_id' => $company->getKey(),
            'roleable_type' => ShipsCompany::class,
            'role' => '선사',
        ]);

        $ship = Ship::factory()->create([
            'id' => 99999,
            'ships_company_id' => $company->getKey(),
            'name' => 'test ship',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        SmartGlasses::factory()->create([
            'user_name' => 'jerry',
            'position' => 'jerry',
            'auth_code' => '12345678',
            'ship_id' => $ship->id,
            'ships_company_id' => $company->id,
            'model' => 'test model',
            'serial_number' => '123456789',
            'firmware_version' => 1,
            'os_version' => 1,
            'status' => 'active',
        ]);

        SmartGlasses::factory(10)->create();

    }
}
