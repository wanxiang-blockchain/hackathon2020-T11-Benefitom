<?php
return [

	// 安全检验码，以数字和字母组成的32位字符。
	'key' => 'hcoqe22ed4wrydj7uh9ogvx8z2sgew5u',

	// 签名方式
	'sign_type' => 'RSA',

	// 商户私钥。
	'private_key_path' => __DIR__ . '/../resources/key/rsa_private_key.pem',

	// 阿里公钥。
	'public_key_path' => __DIR__ . '/../resources/ali_rsa_public_key.pem',

	// 服务器异步通知页面路径。
	'notify_url' => env('APP_URL') . '/pay/notify',

	// 页面跳转同步通知页面路径。
	'return_url' => env('APP_URL') . '/pay/back'
];
