<?php
namespace wechatpay;
// 统一下单
use wechatpay\WxPayApi;  
// 支付配置
use wechatpay\WxPayConfig;
// 统一下单输入对象
use wechatpay\base\WxPayUnifiedOrder;
// JSAPI支付实现类
use wechatpay\JsApiPay;  
// 支付回调类
use wechatpay\Notify;  
// 刷卡支付实现类
use wechatpay\NativePay;  

class WxPay
{
	// js 支付
	public function jspay(array $order)
	{
		$tools = new JsApiPay();
		//①、获取用户openid
		$openId = !empty($order["openid"]) ? $order["openid"] : $tools->GetOpenid();
		// 填写自己的回调地址
		$notify_url = !empty($order["notify_url"]) ? $order["notify_url"] : "";
		// 商品描述
		$body = !empty($order["body"]) ? $order["body"] : '';
		// 附加数据
		$attach = !empty($order["attach"]) ? $order["attach"] : '';
		// 订单号
		$out_trade_no = !empty($order["out_trade_no"]) ? $order["out_trade_no"] : '';
		// 订单金额,单位分
		$total_fee = !empty($order["total_fee"]) ? floatval($order["total_fee"]) : '';
		// 交易起始时间
		$time_start = !empty($order["time_start"]) ? $order["time_start"] : date("YmdHis");
		// 交易结束时间
		$time_expire = !empty($order["time_expire"]) ? $order["time_expire"] : date("YmdHis", time() + 600);
		// 订单优惠标记
		$goods_tag = !empty($order["goods_tag"]) ? $order["goods_tag"] : '';
		//②、统一下单
		$input = new WxPayUnifiedOrder();
		$input->SetBody($body);
		$input->SetAttach($attach);
		$input->SetOut_trade_no($out_trade_no);
		$input->SetTotal_fee($total_fee);
		$input->SetTime_start($time_start);
		$input->SetTime_expire($time_expire);
		$input->SetGoods_tag($goods_tag);
		// 支付成功回调地址此处不填写时使用 WxPayConfig::NOTIFY_URL 官方demo 此配置未填,但是 WxPayApi 有使用
		$input->SetNotify_url($notify_url);
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($openId);
		$order = WxPayApi::unifiedOrder($input);
		// 统一下单失败
		if($order['return_code'] == 'FAIL')
		{
			echo $order['return_msg'];
			exit;
		}
		// 生成 Js 支付参数
		return $tools->GetJsApiParameters($order);
	}

	// 支付回调
	public function notify($callback = false)
	{
		$api = new Notify;
		$api->startHandle($callback);
	}

    // 扫码支付使用 模式二
	public function native_pay(array $order)
	{
		// 填写自己的回调地址
		$notify_url = !empty($order["notify_url"]) ? $order["notify_url"] : "";
		// 商品描述
		$body = !empty($order["body"]) ? $order["body"] : '';
		// 附加数据
		$attach = !empty($order["attach"]) ? $order["attach"] : '';
		// 订单号
		$out_trade_no = !empty($order["out_trade_no"]) ? $order["out_trade_no"] : '';
		// 订单金额,单位分
		$total_fee = !empty($order["total_fee"]) ? floatval($order["total_fee"]) : '';
		// 交易起始时间
		$time_start = !empty($order["time_start"]) ? $order["time_start"] : date("YmdHis");
		// 交易结束时间
		$time_expire = !empty($order["time_expire"]) ? $order["time_expire"] : date("YmdHis", time() + 600);
		// 订单优惠标记
		$goods_tag = !empty($order["goods_tag"]) ? $order["goods_tag"] : '';
		// 产品ID
		$product_id = !empty($order["product_id"]) ? $order["product_id"] : '';
		//②、统一下单
		$input = new WxPayUnifiedOrder();
		$input->SetBody($body);
		$input->SetAttach($attach);
		$input->SetOut_trade_no($out_trade_no);
		$input->SetTotal_fee($total_fee);
		$input->SetTime_start($time_start);
		$input->SetTime_expire($time_expire);
		$input->SetGoods_tag($goods_tag);
		// 支付成功回调地址此处不填写时使用 WxPayConfig::NOTIFY_URL 官方demo 此配置未填,但是 WxPayApi 有使用
		$input->SetNotify_url($notify_url);
		$input->SetTrade_type("NATIVE");
		$input->SetProduct_id($product_id);
		$notify = new NativePay;
		$result = $notify->GetPayUrl($input);
		return $url2 = $result["code_url"];
	}
}