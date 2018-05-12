<?php 
require_once __DIR__ . '/base.php';  
// 统一下单
use wechatpay\WxPayApi;  
// 支付配置
use wechatpay\WxPayConfig;
// 统一下单输入对象
use wechatpay\base\WxPayUnifiedOrder;

// JSAPI支付实现类
use wechatpay\JsApiPay;  

//①、获取用户openid
$tools = new JsApiPay();
/* 
	获取用户 openId 默认这一步骤重静默授权一次，并获取到临时的 access_token, 
	因为整个支付都是不需要 access_token  的，但是获取共享收货地址时需要
	如不需要共享地址可以直接赋值
	如:
	$openId = !empty(SESSION['openid']) ? SESSION['openid'] : $tools->GetOpenid();
*
*/
$openId = $tools->GetOpenid();
$notify_url = "http://192.168.0.99/example/notify.php";
//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody("test");
$input->SetAttach("test");
$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
$input->SetTotal_fee("1");
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag("test");
// 支付成功回调地址此处不填写时使用 WxPayConfig::NOTIFY_URL 官方demo 此配置未填,但是 WxPayApi 有使用
$input->SetNotify_url($notify_url);
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);
$order = WxPayApi::unifiedOrder($input);
// 统一下单失败
if($order['return_code'] == 'FAIL')
{
	echo $order['return_msg'];
	exit();
}
// 生成 Js 支付参数
$jsApiParameters = $tools->GetJsApiParameters($order);
//editAddress 此接口已被微信废弃，请勿使用
//获取共享收货地址js函数参数
// $editAddress = $tools->GetEditAddressParameters();

//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
/**
 * 注意：
 * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
 * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
 * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
 */
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/> 
    <title>微信支付样例-支付</title>
    <script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			<?php echo $jsApiParameters; ?>,
			function(res){
				WeixinJSBridge.log(res.err_msg);
				alert(res.err_code+res.err_desc+res.err_msg);
                // 支付调用成功
                // if(res.err_msg == "get_brand_wcpay_request:ok" ) {
                //     window.location.href = "";
                // } else {
                //     alert('交易取消');
                //     window.location.href = "";
                // }
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
	</script>
</head>
<body>
    <br/>
    <font color="#9ACD32"><b>该笔订单支付金额为<span style="color:#f00;font-size:50px">1分</span>钱</b></font><br/><br/>
	<div align="center">
		<button style="width:210px; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" >立即支付</button>
	</div>
</body>
</html>