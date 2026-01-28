<?php

namespace App\Http\Controllers\Apis\V1\Sendings;

use App\Http\Controllers\Controller;
use App\Models\Sendings\Campaign;
use App\Models\Sendings\Sender;
use App\Models\Sendings\Servers\Server;
use App\Models\Users\ApiKey;
use Exception;
use Illuminate\Http\Request;
use Throwable;

class OTPSendingV1ApiController extends Controller
{
    public function send(Request $request)
    {
        try{
            $request->validate([
                'receiver' => 'required',
                'sender_name' => 'required|string',
            ]);

            $api = ApiKey::with('user.plan')->findOrFail($request->api_key_id);

            $servers = $api->user->plan->servers;

            if (empty($servers)) {
                throw new Exception("Servers not found");
            }

            $server = Server::with('senders')->findOrFail($servers[0]);

            $allowedSenderIds = $api->permissions['senders'] ?? [];
            $sender = $server->senders
                ->where('name', $request->sender_name)
                ->first();

            if (!$sender) {
                throw new Exception("Sender not found on this server");
            }
            if (!in_array($sender->id, $allowedSenderIds, true)) {
                throw new Exception("Sender not allowed");
            }

            $template = $api->template ?? "รหัสอ้างอิง [{{ref_id}}]:OTP ของคุณคือ {{otp_code}}";

            $otp_code = random_int(100000, 999999);
            $ref_id = $request->ref_id ?? rtrim(strtr(base64_encode(random_bytes(4)), '+/', '-_'), '=');

            $data = [
                '{{otp_code}}' => $otp_code,
                '{{ref_id}}' => $ref_id,
            ];
            $message = str_replace(
                array_keys($data),
                array_values($data),
                $template
            );
            $receiver = $request->receiver;

            if (!is_array($receiver)) {
                $receiver = [$receiver];
            }

            $cost = count($receiver) ?? 1;
            $count_receiver = count($receiver);


//            $campaign = [
            $campaign = Campaign::create([
                'name' => 'ส่ง OTP',
                'action_key' => 'otp',
                'receivers' => $receiver,
                'message' => $message,
                'data' => [
                    'cost' => $cost,
                    'real_cost' => $cost * $count_receiver,
                    'receiver_count' => $count_receiver,
                    'otp_code' => $otp_code,
                    'ref_id' => $ref_id,
                ],
                'total_cost' => $cost * $count_receiver,
                'status' => Campaign::STATUS_PENDING,
                'sender_name' => $sender->name,
                'sender_id' => $sender->id,
                'server_name' => $server->name,
                'server_id' => $server->id,
                'user_id' => $api->user_id,
            ]);

            if($campaign) {
                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully',
                    'data' => [
                        'campaign_id' => $campaign->id,
                        'message' => $message,
                        'ref_id' => $ref_id,
                        'otp_code' => $otp_code,
                    ],
                    'code' => 201,
                ], 201);
            }

        }catch (Throwable $e) {
            $response = [
                'success' => false,
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

    public function report(Request $request)
    {
        try{
            $request->validate([
                'campaign_id' => 'required',
            ]);
            $campaign_id = $request->campaign_id;

            $campaign = Campaign::where('action_key', 'otp')->findOrFail($campaign_id);

            return response()->json([
                'success' => true,
                'message' => 'Get Report Successfully',
                'data' => [
                    'campaign_id' => $campaign_id,
                    'ref_id' => $campaign->data['ref_id'],
                    'status' => $campaign->status_text,
                    'sent_at' => $campaign->sent_at,
                    'updated_at' => $campaign->updated_at,
                    'created_at' => $campaign->created_at,
                ],
                'code' => 200,
            ], 200);

        }catch (Throwable $e) {
            $response = [
                'success' => false,
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
