<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;


class TicketController extends Controller
{
    //
    public function createIssue(Request $request)
    {
        try {
            $client = new Client();
            $username = env('JIRA_USERNAME');
            $password = env('JIRA_PASSWORD');

            $endpoint = 'https://coojt.atlassian.net/rest/api/2/issue';


            $data = [
                'fields' => [
                    'project' => [
                        'key' => $request['projectName']
                    ],
                    'summary' => $request['summary'],
                    'description' => $request['description'],
                    'issuetype' => [
                        'name' => $request['issue']
                    ]
                ]
            ];
            $response = $client->request('POST', $endpoint, [
                'auth' => [$username, $password],
                'json' => $data,
                // Include request body data as JSON
            ]);

            $responseBody = $response->getBody()->getContents();
            return response()->json(['status' => 'success', 'data' => $responseBody]);
        } catch (\Exception $e) {
            dd($e, $data);
        }
    }

    public function getProjects()
    {
        try {
            $client = new Client();
            $username = env('JIRA_USERNAME');
            $password = env('JIRA_PASSWORD');
            $endpoint = 'https://coojt.atlassian.net/rest/api/2/issue/createmeta';


            $response = $client->request('GET', $endpoint, [
                'auth' => [$username, $password]
            ]);

            $responseBody = $response->getBody()->getContents();
            return response()->json(['status' => 'success', 'data' => $responseBody]);
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function getIssues(Request $request, $project)
    {
        try {
            $client = new Client();
            $username = env('JIRA_USERNAME');
            $password = env('JIRA_PASSWORD');
            $endpoint = 'https://coojt.atlassian.net/rest/api/2/search';
            $jql = "project=$project";

            $response = $client->request('GET', $endpoint, [
                'auth' => [$username, $password],
                'query' => ['jql' => $jql]
            ]);

            $responseBody = $response->getBody()->getContents();
            return response()->json(['status' => 'success', 'data' => $responseBody]);
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function getUsers(Request $request, $project)
    {
        try {
            $client = new Client();
            $username = env('JIRA_USERNAME');
            $password = env('JIRA_PASSWORD');
            $endpoint = 'https://coojt.atlassian.net/rest/api/2/user/assignable/search';
            $jql = "project=$project";

            $response = $client->request('GET', $endpoint, [
                'auth' => [$username, $password],
                'query' => $jql
            ]);

            $responseBody = $response->getBody()->getContents();
            return response()->json(['status' => 'success', 'data' => $responseBody]);
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function updateIssue(Request $request, $projectName)
    {
        try {
            $client = new Client();
            $username = env('JIRA_USERNAME');
            $password = env('JIRA_PASSWORD');

            $endpoint = "https://coojt.atlassian.net/rest/api/2/issue/{$projectName}";
            $t_endpoint = "https://coojt.atlassian.net/rest/api/2/issue/{$projectName}/transitions";

            $tdata = [

                'transition' => [
                    'id' => $request['tra_Id']
                ]
            ];

            $data = [
                'fields' => [

                    'summary' => $request['summary'],
                    'description' => $request['description'],

                    'issuetype' => [
                        'id' => $request['issueId'],
                        'name' => $request['issue']
                    ]
                ]
            ];

            $response = $client->request('PUT', $endpoint, [
                'auth' => [$username, $password],
                'json' => $data,
                // Include request body data as JSON
            ]);

            $response = $client->request('POST', $t_endpoint, [
                'auth' => [$username, $password],
                'json' => $tdata,
                // Include request body data as JSON
            ]);
            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            if ($statusCode >= 200 && $statusCode < 300) {
                // Successful response
                return response()->json(['status' => 'success', 'data' => $responseBody]);
            } else {
                return response()->json(['status' => 'failed', 'message' => 'Jira API error', 'data' => $responseBody], $statusCode);
            }

        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            dd(($request->all()));
            return response()->json(['status' => 'failed', 'message' => 'Connection error'], 500);
        }
    }
    public function getEmployees(Request $request)
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

                $headers = [
                    'Authorization' => 'Bearer ' . $bearerToken,
                    'Content-Type' => 'application/json',
                ];

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

                    // $request->session()->put('_token', $token);

                } else {
                    return response()->json(['error' => 'Authentication failed'], $authenticatedResponse->getStatusCode());
                }
                $client = new Client();
                $endpoint = 'https://stage-api-hr.bizdash.app/integration/auth/employee/list';

                if ($token !== null) {
                    $headers = [
                        'Authorization' => 'Bearer ' . $token,
                    ];
                    $response = $client->request('GET', $endpoint, [
                        'headers' => $headers
                    ]);

                    $responseBody = $response->getBody()->getContents();

                    return response()->json(['status' => 'success', 'data' => $responseBody]);
                } else if ($request->session()->has('token')) {
                    return '1';
                } else {
                    return 'User ID not found in the session.';
                }
            }


        } catch (\GuzzleHttp\Exception\ClientException $clientException) {
            // Handle specific ClientException, such as 401 Unauthorized
            return response()->json(['error' => 'Unauthorized'], 401);
        } catch (\GuzzleHttp\Exception\ServerException $serverException) {
            // Handle specific ServerException, such as 500 Internal Server Error
            return response()->json(['error' => 'Internal Server Error'], 500);
        } catch (\Exception $e) {
            // Handle other generic exceptions

            return response()->json(['error' => 'An error occurred'], 500);
        }
    }

}