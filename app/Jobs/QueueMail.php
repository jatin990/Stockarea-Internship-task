<?php

namespace App\Jobs;

use App\Mail\bookupdate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class QueueMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($d)
    {
        $this->d=$d;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mail=new bookupdate($this->d);
        Mail::to($this->d['email'])->send($mail);
    }
}
