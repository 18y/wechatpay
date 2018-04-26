<?php
namespace wechatpay\face;
use wechatpay\face\log\ILogHandler;

class CLogFileHandler implements ILogHandler
{
	private $handle = null;
	
	public function __construct($file = '',$path = './')
	{
		$this->checkPath($path);
		$this->handle = fopen($path.$file,'a');
	}
	
	public function write($msg)
	{
		fwrite($this->handle, $msg, 4096);
	}
	
	public function checkPath($path)
	{
		if(is_dir( $path ))
		{
			return true;
		}
		if(is_dir(dirname($path))){
		    return mkdir($path);
		}
		$this->checkPath(dirname($path));
		return mkdir($path);
	}

	public function __destruct()
	{
		fclose($this->handle);
	}
}