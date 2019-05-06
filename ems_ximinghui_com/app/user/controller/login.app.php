<?php
/*
 * Created on 2016-5-19
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class action extends app
{
	public function display()
	{
		$action = $this->ev->url(3);
		if(!method_exists($this,$action))
		$action = "index";
		$this->$action();
		exit;
	}

	private function index()  //用户登录
	{
		$appid = 'user';
		$app = $this->G->make('apps','core')->getApp($appid);
		$this->tpl->assign('app',$app);
		if($this->ev->get('userlogin'))
		{
			$tmp = $this->session->getSessionValue();
			if(TIME - $tmp['sessionlasttime'] < 1)
			{
				$message = array(
					'statusCode' => 300,
					"message" => "操作失败"
				);
				exit(json_encode($message));
			}
			$args = $this->ev->get('args');
			$user = $this->user->getUserByUserName($args['username']);
			if($user['userid'])
			{
				if($user['userpassword'] == md5($args['userpassword']) || $user['userpassword'] == strtoupper(md5($args['userpassword'])) )
				{
					if($app['appsetting']['loginmodel'] == 1)
					$this->session->offOnlineUser($user['userid']);
					$this->session->setSessionUser(array('sessionuserid'=>$user['userid'],'sessionpassword'=>$user['userpassword'],'sessionip'=>$this->ev->getClientIp(),'sessiongroupid'=>$user['usergroupid'],'sessionlogintime'=>TIME,'sessionusername'=>$user['username']));
					$message = array(
						'statusCode' => 201,
						"message" => "操作成功",
					    "callbackType" => 'forward',
					    "forwardUrl" => "reload"
					);
					$this->G->R($message);
				}
				else
				{
					$message = array(
						'statusCode' => 300,
						'errorinput' => 'args[username]',
						"message" => "登录失败，因为用户名或密码错误"
					);
					exit(json_encode($message));
				}
			}
			else
			{
				//根据用户名密码查询新明辉用户 ,$args['userpassword']
                $xmhUser = $this->db->loginByUsername($args['username']);
                if($xmhUser['id'])
                {
                    if($xmhUser['pwd'] == strtoupper(md5($args['userpassword'])))
                    {
                        if($app['appsetting']['loginmodel'] == 1)
                            $this->session->offOnlineUser($xmhUser['id']);
							$this->session->setSessionUser(array('sessionuserid'=>$xmhUser['id'],'sessionpassword'=>$xmhUser['pwd'],'sessionip'=>$this->ev->getClientIp(),'sessiongroupid'=>$xmhUser['usergroupid'],'sessionlogintime'=>TIME,'sessionusername'=>$args['username']));
							$message = array(
								'statusCode' => 201,
								"message" => "操作成功",
								"callbackType" => 'forward',
								"forwardUrl" => "reload"
							);
                        $this->G->R($message);
                    }
                    else
                    {
                        $message = array(
                            'statusCode' => 300,
                            'errorinput' => 'args[username]',
                            "message" => "登录失败，因为用户名或密码错误"
                        );
                        exit(json_encode($message));
                    }
                }else{
                    $message = array(
                        'statusCode' => 300,
                        'errorinput' => 'args[username]',
                        "message" => "登录失败，因为用户名不存在"
                    );
                    exit(json_encode($message));
				}
			}
		}
		else
		{
			$this->tpl->display('login');
		}
	}
}


?>
