<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //최초 관리자생성은 아래 구분을 반복시켜서 시딩한다.
        //Production 에서 사용금지.
        if (env('APP_ENV', 'local')) {
            $userModel = User::class;

            foreach (
                [

                    'xiso' => 'ceo@amuz.co.kr',
                    'jerry' => 'jerry@amuz.co.kr'

                ] as $name => $email
            ) {
                $user = $userModel::query()->create([
                    'email' => $email,
                    'name' => $name,
                    'password' => Hash::make('amuz1234'),
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10)
                ]);
            }

            $ownedTeam = Team::factory()->create([
                'user_id' => $user->getKey()
            ]);
            $user->current_team_id = $ownedTeam->getKey();
            $user->save();
        }

        //관리자 권한지급
        (new RolesAndPermissionsSeeder())->run();

        (new DummySeeder())->run();

        // 장비 데이터 생성
        (new EquipmentSeeder())->run();
    }
}
