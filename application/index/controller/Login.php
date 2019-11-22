<?php
namespace app\index\controller;
use think\Controller;
use think\Loader;
use think\Db;

class Login extends Controller{
    public function index(){
        if(session('user')){
        	$this->redirect('/index.php');
        }else{
	    	if(empty($_POST)){
	    		// return $this->fetch();
		        $this->buildHtml('login','./');
        		$this->redirect('/login.html');
	    	}else{
	            $where['username']=$_POST["username"];
	            $where['password']=md5($_POST["password"].'youmei');
	    		$result=Db::name('user')->where($where)->find();
	    		if($result){
                    session('user',$result);
	    			return json(['success'=>'登录成功！']);
	    		}else{
	    			return json(['error'=>'账号或密码错误！']);
	    		}
	    	}
    	}
	}

    public function StartCaptchaServlet(){
    	import('GeetestLib',EXTEND_PATH);
		$GtSdk = new \GeetestLib('f87955f1a528635cf7eb8f2b776e93e0', '374df16bfac94069eeb2311fc3fa3824');
		$data = array(
				"user_id" => "test", # 网站用户id
				// "client_type" => "web", #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
				"ip_address" => $_SERVER["REMOTE_ADDR"] # 请在此处传输用户请求验证时所携带的IP
			);
		$status = $GtSdk->pre_process($data, 1);
		session('gtserver',$status);
		session('user_id',$data['user_id']);
		echo $GtSdk->get_response_str();
    }

    public function VerifyLoginServlet(){
    	import('GeetestLib',EXTEND_PATH);
		$GtSdk = new \GeetestLib('f87955f1a528635cf7eb8f2b776e93e0', '374df16bfac94069eeb2311fc3fa3824');
		$data = array(
		        "user_id" => session('user_id'),
		        "client_type" => "web",
		        "ip_address" => $_SERVER["REMOTE_ADDR"]
		    );
		if ($_SESSION['gtserver'] == 1) {   //服务器正常
		    $result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $data);
		    if ($result) {
		        echo '{"status":"success"}';
		    } else{
		        echo '{"status":"fail"}';
		    }
		}else{  //服务器宕机,走failback模式
		    if ($GtSdk->fail_validate($_POST['geetest_challenge'],$_POST['geetest_validate'],$_POST['geetest_seccode'])) {
		        echo '{"status":"success"}';
		    }else{
		        echo '{"status":"fail"}';
		    }
		}
    }

}
