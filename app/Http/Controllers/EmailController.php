<?php

namespace App\Http\Controllers;

use App\Mail\ApproverEmail;
use Illuminate\Http\Request;
use Mail;

class EmailController extends Controller
{
    public function test_email(){
        $mydata = [
            "try" => 'Ivan motorista'
        ];
        Mail::to('louisitoojide@gmail.com')->send(new ApproverEmail($mydata));
    }
}
