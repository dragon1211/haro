<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TerminalApproveEmail extends Mailable
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
        $subject = '端末'.($this->data['allow'] == 1 ? '許可' : '禁止') ;
        $name = $this->data['allow'] == 1 ? 'ハルト代理店アプリの登録が完了しました。' : 'ハルト代理店アプリの利用が停止されました。';

        return $this->view('emails.terminal')
                    ->from($address, $name)
                    ->subject($subject)
                    ->with(['data' => $this->data]);
    }
}
