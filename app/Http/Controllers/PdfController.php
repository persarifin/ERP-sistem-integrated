<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Entities\User;
use App\Entities\UserAttachment;
use PDF;

class PdfController extends Controller
{
    public function index(Request $request)
    {
        try {
            $foundUser = User::find($request->user_id);

            if (!$foundUser) {
                throw new \Exception("User not found!", 404);
            }

            $foundProfileImage = UserAttachment::where([['user_id', '=', $foundUser->id], ['attachment_type', '=', 'PHOTO PROFILE']])->first();

            if (!$foundProfileImage) {
                throw new \Exception("Photo profile not found!", 404);
            }

            $user = $request->all();
            $user["profileImage"] = $foundProfileImage->file_location . $foundProfileImage->file_name;
            $data["user"] = $user;

            view()->share('proof-selection-player', $data);

            PDF::setOptions(['isRemoteEnabled' => true]);
            $pdf  = PDF::loadView('proof-selection-player', $data);
            $filename = 'bukti_pendaftaran_' . $foundProfileImage->user_id . ".pdf";
            \Storage::put('public/pdf/' . $filename, $pdf->output());

            return response()->json([
                "success" => true,
                "data" => base64_encode(\Storage::get('public/pdf/' . $filename)),
            ], 200);

            // \Log::debug($request->all());
            // return response()->json(["success" => true], 200);
            // return view('proof-selection-player', $data);
            // return $pdf->download('selection-player.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
