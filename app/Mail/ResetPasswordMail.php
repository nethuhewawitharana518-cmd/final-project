<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $email, public string $token)
    {
    }

    public function build()
    {
        $resetUrl = route('password.reset', ['token' => $this->token]) . '?email=' . urlencode($this->email);
        
        return $this->subject('Reset Your FoodRescue Password')
                    ->view('emails.reset-password')
                    ->with([
                        'resetUrl' => $resetUrl,
                    ]);
    }
}
