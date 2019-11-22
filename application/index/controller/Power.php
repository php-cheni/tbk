<?php
namespace app\index\controller;
use think\Controller; 
use think\Validate;
use think\Request;
use think\Db;

class Power extends Common{
    public function index(){
        $user=Db::name('user')->select();
        $role=Db::name('role')->column('id,name');
        foreach ($user as $key => $value) {
            $user[$key]['role']=$role[$value['role']];
        }
        $this->assign(['user'=>$user]);
        return $this->fetch();
    }

    public function adduser(){
        if ($_POST) {
            $_POST['password']=md5($_POST['password'].'youmei');
            $validate = new Validate(['username'=>'require|length:1,10','password'=>'require|length:32','role'=>'require|number']);
            if ($validate->check($_POST)) {
                $username=Db::name('user')->where('username',$_POST['username'])->find();
                if (!$username) {
                    $result=Db::name('user')->insert($_POST);
                    $result=$result?['success'=>'已经添加成功！']:['error'=>'额...添加失败了！'];
                    return json($result);
                }else{
                    $result=['error'=>'这个用户已经存在！'];
                    return json($result);
                }
            }else{
                return json(['error'=>$validate->getError()]);
            }
        }else{
            $role=Db::name('role')->select();
            $this->assign(['role'=>$role]);
            return $this->fetch();
        }
    }

    public function edituser(){
        if ($_POST) {
            $validate = new Validate(['username'=>'require|length:1,10','role'=>'require|number']);
            if ($validate->check($_POST)) {
                $result=Db::name('user')->update($_POST);
                $result=$result?['success'=>'已经修改成功！']:['error'=>'额...修改失败了！'];
                return json($result);
            }else{
                return json(['error'=>$validate->getError()]);
            }
        }else{
            $id=input('id');
            $user=Db::name('user')->find($id);
            $role=Db::name('role')->select();
            $this->assign(['role'=>$role,'user'=>$user]);
            return $this->fetch();
        }
    }

    public function deluser(){
        $result=Db::name('user')->delete(input('id'));
        $result=$result?['success'=>'已经删除成功！']:['error'=>'额...删除失败了！'];
        return json($result);
    }


    public function role(){
    	$role=Db::name('role')->paginate(10)->each(function($item, $key){
            $power=Db::name('menu')->where(['id'=>['in',$item['power']]])->column('name');
            $item['power']='['.implode('],[',$power).']';
            return $item;
        });
		$this->assign([
            'role'=>$role,
            'page'=>$role->render(),
            'count'=>$role->count(),
        ]);
    	return $this->fetch();
    }

    public function addrole(){
        if ($_POST) {
            $_POST['power']=implode(',',$_POST['power']);
            $validate = new Validate(['name'=>'require|length:1,10',]);
            if ($validate->check($_POST)) {
                $result=Db::name('role')->insert($_POST);
                $result=$result?['success'=>'已经添加成功！']:['error'=>'额...添加失败了！'];
                return json($result);
            }else{
                return json(['error'=>$validate->getError()]);
            }
        }else{
            $power=Db::name('menu')->where(['power'=>1,'pid'=>0])->select();
            foreach ($power as $key => $value) {
                $power[$key]['son']=Db::name('menu')->where('pid',$value['id'])->select();
            }
            $this->assign(['power'=>$power]);
            return $this->fetch();
        }
    }

    public function editrole(){
        if ($_POST) {
            $_POST['power']=implode(',',$_POST['power']);
            $validate = new Validate(['name'=>'require|length:1,10',]);
            if ($validate->check($_POST)) {
                $result=Db::name('role')->update($_POST);
                $result=$result?['success'=>'已经修改成功！']:['error'=>'额...修改失败了！'];
                return json($result);
            }else{
                return json(['error'=>$validate->getError()]);
            }
        }else{
            $id=input('id');
            $role=Db::name('role')->find($id);
            $menu=explode(',', $role['power']);
            $power=Db::name('menu')->where(['power'=>1,'pid'=>0])->select();
            foreach ($power as $key => $value) {
                $power[$key]['y']=in_array($value['id'],$menu)?1:0;
                $power[$key]['son']=Db::name('menu')->where('pid',$value['id'])->select();
                foreach ($power[$key]['son'] as $k => $v) {
                    $power[$key]['son'][$k]['y']=in_array($v['id'],$menu)?1:0;
                }
            }
            $this->assign(['role'=>$role,'power'=>$power]);
            return $this->fetch();
        }
    }

    public function delrole(){
        $result=Db::name('role')->delete(input('id'));
        $result=$result?['success'=>'已经删除成功！']:['error'=>'额...删除失败了！'];
        return json($result);
    }

    public function power(){
        $power=Db::name('menu')->order('id desc')->paginate(15)->each(function($item, $key){
            $pid=Db::name('menu')->where('id',$item['pid'])->value('name');
            $item['show']=$item['show']?'显示':'不显示';
            $item['icon']=$item['icon']?$item['icon']:'无icon';
            $item['pid']=$item['pid']?$pid:'顶级模块';
            $item['power']=$item['power']?'已限制':'未限制';
            return $item;
        });
        $this->assign([
            'power'=>$power,
            'page'=>$power->render(),
            'count'=>$power->count(),
        ]);
        return $this->fetch();
    }

    public function addpower(){
        if ($_POST) {
            $validate = new Validate([
                'name'=>'require|length:1,10',
                'url'=>'require|length:2,20',
                'pid'=>'require|number',
                'power'=>'require|number|max:1',
                'show'=>'require|number|max:1',
            ]);
            if ($validate->check($_POST)) {
                $result=Db::name('menu')->insert($_POST);
                $result=$result?['success'=>'已经添加成功！']:['error'=>'额...添加失败了！'];
                $power=Db::name('menu')->column('id');
                Db::name('role')->where('id',1)->update(['power'=>implode(',',$power)]);
                return json($result);
            }else{
                return json(['error'=>$validate->getError()]);
            }
        }else{
            $power=Db::name('menu')->where('pid',0)->select();
            $this->assign(['power'=>$power]);
            return $this->fetch();
        }
    }

    public function editpower(){
        if ($_POST) {
            $validate = new Validate([
                'name'=>'require|length:1,10',
                'url'=>'require|length:2,20',
                'pid'=>'require|number',
                'power'=>'require|number|max:1',
                'show'=>'require|number|max:1',
            ]);
            if ($validate->check($_POST)) {
                $result=Db::name('menu')->update($_POST);
                $result=$result?['success'=>'已经修改成功！']:['error'=>'额...修改失败了！'];
                return json($result);
            }else{
                return json(['error'=>$validate->getError()]);
            }
        }else{
            $id=input('id');
            $power=Db::name('menu')->find($id);
            $list=Db::name('menu')->where('pid',0)->select();
            $this->assign(['power'=>$power,'list'=>$list]);
            return $this->fetch();
        }
    }
}