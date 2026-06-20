<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\BusinessHour;
class BusinessHourSeeder extends Seeder
{
    public function run(): void
    {
        $hours = [];
        $tenants = [1, 2, 3];
        $schedules = [
            1 => [0=>false,1=>'09:00-19:00',2=>'09:00-19:00',3=>'09:00-19:00',4=>'09:00-19:00',5=>'09:00-19:00',6=>'09:00-13:00'],
            2 => [0=>false,1=>'10:00-20:00',2=>'10:00-20:00',3=>'10:00-20:00',4=>'10:00-20:00',5=>'10:00-20:00',6=>'10:00-18:00'],
            3 => [0=>false,1=>'09:00-19:00',2=>'09:00-19:00',3=>'09:00-19:00',4=>'09:00-19:00',5=>'09:00-19:00',6=>'09:00-13:00'],
        ];
        foreach ($tenants as $tId) {
            foreach (range(0, 6) as $day) {
                $sched = $schedules[$tId][$day];
                $hours[] = [
                    'tenant_id' => $tId,
                    'day_of_week' => $day,
                    'open_time' => $sched ? explode('-', $sched)[0] : null,
                    'close_time' => $sched ? explode('-', $sched)[1] : null,
                    'is_open' => $sched ? true : false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        BusinessHour::insert($hours);
    }
}
