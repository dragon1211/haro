<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisterShopEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public $address;

    public function __construct($data, $address)
    {
        $this->data = $data;
        $this->address = $address;
    }

    public function build()
    {
        $address = $this->address;
        $subject = 'ショップの追加申請';
        $name = $this->data->name;

        return $this->view('emails.register_shop')
                    ->from($address, $name)
                    ->subject($subject)
                    ->with(['data' => $this->data]);
    }
}
