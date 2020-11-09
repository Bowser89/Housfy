<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Http\Requests\CreateOfficeForm;
use App\Services\OfficeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

/**
 * CreateOffice.
 */
class CreateOffice extends Command
{
    /**
     * The name and signature of the console command.
     */
        protected $signature = 'offices:create';

    /**
     * The console command description.
     */
    protected $description = 'Creates and persists a new office in the database.';

    /**
     * Validator input rules.
     */
    private array $rules = ['required', 'max:255'];

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
    public function handle(OfficeService $officeService): int
    {
        // User's input
        $officeName    = $this->askValid('Please insert office name', 'name', $this->rules);
        $officeAddress = $this->askValid('Please insert office address', 'address', $this->rules);
        $officeData    = [CreateOfficeForm::OFFICE_NAME_KEY => $officeName, CreateOfficeForm::OFFICE_ADDRESS_KEY => $officeAddress];

        $this->info('About to create office with the following values:');

        $outputHeaders = ['Name', 'Address'];
        $this->table($outputHeaders, [$officeData]);

        // Asks for user confirmation
        $userConfirmsOfficeCreation = $this->confirm('Are you sure you want to proceed?');

        if ($userConfirmsOfficeCreation) {
            // Persists office
            $officeService->createAndPersistOffice($officeData);
            $this->info('Office created successfully!');

            return 0;
        }

        $this->error('Creation aborted.');

        return 0;
    }

    /**
     * Asks a question for user input and validates it.
     */
    private function askValid(string $question, string $field, array $rules)
    {
        $value        = $this->ask($question);
        $errorMessage = $this->validateInput($field, $value, $rules);

        if($errorMessage) {
            $this->error($errorMessage);

            return $this->askValid($question, $field, $rules);
        }

        return $value;
    }

    /**
     * Validates user input.
     */
    private function validateInput(string $field, ?string $value, array $rules): ?string
    {
        $validator = Validator::make([
            $field => $value
        ], [
            $field => $rules
        ]);

        return $validator->fails() ? $validator->errors()->first($field) : null;
    }
}
