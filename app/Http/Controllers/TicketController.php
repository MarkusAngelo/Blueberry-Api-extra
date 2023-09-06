<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Log;

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
                ],
                "transition" => [
                    'id' => 21
                ]
            ];
            $response = $client->request('POST', $endpoint, [
                'auth' => [$username, $password],
                'json' => $data, // Include request body data as JSON
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
            $jql = "project = $project";

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
                'json' => $data, // Include request body data as JSON
            ]);

            $response = $client->request('POST', $t_endpoint, [
                'auth' => [$username, $password],
                'json' => $tdata, // Include request body data as JSON
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
}