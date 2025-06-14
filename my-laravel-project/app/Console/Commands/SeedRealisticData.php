<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SeedRealisticData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-realistic-data {--fresh : Refresh the database before seeding}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the database with realistic demo data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('fresh')) {
            $this->info('Refreshing database...');
            Artisan::call('migrate:fresh', [], $this->getOutput());
        }

        $this->info('Starting to seed the database with realistic data...');
        
        Artisan::call('db:seed', [], $this->getOutput());
        
        $this->info('Database seeded successfully!');
        $this->info('Login credentials:');
        $this->info('- Admin: admin@debazaar.nl / password');
        $this->info('- Business: zakelijk@example.com / password');
        $this->info('- Private: particulier@example.com / password');
        $this->info('- Regular: normaal@example.com / password');
    }
}
