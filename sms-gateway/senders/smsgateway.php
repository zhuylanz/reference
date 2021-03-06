<?php

class smsgateway extends AktuelSms {
    static $baseUrl = "https://smsgateway.me";

    function __construct($message,$gsmnumber){
        $this->message = $this->utilmessage($message);
		if (preg_match('/^0/', $gsmnumber)) {
			$this->gsmnumber = $this->utilgsmnumber($gsmnumber);
		} else {
			$this->gsmnumber = '+' . $gsmnumber;
		}
    }

    function createContact ($name,$number) {
        return $this->makeRequest('/api/v4/contacts','POST',['name' => $name, 'phone_numbers' => $number]);
    }

    function getContacts ($page=1) {
       return $this->makeRequest('/api/v4/contact/search','POST');
    }

    function getContact ($id) {
        return $this->makeRequest('/api/v4/contact/'.$id,'GET');
    }


    function getDevices ($page=1)
    {
        return $this->makeRequest('/api/v4/device/search','POST');
    }

    function getDevice ($id)
    {
        return $this->makeRequest('/api/v4/device/'.$id,'GET');
    }

    function getMessages($page=1)
    {
        return $this->makeRequest('/api/v4/message/search','POST');
    }

    function getSingleMessage($id)
    {
        return $this->makeRequest('/api/v4/message/'.$id,'GET');
    }

    function sendMessageToNumber($to, $message, $device, $options=[]) {
        $query = array_merge(['phone_number'=>$to, 'message'=>$message, 'device_id' => $device], $options);
        return $this->makeRequest('/api/v4/message/send','POST',$query);
    }

    function sendMessageToManyNumbers($to, $message, $device, $options=[]) {
        $query = array_merge(['phone_number'=>$to, 'message'=>$message, 'device_id' => $device], $options);
        return $this->makeRequest('/api/v4/message/send','POST', $query);
    }

    function sendMessageToContact ($to, $message, $device, $options=[]) {
        $query = array_merge(['phone_number'=>$to, 'message'=>$message, 'device_id' => $device], $options);
        return $this->makeRequest('/api/v4/message/send','POST', $query);
    }

    function sendMessageToManyContacts ($to, $message, $device, $options=[]) {
        $query = array_merge(['phone_number'=>$to, 'message'=>$message, 'device_id' => $device], $options);
        return $this->makeRequest('/api/v4/message/send','POST', $query);
    }

    function sendManyMessages ($data) {
        $query['data'] = $data;
        return $this->makeRequest('/api/v4/message/send','POST', $query);
    }

    private function makeRequest ($url, $method, $fields=[]) {
        $params = $this->getParams();

        // $fields['email'] = $params->email;
        // $fields['password'] = $params->pass;
        // $fields['device_id'] = '90154';
        $fields['device_id'] = $params->senderid;
        $token = $params->signature;
        // $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhZG1pbiIsImlhdCI6MTUyNjU2MzkzMywiZXhwIjo0MTAyNDQ0ODAwLCJ1aWQiOjM5MjE4LCJyb2xlcyI6WyJST0xFX1VTRVIiXX0.1rjWmmd0hcruos5krzf1xZ6ZmA4MIswYJccz4ZhdsQ8';
        $url = smsGateway::$baseUrl.$url;

        //$fieldsString = http_build_query($fields);
        $fieldsString = json_encode([$fields]);
        $ch = curl_init();


        if($method == 'POST')
        {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);                                                                  
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                'Content-Type: application/json',
                'Authorization: '.$token
            ));
        }
        else
        {
            $fieldsString = http_build_query($fields);
            $url .= '?'.$fieldsString;
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: '.$token
            ));
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);  // we want headers
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec ($ch);

        $return['response'] = json_decode($result,true);

        if($return['response'] == false)
            $return['response'] = $result;

        $return['status'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close ($ch);

        return $return;
    } 


    function send(){
        
        if($this->gsmnumber == "numbererror"){
            $log[] = ("Number format error.".$this->gsmnumber);
            $error[] = ("Number format error.".$this->gsmnumber);
            return null;
        }
        $params = $this->getParams();

        // check list of devices and get the first one:
        $getDevicesresult = $this->getDevices();
        $json_string = json_encode($getDevicesresult, JSON_PRETTY_PRINT);
        $deviceID = $getDevicesresult['response']['results'][0]['id'];
        if (!$deviceID) {
            $log[] = "Can not get deviceID. error : ".$json_string;
            $error[] = "Can not get deviceID. error: ".$json_string;
        }

        // the mess number and content:
        $number = $this->gsmnumber;
        $message = $this->message;
        $options = [
            'expires_at' => strtotime('+20 minutes') // Cancel the message in 1 hour if the message is not yet sent
        ];

        // call API to send message:
        $result = $this->sendMessageToNumber($number, $message, $deviceID, $options);
        $json_string = json_encode($result, JSON_PRETTY_PRINT);
        
        $log[] = "Request send message: ".$message . 'to number: '.$number;

        $return = $result;
        $log[] = "smsGateway server response returned: ".$json_string;

      
        if ($result['response'][0]) {
            $this->addLog("Call API success.");
            $log[] = "Call API success.";
            $Status = $result['response'][0];
            $send_at = date('Y-m-d h:i:s',$result['response'][0]['created_at']);

            $messid = $result['response'][0]['id'];
            $this->addLog("Message id: " . $messid . " was sent at" . $send_at . "  Status: ".$Status);
            $log[] = "Message id: " . $messid . " sent at: " . $send_at . " Status: ".$Status;

        }else{
            $log[] = "Unable to send message. error : ".$json_string;
            $error[] = "An error occurred while sending messages. error: ".$json_string;
        }
        return array(
            'log' => $log,
            'error' => $error,
            'msgid' => $messid,
        );
    }   

    function balance(){
        // check list of devices and get the first one:
        $getDevicesresult = $this->getDevices();
        $DeviceID = $getDevicesresult['response']['results'][0]['id'];
        $Devicename = $getDevicesresult['response']['results'][0]['name'];
        $Devicemake = $getDevicesresult['response']['results'][0]['attributes']['make'];
        $Devicemodel = $getDevicesresult['response']['results'][0]['attributes']['model'];
        $Devicenumber = $getDevicesresult['response']['results'][0]['attributes']['phone_number'];
        $battery = $getDevicesresult['response']['results'][0]['attributes']['battery'];
        $Devicesignal = $getDevicesresult['response']['results'][0]['attributes']['signal_percent'];
        $Devicewifi = $getDevicesresult['response']['results'][0]['attributes']['wifi'];
        $device_info = 'ID: '.$DeviceID.' | '.'Name: '.$Devicename. ' '.$Devicemake.' '.$Devicemodel.' | '.'Number: '.$Devicenumber.' | '.'Battery: '.$battery.' | '.'Devicesignal: '.$Devicesignal.' | '.'Devicewifi: '.$Devicewifi;

        if ($device_info) {
            return $device_info;
        }else {
            return 'Can not get Device\'s info';
        }
    }

    function report($msgid){
        $id = $msgid;
        $result = $this->getSingleMessage($id);

        if ($result['response']['id']) {
            $status = $result['response']['status'];
            // $report = 'Status: ' . $status . '. Error' . $result['response']['result']['error'];
            return $status;
        }else {
            return 'Unknown';
        }
    }

    function utilgsmnumber($number){
        $params = $this->getParams();
        $countrycode = $params->countrycode;
        $Cnumber = $countrycode . substr($number, 1);
        return $Cnumber;
    }
    
    function utilmessage($message){
        // $params = $this->getParams();
        // $sign = $params->sign
        // $message = $message . ' ' . $sign
        return $message;
    }
}

return array(
    'value' => 'smsgateway',
    'label' => 'SMS Gateway',
    'fields' => array(
        'email','pass','countrycode'
    )
);

// $test = new smsgateway('abc', '1234');
//print_r($test->balance());
//print_r($test->sendMessageToNumber('01658040978', 'testing php', 'ab'));
// print_r($test->getContact('12583148'));