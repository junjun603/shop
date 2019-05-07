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
		if($this->ev->isMobile())
		{
			header("location:index.php?content-phone");
			exit;
		}
		$action = $this->ev->url(3);
		if(!method_exists($this,$action))
		$action = "index";
		$this->$action();
		exit;
	}

	private function index()
	{
	    $lsTime = $this->category->getSyncTime('get');//获取上次同步时间
        $beforeTimeInt = strtotime(date("Y-m-d H:i:s", intval($lsTime[0])));
        $nowTimeInt = strtotime(date("Y-m-d H:i:s"));
        $minus = $nowTimeInt - $beforeTimeInt - (60*60*24);
        if($minus>0){
            $this->category->getSyncTime('set'); //写入当前时间
            $this->suretime(); //把新明辉的人员架构同步到ems里
        }
		$catids = array();
		$catids['index'] = $this->category->getCategoriesByArgs(array(array("AND","catindex > 0")));
		$contents = array();
		if($catids['index'])
		{
			foreach($catids['index'] as $p)
			{
				$catstring = $this->category->getChildCategoryString($p['catid']);
				$contents[$p['catid']] = $this->content->getContentList(array(array("AND","find_in_set(contentcatid,:catstring)",'catstring',$catstring)),1,$p['catindex']?$p['catindex']:10);
			}
		}
		$this->category->app = 'course';
		$coursecats = $this->category->getCategoriesByArgs(array(array("AND","catparent = '0'")));
		$topcourse = array();
		foreach($coursecats as $cat)
		{
			$catstring = $this->category->getChildCategoryString($cat['catid']);
			$topcourse[$cat['catid']] =  $this->course->getCourseList(array(array("AND","find_in_set(cscatid,:cscatid)",'cscatid',$catstring)),1,6);
		}
		$this->tpl->assign('topcourse',$topcourse);
		$courses = $this->course->getCourseList(1,1,8);
		$basic = $this->G->make('basic','exam');
		$basics = $basic->getBestBasics();
		$this->tpl->assign('coursecats',$coursecats);
		$this->tpl->assign('courses',$courses);
		$this->tpl->assign('basics',$basics);
		$this->tpl->assign('contents',$contents);

        $this->tpl->assign('sess',$_SESSION);

        if(isset($_GET['flag']) && $_GET['flag']==1 &&
			isset($_GET['username']) &&$_GET['username'] &&
			isset($_GET['password']) &&$_GET['password']){

            $this->tpl->assign('flag',$_GET['flag']);
            $this->tpl->assign('username',$_GET['username']);
            $this->tpl->assign('password',$_GET['password']);
        }


		$this->tpl->display('index');
	}


    private function suretime(){  //定时同步新明辉的员工信息
        $confuseList = $this->category->db()->syncTimer();
        // var_dump($confuseList);
        // $confuseList = $this->db->syncTimer();
        // $exitAr = []; //辞职人员
        // $changeAr = []; // 信息变更人员
        foreach ($confuseList['xmhUl'] as $xmhPer){
            foreach ( $confuseList['emsUl'] as $emsPer ){
                if($xmhPer['emp_code']==$emsPer['username'] && $xmhPer['name']==$emsPer['real_name']){
                    if($xmhPer['status'] == '在职'){
                        if($xmhPer['pwd']==$emsPer['userpassword']
                            && $xmhPer['tel']==$emsPer['userphone']
                            && $xmhPer['address']==$emsPer['useraddress']){
                            continue;
                        }else{
                            $nowEmpInfo = [];
                            // $changeAr[] = $xmhPer['name'];
                            $nowEmpInfo['userpassword'] = $xmhPer['pwd'];
                            $nowEmpInfo['userphone'] = $xmhPer['tel'];
                            $nowEmpInfo['useraddress'] = $xmhPer['address'];
                            $nowEmpInfo['userid'] = $emsPer['userid'];
                            $this->category->db()->updateEmpInfo($nowEmpInfo);
                        }
                    }else{ //人员已经辞职
                        $nowEmpInfo = [];
                        $nowEmpInfo['userid'] = $emsPer['userid'];
                        $nowEmpInfo['op'] = 'delete';
                        // $exitAr[] =  $xmhPer['name'];;
                        $this->category->db()->updateEmpInfo($nowEmpInfo);
                    }
                }else continue;
            }
        }
        $simXmh = array_column($confuseList['xmhUl'],'name','emp_code');
        $simEms = array_column($confuseList['emsUl'],'real_name','username');
        // 新增员工处理
        $addEmpInfo = array_diff_assoc($simXmh,$simEms);
        foreach ($addEmpInfo as $empCode=>$empName){
            foreach ($confuseList['xmhUl'] as $xmhAddPer){
                if($xmhAddPer['emp_code'] == $empCode && $xmhAddPer['name'] == $empName){
                    if($xmhAddPer['status'] == '在职'){
                        //插入新入职的员工
                        $this->category->db()->insertNewEmployeeInfo($xmhAddPer);
                    }else{
                        continue;
                    }
                }else continue;
            }
        }
        //  echo 123;
    }

}


?>
