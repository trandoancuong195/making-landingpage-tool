<?php

namespace App\Classes;

use App\Models\Order;
use App\Models\SmsLog;

class Sms
{
    //generate random number
    public static function randomNumber($length)
    {
        $numbers = range(0, 9);
        shuffle($numbers);
        $digits = '';
        for ($i = 0; $i < $length; $i++)
            $digits .= $numbers[$i];
        return $digits;
    }
    public static function get_client_ip()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    public static function call_ntl_api_job($url, $params = '', $method = 'GET', $auth = '')
    {
        $api_url = $url;
        $headers = array(
            "Content-Type: application/json"
        );
        if (!empty($auth)) {
            $headers[] = 'Authorization: ' . $auth;
        }
        $curl = curl_init();
        if ($method == 'POST') {
            $params = json_encode($params);
            array_push($headers, "Content-Length: " . strlen($params));
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        } else if ($method == 'PUT') {
            $params = json_encode($params);
            array_push($headers, "Content-Length: " . strlen($params));
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        } else if ($method == 'DEL') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, $api_url);
        //curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return $response;
    }

    public static function checkValidPhone($phone, $valid_phone, $prefix='signup')
    {
        $now = strtotime("now");
        $time_compare = $now - 5 * 60; // 5 phut
        //check sms_log
        if (!empty($phone) && !empty($valid_phone)) {
            $check_code = SmsLog::where('phone', '=', $phone)->wherePrefix($prefix)->orderBy('id', 'DESC')->first(['code','created_at']);
            if (!empty($check_code) && $check_code->code == $valid_phone && $check_code->created_at->timestamp > $time_compare) {
                return ['status' => true, 'message' => 'Xác thực thành công'];
            }
            if(!empty($check_code) && $check_code->created_at->timestamp < $time_compare){
                return ['status' => false, 'message' => 'Mã xác thực hết hạn'];
            }
        }
        return ['status' => false, 'message' => 'Mã xác thực không đúng'];;
    }
    public static function send_sms($params)
    {
        $success = false;
        $access_token = Sms::sms_access_token();
        if (!empty($params) && !empty($access_token)) {
            $config     = config('api.sms_brandname');
            $api_url    = $config['api_link'] . 'api/push-brandname-otp';
            $api_header = ["cache-control: no-cache", 'Content-Type:application/json'];
            $api_data   = [
                'access_token' => $access_token,
                'session_id'   => session_id(),
                'BrandName'    => $config['brand_name'],
                'Phone'        => $params['phone'],
                'Message'      => base64_encode($params['message'])
            ];
            $result = Static::run_curl_request($api_url, '', 'POST', json_encode($api_data), $api_header);
            if ($result['success']) {
                $obj = json_decode($result['result']);
                if (empty($obj->error)) {
                    $success = true;
                }
            }
        }
        return $success;
    }
    private function sms_access_token()
    {
        $access_token = '';
        $config     = config('api.sms_brandname');
        $api_url    = $config['api_link'] . 'oauth2/token';
        $api_header = ["cache-control: no-cache", 'Content-Type:application/json'];
        $api_data   = [
            'grant_type'    => $config['grant_type'],
            'client_id'     => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'scope'         => $config['scope'],
            'session_id'    => session_id(),
        ];
        $result = Static::run_curl_request($api_url, '', 'POST', json_encode($api_data), $api_header);
        if ($result['success']) {
            $obj = json_decode($result['result']);
            $access_token = !empty($obj->access_token) ? $obj->access_token : '';
        }
        return $access_token;
    }
    public static function run_curl_request($url, $port='', $method='POST', $data, $header){
        $arr_out = [];
        // Khởi tạo Curl
        $curl = curl_init();
        // SET Option
        $option = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_POSTFIELDS     => $data,
            CURLOPT_HTTPHEADER     => $header
        ];
        if( $port )
        {
            $option[CURLOPT_PORT] = $port;
        }
        // Apply option and run
        curl_setopt_array($curl, $option);
        $response = curl_exec($curl);
        $error    = curl_error($curl);
        // Close Curl
        curl_close($curl);
        // Check response
        if( $error ){
            $arr_out['success'] = false;
            $arr_out['error']   = $error;
        } else {
            $arr_out['success'] = true;
            $arr_out['result']  = $response;
        }
        return $arr_out;
    }
}

