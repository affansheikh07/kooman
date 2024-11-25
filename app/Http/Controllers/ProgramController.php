<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Program;
use Illuminate\Support\Facades\Validator;

class ProgramController extends Controller
{
    /**
     * Store a newly created program.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register_program(Request $request){

        $rules = [
            'program_name' => 'required|string|max:255',
            'program_area' => 'required|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => '401',
            ], 401);
        }

        Program::create([
            'program_name' => $request->program_name,
            'program_area' => $request->program_area,
        ]);

        return response()->json([
            'message' => 'Program created successfully.',
            'status' => '200',
        ], 200);
    }

    function fetch_all_programs(Request $req){
        
    $perPage = 30; 
    
    $programs = Program::orderBy("id", "desc")->paginate($perPage);
    
    $programData = $programs->items();
    
    $totalCount = Program::count(); 
    
    return response()->json(['status' => '200', 'Programs' => $programData, 'total_programs' => $totalCount], 200);

    }

    /**
     * Update an existing program by ID.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_program_by_id(Request $request, $id){

        $program = Program::find($id);
        if (!$program) {
            return response()->json([
                'message' => 'Program not found',
                'status' => '401',
            ], 401);
        }

        // Validation rules for update
        $rules = [
            'program_name' => 'nullable|string|max:255',
            'program_area' => 'nullable|string|max:255',
            'status' => 'nullable|string', // Optional status field
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => '401',
            ], 401);
        }

        // Update only the fields that are provided in the request
        $updatedData = $request->only(['program_name', 'program_area', 'status']);

        foreach ($updatedData as $key => $value) {
            if (is_null($value)) {
                unset($updatedData[$key]);
            }
        }

        $program->update($updatedData);

        return response()->json([
            'message' => 'Program updated successfully.',
            'status' => '200',
        ], 200);
    }

    /**
     * Delete a program by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete_program_by_id($id){

        $program = Program::find($id);
        if (!$program) {
            return response()->json([
                'message' => 'Program not found',
                'status' => '401',
            ], 401);
        }

        // Delete the program
        $program->delete();

        return response()->json([
            'message' => 'Program deleted successfully.',
            'status' => '200',
        ], 200);
    }
}
