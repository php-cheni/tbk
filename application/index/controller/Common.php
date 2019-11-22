<?php
namespace app\index\controller;
use think\Controller;
use think\Db;

class Common extends Controller{
    public function _initialize(){
        // 初始化的时候检查用户权限
        if(!session('user')){
            $this->redirect('/login.html');
        }else{
            $request= \think\Request::instance();   
            $controller = $request->controller();
            $action = $request->action();
            $data=$this->checkPower("$controller/$action");
            if (!$data) {$this->error('暂无权限！请联系超管。');}

            $power=Db::name('role')->where('id',session('user.role'))->value('power');
            $power=explode(',',$power);
            $menu=Db::name('menu')->where(['pid'=>0,'show'=>1])->select();
            foreach ($menu as $key => $value) {
                $menu[$key]['son']=Db::name('menu')->where(['pid'=>$value['id'],'show'=>1])->select();
                foreach ($menu[$key]['son'] as $k => $v) {
                    if (!in_array($v['id'],$power) and $v['power']==1) {
                        unset($menu[$key]['son'][$k]);
                    }
                }
                if (!in_array($value['id'],$power) and $value['power']==1) {
                    unset($menu[$key]);
                }
            }
            $current=Db::name('menu')->where('url',$controller)->value('id');
            $this->assign([
                'menu'=>$menu,
                'current'=>$current,
            ]);
        }
    }

    public function _empty(){
        return $this->fetch('Index/error');
    }

    private function checkPower($power){
        // 获取当前用户对应的角色
        $menu=Db::name('role')->where('id',session('user.role'))->value('power');
        $adminPower=explode(',', $menu);
        //设置首页所有登录用户可访问
        // 查询当前(控制器/方法)对应的权限的ID
        $powerId=Db::name('menu')->where(array('url'=>strtolower($power)))->find();
        if ($powerId['power']) {$result=in_array($powerId['id'],$adminPower)?1:0;}else{$result=1;}
        return $result;
    }

    public function logout(){
        session('user',null);
        $this->redirect('/index');
    }
}