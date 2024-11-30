<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Checked_in;
use App\Models\Ready_to_check_out;
use App\Models\Student;
use App\Models\Check_out;
use Illuminate\Support\Facades\Validator;

class CheckedController extends Controller
{
    public function checkIn(Request $request){

    $student = Student::where('student_id', $request->student_id)->first();

    if (!$student) {
        return response()->json(['message' => 'Student not found', 'status' => '401'], 401);
    }

    Checked_in::create([
        'student_id'   => $student->student_id,
        'username'     => $student->username,
        'program_name' => $student->program,
        'max_time'     => $student->max_time,
        'time_spent'   => 0,
        'time_over'    => 0,
        'status'       => 'checked_in',
    ]);

    return response()->json(['message' => 'Checked in successfully',  'status' => '200'], 200);
    
    }

    public function checkout_from_checkins(Request $request){

    $validated = $request->validate([
        'id' => 'required|integer|exists:checked_ins,id',
        'status' => 'required|string|in:check_out',
    ]);

    $entry = Checked_in::find($validated['id']);

    if (!$entry) {
        return response()->json(['message' => 'Entry not found', 'status' => '401'], 401);
    }

    if ($validated['status'] === 'check_out'){

        $formattedTimeSpent = "[{$entry->time_spent}/{$entry->max_time}]";

        Check_out::create([
            'student_id' => $entry->student_id,
            'program_name' => $entry->program_name,
            'username' => $entry->username,
            'time_spent' => $formattedTimeSpent,
            'in_time' => $entry->created_at->format('H:i:s'), 
            'out_time' => now()->format('H:i:s'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $entry->delete();

        return response()->json([
            'message' => 'Checked out successfully',
            'status' => '200'
        ], 200);
    }

    return response()->json(['message' => 'Invalid status', 'status' => '401'], 401);

    }

    public function checkout_from_readytocheckout(Request $request){

    $validated = $request->validate([
        'id' => 'required|exists:ready_to_check_outs,id',
        'status' => 'required|string',
    ]);

    $readyToCheckOut = Ready_to_check_out::join('students', 'ready_to_check_outs.student_id', '=', 'students.student_id')
        ->where('ready_to_check_outs.id', $validated['id'])
        ->select(
            'ready_to_check_outs.*',
            'students.max_time'
        )
        ->first();

    if (!$readyToCheckOut) {
        return response()->json(['message' => 'Entry not found', 'status' => '401'], 401);
    }

    $maxTime = $readyToCheckOut->max_time;

    Check_out::create([
        'student_id' => $readyToCheckOut->student_id,
        'program_name' => $readyToCheckOut->program_name,
        'username' => $readyToCheckOut->username,
        'time_spent' => "[" . $readyToCheckOut->time_spent . "/" . $maxTime . "]",
        'in_time' => $readyToCheckOut->created_at->format('H:i:s'),
        'out_time' => now()->format('H:i:s'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    Ready_to_check_out::where('id', $validated['id'])->delete();

    return response()->json(['message' => 'Checked out successfully', 'status' => '200'], 200);

    }




}
