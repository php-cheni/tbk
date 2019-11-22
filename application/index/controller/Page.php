<?php
namespace app\index\controller;
use think\Controller; 
use think\Validate;
use think\Request;
use think\Db;

class Page extends Common{
    public function index(){
    	$single=Db::name('single')->field('content',true)->paginate(15)->each(function($item, $key){
    		$item['category']=Db::name('nav')->where('id',$item['category'])->value('name');
    		return $item;
    	});
		$this->assign([
			'single'=>$single,
			'page'=>$single->render(),
			'count'=>$single->count(),
		]);
    	return $this->fetch();
    }

    public function addpage(){
        if ($_POST) {
            $validate = new Validate([
                'title'=>'require|length:1,5',
                'en_title'=>'require|length:2,20|regex:/^[a-z\d]+$/',
                'category'=>'require|number',
                'keywords'=>'require|length:10,100',
                'description'=>'require|length:20,255',
                'content'=>'require|min:300',
            ]);
            if ($validate->check($_POST)) {
                $single=Db::name('single')->where('en_title',$_POST['en_title'])->find();
                if (!$single) {
                    $result=Db::name('single')->insert($_POST);
                    $result=$result?['success'=>'已经添加成功！']:['error'=>'额...添加失败了！'];
                }else{
                    $result=['error'=>'这个页面'.$_POST['en_title'].'已经存在！'];
                }
                return json($result);
            }else{
                return json(['error'=>$validate->getError()]);
            }
        }else{
            $category=Db::name('nav')->column('id,name');
            $this->assign(['category'=>$category]);
            return $this->fetch();
        }
    }

    public function editpage(){
        if ($_POST) {
            $validate = new Validate([
                'title'=>'require|length:1,5',
                'en_title'=>'require|length:2,20|regex:/^[a-z\d]+$/',
                'category'=>'require|number',
                'keywords'=>'require|length:10,100',
                'description'=>'require|length:20,255',
                'content'=>'require|min:300',
            ]);
            if ($validate->check($_POST)) {
                $result=Db::name('single')->update($_POST);
                $result=$result?['success'=>'已经修改成功！']:['error'=>'额...修改失败了！'];
                return json($result);
            }else{
                return json(['error'=>$validate->getError()]);
            }
        }else{
            $id=input('id');
            $detail=Db::name('single')->find($id);
            $category=Db::name('nav')->column('id,name');
            $this->assign(['category'=>$category,'detail'=>$detail]);
            return $this->fetch();
        }
    }

    public function delpage(){
        $result=Db::name('single')->delete(input('id'));
        $result=$result?['success'=>'已经删除成功！']:['error'=>'额...删除失败了！'];
        return json($result);
    }
}