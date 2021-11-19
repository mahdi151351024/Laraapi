<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    public function sendInvitation(Request $request)
    {
        $to_name = "Customer Name";
        $to_email = $request->email;
        $data = ["name" => "Admin", "body" => "You are invited to register for getting our software services. Registration link: "];
        \Mail::send("email.invitation_template", $data, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
            ->subject("Invitation");
            $message->from("mahdiodesk2015@gmail.com", "Invitation Mail");
        });
        return response()->json([
            'status' => true,
            'message' => 'Invitation mail sent successfully',
            'data' => $data
        ]);
    }
}
