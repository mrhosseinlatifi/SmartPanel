<?php
function domin() {
    $requestUri = $_SERVER['REQUEST_URI'];
    $httpHost = $_SERVER['HTTP_HOST'];

    $url = $httpHost . $requestUri;

    $url = preg_replace('/^https?:\/\//', '', $url);

    $url = preg_replace('/\?.*$/', '', $url);

    $url = dirname(dirname($url));

    return $url;
}

//----------ادرس دامنه و فولدر-----------//
// درصورت بروز مشکل تغییر دهید
@$domin = domin();
define('domin', $domin);
//----------------------------------------//
