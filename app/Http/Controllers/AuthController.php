<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    //
    protected $bearerToken;
    public function getToken(Request $request)
    {
        try {
            $key = env('EMP_KEY');
            $secret = env('EMP_SECRET');

            $endpoint = 'https://stage-api-portal.bizdash.app/integration/auth/authenticate';

            $requestData = [
                'key' => $key,
                'secret' => $secret,
            ];

            // Make a POST request to the external endpoint
            $response = Http::post($endpoint, $requestData);

            if ($response->successful()) {
                // If the request was successful, extract the Bearer Token from the response headers
                $responseBody = $response->json();
                $bearerToken = $responseBody['access_token'];
                $this->bearerToken = $bearerToken;

                // Return a response (you can customize the response as needed)
                return response()->json(['message' => 'Token obtained successfully']);

            } else {
                // If the request was not successful, handle the error
                return response()->json(['error' => 'Failed to authenticate'], $response->status());
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function login(Request $request)
    {
        try {
            $email = $request->input('email'); // Get the email from the request
            $password = $request->input('password'); // Get the password from the request

            // Include the Bearer Token in the headers of your request
            $headers = [
                'Authorization' => 'Bearer ' . $this->bearerToken,
                'Content-Type' => 'application/json', // Set the content type for the request
            ];

            // Construct the request data containing email and password
            $requestData = [
                'email' => $email,
                'password' => $password,
            ];

            // Make your authenticated request using the Bearer Token and request data
            $authenticatedResponse = Http::withHeaders($headers)->post('https://stage-api-portal.bizdash.app/integration/auth/login', $requestData);

            if ($authenticatedResponse->successful()) {
                // Authentication was successful, continue with the login logic
                return response()->json(['message' => 'Logged in successfully']);
            } else {
                // Authentication failed, handle the error
                dd('a', $authenticatedResponse);
                return response()->json(['error' => 'Authentication failed'], $authenticatedResponse->status());
            }
        } catch (\Exception $e) {
            dd('b', $authenticatedResponse);
            return response()->json(['error' => 'Bearer Token not found'], 401);
        }
    }

}