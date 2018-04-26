<?php 
namespace wechatpay\face\log;
/**
 * 日志接口
 */
interface ILogHandler
{
	public function write($msg);
	
}
