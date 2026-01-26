<?php

namespace App\Http\Controllers\Apis\V1\Sendings;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Throwable;

class OTPSendingV1ApiController extends Controller
{
    public function send(Request $request)
    {
        try{
            $request->validate([
                'receiver' => 'required'
            ]);
            $receiver = $request->receiver;
        }catch (Throwable $e) {
            $response = [
                'message' => 'มีบางอย่างผิดพลาด โปรดลองอีกครั้งในภายหลัง',
                'description' => $e->getMessage() ?? '',
                'code' => 500,
            ];
            if(config('app.debug')) $response['debug'] = [
                'message' => $e->getMessage() ?? '',
                'request' => $request->all() ?? '',
            ];
            return response()->json($response, 500);
        }
    }
}
