<?php
// error_reporting(E_ERROR);
use wechatpay\handler\PhpQrcode;
$url = urldecode($_GET["data"]) || 1;
PhpQrcode::png($url);
