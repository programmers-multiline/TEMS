<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ToolExtensionNotif extends Mailable
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

        $return = $this->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        if($this->data['type'] == 'approved'){
            $return->subject('Tool Extension Approved');
        }elseif($this->data['type'] == 'notif'){
            $return->subject('Expiring Tools Notice: Request Extension if Needed.');
        }elseif($this->data['type'] == 'notif_acct'){
            $return->subject('New Tools Uploaded - Cost Required');
        }else{
            $return->subject('Tool Extension');
        }
        $return->markdown('pages.components.tool_extension_notif',['mail_data' => $this->data]);
        // dd($this->data);
        return $return;

    }
}
