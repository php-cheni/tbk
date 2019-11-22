<?php
namespace app\index\controller;
use think\Controller; 
use think\Validate;
use think\Request;
use think\Db;

class Trends extends Common{
    public function index(){
    	$trends=Db::name('trends')->paginate(6)->each(function($item, $key){
            $item['img']=json_decode($item['img'],true);
            return $item;
        });
		$this->assign([
            'trends'=>$trends,
            'page'=>$trends->render(),
            'count'=>$trends->count(),
        ]);
    	return $this->fetch();
    }

    public function add(){
        if ($_POST) {
            $_POST['img']=json_encode($_POST['img']);
            $validate = new Validate([
                'title'=>'require|length:1,15',
                'description'=>'require|length:20,255',
                'img'=>'require',
            ]);
            if ($validate->check($_POST)) {
                $result=Db::name('trends')->insert($_POST);
                $result=$result?['success'=>'已经添加成功！']:['error'=>'额...添加失败了！'];
                return json($result);
            }else{
                return json(['error'=>$validate->getError()]);
            }
        }else{
            return $this->fetch();
        }
    }

    public function edit(){
        if ($_POST) {
            $_POST['img']=json_encode($_POST['img']);
            $validate = new Validate([
                'title'=>'require|length:1,15',
                'description'=>'require|length:20,255',
                'img'=>'require',
            ]);
            if ($validate->check($_POST)) {
                $result=Db::name('trends')->update($_POST);
                $result=$result?['success'=>'已经修改成功！']:['error'=>'额...修改失败了！'];
                return json($result);
            }else{
                return json(['error'=>$validate->getError()]);
            }
        }else{
            $id=input('id');
            $trends=Db::name('trends')->find($id);
            $trends['img']=json_decode($trends['img'],true);
            $this->assign(['trends'=>$trends]);
            return $this->fetch();
        }
    }


    public function deltrends(){
        $result=Db::name('trends')->delete(input('id'));
        $result=$result?['success'=>'已经删除成功！']:['error'=>'额...删除失败了！'];
        return json($result);
    }

    public function active(){
        $active=Db::name('active')->paginate(6,true);
        $this->assign([
            'active'=>$active,
            'page'=>$active->render(),
            'count'=>$active->count(),
        ]);
        return $this->fetch();
    }

    public function addactive(){
        if ($_POST) {
            $validate = new Validate([
                'title'=>'require|length:1,15',
                'img'=>'require',
                'url'=>'require|length:1,100',
            ]);
            if ($validate->check($_POST)) {
                $result=Db::name('active')->insert($_POST);
                $result=$result?['success'=>'已经添加成功！']:['error'=>'额...添加失败了！'];
                return json($result);
            }else{
                return json(['error'=>$validate->getError()]);
            }
        }else{
            return $this->fetch();
        }
    }

    public function editactive(){
        if ($_POST) {
            $validate = new Validate([
                'title'=>'require|length:1,15',
                'img'=>'require',
                'url'=>'require|length:1,100',
            ]);
            if ($validate->check($_POST)) {
                $result=Db::name('active')->update($_POST);
                $result=$result?['success'=>'已经修改成功！']:['error'=>'额...修改失败了！'];
                return json($result);
            }else{
                return json(['error'=>$validate->getError()]);
            }
        }else{
            $id=input('id');
            $active=Db::name('active')->find($id);
            $this->assign(['active'=>$active]);
            return $this->fetch();
        }
    }
}