<<<<<<< HEAD
<?php

namespace app\index\controller;

vendor('taobao-sdk-PHP.TopSdk');
vendor('KeplerApi');
vendor('Page');

use think\Controller;
use think\Db;

class Coupon extends Controller
{
	private $c;

	public function __construct()
	{
		$this->c = new \TopClient;
		$this->c->appkey = '28026649';
		$this->c->secretKey = '64c92c61e11a4faf7a78d033eea4ade2';
		$this->c->format  = 'json';
	}

	public function search($q = '', $p = 1)
	{
		if (empty($q)) {
			return json(['msg' => '未查询到相关商品']);
		}

		$file = fopen("q.log", "a");
		fwrite($file, "【 $q 】" . date('Y-m-d H:i:s') . PHP_EOL);
		fclose($file);

		// 搜索商品
		$req = new \TbkDgMaterialOptionalRequest;
		$req->setQ($q);
		$req->setMaterialId('17004');
		$req->setAdzoneId('109652200299');
		$req->setNeedFreeShipment('true');
		$req->setHasCoupon('true');
		$req->setPageNo($p);
		$req->setIp($_SERVER['REMOTE_ADDR']);
		// 链接形式：1：PC，2：无线
		$req->setPlatform('2');

		// 商品详情 ['result_list']['map_data']
		// 总数 total_results
		$resp = json_decode(json_encode($this->c->execute($req)), true);
		if (empty($resp['total_results']) && empty($resp['result_list'])) {
			return json(['msg' => '未查询到相关商品']);
		}

		$page = new \Page($resp['total_results'], 20);

		foreach ($resp['result_list']['map_data'] as $key => $value) {
			if (isset($value['coupon_amount'])) {
				$resp['result_list']['map_data'][$key]['price'] = $value['zk_final_price'] - $value['coupon_amount'];
			} else {
				$resp['result_list']['map_data'][$key]['price'] = $value['zk_final_price'];
			}
		}

		return $resp ? json(['success' => '共查询到' . (empty($resp['total_results']) ? 0 : $resp['total_results']) . '件商品', 'data' => $resp['result_list']['map_data'], 'page' => $page->show('/coupon')]) : json(['msg' => '未查询到相关商品']);
	}

	// 生成淘口令
	public function createtpwd($tpwd)
	{
		$req = new \TbkTpwdCreateRequest;
		$req->setUserId("lalalala灬别恋她");
		$req->setText("领券购物，快乐生活");
		$req->setUrl($tpwd);
		$resp = json_decode(json_encode($this->c->execute($req)), true);
		$file = fopen("tpwd.log", "a");
		fwrite($file, "【 " . $resp['data']['model'] . " 】" . date('Y-m-d H:i:s') . PHP_EOL);
		fclose($file);
		return empty($resp['data']['model']) ? "系统出现错误" : $resp['data']['model'];
	}

	// 生成淘礼金口令
	// public function createtpwd($tpwd)
	// {
	// 	$req = new \TbkDgVegasTljCreateRequest;
	// 	$req->setAdzoneId("109652200299");
	// 	$req->setItemId($tpwd);
	// 	$req->setTotalNum(1);
	// 	$req->setName("淘礼金来啦");
	// 	$req->setUserTotalWinNumLimit("1");
	// 	$req->setSecuritySwitch("true");
	// 	$req->setPerFace(0.01);
	// 	$req->setSendStartTime(date('Y-m-d H:i:s'));
	// 	$req->setSendEndTime(date("Y-m-d 23:59:59"));
	// 	$resp = json_decode(json_encode($this->c->execute($req)), true);
	// 	$file = fopen("tlj.log", "a");
	// 	fwrite($file, date('Y-m-d H:i:s') . PHP_EOL . json_encode($resp, JSON_UNESCAPED_UNICODE) . PHP_EOL);
	// 	fclose($file);
	// 	if ($resp['result']['success']) {
	// 		return $this->createtpwd($resp['result']['model']['send_url']);
	// 	} else {
	// 		return isset($resp['sub_msg']) ? $resp['sub_msg'] : $resp['result']['msg_info'];
	// 	}
	// }
	
	// 查询选品库 
	public function test()
	{
		$req = new \TbkDgNewuserOrderGetRequest;
		$req->setActivityId("120013_12");
		$resp = json_decode(json_encode($this->c->execute($req)), true);
		print_r($resp);
	}

	public function KeplerApi()
	{
		$jd = new \KeplerApi;
		// $name ='jd.union.open.goods.jingfen.query';
		// $data['goodsReq'] = [
		// 	'eliteId' => 10
		// ];

		// $name = 'jd.union.open.promotion.common.get';
		// $data['promotionCodeReq']=[
		// 	'materialId'=> 'https://item.jd.com/58559777928.html',
		// 	'siteId'=> '1916100763',
		// ];

		// $name = 'jd.union.open.goods.promotiongoodsinfo.query';
		// $data['skuIds'] = '58559777928';

		$name = 'jd.union.open.promotion.byunionid.get';
		$data['promotionCodeReq'] = [
			'materialId' => 'https://item.jd.com/58559777928.html',
			'siteId' => '1916100763',
		];

		// $data['skuIds'] = [
		// 	'pageNo' => 1,
		// 	'pageSize' => 20,
		// 	'type' => 1,
		// 	'time' => date('YmdHm'),
		// ];


		print_r($data);
		// exit;

		$req = $jd->GetKelperApiData($name, $data);
		print_r(json_decode($req, true));
	}
}
=======
<?php

namespace app\index\controller;

vendor('taobao-sdk-PHP.TopSdk');
vendor('KeplerApi');
vendor('Page');

use think\Controller;
use think\Db;

class Coupon extends Controller
{
	private $c;

	public function __construct()
	{
		$this->c = new \TopClient;
		$this->c->appkey = '28026649';
		$this->c->secretKey = '64c92c61e11a4faf7a78d033eea4ade2';
		$this->c->format  = 'json';
	}

	public function search($q = '', $p = 1)
	{
		if (empty($q)) {
			return json(['msg' => '未查询到相关商品']);
		}

		$file = fopen("log/q.log", "a");
		fwrite($file, "【 $q 】" . date('Y-m-d H:i:s') . PHP_EOL);
		fclose($file);

		// 搜索商品
		$req = new \TbkDgMaterialOptionalRequest;
		$req->setQ($q);
		$req->setMaterialId('17004');
		$req->setAdzoneId('109652200299');
		$req->setNeedFreeShipment('true');
		$req->setHasCoupon('true');
		$req->setPageNo($p);
		$req->setIp($_SERVER['REMOTE_ADDR']);
		// 链接形式：1：PC，2：无线
		$req->setPlatform('2');

		// 商品详情 ['result_list']['map_data']
		// 总数 total_results
		$resp = json_decode(json_encode($this->c->execute($req)), true);
		if (empty($resp['total_results']) && empty($resp['result_list'])) {
			return json(['msg' => '未查询到相关商品']);
		}

		$page = new \Page($resp['total_results'], 20);

		foreach ($resp['result_list']['map_data'] as $key => $value) {
			if (isset($value['coupon_amount'])) {
				$resp['result_list']['map_data'][$key]['price'] = $value['zk_final_price'] - $value['coupon_amount'];
			} else {
				$resp['result_list']['map_data'][$key]['price'] = $value['zk_final_price'];
			}
		}

		return $resp ? json(['success' => '共查询到' . (empty($resp['total_results']) ? 0 : $resp['total_results']) . '件商品', 'data' => $resp['result_list']['map_data'], 'page' => $page->show('/coupon')]) : json(['msg' => '未查询到相关商品']);
	}

	// 生成淘口令
	public function createtpwd($tpwd)
	{
		$req = new \TbkTpwdCreateRequest;
		$req->setUserId("lalalala灬别恋她");
		$req->setText("领券购物，快乐生活");
		$req->setUrl($tpwd);
		$resp = json_decode(json_encode($this->c->execute($req)), true);
		$file = fopen("log/tpwd.log", "a");
		fwrite($file, "【 " . $resp['data']['model'] . " 】" . date('Y-m-d H:i:s') . PHP_EOL);
		fclose($file);
		return empty($resp['data']['model']) ? "系统出现错误" : $resp['data']['model'];
	}

	// 淘礼金使用情况
	// function test()
	// {
	// 	$req = new \TbkDgVegasTljInstanceReportRequest;
	// 	$req->setRightsId("3MTmZRsLCeO3uO%2F4WTtCiqJ7%2BkHL3AEW");
	// 	$resp = json_decode(json_encode($this->c->execute($req)), true);
	// 	print_r($resp);
	// }

	// 读取日志
	// function test()
	// {
	// 	$url = fopen('tlj.log', 'rb');
	// 	$i = 0;
	// 	while (!feof($url)) {
	// 		echo fgets($url).'<br>';
	// 		$i++;
	// 	}
	// }

	function test()
	{
		// $media = $this->http_request('https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=' . $this->getToken(), json_encode(['type' => 'image', 'offset' => 0, 'count' => 1]));
		$media = $this->http_request('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $this->getToken(), json_encode(['touser' => 'ocgU7wwhYzsQAsZLflze7u0OPnE0', 'msgtype' => 'text', 'text' => ['content' => '消息回复 ocgU7wwhYzsQAsZLflze7u0OPnE0']]));
		print_r(json_decode($media));
	}

	private function http_request($url, $data = array())
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		// 我们在POST数据哦！
		curl_setopt($ch, CURLOPT_POST, 1);
		// 把post的变量加上
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}

	private function getToken()
	{
		$data = json_decode(file_get_contents('access_token.json'), true);
		if ($data['expire_time'] < time()) {
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . config('account')['appid'] . "&secret=" . config('account')['appsecret'];
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$res = curl_exec($ch);
			if (curl_errno($ch)) {
				var_dump(curl_error($ch));
			}
			curl_close($ch);
			$arr = json_decode($res, true);
			$access_token = $arr['access_token'];
			$data = ['expire_time' => time() + 7000, 'access_token' => $access_token];
			//存入文件
			$fp = fopen("log/access_token.json", "w");
			fwrite($fp, json_encode($data));
			fclose($fp);
		} else {
			$access_token = $data['access_token'];
		}
		return $access_token;
	}
}
>>>>>>> 9794f8261b7805b72bba9952ff4d630b7c43f473
