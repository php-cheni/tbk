<?php
namespace app\index\controller;
use think\Controller; 
use think\Validate;
use think\Request;
use think\Db;

class Config extends Common{
    public function index(){
    	$config=Db::name('config')->select();
    	$this->assign([
    		'config'=>$config,
		]);
    	return $this->fetch();
    }

    public function update(){
    	$config=Db::name('config')->column('variable,validate');
    	$validate = new Validate($config);
    	$data=input();
		if ($validate->check($data)) {
            self::adminentrance(input('admin'));
            $update=0;
			foreach ($data as $key => $value) {
				$result=Db::name('config')->where('variable',$key)->update(['value'=>$value]);
                if ($result) {$update=1;}
			}
            $result=$update?['success'=>'已经保存成功！']:['error'=>'什么都没改你保存啥？'];
			return json($result);
		}else{
			return json(['error'=>$validate->getError()]);
		}
    }

    public function carousel(){
    	$banner=Db::name('banner')->order('sort asc,id desc')->select();
    	$this->assign([
    		'banner'=>$banner,
		]);
    	return $this->fetch();
    }

    public function delcarousel(){
    	$result=Db::name('banner')->delete(input('id'));
    	$result=$result?['success'=>'已经删除成功！']:['error'=>'额...删除失败了！'];
    	return json($result);
    }

    public function addcarousel(){
    	if ($_POST) {
    		$validate = new Validate([
    			'title'=>'require|length:1,20',
    			'url'=>'url',
    			'sort'=>'require|number',
    			'icon'=>'require|length:1,20',
    			'content'=>'require|length:1,200',
    			'img'=>'length:1,100',
			]);
    		if ($validate->check($_POST)) {
				$result=Db::name('banner')->insert($_POST);
				$result=$result?['success'=>'已经添加成功！']:['error'=>'额...添加失败了！'];
    			return json($result);
    		}else{
    			return json(['error'=>$validate->getError()]);
    		}
    	}else{
    		return $this->fetch();
    	}
    }

    public function editcarousel(){
        if ($_POST) {
            $validate = new Validate([
                'title'=>'require|length:1,20',
                'url'=>'url',
                'sort'=>'require|number',
                'icon'=>'require|length:1,20',
                'content'=>'require|length:1,200',
                'img'=>'length:1,100',
            ]);
            if ($validate->check($_POST)) {
                $result=Db::name('banner')->update($_POST);
                $result=$result?['success'=>'已经修改成功！']:['error'=>'额...添加失败了！'];
                return json($result);
            }else{
                return json(['error'=>$validate->getError()]);
            }
        }else{
            $carousel=Db::name('banner')->find(input('id'));
            $this->assign(['carousel'=>$carousel]);
            return $this->fetch();
        }
    }

	public function upload(){
	    $file = request()->file('file');
	    $info = $file->validate(['size'=>2097152,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
	    if($info){
	        return json(['success'=>str_replace("\\","/",$info->getSaveName())]);
	    }else{
	        return json(['error'=>$file->getError()]);
	    }
	}

    public function nav(){
        $nav=Db::name('nav')->field('content',true)->paginate(6);
        $this->assign([
            'nav'=>$nav,
            'page'=>$nav->render(),
            'count'=>$nav->count(),
        ]);
        return $this->fetch();
    }

    public function addnav(){
        if ($_POST) {
            $validate = new Validate([
                'name'=>'require|length:1,8',
                'url'=>'require',
                'show'=>'require|number|max:1',
                'sort'=>'require|number|max:1',
                'keywords'=>'require|length:1,100',
                'description'=>'require|length:1,255',
            ]);
            if ($validate->check($_POST)) {
                $result=Db::name('nav')->insert($_POST);
                $result=$result?['success'=>'已经添加成功！']:['error'=>'额...添加失败了！'];
                return json($result);
            }else{
                return json(['error'=>$validate->getError()]);
            }
        }else{
            return $this->fetch();
        }
    }

    public function editnav(){
        if ($_POST) {
            $validate = new Validate([
                'name'=>'require|length:1,8',
                'url'=>'require',
                'show'=>'require|number|max:1',
                'sort'=>'require|number|max:1',
                'keywords'=>'require|length:1,100',
                'description'=>'require|length:1,255',
            ]);
            if ($validate->check($_POST)) {
                $result=Db::name('nav')->update($_POST);
                $result=$result?['success'=>'已经修改成功！']:['error'=>'额...修改失败了！'];
                return json($result);
            }else{
                return json(['error'=>$validate->getError()]);
            }
        }else{
            $id=input('id');
            $nav=Db::name('nav')->find($id);
            $this->assign(['nav'=>$nav]);
            return $this->fetch();
        }
    }

    public function delnav(){
        $result=Db::name('nav')->delete(input('id'));
        $result=$result?['success'=>'已经删除成功！']:['error'=>'额...删除失败了！'];
        return json($result);
    }

    public function link(){
        $link=Db::name('link')->field('content',true)->paginate(10);
        $this->assign([
            'link'=>$link,
            'page'=>$link->render(),
            'count'=>$link->count(),
        ]);
        return $this->fetch();
    }

    public function addlink(){
        if ($_POST) {
            $validate = new Validate([
                'name'=>'require|length:1,10',
                'url'=>'require|url',
                'show'=>'require|number|max:1',
                'sort'=>'require|number|max:100',
            ]);
            if ($validate->check($_POST)) {
                $result=Db::name('link')->insert($_POST);
                $result=$result?['success'=>'已经添加成功！']:['error'=>'额...添加失败了！'];
                return json($result);
            }else{
                return json(['error'=>$validate->getError()]);
            }
        }else{
            return $this->fetch();
        }
    }

    public function editlink(){
        if ($_POST) {
            $validate = new Validate([
                'name'=>'require|length:1,10',
                'url'=>'require|url',
                'show'=>'require|number|max:1',
                'sort'=>'require|number|max:100',
            ]);
            if ($validate->check($_POST)) {
                $result=Db::name('link')->update($_POST);
                $result=$result?['success'=>'已经修改成功！']:['error'=>'额...修改失败了！'];
                return json($result);
            }else{
                return json(['error'=>$validate->getError()]);
            }
        }else{
            $id=input('id');
            $link=Db::name('link')->find($id);
            $this->assign(['link'=>$link]);
            return $this->fetch();
        }
    }

    private function adminentrance($newname){
        $oldname=Db::name('config')->where(['variable'=>'admin'])->value('value');
        $result=rename("$oldname.php","$newname.php");
    }
}