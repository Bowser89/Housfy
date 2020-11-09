<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Models\Office;
use App\Repository\OfficeRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * OfficeRepositoryTest.
 *
 * @coversDefaultClass \App\Repository\OfficeRepository
 */
class OfficeRepositoryTest extends TestCase
{
    use DatabaseMigrations;

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

        $this->officeRepository = new OfficeRepository();
    }

    /**
     * Tests that the method retrieves successfully an office by its id.
     *
     * @covers ::find
     */
    public function testFindOfficeById(): void
    {
        $officeId = 1;
        $office   = $this->officeRepository->find($officeId);

        $this->assertNotNull($office);
        $this->assertInstanceOf(Office::class, $office);
    }

    /**
     * Tests that the method returns null if office is not found.
     *
     * @covers ::find
     */
    public function testFindOfficeByIdReturnsNullIfOfficeNotFound(): void
    {
        $officeId = -1;
        $office   = $this->officeRepository->find($officeId);

        $this->assertNull($office);
    }

    /**
     * Tests that the method retrieves successfully all the offices.
     *
     * @covers ::all
     */
    public function testFindAllOffices(): void
    {
        $offices = $this->officeRepository->all();

        $this->assertNotEmpty($offices);

        foreach ($offices as $office) {
            $this->assertInstanceOf(Office::class, $office);
        }
    }
}
