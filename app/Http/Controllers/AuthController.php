<?php

namespace App\Http\Controllers;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Log;


class AuthController extends Controller
{
    //
    // protected $bearerToken;
    // public function getToken(Request $request)
    // {
    //     try {

    //         }
    //     } catch (\Exception $e) {
    //         dd($e);
    //     }
    // }


    public function login(Request $request)
    {
        try {
            $key = 'ojt2023';
            $secret = 'gpWVn69GM9FrkH7k';

            $endpoint = 'https://stage-api-portal.bizdash.app/integration/auth/authenticate';

            $client = new Client();

            $response = $client->post($endpoint, [
                'json' => [
                    'key' => $key,
                    'secret' => $secret,
                ]
            ]);
            if ($response->getStatusCode() === 200) {
                $responseBody = json_decode($response->getBody(), true);
                $bearerToken = $responseBody['access_token'];

                // $email = $request->input('email');
                // $password = $request->input('password');


                $headers = [
                    'Authorization' => 'Bearer ' . $bearerToken,
                    'Content-Type' => 'application/json',
                ];

                // $requestData2 = [
                //     'email' => $request['email'],
                //     'password' => $request['password'],
                // ];

                $pass = 'KvuWvvvjf2Tp3';
                $mail = 'app+default@clarkoutsourcing.com';

                $requestData2 = [
                    'email' => $mail,
                    'password' => $pass,
                ];
                $authenticatedResponse = $client->post('https://stage-api-portal.bizdash.app/integration/auth/login', [
                    'headers' => $headers,
                    'json' => $requestData2,
                ]);

                if ($authenticatedResponse->getStatusCode() === 200) {
                    $responseBody = json_decode($authenticatedResponse->getBody(), true);
                    $token = $responseBody['access_token'];

                    $request->session()->put('_token', $token);

                    return response()->json(['message' => $token]);
                } else {
                    return response()->json(['error' => 'Authentication failed'], $authenticatedResponse->getStatusCode());
                }
            } else {
                return response()->json(['error' => 'Failed to authenticate'], $response->getStatusCode());
            }
        } catch (RequestException $e) {
            return response()->json(['error' => 'Bearer Token not found'], 401);
        }
    }


}