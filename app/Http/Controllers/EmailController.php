<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Http\Requests\SendEmailRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Mail;
use Validator;
use Log;

class EmailController extends Controller
{
    public function confirmationRegistration(SendEmailRequest $request)
    { 
      $details = $request->all();
      $originalRedirectUrl = $details["redirect_url"];
      foreach($details["participants"] as $participant){
        if($participant === "UNDANGAN"){
          $details["subject"] = "Konfirmasi Registrasi Kongres AFP Jatim 2021";
          $details["content"] = "Terima kasih telah melakukan registrasi online untuk Kongres AFP Jatim 2021.<br/>Untuk detail informasi mengenai agenda Kongres, undangan dapat menghubungi:<br/><br/>
            Nomer WA<br/>
            Email";
          SendEmailJob::dispatch($details);
        }else if($participant === "KAB/KOTA"){
          $details["subject"] = $details['city'].": Konfirmasi Registrasi Kongres AFP JATIM 2021";
          $details["content"] = "Terima kasih telah melakukan registrasi online untuk Kongres AFP Jatim 2021.<br/>Untuk melengkapi data Surat Mandat sebagai konfirmasi kehadiran, mohon untuk mengisi Form Online yang disediakan dengan klik tombol dibawah ini.";
          if (preg_match('/\?./', $originalRedirectUrl)) {
            $details["redirect_url"] = $details["redirect_url"]."&organization=".$details["city"];
          }
          $details["buttonText"] = "Click Now";
          SendEmailJob::dispatch($details);
          $details["redirect_url"] = $originalRedirectUrl;
        }else{
          $details["subject"] = $details['club'].": Konfirmasi Registrasi Kongres AFP JATIM 2021";
          $details["content"] = "Terima kasih telah melakukan registrasi online untuk Kongres AFP Jatim 2021.<br/>Untuk melengkapi data Surat Mandat sebagai konfirmasi kehadiran, mohon untuk mengisi Form Online yang disediakan dengan klik tombol dibawah ini.";
          if (preg_match('/\?./', $originalRedirectUrl)) {
            $details["redirect_url"] = $details["redirect_url"]."&organization=".$details["club"];
          }
          $details["buttonText"] = "Click Now";
          SendEmailJob::dispatch($details);
          $details["redirect_url"] = $originalRedirectUrl;
        }
      }
      return response()->json(['success' => true, 'message' => 'Email sent successfully!'], 200);
    }
}
