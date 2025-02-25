<?php
function sendsms($username,$password,$pattern_code,$from,$tonumber,$value='code'){
    $to = array($tonumber);
    $code = rand(11111,99999);
    $input_data = array($value => $code);
    $url = "https://ippanel.com/patterns/pattern?username=" . $username . "&password=" . urlencode($password) . "&from=$from&to=" . json_encode($to) . "&input_data=" . urlencode(json_encode($input_data)) . "&pattern_code=$pattern_code";
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, $input_data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT,30);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if($err){
        error_log('SMS '.$err);
        return ['result'=>'false'];
    }else{
        if(is_numeric($response)){
    return ['result'=>'OK','code'=>$code];
        }else{
    return ['result'=>'false','error'=>$response];        
        }
    }
}
