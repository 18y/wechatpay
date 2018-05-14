# 项目介绍

这是一个根据微信支付官方demo (wxpayapi_php_v3) 所制作的 namespace 版

如果没看过支付文档，建议先看看[https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=7_1](https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=7_1 "支付文档")

官方的支付 sdk 已经很久未更新了，获取共享地址 editAddress 也早已废弃，

但是SDk 里面还有，因此踩了不少坑.. 共享地址<a href="https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=7_8&index=7">最新使用方式 JS SDK</a>

此 SDK 里面将官方的 SDK 调整为命名空间, 方便学习使用

使用方式

> 使用 Composer 安装

    composer require 18y/wechatpay

composer 安装之后将 /vendor/18y/wechatpay/example 目录复制到与
/vendor 同一目录下即可运行

> example 目录说明

`native.php` 刷卡支付(生成支付二维码)，不能使用localhost，使用ip打开，可以看到效果

`jsapi.php` 公众号支付(js唤起支付)，需要openid, 需要使用自己的正确配置

`notify.php` 支付成功回调地址，标志该笔订单支付成功。

配置文件所在位置

    /vendor/18y/wechatpay/src/WxPayConfig.php

>其他注意

微信支付目录填写规则

    如支付页面为 http://127.0.0.1/wechat/pay.html
	则支付目录为 http://127.0.0.1/wechat/
    最后一个斜杠不能省略
	
	使用tp框架时如 http://127.0.0.1/index/wechat/pay.html
	则支付目录为 http://127.0.0.1/index/wechat/
	最后一个斜杠不能省略
	





