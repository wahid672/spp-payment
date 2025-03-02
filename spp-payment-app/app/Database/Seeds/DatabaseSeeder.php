<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // First, run the settings seeder
        $this->call('SettingsSeeder');

        // Then, run the initial data seeder
        $this->call('InitialDataSeeder');

        // Output success message
        echo "Database seeding completed successfully!\n";
        echo "Default admin credentials:\n";
        echo "Username: admin\n";
        echo "Password: admin123\n\n";
        echo "Default bendahara credentials:\n";
        echo "Username: bendahara\n";
        echo "Password: bendahara123\n\n";
        echo "Sample student credentials:\n";
        echo "Username: student1\n";
        echo "Password: student123\n";
    }
}
