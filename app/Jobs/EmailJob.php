<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\GenericEmail;

class EmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $subject;
    protected $content;

    public function __construct(string $email, string $subject, string $content)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->content = $content;
    }

    public function handle(): void
    {
        Mail::to($this->email)->send(new GenericEmail($this->subject, $this->content));
    }
}