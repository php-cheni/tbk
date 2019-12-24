<?php

namespace app\index\controller;

vendor('taobao-sdk-PHP.TopSdk');

use think\Controller;

class Wechat extends Controller
{
	private $c;

	public function __construct()
	{
		$this->c = new \TopClient;
		$this->c->appkey = '28026649';
		$this->c->secretKey = '64c92c61e11a4faf7a78d033eea4ade2';
		$this->c->format  = 'json';
	}

	public function index()
	{
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"]; //接收微信发来的XML数据
		$file = fopen("log/postStr.log", "a");
		fwrite($file, "【 $postStr 】" . PHP_EOL);
		fclose($file);
		if (!empty($postStr)) {
			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			$fromUsername = $postObj->FromUserName; //请求消息的用户
			$toUsername = $postObj->ToUserName; //"我"的公众号id
			$keyword = trim($postObj->Content); //消息内容
			$time = time(); //时间戳
			$msgtype = 'text'; //消息类型：文本
			$textTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            </xml>";

			if (empty($keyword)) {
				$contentStr = "欢迎关注指尖领券购物\n在淘宝中复制链接分享给我，我会为你查券\n也可以通过以下链接搜索优惠商品：\nhttps://www.php54.com/coupon.html";
			} else {
				preg_match('/[a-zA-Z0-9]{11}/', $keyword, $match);
				$contentStr = $match ? $this->trans($match[0]) : json_decode($this->http_request("http://openapi.tuling123.com/openapi/api/v2", json_encode(['perception' => ['inputText' => ['text' => $keyword]], 'userInfo' => ['apiKey' => '5207baf8666d4b609a64b360a56b1593', 'userId' => substr($fromUsername, 20, 8)]], JSON_UNESCAPED_UNICODE)))->results[0]->values->text;
			}
		}

		return sprintf($textTpl, $fromUsername, $toUsername, $time, $msgtype, $contentStr);
	}

	private function trans($tpwd)
	{
		$data = json_decode(file_get_contents("http://www.taofake.com/index/tools/gettkljm.html?tkl=$tpwd"), true);
		if (empty($data) || $data['msg'] != 'ok') {
			return '系统异常（反馈WX：654080169）';
		}
		preg_match('/[1-9]([0-9]{5,11})/', $data['data']['url'], $match);
		$search = $this->search($match[0]);
		if ($search == false) {
			$file = fopen("log/error.log", "a");
			fwrite($file, $search . PHP_EOL);
			fclose($file);
			return '亲不好意思，该商品优惠领光啦！（反馈WX：654080169）';
		}
		$good = $search[0];
		$notice = "未找到,已经为您推荐相似商品~（反馈WX：654080169）\n";
		foreach ($search as $key => $value) {
			if ($value->item_id == $match[0]) {
				$good = $value;
				$notice = '';
				break;
			}
		}

		$tpwd = isset($good->coupon_share_url) ? $this->createtpwd('https:' . $good->coupon_share_url) : $this->createtpwd('https:' . $good->url);
		$coupon = isset($good->coupon_amount) ? "  优惠券：￥" . $good->coupon_amount : "";
		$award = isset($good->coupon_amount) ? floor(($good->zk_final_price - $good->coupon_amount) * $good->commission_rate / 160) / 100 : floor($good->zk_final_price * $good->commission_rate / 160) / 100;
		return $notice . "【" . $good->short_title . "】售价：￥" . $good->zk_final_price . $coupon . "  下单红包：￥" . $award  . " 复制到淘寶打开：" . $tpwd;
	}


	private function createtpwd($url)
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
