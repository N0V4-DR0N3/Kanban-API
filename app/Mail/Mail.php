<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Symfony\Component\Mailer\Envelope;

/**
 * @template TData of array
 *
 * @mixin Queueable
 */
abstract class Mail extends Mailable
{
    use Queueable;

    abstract public function envelope(): Envelope;

    abstract public function content(): Content;
}
