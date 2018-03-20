<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $reset_url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($reset_token)
    {
        //Set token
        $this->reset_url = action( 'Admin\AccountController@resetPasswordByToken', [ 'token' => $reset_token ] );
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from( 'noreply@psscommercial.com')
                    ->subject('Password reset')
                    ->view('mail.account.reset-password');
    }
}
