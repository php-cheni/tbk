<?php
namespace app\index\controller;
use think\Controller; 
use think\Validate;
use think\Request;
use think\Db;

class News extends Common{
    public function index(){
    	$article=Db::name('article')->field('content',true)->order('settop desc,id desc')->paginate(6)->each(function($item, $key){
    		$item['category']=Db::name('category')->where('id',$item['category'])->value('name');
    		return $item;
    	});
		$this->assign([
			'article'=>$article,
			'page'=>$article->render(),
			'count'=>$article->count(),
		]);
    	return $this->fetch();
    }

    public function delarticle(){
    	$result=Db::name('article')->delete(input('id'));
    	$result=$result?['success'=>'已经删除成功！']:['msg'=>'额...删除失败了！'];
    	return json($result);
    }

    public function edit(){
    	if ($_POST) {
            $validate = new Validate([
                'title'=>'require|length:5,30',
                'category'=>'require|number',
                'description'=>'require|length:20,255',
                'img'=>'require',
                'content'=>'require|min:300',
            ]);
            if ($validate->check($_POST)) {
                if ($_POST['settop']) {Db::name('article')->where('settop',1)->update(['settop'=>0]);}else{unset($_POST['settop']);}
                $result=Db::name('article')->update($_POST);
                $result=$result?['success'=>'已经修改成功！']:['error'=>'额...修改失败了！'];
                return json($result);
            }else{
                return json(['error'=>$validate->getError()]);
            }
    	}else{
	        $id=input('id');
	        $detail=Db::name('article')->find($id);
	        $category=Db::name('category')->column('id,name');
	        $this->assign([
	            'detail'=>$detail,
	            'category'=>$category,
	        ]);
	        return $this->fetch();
    	}
    }

	public function upload(){
	    $file = request()->file('file');
	    $info = $file->validate(['size'=>2097152,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
	    if($info){
	        return json(['code'=>0,'msg'=>'上传成功！','data'=>['src'=>'/uploads/'.str_replace("\\","/",$info->getSaveName()),'title'=>'幽美集团']]);
	    }else{
	        return json(['error'=>$file->getError()]);
	    }
	}

	public function addnews(){
    	if ($_POST) {
            $validate = new Validate([
                'title'=>'require|length:5,30',
                'category'=>'require|number',
                'description'=>'require|length:20,255',
                'img'=>'require',
                'content'=>'require|min:300',
            ]);
            if ($validate->check($_POST)) {
                $_POST['date']=date('Y-m-d');
        		if ($_POST['settop']) {Db::name('article')->where('settop',1)->update(['settop'=>0]);}
        		$result=Db::name('article')->insertGetId($_POST);
        		$result=$result?['success'=>'已经添加成功！','article'=>$result]:['error'=>'额...添加失败了！'];
        		return json($result);
            }else{
                return json(['error'=>$validate->getError()]);
            }
    	}else{
            $category=Db::name('category')->column('id,name');
            $this->assign(['category'=>$category]);
	        return $this->fetch();
    	}
	}

    public function category(){
        $category=Db::name('category')->order('id desc')->paginate(15);
        $this->assign([
            'category'=>$category,
            'page'=>$category->render(),
            'count'=>$category->count(),
        ]);
        return $this->fetch();
    }

    public function addcategory(){
        if ($_POST) {
            $validate = new Validate([
                'name'=>'require|length:1,5',
                'en_name'=>'require|length:2,20|regex:/^[a-z\d]+$/',
                'recommend'=>'require|number|max:100',
            ]);
            if ($validate->check($_POST)) {
                $category=Db::name('category')->where('en_name',$_POST['en_name'])->find();
                if (!$category) {
                    $result=Db::name('category')->insert($_POST);
                    $result=$result?['success'=>'已经添加成功！']:['error'=>'额...添加失败了！'];
                }else{
                    $result=['error'=>'这个页面名称'.$_POST['en_name'].'已经存在！'];
                }
                return json($result);
            }else{
                return json(['error'=>$validate->getError()]);
            }
        }else{
            return $this->fetch();
        }
    }

    public function editcategory(){
        if ($_POST) {
            $validate = new Validate([
                'name'=>'require|length:1,5',
                'en_name'=>'require|length:2,20|regex:/^[a-z\d]+$/',
                'recommend'=>'require|number|max:100',
            ]);
            if ($validate->check($_POST)) {
                $category=Db::name('category')->where(['en_name'=>$_POST['en_name'],'id'=>['neq',$_POST['id']]])->find();
                if (!$category) {
                    $result=Db::name('category')->update($_POST);
                    $result=$result?['success'=>'已经修改成功！']:['error'=>'额...修改失败了！'];
                }else{
                    $result=['error'=>'这个页面名称'.$_POST['en_name'].'已经存在！'];
                }
                return json($result);
            }else{
                return json(['error'=>$validate->getError()]);
            }
        }else{
            $category=Db::name('category')->find(input('id'));
            $this->assign(['category'=>$category]);
            return $this->fetch();
        }
    }

    public function delcategory(){
        $result=Db::name('category')->delete(input('id'));
        
        if ($result and file_exists('article/'.lockcode(input('id')).'.html')) {
            unlink('article/'.lockcode(input('id')).'.html');
            $result=['success'=>'已经删除成功！'];
        }else{
            $result=['msg'=>'额...删除失败了！'];
        }
        return json($result);
    }
}