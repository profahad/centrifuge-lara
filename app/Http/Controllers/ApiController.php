<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use phpcent\Client;
use Pusher\Pusher;

class ApiController extends Controller
{

    public function generateToken(Request $request): JsonResponse
    {
        try {

            $validator = Validator::make(
                $request->all(),
                [
                    'room' => 'required|String',
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    "success" => false,
                    "message" => $validator->getMessageBag()->first()
                ]);
            }

            $room = $request->get('room');

            $payload = array(
                "aud" => env('JITSI_APP_ID'),
                "iss" => env('JITSI_APP_ID'),
                "sub" => env('JITSI_HOST'),
                "exp" => Carbon::now()->addHours(2)->timestamp,
                "room" => $room,
                "context" => array(
                    "user" => array(
                        "avatar" => "https://avatars.githubusercontent.com/u/18231224?v=4",
                        "name" => "Muhammad Fahad",
                        "email" => "contact@faaadi.com",
                        "id" => "007"
                    )
                )
            );
            $jwt = JWT::encode($payload, env('JITSI_APP_SECRET'));

            return response()->json([
                "success" => true,
                "message" => "Token will be expired within 2 hours.",
                "data" => [
                    "room" => $room,
                    "token" => $jwt
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    /**
     * Create Instance of Centrifugal Api
     * @return Client
     */
    private function getCentClient(): Client
    {
        $client = new Client(env("CENTRIFUGE_HOST") . '/api',
            env("CENTRIFUGE_API_KEY"),
            env("CENTRIFUGE_API_SECRET"));
        $client->setSafety(false); // If SSL not working or not integrated
        return $client;
    }

    public function genCentToken(Request $request): JsonResponse
    {
        try {
            $payload = array(
                "sub" => $request->get('user_id', Str::random(10)),
                //"exp" => Carbon::now()->addHours(2)->timestamp,
            );
            $jwt = JWT::encode($payload, env('CENTRIFUGE_HMAC_SECRET'));

            return response()->json([
                "success" => true,
                "message" => "Token have no expiration time",
                "data" => [
                    "token" => $jwt
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function broadcastCentrifuge(Request $request): JsonResponse
    {
        try {
            $client = $this->getCentClient();
            $response = $client->publish("chat",
                [
                    "message" => $request->get("message",
                        "Welcome in centrifuge from fahad")
                ]);
            if ($response) {
                return response()->json([
                    "success" => true,
                    "message" => "Message broadcast successfully",
                ]);
            } else {
                return response()->json([
                    "success" => false,
                    "message" => $response
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function centrifugalPresence(Request $request): JsonResponse
    {
        try {

            $targetUser = $request->get("user_id", null);
            $client = $this->getCentClient();
            $response = $client->presence("chat");
            $found = false;
            if ($response) {
                $users = array();
                foreach ($response->result->presence as $v) {
                    $users[] = $v;
                    if (!$found)
                        $found = !is_null($targetUser) && $v->user == $targetUser;
                }
                return response()->json([
                    "success" => true,
                    "message" => "Data retrieved successfully",
                    "data" => array(
                        "targetUser" => $targetUser,
                        "found" => $found,
                        "users" => $users
                    )
                ]);
            } else {
                return response()->json([
                    "success" => false,
                    "message" => $response
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public
    function broadcastPusher(Request $request): JsonResponse
    {
        try {

            $options = array(
                'cluster' => 'ap1',
                'useTLS' => true
            );
            $pusher = new Pusher(
                '7e1a469b2dfbc73b3463',
                '830ff1c1559f1f5dfe7a',
                '1234944',
                $options
            );
            $data['message'] = $request->get("message", "This is fahad");
            $pusher->trigger('chat', 'public', $data);

            return response()->json([
                "data" => $data,
                "success" => true,
                "message" => "Message broadcast successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
}
