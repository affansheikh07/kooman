<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Checked_in;
use App\Models\Ready_to_check_out;
use Carbon\Carbon;


class IncrementTimeSpent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'increment:time_spent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Increment time_spent for checked-in students';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
{
    $currentTime = now(); // Get current time

    // Fetch all entries from `checked_ins`
    $checkedIns = Checked_in::all();

    foreach ($checkedIns as $entry) {
        // Increment time_spent
        $entry->time_spent += 1;

        // Check if the time_spent exceeds the max_time
        if ($entry->time_spent > $entry->max_time) {
            // Increment time_over
            $entry->time_over += 1;

            // Move entry to ready_to_check_outs (if not already present)
            $readyToCheckOut = Ready_to_check_out::firstOrNew(
                ['student_id' => $entry->student_id] // Match existing row by student_id
            );

            // Update values in ready_to_check_outs
            $readyToCheckOut->username = $entry->username;
            $readyToCheckOut->program_name = $entry->program_name;
            $readyToCheckOut->time_spent = $entry->time_spent - 1; // Restore time_spent before increment
            $readyToCheckOut->time_over = $entry->time_over - 1;   // Restore time_over before increment
            $readyToCheckOut->status = 'ready_to_check_out';
            $readyToCheckOut->save();

            // Delete the entry from `checked_ins` since it's now in ready_to_check_outs
            $entry->delete();
        } else {
            // Save the updated `time_spent` in the current entry
            $entry->save();
        }
    }

    // Fetch entries already in `ready_to_check_outs`
    $readyToCheckOuts = Ready_to_check_out::all();

    foreach ($readyToCheckOuts as $entry) {
        // Increment time_spent
        $entry->time_spent += 1;

        // Increment time_over
        if ($entry->time_spent > $entry->max_time) {
            $entry->time_over += 1;
        }

        // Save the updated values
        $entry->save();
    }

    return 0;
}









}
