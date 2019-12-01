<?php

namespace app\index\controller;

vendor('taobao-sdk-PHP.TopSdk');

use think\Validate;
use think\Db;

class Tbk extends Common
{
	private $c;

	public function __construct()
	{
		parent::__construct();
		$this->c = new \TopClient;
		$this->c->appkey = '28026649';
		$this->c->secretKey = '64c92c61e11a4faf7a78d033eea4ade2';
		$this->c->format  = 'json';
	}

	public function tlj()
	{
		return $this->fetch();
	}

	public function createtlj()
	{
		$validate = new Validate(['skuid' => 'require|number', 'money' => 'require|number', 'num' => 'require|number']);
		if (!$validate->check($_POST)) {
			return json(['error' => $validate->getError()]);
		}

		$req = new \TbkDgVegasTljCreateRequest;
		$req->setAdzoneId("109652200299");
		$req->setItemId($_POST['skuid']);
		$req->setTotalNum($_POST['num']);
		$req->setName("淘礼金来啦");
		$req->setUserTotalWinNumLimit("1");
		$req->setSecuritySwitch("true");
		$req->setPerFace($_POST['money']);
		$req->setSendStartTime(date('Y-m-d H:i:s'));
		$req->setSendEndTime(date("Y-m-d 23:59:59"));
		$resp = json_decode(json_encode($this->c->execute($req)), true);
		$file = fopen("tlj.log", "a");
		fwrite($file, date('Y-m-d H:i:s') . PHP_EOL . json_encode($resp, JSON_UNESCAPED_UNICODE) . PHP_EOL);
		fclose($file);
		if ($resp['result']['success']) {
			return $this->createtpwd($resp['result']['model']['send_url']) . "\n" . $resp['result']['model']['send_url'];
		} else {
			return isset($resp['error_response']['sub_msg']) ? $resp['error_response']['sub_msg'] : $resp['result']['msg_info'];
		}
	}

	public function tpwd()
	{
		return $this->fetch();
	}

	public function createtpwd($url)
	{
		$req = new \TbkTpwdCreateRequest;
		$req->setUserId("lalalala灬别恋她");
		$req->setText("领券购物，快乐生活");
		$req->setUrl($url);
		$resp = json_decode(json_encode($this->c->execute($req)), true);
		$file = fopen("tpwd.log", "a");
		fwrite($file, date('Y-m-d H:i:s') . PHP_EOL . json_encode($resp, JSON_UNESCAPED_UNICODE) . PHP_EOL);
		fclose($file);
		return isset($resp['sub_msg']) ? $resp['sub_msg'] : $resp['data']['model'];
	}

	public function tpwdConvert()
	{
		if ($_POST) {
			$data = json_decode(file_get_contents("http://www.taofake.com/index/tools/gettkljm.html?tkl=" . $_POST['tpwd']), true);
			if (empty($data) || $data['msg'] != 'ok') {
				return '接口调用异常';
			}

			$content = '';
			foreach ($data['data'] as $key => $value) {
				$content .= "$key ：$value\n";
			}
			return $content;
		} else {
			return $this->fetch();
		}
	}
}
