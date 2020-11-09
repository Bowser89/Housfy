<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OfficeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
*/

/**
 * Retrieves all offices.
 */
Route::get('offices', [OfficeController::class, 'retrieveAllOffices'])->name('offices.all');

/**
 * Retrieves information about specific office.
 */
Route::get('offices/{officeId}', [OfficeController::class, 'retrieveOfficeInformation'])->name('offices.retrieve_office');

/**
 * Creates new office.
 */
Route::post('offices/create/new', [OfficeController::class, 'createOffice'])->name('offices.create');

/**
 * Updates information about specific office.
 */
Route::post('offices/{officeId}', [OfficeController::class, 'updateOffice'])->name('offices.update');

/**
 * Deletes an office.
 */
Route::post('offices/delete/{officeId}', [OfficeController::class, 'deleteOffice'])->name('offices.delete');
