<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('/students', [StudentsController::class, 'index'])->name('students');
    Route::post('/students/new', [StudentsController::class, 'new'])->name('students.new');
    Route::get('/students/enroll/{id}', [StudentsController::class, 'enroll'])->name('students.enroll');
    Route::get('/students/unenroll/{id}', [StudentsController::class, 'unenroll'])->name('students.unenroll');
    Route::post('/students', [StudentsController::class, 'save_face'])->name('students.save_face');
    Route::post('/find-students', [StudentsController::class, 'find_matno'])->name('students.find_matno');
    Route::get('/attendance', [StudentsController::class, 'attendance'])->name('attendance');
    Route::get('/attendance/mark/{id}', [StudentsController::class, 'mark_attendance'])->name('attendance.mark');
});

require __DIR__.'/auth.php';
