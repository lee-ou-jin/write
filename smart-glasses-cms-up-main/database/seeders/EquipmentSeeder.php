<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\Ship;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 선박 데이터가 있는지 확인
        if (!Ship::exists()) {
            $this->command->error('선박 데이터가 없습니다. 먼저 ShipSeeder를 실행해주세요.');
            return;
        }

        // 장비 데이터 생성
        $ships = Ship::all();
        $equipmentTypes = [
            '엔진',
            '발전기',
            '펌프',
            '컴프레서',
            '냉동장치',
            '항해장비',
            '안전장비',
            '통신장비',
        ];

        foreach ($ships as $ship) {
            // 각 선박당 3-8개의 장비 생성
            $equipmentCount = rand(3, 8);

            for ($i = 0; $i < $equipmentCount; $i++) {
                $equipmentType = $equipmentTypes[array_rand($equipmentTypes)];
                $modelNumber = strtoupper(substr(md5(uniqid()), 0, 8));
                $serialNumber = strtoupper(substr(md5(uniqid()), 0, 12));

                Equipment::create([
                    'ship_id' => $ship->id,
                    'name' => "MODEL-{$modelNumber}",
                    'type' => $equipmentType,
                    'model' => "MODEL-{$modelNumber}",
                    'serial_number' => "SN-{$serialNumber}",
                ]);
            }
        }
    }
}
