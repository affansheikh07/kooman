<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\EmployeeController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register_admin',[AdminController::class,'register_admin']);
Route::post('/login_admin',[AdminController::class,'login_admin']);

Route::post('/fetch_all_admins',[AdminController::class,'fetch_all_admins']);
Route::post('/delete_admin_by_id/{id}',[AdminController::class,'delete_admin_by_id']);
Route::post('/update_admin_by_id/{id}',[AdminController::class,'update_admin_by_id']);

Route::post('/register_program',[ProgramController::class,'register_program']);
Route::post('/fetch_all_programs',[ProgramController::class,'fetch_all_programs']);
Route::post('/update_program_by_id/{id}',[ProgramController::class,'update_program_by_id']);
Route::post('/delete_program_by_id/{id}',[ProgramController::class,'delete_program_by_id']);

Route::post('/register_student',[StudentController::class,'register_student']);
Route::post('/update_student_by_id/{id}',[StudentController::class,'update_student_by_id']);
Route::post('/fetch_all_students',[StudentController::class,'fetch_all_students']);
Route::post('/delete_student_by_id/{id}',[StudentController::class,'delete_student_by_id']);

Route::post('/register_employee',[EmployeeController::class,'register_employee']);
Route::post('/fetch_all_empolyees',[EmployeeController::class,'fetch_all_empolyees']);
Route::post('/update_employee_by_id/{id}',[EmployeeController::class,'update_employee_by_id']);
Route::post('/delete_employee_by_id/{id}',[EmployeeController::class,'delete_employee_by_id']);
