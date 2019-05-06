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

	private function selectactor()
	{
		$groupid = $this->ev->get('groupid');
		$group = $this->user->getGroupById($groupid);
		if($group)
		{
			$this->user->selectDefaultActor($groupid);
			$message = array(
				'statusCode' => 200,
				"message" => "操作成功",
				"callbackType" => "forward",
			    "forwardUrl" => "reload"
			);
		}
		else
		$message = array(
			'statusCode' => 300,
			"message" => "操作失败，存在同名角色！"
		);
		exit(json_encode($message));
	}

	private function batchRole(){  //批量添加角色
        header("Content-type: text/html; charset=utf-8");
        set_time_limit(0);
        if(count($_FILES["myfile"])==0) exit(json_encode(array('status' => 2),JSON_UNESCAPED_UNICODE));
        elseif ($_FILES["myfile"]["error"] > 0) echo "错误：" . $_FILES["myfile"]["error"] . "<br>";
            else
            {
                $text = file_get_contents($_FILES["myfile"]["tmp_name"]);
                define('UTF32_BIG_ENDIAN_BOM', chr(0x00) . chr(0x00) . chr(0xFE) . chr(0xFF));
                define('UTF32_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE) . chr(0x00) . chr(0x00));
                define('UTF16_BIG_ENDIAN_BOM', chr(0xFE) . chr(0xFF));
                define('UTF16_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE));
                define('UTF8_BOM', chr(0xEF) . chr(0xBB) . chr(0xBF));
                $first2 = substr($text, 0, 2);
                $first3 = substr($text, 0, 3);
                $first4 = substr($text, 0, 3);
                $encodType = "";
                if ($first3 == UTF8_BOM)
                    $encodType = 'UTF-8 BOM';
                else if ($first4 == UTF32_BIG_ENDIAN_BOM)
                    $encodType = 'UTF-32BE';
                else if ($first4 == UTF32_LITTLE_ENDIAN_BOM)
                    $encodType = 'UTF-32LE';
                else if ($first2 == UTF16_BIG_ENDIAN_BOM)
                    $encodType = 'UTF-16BE';
                else if ($first2 == UTF16_LITTLE_ENDIAN_BOM)
                    $encodType = 'UTF-16LE';
                //下面的判断主要还是判断ANSI编码的·
                if ($encodType == '') {//即默认创建的txt文本-ANSI编码的
                    $content = iconv("GBK", "UTF-8", $text);
                } else if ($encodType == 'UTF-8 BOM') {//本来就是UTF-8不用转换
                    $content = $text;
                } else {//其他的格式都转化为UTF-8就可以了
                    $content = iconv($encodType, "UTF-8", $text);
                    //把转码后的内容放到文件中
                    $res = file_put_contents($_FILES["myfile"]["tmp_name"],$content);
                }
                  //  $temp = file($_FILES["myfile"]["tmp_name"]);
                $temp = explode('###',str_replace("\r\n","###",$content));
                $temp = array_splice($temp,0,count($temp)-1);
                if(count($temp)>0){
                    for ($i = 0; $i < count($temp); $i++) {
                       // $otherArr = explode("\t", $temp[$i]);
                        $otherArr =explode(',',$temp[$i]);
                       $res = $this->db->batchAddRole($otherArr);
                       if($res==0) exit(json_encode( array('status' => 0),JSON_UNESCAPED_UNICODE));
                    }
                    exit(json_encode( array('status' => 1),JSON_UNESCAPED_UNICODE));
                }else{
                    $resArr = array('status' => 0);
                    exit(json_encode($resArr,JSON_UNESCAPED_UNICODE));
				}

            }
	}

	private function modifyactor()
	{
		$page = $this->ev->get('page');
		if($this->ev->get('modifyactor'))
		{
			$groupid = $this->ev->get('groupid');
			$args = $this->ev->get('args');
			$r = $this->user->modifyActor($groupid,$args);
			if($r)
			{
				$message = array(
					'statusCode' => 200,
					"message" => "操作成功",
					"callbackType" => "forward",
				    "forwardUrl" => "index.php?user-master-actor"
				);
			}
			else
			{
				$message = array(
					'statusCode' => 300,
					"message" => "操作失败，存在同名角色！",
				    "callbackType" => ''
				);
			}
			exit(json_encode($message));
		}
		else
		{
			$groupid = $this->ev->get('groupid');
			$group = $this->user->getGroupById($groupid);
			$this->tpl->assign('group',$group);
			$this->tpl->display('modifyactor');
		}
	}

	private function delactor()
	{
		$page = intval($this->ev->get('page'));
		$groupid = $this->ev->get('groupid');
		$r = $this->user->delActorById($groupid);
		if($r)
		{
			$message = array(
				'statusCode' => 200,
				"message" => "操作成功",
				"callbackType" => "forward",
				"forwardUrl" => "index.php?user-master-actor&page={$page}"
			);
		}
		else
		{
			$message = array(
				'statusCode' => 300,
				"message" => "操作失败，该角色下存在用户，请删除所有用户后再删除本角色"
			);
		}
		exit(json_encode($message));
	}

	private function add()
	{
		if($this->ev->post('insertactor'))
		{
			$args = $this->ev->post('args');
			$id = $this->user->insertActor($args);
			$message = array(
				'statusCode' => 200,
				"message" => "操作成功",
				"callbackType" => "forward",
				"forwardUrl" => "index.php?user-master-actor&moduleid={$args['groupmoduleid']}"
			);
			exit(json_encode($message));
		}
		else
		{
			$this->tpl->display('addactor');
		}
	}

	private function index()
	{
		$search = $this->ev->post('search');
		$args = 1;
		$page = $this->ev->get('page');
		$page = $page>1?$page:1;
		if($search['groupmoduleid'])
		{
			$args = array(array('AND',"groupmoduleid = :groupmoduleid",'groupmoduleid',$search['groupmoduleid']));
		}
		$actors = $this->user->getUserGroupList($args,10,$page);
		$this->tpl->assign('page',$page);
		$this->tpl->assign('actors',$actors);
		$this->tpl->display('actor');
	}
}


?>
