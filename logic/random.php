<?php

class random
{
    public function get_integers($num=1, $min=1, $max=2){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.random.org/json-rpc/1/invoke');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{
            "jsonrpc": "2.0",
            "method": "generateIntegers",
            "params": {
                "apiKey": "5a566a57-06c9-42c1-8628-c9dc86cbd308",
                "n": '.$num.',
                "min": '.$min.',
                "max": '.$max.',
                "replacement": true
            },
            "id": 3
        }');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $out = json_decode(curl_exec($ch),true);
        curl_close($ch);
        return $out['result']['random']['data'][0];
    }
}