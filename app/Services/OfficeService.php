<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Requests\CreateOfficeForm;
use App\Http\Requests\UpdateOfficeForm;
use App\Models\Office;
use App\Repository\OfficeRepository;

/**
 * OfficeService.
 */
class OfficeService
{
    /**
     * Creates and persists an office object.
     */
    public function createAndPersistOffice(array $officeData)
    {
        $office = new Office();
        $office
            ->setName($officeData[CreateOfficeForm::OFFICE_NAME_KEY])
            ->setAddress($officeData[CreateOfficeForm::OFFICE_ADDRESS_KEY]);

        $office->save();

        return $office;
    }

    /**
     * Updates an office.
     */
    public function updateOffice(Office $office, array $officeData): Office
    {
        if (isset($officeData[UpdateOfficeForm::OFFICE_NAME_KEY]) && $officeData[UpdateOfficeForm::OFFICE_NAME_KEY]) {
            $office->setName($officeData[UpdateOfficeForm::OFFICE_NAME_KEY]);
        }

        if (isset($officeData[UpdateOfficeForm::OFFICE_ADDRESS_KEY]) && $officeData[UpdateOfficeForm::OFFICE_ADDRESS_KEY]) {
            $office->setAddress($officeData[UpdateOfficeForm::OFFICE_ADDRESS_KEY]);
        }

        $office->save();

        return $office;
    }
}
