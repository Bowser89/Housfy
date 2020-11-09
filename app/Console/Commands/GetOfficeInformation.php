<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Office;
use App\Repository\OfficeRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

/**
 * GetOfficeInformation.
 */
class GetOfficeInformation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offices:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieves information about an office.';

    /**
     * Validator rules for office id.
     */
    private array $rules = ['required','numeric','min:0'];

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
        // User's input
        $officeId = $this->askValid('Please insert the office id', 'id', $this->rules);

        // Retrieves office
        $office = $this->retrieveOfficeByIdOrFail($officeRepository, (int) $officeId);

        if (!$office) {
            return 0;
        }

        // Printing output
        $tableHeader = ['Id', 'Name', 'Address'];
        $this->table($tableHeader, [$office->toArray()]);

        return 0;
    }

    /**
     * Retireves an office by its id or breaks the command.
     */
    private function retrieveOfficeByIdOrFail(OfficeRepository $officeRepository, int $officeId): ?Office
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
