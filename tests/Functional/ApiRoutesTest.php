<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Http\Requests\CreateOfficeForm;
use App\Http\Requests\UpdateOfficeForm;
use App\Repository\OfficeRepository;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * ApiRoutesTest.
 */
class ApiRoutesTest extends TestCase
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

        $this->officeRepository = new OfficeRepository();
        $this->artisan('migrate');
        $this->artisan('db:seed');
    }

    /**
     * Tests that the endpoint retrieves successfully all the offices.
     */
    public function testRetrieveAllOfficesEndpoint(): void
    {
        $endPointUrl = '/api/offices';
        $offices     = $this->officeRepository->all();
        $this->json('GET', $endPointUrl)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($offices->toArray());
    }

    /**
     * Tests that the endpoint retrieves successfully an office.
     */
    public function testRetrieveOfficeInformationEndpointSuccessfully(): void
    {
        $officeId        = 1;
        $endpointUrl     = sprintf('/api/offices/%d', $officeId);
        $retrievedOffice = $this->officeRepository->find($officeId);

        $this->json('GET', $endpointUrl)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($retrievedOffice->toArray());
    }

    /**
     * Tests that the endpoint returns a 404 response if office is not found.
     */
    public function testRetrieveOfficeInformationEndpointIfNotFound(): void
    {
        $officeId             = -1;
        $endpointUrl          = sprintf('/api/offices/%d', $officeId);
        $errorResponseMessage = [
            'status'  => Response::HTTP_NOT_FOUND,
            'message' => sprintf('Unable to find office with id: %s', $officeId)
        ];

        $this->json('GET', $endpointUrl)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson($errorResponseMessage);

    }

    /**
     * Tests that the endpoint creates successfully an office.
     */
    public function testCreateOfficeEndpointSuccessfully(): void
    {
        $endPointUrl            = 'api/offices/create/new';
        $officeData             = [CreateOfficeForm::OFFICE_NAME_KEY => 'office name', CreateOfficeForm::OFFICE_ADDRESS_KEY => 'office address'];
        $successResponseMessage = [
            'status'  => Response::HTTP_OK,
            'message' => 'Office was persisted successfully!'
        ];

        $this->json('POST', $endPointUrl, $officeData)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($successResponseMessage);
    }

    /**
     * Tests that the endpoint returns a 422 if office name is not set.
     */
    public function testCreateOfficeEndpointFailsIfRequestModelHasNoNameFieldSet(): void
    {
        $endPointUrl          = 'api/offices/create/new';
        $officeData           = [CreateOfficeForm::OFFICE_ADDRESS_KEY => 'office address'];
        $errorResponseMessage =  [
            'errors' =>
                [
                    'name' => [
                        'The name field is required.'
                    ]
                ]
        ];

        $this->json('POST', $endPointUrl, $officeData)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson($errorResponseMessage);
    }

    /**
     * Tests that the endpoint returns a 422 if office name is greater than 255 chars.
     */
    public function testCreateOfficeEndpointFailsIfRequestModelHasNameLongerThan255(): void
    {
        $endPointUrl                = 'api/offices/create/new';
        $string256Lenght            = 'mtOoY5nhJAZXsvwTE7pchZ95Dh3E6oHKzVI4TjJFV1RdhL5PKssw0hIOmll5GfNfjCkcxgHfGtVpBDHT1YzhpEgTAzKQbrhZ6cs1TCmY2PeeAmzbnfjmeUHC28jtS0yAO6e6sEQVQSKNgDlSvb1C8lQwnpFvB79qAYvani2TpGBIroIq9gTpHeTHNm78RHufS8zUxDnbY6nh3OIDVSni3kRjggE4hOGGdI0tCdSf6onfN5GGYtQwWJn1qbo1uA0A';
        $officeData                 = [CreateOfficeForm::OFFICE_NAME_KEY => $string256Lenght];
        $errorResponseMessage =  [
            'errors' =>
                [
                    'name' => [
                        'The name may not be greater than 255 characters.'
                    ]
                ]
        ];

        $this->json('POST', $endPointUrl, $officeData)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson($errorResponseMessage);
    }

    /**
     *  Tests that the endpoint returns a 422 if office address is missing.
     */
    public function testCreateOfficeEndpointFailsIfAddressFieldMissing(): void
    {
        $endPointUrl            = 'api/offices/create/new';
        $officeData                 = [CreateOfficeForm::OFFICE_NAME_KEY => 'office name'];
        $errorJsonResponseStructure =  [
            'errors' =>
                [
                    'address' => [
                        'The address field is required.'
                    ]
                ]
        ];

        $this->json('POST', $endPointUrl, $officeData)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson($errorJsonResponseStructure);
    }

    /**
     * Tests that the endpoint updates successfully an office.
     */
    public function testUpdateOfficeEndpointSuccessfully(): void
    {
        $officeId               = 1;
        $endPointUrl            = sprintf('api/offices/%d', $officeId);
        $officeData             = [UpdateOfficeForm::OFFICE_NAME_KEY => 'office name', UpdateOfficeForm::OFFICE_ADDRESS_KEY => 'office address'];
        $successResponseMessage = [
            'status'  => Response::HTTP_OK,
            'message' => 'Office was updated successfully!'
        ];

        $this->json('POST', $endPointUrl, $officeData)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($successResponseMessage);
    }

    /**
     * Tests thart the endpoint returns a 404 if office not found.
     */
    public function testUpdateOfficeEndpointFailsIfOfficeNotFound(): void
    {
        $officeId               = -1;
        $endPointUrl            = sprintf('api/offices/%d', $officeId);
        $officeData             = [UpdateOfficeForm::OFFICE_NAME_KEY => 'office name', UpdateOfficeForm::OFFICE_ADDRESS_KEY => 'office address'];
        $errorResponseMessage = [
            'status'  => Response::HTTP_NOT_FOUND,
            'message' => sprintf('Unable to find office with id: %s', $officeId)
        ];

        $this->json('POST', $endPointUrl, $officeData)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson($errorResponseMessage);
    }

    /**
     * Tests that the endpoint returns a 422 if request model not valid.
     */
    public function testUpdateOfficeEndpointFailsIfRequestModelNotValid(): void
    {
        $officeId               = 1;
        $endPointUrl            = sprintf('api/offices/%d', $officeId);
        $errorJsonResponseStructure =  [
            'errors' =>
                [
                    'address' => [
                        'The address field is required when the name is null.'
                    ]
                ]
        ];

        $this->json('POST', $endPointUrl, [])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson($errorJsonResponseStructure);
    }

    /**
     * Test that the endpoint deletes an office successfully.
     */
    public function testDeleteOfficeEndpointSuccessfully(): void
    {
        $officeId    = 1;
        $endPointUrl = sprintf('api/offices/delete/%d', $officeId);
        $successResponseMessage = [
            'status'  => Response::HTTP_OK,
            'message' => 'Office was deleted successfully!'
        ];

        $this->json('POST', $endPointUrl)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($successResponseMessage);
    }

    /**
     * Test that the endpoint fails if office is not found.
     */
    public function testDeleteOfficeEndpointFailsIfOfficeNotFound(): void
    {
        $officeId    = -1;
        $endPointUrl = sprintf('api/offices/delete/%d', $officeId);
        $errorMessage = sprintf('Unable to find office with id: %s', $officeId);
        $errorResponseMessage = [
            'status'  => Response::HTTP_NOT_FOUND,
            'message' => $errorMessage
        ];

        $this->json('POST', $endPointUrl)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson($errorResponseMessage);
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
