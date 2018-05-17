<?php

class routesms extends AktuelSms {
    function __construct($message,$gsmnumber){
        $this->message = $this->utilmessage($message);
        $this->gsmnumber = $this->utilgsmnumber($gsmnumber);
    }

    function send(){
        if($this->gsmnumber == "numbererror"){
            $log[] = ("Number format error.".$this->gsmnumber);
            $error[] = ("Number format error.".$this->gsmnumber);
            return null;
        }
        $params = $this->getParams();

        $url = "https://api.sendsms.co.tz/api/sendsms/plain?user=$params->user&password=$params->pass&sender=$params->senderid&SMSText=".urlencode($this->message)."&GSM=$this->gsmnumber";

        
        //"http://121.241.242.114:8080/bulksms/bulksms?username=$params->user&password=$params->pass&type=1&dlr=0&destination=$this->gsmnumber&source=$params->senderid&message=".urlencode($this->message).""

        $log[] = "Request url: ".$url;
        $result = file_get_contents($url);

        $return = $result;
        $log[] = "Sunucudan dönen cevap: ".$result;

        $result = explode("|", $result);
        if ($result[0] > "1") {
            $this->addLog("Message sent.");
            $log[] = "Message sent";
            $msgid = $result[2];
            $log[] = "Message id: ".$msgid;
        }elseif($result[0] == "-1"){
            $log[] = "SEND_ERROR - Currently not in use ";
            $error[] = "SEND_ERROR - Currently not in use ";
        }elseif($result[0] == "-2"){
            $log[] = "NOT_ENOUGHCREDITS";
            $error[] = "NOT_ENOUGHCREDITS";
        }elseif($result[0] == "-3"){
            $log[] = "NETWORK_NOTCOVERED";
            $error[] = "NETWORK_NOTCOVERED";
        }elseif($result[0] == "-4"){
            $log[] = "SOCKET_EXCEPTION - Currently not in use ";
            $error[] = "SOCKET_EXCEPTION - Currently not in use ";
        }elseif($result[0] == "-5"){
            $log[] = "INVALID_USER_OR_PASS ";
            $error[] = "INVALID_USER_OR_PASS ";
        }elseif($result[0] == "-6"){
            $log[] = "MISSING_DESTINATION_ADDRESS ";
            $error[] = "MISSING_DESTINATION_ADDRESS ";
        }elseif($result[0] == "-7"){
            $log[] = "MISSING_SMSTEXT ";
            $error[] = "MISSING_SMSTEXT ";
        }elseif($result[0] == "-8"){
            $log[] = "MISSING_SENDERNAME ";
            $error[] = "MISSING_SENDERNAME ";
        }elseif($result[0] == "-9"){
            $log[] = "DESTADDR_INVALIDFORMAT ";
            $error[] = "DESTADDR_INVALIDFORMAT ";
        }elseif($result[0] == "-10"){
            $log[] = "MISSING_USERNAME ";
            $error[] = "MISSING_USERNAME ";
        }elseif($result[0] == "-11"){
            $log[] = "MISSING_PASS ";
            $error[] = "MISSING_PASS ";
        }elseif($result[0] == "-12"){
            $log[] = "MISSING_USERNAME ";
            $error[] = "MISSING_USERNAME ";
        }elseif($result[0] == "-13"){
            $log[] = "INVALID_DESTINATION_ADDRESS ";
            $error[] = "INVALID_DESTINATION_ADDRESS ";
        }else{
            $log[] = "Message Not Sent Other Error. Error: $return";
            //echo $result[0];
            //$error[] = echo $result[0];

            $error[] = "Message Not sent Other Error, Show this to the admin-->. Error:" .$result[0]. "";
            //echo $result[0];
        }

        return array(
            'log' => $log,
            'error' => $error,
            'msgid' => $msgid,
        );
    }

    function balance(){
        return null;
    }

    function report($msgid){
        return null;
    }

    //You can spesifically convert your gsm number. See netgsm for example
    function utilgsmnumber($number){
        return $number;
    }
    //You can spesifically convert your message
    function utilmessage($message){
        return $message;
    }
}

return array(
    'value' => 'routesms',
    'label' => 'Route Sms',
    'fields' => array(
        'user','pass'
    )
);