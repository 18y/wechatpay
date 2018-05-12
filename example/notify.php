<?php
require_once __DIR__ . '/base.php';  
use wechatpay\base\WxPayOrderQuery;
use wechatpay\WxPayApi;
use wechatpay\handler\WxPayNotify;  
use wechatpay\handler\Log;
use wechatpay\face\CLogFileHandler;

/**
 * 重写 WxPayNotify 的处理函数 NotifyProcess
 * 微信各种验证成功之后会带着参数访问  NotifyProcess 方法，具体看 wechatpay\handler\WxPayNotify
 */
class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}
		return true;
	}
}
$log_file = date('Y-m-d').'.log';
$log_path = "./log/";
$logHandler= new CLogFileHandler($log_file,$log_path);
// 初始化日志
$log = Log::Init($logHandler, 15);
Log::DEBUG("begin notify");
// 支付成功时，微信会主动请求
$notify = new PayNotifyCallBack();
// 参数为 false 表示不需要 签名输出 
$notify->Handle(false);
