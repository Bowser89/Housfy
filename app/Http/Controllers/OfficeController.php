<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateOfficeForm;
use App\Http\Requests\UpdateOfficeForm;
use App\Models\Office;
use App\Repository\OfficeRepository;
use App\Services\OfficeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OfficeController extends Controller
{
    /**
     * The office service instance.
     */
    private OfficeService $officeService;

    /**
     * The office repository instance.
     */
    private OfficeRepository $officeRepository;

    /**
     * The constructor method.
     */
    public function __construct(OfficeService $officeService, OfficeRepository $officeRepository)
    {
        $this->officeService    = $officeService;
        $this->officeRepository = $officeRepository;
    }

    /**
     * Action for route: offices.all.
     */
    public function retrieveAllOffices(): JsonResponse
    {
        $offices = $this->officeRepository->all();

        return new JsonResponse($offices->toArray());
    }

    /**
     * Action for route: offices.retrieve_offices.
     */
    public function retrieveOfficeInformation(int $officeId): JsonResponse
    {
        try {
            $office = $this->retrieveOfficeByIdOrThrow($officeId);
        } catch (NotFoundHttpException $e) {
            $errorMessage = $this->createResponseMessage($e->getMessage(), Response::HTTP_NOT_FOUND);
            return new JsonResponse($errorMessage, Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($office->toArray());
    }

    /**
     * Action for route: offices.create.
     */
    public function createOffice(CreateOfficeForm $model): JsonResponse
    {
        $this->officeService->createAndPersistOffice($model->all());
        $this->removeOfficesFromCache();

        $successMessage = $this->createResponseMessage('Office was persisted successfully!', Response::HTTP_OK);

        return new JsonResponse($successMessage);
    }

    /**
     * Action for route: offices.update.
     */
    public function updateOffice(UpdateOfficeForm $model, int $officeId)
    {
        try {
            $office = $this->retrieveOfficeByIdOrThrow($officeId);
            $this->officeService->updateOffice($office, $model->all());
        } catch (NotFoundHttpException $e) {
            $errorMessage = $this->createResponseMessage($e->getMessage(), Response::HTTP_NOT_FOUND);
            return new JsonResponse($errorMessage, Response::HTTP_NOT_FOUND);
        }

        $this->removeOfficesFromCache();

        $successMessage = $this->createResponseMessage('Office was updated successfully!', Response::HTTP_OK);

        return new JsonResponse($successMessage);
    }

    /**
     * Action for route offices.delete.
     */
    public function deleteOffice(int $officeId)
    {
        try {
            $office = $this->retrieveOfficeByIdOrThrow($officeId);
            $office->delete();
            // Invalidate cache
        } catch (NotFoundHttpException $e) {
            $errorMessage = $this->createResponseMessage($e->getMessage(), Response::HTTP_NOT_FOUND);
            return new JsonResponse($errorMessage, Response::HTTP_NOT_FOUND);
        } catch (\Exception $e){
            $errorMessage = $this->createResponseMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            return new JsonResponse($errorMessage, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $this->removeOfficesFromCache();

        $successMessage = $this->createResponseMessage('Office was deleted successfully!', Response::HTTP_OK);

        return new JsonResponse($successMessage);
    }

    /**
     * Removes cached offices.
     */
    private function removeOfficesFromCache(): void
    {
        Cache::forget(OfficeRepository::CACHED_OFFICES_KEY);
    }

    /**
     * Returns an office by its id.
     *
     * @throws NotFoundHttpException If the office is not found.
     */
    private function retrieveOfficeByIdOrThrow(int $officeId): Office
    {
        /** @var Office $office */
        $office = $this->officeRepository->find($officeId);

        if (!$office) {
            throw new NotFoundHttpException(sprintf('Unable to find office with id: %s', $officeId));
        }

        return $office;
    }

    /**
     * Returns a formatted message to be sent in response.
     */
    private function createResponseMessage(string $message, int $status): array
    {
        return [
            'status'  => $status,
            'message' => $message,
        ];
    }
}
