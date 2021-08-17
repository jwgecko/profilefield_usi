<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * USI Verification API Class (via AVETMISS Done API).
 *
 * @package     profilefield
 * @subpackage  usi
 * @copyright   JWGecko https://jwgecko.com/
 */

namespace user_profile_field_usi;

use moodle_exception;

class USIVerification {
    const HTTP_SUCCESS = 200;
    const HTTP_BAD_REQUEST = 400;

    private $token;
    private $server;
    private $verifyendpoint = '/api/usi/verify/';

    public function __construct($token, $server) {
        $this->token = $token;
        if (!$server) {
            $server = 'https://adts.avetmissfree.com';
        }
        $this->server = $server;
    }

    /**
     * Test API token to make sure it is authorised.
     *
     * @return bool
     * @throws moodle_exception
     */
    public function valid_token() {
        $response = $this->call(
            $this->server . $this->verifyendpoint,
            $this->token
        );

        if ($response['code'] == self::HTTP_BAD_REQUEST) {
            // The token worked just that we don't have enough parameters in the request.
            return true;
        }
        return false;
    }

    /**
     * Verify USI data using user data provided against AVETMISS Done API.
     *
     * @param $userdata
     * @return array
     * @throws \coding_exception
     * @throws moodle_exception
     */
    public function verify_usi($userdata) {
        $response = $this->call(
            $this->server . $this->verifyendpoint,
            $this->token,
            $userdata
        );

        debugging(">>> verify USI against AVETMISS done API");
        debugging(" HTTP response code: " . $response['code']);
        debugging(" HTTP message: " . $response['message']);
        debugging("<<< verify USI against AVETMISS done API");

        if ($response['code'] !== self::HTTP_SUCCESS) {
            return $this->response_message(
                true,
                'apirequestfailedcode', $response['code'] .
                ': <br/><pre>' . $response['message'] . '</pre>'
            );
        }

        $responsedata = json_decode($response['message']);

        if (intval($responsedata->quota) <= 0) {
            return $this->response_message(true, 'apiquotareached');
        }

        if ($responsedata->USIStatus != 'Valid') {
            if ($responsedata->USIStatus == 'Deactivated') {
                return $this->response_message(true, 'usideactivated');
            }
            return $this->response_message(true, 'usiinvalid');
        }

        foreach ($responsedata->response->Items as $key => $item) {
            if ($item != 'Match') {
                $elementname = $responsedata->response->ItemsElementName[$key];
                return $this->response_message(true, 'usimismatchname', $elementname);
            }
        }

        if ($responsedata->response->DateOfBirth != 'Match') {
            return $this->response_message(true, 'usimismatchdob');
        }

    }

    /**
     * Make a request to the API using cURL using a POST request.
     *
     * @param $url
     * @param $token
     * @param $params
     * @return array
     * @throws moodle_exception
     */
    private function call($url, $token, $params = null) {
        $ch = curl_init();

        $headers = [
            "Authorization: Token $token"
        ];

        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, 1);

        if ($params) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }

        try {
            $response = curl_exec($ch);
        } catch (Exception $e) {
            throw new moodle_exception('apirequestfailed', 'profilefield_usi');
            return [];
        }

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'code' => $httpcode,
            'message' => $response
        ];
    }

    /**
     * Return array with response error state (true/false) and an appropriate
     * message from the language pack.
     *
     * @param $error
     * @param $messagekey
     * @param null $data
     * @return array
     * @throws \coding_exception
     */
    private function response_message($error, $messagekey, $data = null) {
        return [
            'error' => $error,
            'message' => get_string($messagekey, 'profilefield_usi', $data)
        ];
    }
}

