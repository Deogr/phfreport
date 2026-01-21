<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Users
        $admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@phf.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'admin',
        ]);

        $manager = User::create([
            'name' => 'Gym Manager',
            'email' => 'manager@phf.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'manager',
        ]);

        $receptionist = User::create([
            'name' => 'Sarah Receptionist',
            'email' => 'sarah@phf.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'receptionist',
        ]);

        User::create([
            'name' => 'John Receptionist',
            'email' => 'john@phf.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'receptionist',
        ]);

        // Stations
        $mainStation = \App\Models\Station::create([
            'name' => 'Main Entrance',
            'location' => 'Ground Floor',
            'status' => 'active'
        ]);

        $gymStation = \App\Models\Station::create([
            'name' => 'Gym Turnstile',
            'location' => 'First Floor',
            'status' => 'active'
        ]);

        // Services
        $dayPass = \App\Models\Service::create(['name' => 'Day Pass (Gym)', 'price' => 5000, 'status' => 'active']);
        $monthly = \App\Models\Service::create(['name' => 'Monthly Membership', 'price' => 50000, 'status' => 'active']);
        $personal = \App\Models\Service::create(['name' => 'Personal Training', 'price' => 150000, 'status' => 'active']);
        $sauna = \App\Models\Service::create(['name' => 'Sauna Only', 'price' => 10000, 'status' => 'active']);
        $massage = \App\Models\Service::create(['name' => 'Massage', 'price' => 25000, 'status' => 'active']);

        // Attendance Logs (Sample Data for Today)
        \App\Models\AttendanceLog::create([
            'user_id' => $receptionist->id,
            'station_id' => $mainStation->id,
            'service_id' => $dayPass->id,
            'user_count' => 1,
            'payment_method' => 'Cash',
            'amount' => 5000,
            'status' => 'draft',
            'created_at' => now()->subHours(2)
        ]);

        \App\Models\AttendanceLog::create([
            'user_id' => $receptionist->id,
            'station_id' => $mainStation->id,
            'service_id' => $monthly->id,
            'user_count' => 1,
            'payment_method' => 'Mobile',
            'amount' => 50000,
            'status' => 'draft',
            'created_at' => now()->subHours(1)
        ]);

        // Past approved reports for charts
        for ($i = 13; $i >= 0; $i--) {
            \App\Models\ShiftReport::create([
                'user_id' => $receptionist->id,
                'station_id' => $mainStation->id,
                'start_time' => now()->subDays($i)->setHour(8),
                'end_time' => now()->subDays($i)->setHour(16),
                'total_cash' => rand(50000, 200000),
                'total_momo' => rand(50000, 200000),
                'total_revenue' => rand(100000, 400000),
                'status' => 'approved',
                'created_at' => now()->subDays($i)
            ]);
        }

        // Staff Assignments
        $days = ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'];
        foreach ($days as $day) {
            \App\Models\StaffAssignment::create([
                'user_id' => $receptionist->id,
                'station_id' => $mainStation->id,
                'day_of_week' => $day,
                'start_time' => '06:00',
                'end_time' => '22:00'
            ]);

            \App\Models\StaffAssignment::create([
                'user_id' => 4, // John Receptionist
                'station_id' => $gymStation->id,
                'day_of_week' => $day,
                'start_time' => '06:00',
                'end_time' => '22:00'
            ]);
        }
    }
}
