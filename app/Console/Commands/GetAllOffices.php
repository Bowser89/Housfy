<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Repository\OfficeRepository;
use Illuminate\Console\Command;

/**
 * GetAllOffices.
 */
class GetAllOffices extends Command
{
    /**
     * The command signature.
     */
    protected $signature = 'offices:all';

    /**
     * The console command description.
     */
    protected $description = 'Gets all the offices from the  database.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(OfficeRepository $officeRepository): int
    {
        // Retrieves all offices
        $offices = $officeRepository->all()->toArray();

        // Printing output
        $outputHeaders = ['Id', 'Name', 'Address'];
        $this->table($outputHeaders, $offices);

        return 0;
    }
}
