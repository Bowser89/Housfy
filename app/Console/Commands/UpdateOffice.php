<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Http\Requests\UpdateOfficeForm;
use App\Repository\OfficeRepository;
use App\Services\OfficeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

/**
 * UpdateOffice.
 */
class UpdateOffice extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'offices:update';

    /**
     * The console command description.
     */
    protected $description = 'Updates an office.';

    /**
     * Validator rules for office user input.
     */
    private array $officeIdRules     = ['required','numeric','min:0'];
    private array $officeFieldsRules = ['max:255'];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(OfficeRepository $officeRepository, OfficeService $officeService): int
    {
        // User input
        $officeId = $this->askValid('Please insert the office id', 'id', $this->officeIdRules);
        $office   = $this->retrieveOfficeByIdOrFail($officeRepository, $officeId);

        if (!$office) {
            return 0;
        }

        $this->info('Retrieved office with the following values');

        $tableHeader = ['Id', 'Name', 'Address'];
        $this->table($tableHeader, [$office->toArray()]);

        $officeName    = $this->askValid('Please insert the new office name (nullable)', 'name', $this->officeFieldsRules);
        $officeAddress = $this->askValid('Please insert the new office address (nullable)', 'address', $this->officeFieldsRules);

        if (!$officeName && !$officeAddress) {
            $this->error('Name and address cannot be empty at the same time.');

            return 0;
        }

        $updatedOfficeName    = $officeName ?? $office->getName();
        $updatedOfficeAddress = $officeAddress ?? $office->getAddress();

        $this->info('About to update office with following values:');

        $tableHeader = ['Name', 'Address'];
        $this->table($tableHeader, [[$updatedOfficeName, $updatedOfficeAddress]]);

        $userConfirmsOfficeUpdate = $this->confirm('Are you sure you want to proceed?');

        if ($userConfirmsOfficeUpdate) {
            $officeData = [UpdateOfficeForm::OFFICE_NAME_KEY => $officeName, UpdateOfficeForm::OFFICE_ADDRESS_KEY => $officeAddress];
            $officeService->updateOffice($office, $officeData);

            $this->info('Office updated successfully!');

            return 0;
        }

        $this->error('Update aborted');

        return 0;
    }

    /**
     * Retireves an office by its id or breaks the command.
     */
    private function retrieveOfficeByIdOrFail(OfficeRepository $officeRepository, int $officeId)
    {
        $office = $officeRepository->find($officeId);

        if (!$office) {
            $errorMessage = sprintf('Unable to find office with id: %d', $officeId);
            $this->error($errorMessage);
        }

        return $office;
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
