<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ServiceRequestNotification extends Mailable
{
    use Queueable, SerializesModels;

    public string $module;
    public array $data;

    public function __construct(string $module, array $data)
    {
        $this->module = $module;
        $this->data = $data;
    }

    public function build(): self
    {
        return $this
            ->subject("[{$this->module}] Nouvelle demande")
            ->view('emails.service_request_notification');
    }
}
