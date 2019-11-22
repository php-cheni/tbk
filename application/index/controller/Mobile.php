<?php
namespace app\index\controller;
use think\Controller; 
use think\Db;

class Mobile extends Common{
    public function index(){
        $page=['index'=>'首页','about'=>'关于幽美','contact'=>'联系我们','news'=>'新闻中心','leader'=>'重要成员','follow'=>'关注我们'];
        foreach ($page as $key => $value) {
            $msg=file_exists("m/$key.html")?'静态页面已存在':'未生成静态文件';
            $page[$key]=['name'=>$value,'page'=>$msg,'html'=>"/m/$key.html"];
        }
        $this->assign('page',$page);
        return $this->fetch();
    }

    public function buildindex(){
        $config=Db::name('config')->column('variable,value');
        $this->assign(['config'=>$config]);
        $result=$this->buildHtml('m/index','./');
        $result=$result?['success'=>'成功生成静态页面！']:['msg'=>'额...操作失败了！'];
        return json($result);
    }

    public function buildabout(){
        $result=$this->buildHtml('m/about','./');
        $result=$result?['success'=>'成功生成静态页面！']:['msg'=>'额...操作失败了！'];
        return json($result);
    }

    public function buildcontact(){
        $result=$this->buildHtml('m/contact','./');
        $result=$result?['success'=>'成功生成静态页面！']:['msg'=>'额...操作失败了！'];
        return json($result);
    }

    public function buildnews(){
        $result=$this->buildHtml('m/news','./');
        $result=$result?['success'=>'成功生成静态页面！']:['msg'=>'额...操作失败了！'];
        return json($result);
    }

    public function buildleader(){
        $result=$this->buildHtml('m/leader','./');
        $result=$result?['success'=>'成功生成静态页面！']:['msg'=>'额...操作失败了！'];
        return json($result);
    }

    public function buildfollow(){
        $result=$this->buildHtml('m/follow','./');
        $result=$result?['success'=>'成功生成静态页面！']:['msg'=>'额...操作失败了！'];
        return json($result);
    }

    public function buildcontent(){
        if ($_POST['num']) {
            $article=Db::name('article')->order('id desc')->limit($_POST['num'])->select();
        }else{
            $article=Db::name('article')->order('id desc')->select();
        }
        foreach ($article as $key => $value) {
            $prev=Db::name('article')->where(['id'=>['lt',$value['id']]])->order('id desc')->find();
            $next=Db::name('article')->where(['id'=>['gt',$value['id']]])->order('id asc')->find();
            if ($prev) {$prev['url']=lockcode($prev['id']).'.html';}
            if ($next) {$next['url']=lockcode($next['id']).'.html';}
            $this->assign([
                'article'=>$value,
                'prev'=>$prev,
                'next'=>$next,
            ]);
            $result=$this->buildHtml('m/'.lockcode($value['id']),'./');
        }
        $result=$result?['success'=>'成功生成页面静态！']:['msg'=>'额...操作失败了！'];
        return json($result);
    }

    public function delpage(){
        $page=$_POST['page'];
        $result=file_exists("m/$page.html")?unlink("m/$page.html"):false;
        $result=$result?['success'=>'成功删除静态页面！']:['msg'=>'额...操作失败了！'];
        return json($result);
    }
}