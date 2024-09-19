<?php

namespace App\Console\Commands;

use App\Models\BookRental;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateNotReturnedRentalsAsOverDue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-not-returned-rentals-as-over-due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will run automatically and mark all those rentals as overdue which have not been returned within 2 weeks.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $getOverdueRecords = BookRental::where('status',1) // Book is still booked and not returned
                                ->where('is_overdue', 0)
                                ->whereDate('till_date', '<', Carbon::today())
                                ->get();

        if(count($getOverdueRecords) > 0) {
            foreach ($getOverdueRecords as $overdueData) {
                $overdueData->is_overdue = true;
                $overdueData->update();
            }
        }
        return true;
    }
}
