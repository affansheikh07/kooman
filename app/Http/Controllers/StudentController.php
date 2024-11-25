<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    /**
     * Store a newly created student.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register_student(Request $request){

    $rules = [
        'full_name' => 'required|string|max:255',
        'username' => 'required|string|max:255|unique:students,username',
        'program' => 'required|string|max:255',
        'max_time' => 'required|string|max:255',
        'start_date' => 'nullable|date|date_format:Y-m-d',
        'student_id' => 'required|string|max:10|unique:students,student_id',
    ];

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
        return response()->json([
            'message' => $validator->errors()->first(),
            'status' => '401',
        ], 401);
    }

    $data = $request->only(['full_name', 'username', 'program', 'max_time', 'start_date', 'student_id']);

    Student::create($data);

    return response()->json([
        'message' => 'Student created successfully.',
        'status' => '200',
    ], 200);

    }

    /**
 * Update a student's details by ID.
 *
 * @param \Illuminate\Http\Request $request
 * @param int $id
 * @return \Illuminate\Http\JsonResponse
 */
public function update_student_by_id(Request $request, $id){

    $student = Student::find($id);
    if (!$student) {
        return response()->json([
            'message' => 'Student not found',
            'status' => '401',
        ], 401);
    }

    $rules = [
        'full_name' => 'nullable|string|max:255',
        'username' => 'nullable|string|max:255|unique:students,username,' . $id,
        'student_id' => 'nullable|string|max:10|unique:students,student_id',
        'program' => 'nullable|string|max:255',
        'max_time' => 'nullable|string|max:255',
        'start_date' => 'nullable|date|date_format:Y-m-d',
        'status' => 'nullable|string',
    ];

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
        return response()->json([
            'message' => $validator->errors()->first(),
            'status' => '401',
        ], 401);
    }

    $updatedData = $request->only([
        'full_name', 
        'username', 
        'program', 
        'max_time', 
        'start_date', 
        'status',
        'student_id'
    ]);

    foreach ($updatedData as $key => $value) {
        if (is_null($value)) {
            unset($updatedData[$key]);
        }
    }

    $student->update($updatedData);

    return response()->json([
        'message' => 'Student updated successfully.',
        'status' => '200',
    ], 200);

    }

    function fetch_all_students(Request $req){
        
    $perPage = 30; 
    
    $students = Student::orderBy("id", "desc")->paginate($perPage);
    
    $studentData = $students->items();
    
    $totalCount = Student::count(); 
    
    return response()->json(['status' => '200', 'Students' => $studentData, 'total_students' => $totalCount], 200);

    }

    /**
     * Delete a program by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete_student_by_id($id){

        $student = Student::find($id);
        if (!$student) {
            return response()->json([
                'message' => 'Student not found',
                'status' => '401',
            ], 401);
        }

        // Delete the program
        $student->delete();

        return response()->json([
            'message' => 'Student deleted successfully.',
            'status' => '200',
        ], 200);
    }
}
