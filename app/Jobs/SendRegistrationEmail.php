<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendRegistrationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $emailContent = "Успешная регистрация, {$this->user->email}!";
        $subject = 'Добро пожаловать!';
        $to = $this->user->email;
        $from = config('mail.from.address');
        $fromName = config('mail.from.name');

        $fullEmail = "From: $fromName <$from>\r\n"
            . "To: $to\r\n"
            . "Subject: $subject\r\n"
            . "Content-Type: text/plain; charset=utf-8\r\n"
            . "\r\n"
            . $emailContent;

        Mail::raw($emailContent, function ($message) use ($to, $subject, $from, $fromName) {
            $message->from($from, $fromName)
                ->to($to)
                ->subject($subject);
        });

        Storage::put(
            "emails/registration_{$this->user->id}.eml",
            $fullEmail
        );
    }
}
