<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Http\Requests\CreateOfficeForm;
use App\Http\Requests\UpdateOfficeForm;
use App\Repository\OfficeRepository;
use App\Services\OfficeService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * OfficeServiceTest.
 *
 * @coversDefaultClass \App\Services\OfficeService
 */
class OfficeServiceTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Offices table name.
     */
    private const OFFICES_TABLE_NAME = 'offices';

    /**
     * The office service instance.
     *
     * @var OfficeService
     */
    private OfficeService $officeService;

    /**
     * The office repository instance.
     *
     * @var OfficeRepository
     */
    private OfficeRepository $officeRepository;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->artisan('db:seed');

        $this->officeService    = new OfficeService();
        $this->officeRepository = new OfficeRepository();
    }

    /**
     * Tests that the method persist successfully an office object into the database.
     *
     * @covers ::createAndPersistOffice
     */
    public function testCreateAndPersistOffice(): void
    {
        $officeData = [CreateOfficeForm::OFFICE_NAME_KEY => 'new name', CreateOfficeForm::OFFICE_ADDRESS_KEY => 'new address'];

        $this->officeService->createAndPersistOffice($officeData);

        $this->assertDatabaseHas(self::OFFICES_TABLE_NAME, $officeData);
    }

    /**
     * Tests that the method updates successfully an object into the database.
     *
     * @covers ::updateOffice
     */
    public function testUpdateOfficeUpdatesBothField(): void
    {
        $officeToUpdateId = 1;
        $officeToUpdate   = $this->officeRepository->find($officeToUpdateId);
        $officeData       = [CreateOfficeForm::OFFICE_NAME_KEY => 'updated name', CreateOfficeForm::OFFICE_ADDRESS_KEY => 'updated address'];

        $this->officeService->updateOffice($officeToUpdate, $officeData);

        $this->assertDatabaseHas(self::OFFICES_TABLE_NAME,$officeData);
    }

    /**
     * Tests that the update method updates just the address field.
     *
     * @covers ::updateOffice
     */
    public function testUpdateOfficeUpdatesJustAddressField(): void
    {
        $officeToUpdateId      = 1;
        $officeToUpdate        = $this->officeRepository->find($officeToUpdateId);
        $officeToUpdateOldName = $officeToUpdate->getName();
        $officeNewAddress      = 'updated address';

        $this->officeService->updateOffice($officeToUpdate, [UpdateOfficeForm::OFFICE_ADDRESS_KEY => $officeNewAddress]);

        $officeData = ['name' => $officeToUpdateOldName, 'address' => $officeNewAddress];

        $this->assertDatabaseHas(self::OFFICES_TABLE_NAME, $officeData);
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown():void
    {
        $this->artisan('migrate:reset');

        parent::tearDown();
    }
}
