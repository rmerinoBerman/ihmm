<?php
    if($_POST){
        $url = 'http://www.google.com/recaptcha/api/verify';

        $data = array(
            'privatekey'=>  "6LfGP-8SAAAAAGhj5JpbubB6Ia6We6b70fXYlfPO",
            'remoteip'  =>  $_SERVER['REMOTE_ADDR'],
            'challenge' =>  $_POST['challenge'],
            'response'  =>  $_POST['response'],
        );
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),           
            ),
        );
        $mystr = file_get_contents($url, false, stream_context_create($options));
        print_r($mystr);
    }
?>