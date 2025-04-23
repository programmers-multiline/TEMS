<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WarehouseDocsClerkNotif extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $data;

    public function __construct($datas)
    {
        $this->data = $datas;
    }

    public function build()
    {

        $subject = 'TEIS Form Upload';

        if (isset($this->data['type'])) {
            if($this->data['type'] == 'pullout'){
                 $subject = 'TERS Form Upload';
            }elseif($this->data['type'] == 'rttte'){
                $subject = 'TERS and TEIS Form Upload';
            }else{
                $subject = 'TEIS Form Upload';
            }
        }


        return $this->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
        ->subject($subject)
        ->markdown('pages.components.warehouse_staff_notif',['mail_data' => $this->data]);

    }
}
