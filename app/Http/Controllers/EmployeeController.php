<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Store a newly created employee.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register_employee(Request $request){

        $rules = [
            'full_name' => 'required|string|max:255',
            'employee_name' => 'required|string|max:255',
            'program' => 'required|string|max:255',
            'start_date' => 'nullable|date|date_format:Y-m-d',
            'employee_id' => 'required|string|max:10|unique:employees,employee_id',
        ];
    
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => '401',
            ], 401);
        }
    
        $data = $request->only(['full_name', 'employee_name', 'program', 'start_date', 'employee_id']);
    
        Employee::create($data);
    
        return response()->json([
            'message' => 'Employee created successfully.',
            'status' => '200',
        ], 200);
    
        }

    function fetch_all_empolyees(Request $req){
        
    $perPage = 30; 
    
    $employees = Employee::orderBy("id", "desc")->paginate($perPage);
    
    $employeeData = $employees->items();
    
    $totalCount = Employee::count(); 
    
    return response()->json(['status' => '200', 'Employees' => $employeeData, 'total_employees' => $totalCount], 200);

    }

    public function update_employee_by_id(Request $request, $id){

        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json([
                'message' => 'Employee not found',
                'status' => '401',
            ], 401);
        }
    
        $rules = [
            'full_name' => 'nullable|string|max:255',
            'employee_name' => 'nullable|string|max:255',
            'program' => 'nullable|string|max:255',
            'start_date' => 'nullable|date|date_format:Y-m-d',
            'employee_id' => 'nullable|string|max:10|unique:employees,employee_id', 
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
            'employee_name', 
            'program', 
            'employee_id', 
            'start_date', 
            'status',
        ]);
    
        foreach ($updatedData as $key => $value) {
            if (is_null($value)) {
                unset($updatedData[$key]);
            }
        }
    
        $employee->update($updatedData);
    
        return response()->json([
            'message' => 'Employee updated successfully.',
            'status' => '200',
        ], 200);
    
    }

    /**
     * Delete a program by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete_employee_by_id($id){

        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json([
                'message' => 'Employee not found',
                'status' => '401',
            ], 401);
        }

        // Delete the program
        $employee->delete();

        return response()->json([
            'message' => 'Employee deleted successfully.',
            'status' => '200',
        ], 200);
    }
}
