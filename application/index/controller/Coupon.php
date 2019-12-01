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
	public function test1()
	{
		$req = new \TbkUatmFavoritesItemGetRequest;
		$req->setPlatform("2");
		$req->setPageSize("1");
		$req->setAdzoneId("109652200299");
		$req->setUnid("3456");
		$req->setFavoritesId("19949525");
		// $req->setPageNo("2");
		$req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,seller_id,volume,nick,shop_title,zk_final_price_wap,event_start_time,event_end_time,tk_rate,status,type");

		$resp = json_decode(json_encode($this->c->execute($req)), true);
		print_r($resp);
		// return empty($resp['data']['model']) ? "系统出现错误" : $resp['data']['model'];
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
