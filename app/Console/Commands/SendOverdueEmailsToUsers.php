<?php

namespace App\Console\Commands;

use App\Mail\BookOverdueNotificationMail;
use App\Models\BookRental;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class SendOverdueEmailsToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:overdue-emails-to-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will send emails to users who have not returned the overdue book.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $getOverdueRecordsForEMail = BookRental::where('status',1) // Book is still booked and not returned
            ->whereDate('till_date', '<', Carbon::today())
            ->with(['user','book'])
            ->get();

        if(count($getOverdueRecordsForEMail) > 0) {
            foreach ($getOverdueRecordsForEMail as $bookData) {
                if($bookData->is_overdue === 0 ) {
                    $bookData->is_overdue = true;
                    $bookData->update();
                }

                Mail::to($bookData->user->email)->send(new BookOverdueNotificationMail($bookData));
            }
        }
    }
}
