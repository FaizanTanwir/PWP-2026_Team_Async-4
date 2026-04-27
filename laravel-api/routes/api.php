<?php

// use App\Http\Controllers\AuthController;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Route;
// use App\Enums\UserRole;
// use App\Http\Controllers\CourseController;
// use App\Http\Controllers\LanguageController;
// use App\Http\Controllers\SentenceController;
// use App\Http\Controllers\UnitController;
// use App\Http\Controllers\WordController;
// use App\Http\Controllers\SubmissionController;

// // Public route to get the token
// Route::post('/login', [AuthController::class, 'login'])->name('login');
// Route::post('/register', [AuthController::class, 'register']);

// Route::get('languages', [LanguageController::class, 'index']);
// Route::get('languages/{language}', [LanguageController::class, 'show']);

// Route::get('courses', [CourseController::class, 'index']);
// Route::get('courses/{course}', [CourseController::class, 'show']);

// // get all units for a course, and get a single unit
// Route::get('courses/{course}/units', [UnitController::class, 'index']);
// Route::get('units/{unit}', [UnitController::class, 'show']);

// // Protected routes (require a valid token)
// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
//     Route::get('units/{unit}/sentences', [SentenceController::class, 'index']);
//     Route::get('submissions/{submission}', [SubmissionController::class, 'show']);

//     // Nested routes for logic-specific access
//     Route::get('units/{unit}/submissions', [SubmissionController::class, 'index']);
//     Route::post('units/{unit}/submissions', [SubmissionController::class, 'store']);

//     Route::get('/user', function (Request $request) {
//         return $request->user();
//     });

//     // API routes for ADMIN only
//     Route::middleware('role:' . UserRole::ADMIN->value)->group(function () {
//         Route::delete('languages/{language}', [LanguageController::class, 'destroy']);
//         Route::delete('words/{word}', [WordController::class, 'destroy']);
//     });

//     // API routes for TEACHER & ADMIN
//     Route::middleware('role:' . UserRole::TEACHER->value . '|' . UserRole::ADMIN->value)
//         ->group(function () {
//             Route::post('languages', [LanguageController::class, 'store']);
//             Route::patch('languages/{language}', [LanguageController::class, 'update']);

//             Route::post('courses', [CourseController::class, 'store']);
//             Route::patch('courses/{course}', [CourseController::class, 'update']);
//             Route::delete('courses/{course}', [CourseController::class, 'destroy']);

//             // Create units directly under a course
//             Route::post('courses/{course}/units', [UnitController::class, 'store']);
//             Route::patch('units/{unit}', [UnitController::class, 'update']);
//             Route::delete('units/{unit}', [UnitController::class, 'destroy']);

//             // Create sentences directly under a unit
//             Route::post('units/{unit}/sentences', [SentenceController::class, 'store']);
//             Route::patch('sentences/{sentence}', [SentenceController::class, 'update']);
//             Route::delete('sentences/{sentence}', [SentenceController::class, 'destroy']);

//             Route::patch('words/{word}', [WordController::class, 'update']);
//         });
// });


use App\Http\Controllers\{
    AuthController, CourseController, LanguageController,
    SentenceController, UnitController, WordController, SubmissionController, QuizController
};
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* --- Public Routes --- */
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);

// Public Read-only Hierarchy
Route::get('languages', [LanguageController::class, 'index']);
Route::get('languages/{language}', [LanguageController::class, 'show']);

// Nested: Get courses belonging to a specific language
Route::get('languages/{language}/courses', [CourseController::class, 'index']);

Route::get('courses/{course}', [CourseController::class, 'show']);
Route::get('courses/{course}/units', [UnitController::class, 'index']); // New Hierarchical route

Route::get('units/{unit}', [UnitController::class, 'show']);

/* --- Protected Routes --- */
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('units/{unit}/quiz', [QuizController::class, 'generate']);

    // Student/Teacher Practice & Results
    Route::prefix('units/{unit}')->group(function () {
        Route::get('sentences', [SentenceController::class, 'index']);
        Route::get('submissions', [SubmissionController::class, 'index']);
        Route::post('submissions', [SubmissionController::class, 'store']);
    });

    Route::get('submissions/{submission}', [SubmissionController::class, 'show']);

    /* --- TEACHER & ADMIN: Content Management --- */
    Route::middleware('role:' . UserRole::TEACHER->value . '|' . UserRole::ADMIN->value)->group(function () {

        // Create courses directly under a language
        Route::post('languages/{language}/courses', [CourseController::class, 'store']);

        // Create units directly under a course
        Route::post('courses/{course}/units', [UnitController::class, 'store']);

        // Create sentences directly under a unit
        Route::post('units/{unit}/sentences', [SentenceController::class, 'store']);

        // Shallow Updates/Deletes (Using the specific ID is cleaner for updates)
        Route::apiResource('languages', LanguageController::class)->only(['store', 'update']);
        Route::apiResource('courses', CourseController::class)->only(['show', 'update', 'destroy']);
        Route::apiResource('units', UnitController::class)->only(['update', 'destroy']);
        Route::apiResource('sentences', SentenceController::class)->only(['update', 'destroy']);
        Route::patch('words/{word}', [WordController::class, 'update']);

        Route::post('/units/{unit}/sentences/preview', [SentenceController::class, 'preview']);
    });

    /* --- ADMIN ONLY --- */
    Route::middleware('role:' . UserRole::ADMIN->value)->group(function () {
        Route::delete('languages/{language}', [LanguageController::class, 'destroy']);
        Route::delete('words/{word}', [WordController::class, 'destroy']);
    });
});
