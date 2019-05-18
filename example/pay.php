<?php 
// 设置字符集，设置时区
require_once __DIR__ . '/base.php';

use wechatpay\WxPay;  

$api = new WxPay;

// js 支付
function jspay()
{
	global $api;
	$order = array();
	// 填写自己的回调地址
	$notify_url = "http://paysdk.weixin.qq.com/example/notify.php";
	$order["notify_url"] = $notify_url;
	// 商品名
	$order["body"] = 'test';
	// 订单号
	$order["out_trade_no"] = date("YmdHis");
	// 订单金额，单位：分
	$order["total_fee"] = 1;
	// 返回 js 支付参数
	$jsApiParameters =  $api->jspay($order);
    // 支付成功
    $success_url = "/success_url.php";
    // 支付失败跳转
    $error_url = "/error_url.php";
    echo <<<EOT
            <html>
            <head>
                <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
                <meta name="viewport" content="width=device-width, initial-scale=1"/> 
                <title>微信支付</title>
            </head>
            <body>
            </body>
            </html>
            <script>
            //调用微信JS api 支付
            function jsApiCall()
            {
                WeixinJSBridge.invoke(
                    'getBrandWCPayRequest',$jsApiParameters,
                    function(res){
                        WeixinJSBridge.log(res.err_msg);
                        if(res.err_msg == "get_brand_wcpay_request:ok" ) {
                            window.location.href = "$success_url";
                        } else {
                            alert('交易取消'+res.err_msg);
                            window.location.href = "$error_url";
                        }
                    }
                );
            }
             
            function callpay()
            {
                if (typeof WeixinJSBridge == "undefined"){
                    if( document.addEventListener ){
                        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                    }else if (document.attachEvent){
                        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
                        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                    }
                }else{
                    jsApiCall();
                }
            }
            callpay();
            </script>
EOT;
die;

}

// 扫码支付使用 模式二, 生成支付url
function native_pay()
{
	global $api;
	$order = array();
	// 填写自己的回调地址
	$notify_url = "http://paysdk.weixin.qq.com/example/notify.php";
	$order["notify_url"] = $notify_url;
	// 商品名
	$order["body"] = 'test';
	// 订单号
	$order["out_trade_no"] = date("YmdHis");
	// 订单金额，单位：分
	$order["total_fee"] = 1;
	// 产品ID
	$order["product_id"] = '123456789';
	// 返回支付url
	$url2 = urlencode($api->native_pay($order));
	// 将支付url生成二维码。
    echo <<<EOT
		<img alt="模式二扫码支付" src="http://qr.liantu.com/api.php?text=$url2" style="width:150px;height:150px;"/>
EOT;
}

// 支付回调
function notify()
{
	global $api;
	$api->notify(function($order){
		// var_dump($data);
		// 订单确认成功，订单已确认都返回 true, 返回 false 时微信会将此通知重发
		return true;
	});
}
// jspay();
// native_pay();
// notify();