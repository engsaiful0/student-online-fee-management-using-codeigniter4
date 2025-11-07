<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        echo "Running database seeders...\n\n";
        
        // Run Admin Seeder
        $this->call('AdminSeeder');
        
        // Run Student Seeder
        $this->call('StudentSeeder');
        
        echo "\nAll seeders completed successfully!\n";
    }
}

