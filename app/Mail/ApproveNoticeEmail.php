<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApproveNoticeEmail extends Mailable
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
        $subject = 'お知らせを承認';
        $name = 'アーテック管理チーム';

        return $this->view('emails.approve_notice')
                    ->from($address, $name)
                    ->subject($subject)
                    ->with(['data' => $this->data]);
    }
}
