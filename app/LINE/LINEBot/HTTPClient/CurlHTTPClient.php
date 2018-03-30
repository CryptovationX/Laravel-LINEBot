<?php

/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

namespace App\LINE\LINEBot\HTTPClient;

use App\LINE\LINEBot\Constant\Meta;
use App\LINE\LINEBot\Exception\CurlExecutionException;
use App\LINE\LINEBot\HTTPClient;
use App\LINE\LINEBot\Response;

/**
 * Class CurlHTTPClient.
 *
 * A HTTPClient that uses cURL.
 *
 * @package LINE\LINEBot\HTTPClient
 */
class CurlHTTPClient implements HTTPClient
{
    /** @var array */
    private $authHeaders;
    /** @var array */
    private $userAgentHeader;

    /**
     * CurlHTTPClient constructor.
     *
     * @param string $channelToken Access token of your channel.
     */
    public function __construct($channelToken)
    {
        $this->authHeaders = [
            "Authorization: Bearer $channelToken",
        ];
        $this->userAgentHeader = [
            'User-Agent: LINE-BotSDK-PHP/' . Meta::VERSION,
        ];
    }

    /**
     * Sends GET request to LINE Messaging API.
     *
     * @param string $url Request URL.
     * @return Response Response of API request.
     */
    public function get($url)
    {
        return $this->sendRequest('GET', $url, [], []);
    }

    /**
     * Sends POST request to LINE Messaging API.
     *
     * @param string $url Request URL.
     * @param array $data Request body.
     * @return Response Response of API request.
     */
    public function post($url, array $data)
    {
        return $this->sendRequest('POST', $url, ['Content-Type: application/json; charset=utf-8'], $data);
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $additionalHeader
     * @param array $reqBody
     * @return Response
     * @throws CurlExecutionException
     */
    private function sendRequest($method, $url, array $additionalHeader, array $reqBody)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);

        $headers = array_merge($this->authHeaders, $this->userAgentHeader, $additionalHeader);

        $options = [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_HEADER => true,
        ];

        if ($method === 'POST') {
            if (empty($reqBody)) {
                // Rel: https://github.com/line/line-bot-sdk-php/issues/35
                $options[CURLOPT_HTTPHEADER][] = 'Content-Length: 0';
            } else {
                $options[CURLOPT_POSTFIELDS] = json_encode($reqBody);
            }
        }

        curl_setopt_array($curl,$options);

        $result = curl_exec($curl);

        if($result === false)
            throw new \Exception('CURL error: ' . curl_error($this->curl));

    }
}
