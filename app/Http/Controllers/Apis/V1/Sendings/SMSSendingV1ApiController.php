<?php

namespace App\Http\Controllers\Apis\V1\Sendings;

use App\Http\Controllers\Controller;
use App\Models\Sendings\Campaign;
use App\Models\Sendings\Sender;
use App\Models\Sendings\Servers\Server;
use App\Models\Users\ApiKey;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Throwable;

class SMSSendingV1ApiController extends Controller
{
    public function send(Request $request)
    {
        try{
            $request->validate([
                'receivers' => 'required|array',
                'sender_name' => 'required|string',
                'message' => 'required|string',
            ]);
            $message = $request->message;
            $receivers = $request->receivers;

            $api = ApiKey::with('user.plan')->findOrFail($request->api_key_id);

            if (!isset($api->options['sms']) || !$api->options['sms']) {
                throw new Exception("SMS not available");
            }

            $servers = $api->user->plan->servers;
            $user = $api->user;

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

            $cost = count($receivers) ?? 1;
            $count_receiver = count($receivers);


//            $campaign = [
            $campaign = Campaign::create([
                'name' => $request->campaign_name ?? 'sms_otp_'.Carbon::now()->timestamp,
                'action_key' => 'sms',
                'receivers' => $receivers,
                'message' => $message,
                'data' => [
                    'cost' => $cost,
                    'real_cost' => $cost * $count_receiver,
                    'receiver_count' => $count_receiver,
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
                    'message' => 'SMS sent successfully',
                    'data' => [
                        'campaign_id' => $campaign->id,
                        'message' => $message,
                        'cost' => $cost,
                        'credits' => $user->credits,
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

    public function report(Request $request, $campaign_id)
    {
        try{
            $campaign = Campaign::where('action_key', 'sms')->where('id', $campaign_id)->firstOrFail();
            $report = $campaign->response_report_callback;
            $response = $campaign->response_callback;

            return response()->json([
                'success' => true,
                'message' => 'Get Report Successfully',
                'data' => [
                    'success' => true,
                    'campaign_id' => $campaign_id,
                    'campaign_name' => $campaign->name,

                    'total_receiver' =>
                        data_get($report, 'total_receiver')
                        ?? data_get($response, 'total_receiver')
                            ?? $campaign->receivers->count(),

                    'sent' => data_get($report, 'sent') ?? data_get($response, 'sent') ?? 0,
                    'failed' => data_get($report, 'failed') ?? data_get($response, 'failed') ?? 0,
                    'pending' => data_get($report, 'pending') ?? data_get($response, 'pending') ?? 0,
                    'passed' => data_get($report, 'passed') ?? data_get($response, 'passed') ?? 0,

                    'credits_refund' =>
                        data_get($report, 'credits_refund')
                        ?? data_get($response, 'credits_refund')
                            ?? 0,

                    'status' =>
                        data_get($report, 'status')
                        ?? data_get($response, 'status')
                            ?? $campaign->status_text,
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
