<?php

namespace app\index\controller;

vendor('Page');

use think\Controller;
use think\Db;

class Article extends Controller
{
	public function article($p = 1)
	{
		$article = Db::name('article')->order('id desc')->page($p, 10)->select();
		$count = Db::name('article')->count();

		$page = new \Page($count, 10);

		foreach ($article as $key => $value) {
			$article[$key]['code'] = lockcode($value['id']);
		}

		return  json(['data' => $article, 'page' => $page->show('/list')]);
	}
}
