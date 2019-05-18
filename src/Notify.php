<?php
namespace wechatpay;

use wechatpay\base\WxPayOrderQuery;
use wechatpay\WxPayApi;
use wechatpay\handler\WxPayNotify;  

/**
 * 重写 WxPayNotify 的处理函数 NotifyProcess
 * 微信各种验证成功之后会带着参数访问  NotifyProcess 方法，具体看 wechatpay\handler\WxPayNotify
 */
class Notify extends WxPayNotify
{
	private $handler_callback;

	/**
	 * 回调入口
	 * @param       boolean                  $callback 确认订单成功返回true，确认失败返回false
	 * @return      [type]                             
	 */
	public function startHandle($callback = false)
	{
		$this->handler_callback = $callback;
		$this->Handle(false);
	}

	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
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
		// 回调确认订单
		if($this->handler_callback !== false)
		{
            $res = call_user_func($this->handler_callback, $data);
            if(false === $res)
            {
				$msg = "订单确认失败";
            	return false;
            }
		}
		return true;
	}
}
