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
		$validate = new Validate(['skuid' => 'require', 'money' => 'require|number', 'num' => 'require|number']);
		if (!$validate->check($_POST)) {
			return json(['error' => $validate->getError()]);
		}

		$req = new \TbkDgVegasTljCreateRequest;
		$req->setAdzoneId("109652200299");
		$req->setItemId($this->getGoodId($_POST['skuid']));
		$req->setTotalNum($_POST['num']);
		$req->setName("淘礼金来啦");
		$req->setUserTotalWinNumLimit("1");
		$req->setSecuritySwitch("true");
		$req->setPerFace($_POST['money']);
		$req->setSendStartTime(date('Y-m-d H:i:s'));
		$req->setSendEndTime(date("Y-m-d 23:59:59"));
		$resp = json_decode(json_encode($this->c->execute($req)), true);
		$file = fopen("log/tlj.log", "a");
		fwrite($file, date('Y-m-d H:i:s') . PHP_EOL . json_encode($resp, JSON_UNESCAPED_UNICODE) . PHP_EOL);
		fclose($file);
		if ($resp['result']['success']) {
			return $this->createtpwd($resp['result']['model']['send_url']) . "\n" . $resp['result']['model']['send_url'];
		} else {
			return isset($resp['error_response']['sub_msg']) ? $resp['error_response']['sub_msg'] : $resp['result']['msg_info'];
		}
	}

	public function tpwd($url = "")
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
		$file = fopen("log/tpwd.log", "a");
		fwrite($file, date('Y-m-d H:i:s') . PHP_EOL . json_encode($resp, JSON_UNESCAPED_UNICODE) . PHP_EOL);
		fclose($file);
		return isset($resp['sub_msg']) ? $resp['sub_msg'] : $resp['data']['model'];
	}

	public function tpwdConvert()
	{
		if ($_POST) {
			preg_match('/[a-zA-Z0-9]{11}/', $_POST['tpwd'], $match);
			$data = json_decode(file_get_contents("http://www.taofake.com/index/tools/gettkljm.html?tkl=" . $match[0]), true);
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

	private function getGoodId($data)
	{
		preg_match('/[a-zA-Z0-9]{11}/', $data, $match);
		$data = json_decode(file_get_contents("http://www.taofake.com/index/tools/gettkljm.html?tkl=" . $match[0]), true);
		if (empty($data) || $data['msg'] != 'ok') {
			return false;
		}
		preg_match('/[1-9]([0-9]{5,11})/', $data['data']['url'], $match);
		return empty($match) ? false : $match[0];
	}

	public function trans()
	{
		if ($_POST) {
			preg_match('/[a-zA-Z0-9]{11}/', $_POST['tpwd'], $match);
			$data = json_decode(file_get_contents("http://www.taofake.com/index/tools/gettkljm.html?tkl=" . $match[0]), true);
			if (empty($data) || $data['msg'] != 'ok') {
				return '接口调用异常';
			}

			preg_match('/id=[1-9]([0-9]{8,12})/', $data['data']['url'], $match);
			$goodid = substr($match[0], 3);
			$search = $this->search($goodid);

			if ($search == false) {
				return '亲不好意思，该商品优惠领光啦！';
			}

			$good = $search[0];
			$notice = "未找到,已经为您推荐相似商品~（反馈WX：654080169）\n";
			foreach ($search as $key => $value) {
				if ($value->item_id == $goodid) {
					$good = $value;
					$notice = '';
					break;
				}
			}

			$tpwd = isset($good->coupon_share_url) ? $this->createtpwd('https:' . $good->coupon_share_url) : $this->createtpwd('https:' . $good->url);
			$coupon = isset($good->coupon_amount) ? "  优惠券：￥" . $good->coupon_amount : "";
			$award = isset($good->coupon_amount) ? floor(($good->zk_final_price - $good->coupon_amount) * $good->commission_rate / 160) / 100 : floor($good->zk_final_price * $good->commission_rate / 160) / 100;
			return $notice . "【" . $good->short_title . "】售价：￥" . $good->zk_final_price . $coupon . "  红包：￥" . $award  . " \n复制到淘寶打开：" . $tpwd;
		} else {
			return $this->fetch();
		}
	}

	private function search($goodid)
	{
		// 搜索商品
		$req = new \TbkDgMaterialOptionalRequest;
		$q = $this->getItemInfo($goodid);
		if ($q == false) {
			return false;
		}
		$req->setQ($q);
		$req->setAdzoneId('109652200299');
		$req->setNeedFreeShipment('true');
		// $req->setHasCoupon('true');
		// $req->setMaterialId("17004");
		$req->setPageNo('1');
		$req->setPageSize("100");
		$req->setIp($_SERVER['REMOTE_ADDR']);
		// 链接形式：1：PC，2：无线
		$req->setPlatform('2');
		$resp = $this->c->execute($req);
		return isset($resp->result_list->map_data) ? $resp->result_list->map_data : false;
	}

	private function getItemInfo($goodid)
	{
		$req = new \TbkItemInfoGetRequest;
		$req->setNumIids($goodid);
		$resp = $this->c->execute($req);
		return isset($resp->results->n_tbk_item[0]->title) ? $resp->results->n_tbk_item[0]->title : false;
	}

	// // 查询选品库 
	// public function xuan()
	// {
	// 	$req = new \TbkUatmFavoritesItemGetRequest;
	// 	$req->setPlatform("2");
	// 	$req->setPageSize("1");
	// 	$req->setAdzoneId("109652200299");
	// 	$req->setUnid("3456");
	// 	$req->setFavoritesId("19949525");
	// 	// $req->setPageNo("2");
	// 	$req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,seller_id,volume,nick,shop_title,zk_final_price_wap,event_start_time,event_end_time,tk_rate,status,type");

	// 	$resp = json_decode(json_encode($this->c->execute($req)), true);
	// 	print_r($resp);
	// 	return empty($resp['data']['model']) ? "系统出现错误" : $resp['data']['model'];
	// }

	// 京粉api
	// public function KeplerApi()
	// {
	// 	$jd = new \KeplerApi;

	// 	$name = 'jd.union.open.promotion.byunionid.get';
	// 	$data['promotionCodeReq'] = [
	// 		'materialId' => 'https://item.jd.com/58559777928.html',
	// 		'siteId' => '1916100763',
	// 	];

	// 	print_r($data);
	// 	exit;

	// 	$req = $jd->GetKelperApiData($name, $data);
	// 	print_r(json_decode($req, true));
	// }
}
