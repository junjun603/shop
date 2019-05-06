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

    private function modify()
    {
        $ceid = $this->ev->get('ceid');
        $ce = $this->ce->getCeById($ceid);
        //根据证书id，找到考场名称
        $couname = $this->db->getCourseName($ceid);
        $this->tpl->assign('couname',$couname);
        $this->tpl->assign('ce',$ce);
        if($this->ev->get('modifycertificate'))
        {
            $args = $this->ev->get('args');
            $args['cetime'] = strtotime($args['cetime']);
            $this->ce->modifyCe($ceid,$args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "reload"
            );
            exit(json_encode($message));
        }
        else
            $this->tpl->display('certificate_edit');
    }

    private function del()
    {
        $ceid = $this->ev->get('ceid');
        $this->ce->delCe($ceid);
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    private function add()  //添加证书
    {
        if($this->ev->get('addcertificate'))
        {
            $args = $this->ev->get('args');
            $args['cetime'] = strtotime($args['cetime']);
            $this->ce->addCe($args);
            $message = array(
                'statusCode' => 200,
                "message" => "操作成功",
                "callbackType" => "forward",
                "forwardUrl" => "index.php?certificate-master-certificate"
            );
            exit(json_encode($message));
        }
        else
            $this->tpl->display('certificate_add');
    }

    private function courseList(){ //考场列表
        $cList =$this->db->courList();
        exit(json_encode($cList,JSON_UNESCAPED_UNICODE));
    }


    private function modifyqueue()
    {
        $ceqid = $this->ev->get('ceqid');
        $status = $this->ev->get('status');
        $this->ce->modifyCeQueue($ceqid,array('ceqstatus' => $status));
        $message = array(
            'statusCode' => 200,
            "message" => "操作成功",
            "callbackType" => "forward",
            "forwardUrl" => "reload"
        );
        exit(json_encode($message));
    }

    private function issueCer() //处理发证
    {
        if(isset($_GET['id'])&&isset($_GET['cerId'])){
            $empId = $_GET['id']; //员工id
            $cerId = $_GET['cerId']; //证书id
            $status = '已发';
            $res = $this->db->issuanceCer($empId,$cerId,$status);
            if($res==1) exit(json_encode(['status'=>1,'msg'=>'发证成功'],JSON_UNESCAPED_UNICODE));
            else exit(json_encode(['status'=>0,'msg'=>'发证失败,请联系管理员'],JSON_UNESCAPED_UNICODE));
        }else{
            exit(json_encode(['status'=>0,'msg'=>'发证失败,请联系管理员'],JSON_UNESCAPED_UNICODE));
        }
    }



    private function outdata()
    {
        $search = $this->ev->get('search');
        $ceid = $this->ev->get('ceid');
        $ce = $this->ce->getCeById($ceid);
        $args = array();
        $args[] = array("AND","ceqceid = :ceqceid","ceqceid",$ceid);
        if($search['username'])
        {
            $user = $this->_user->getUserByUserName($search['username']);
            if($user)
            {
                $args[] = array("AND","cequserid = :cequserid","cequserid",$user['userid']);
            }
        }
        if($search['ceqstatus'] || $search['ceqstatus'] === '0')
        {
            $args[] = array("AND","ceqstatus = :ceqstatus","ceqstatus",$search['ceqstatus']);
        }
        if($search['stime'])
        {
            $args[] = array("AND","ceqtime >= :sceqtime","sceqtime",strtotime($search['stime']));
        }
        if($search['etime'])
        {
            $args[] = array("AND","ceqtime <= :eceqtime","eceqtime",strtotime($search['etime']));
        }
        $certificates = $this->ce->getCeQueuesByArgs($args);
        include_once "lib/phpexcel/PHPExcel.php";
        include_once "lib/phpexcel/PHPExcel/Writer/Excel2007.php";
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle('审证');

        $index = 1;
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$index,'序号',PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$index,'姓名',PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('C'.$index,'身份证号',PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('D'.$index,'性别',PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('E'.$index,'学历',PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('F'.$index,'电话',PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('G'.$index,'地址',PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('H'.$index,'申请时间',PHPExcel_Cell_DataType::TYPE_STRING);

        foreach($certificates as $key => $p)
        {
            $index = $key + 2;
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$index,$index - 1,PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$index,$p['ceqinfo']['usertruename'],PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('C'.$index,$p['ceqinfo']['username'],PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('D'.$index,$p['ceqinfo']['usersex'],PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('E'.$index,$p['ceqinfo']['userdegree'],PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('F'.$index,$p['ceqinfo']['userphone'],PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('G'.$index,$p['ceqinfo']['useraddress'],PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('H'.$index,date('Y-m-d',$p['ceqtime']),PHPExcel_Cell_DataType::TYPE_STRING);
        }
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $fname = 'data/out/'.TIME.'.xlsx';
        $objWriter->save($fname);
        $message = array(
            'statusCode' => 200,
            "message" => "导出成功，转入下载页面，如果浏览器没有相应，请<a href=\"{$fname}\">点此下载</a>",
            "callbackType" => 'forward',
            "forwardUrl" => "{$fname}"
        );
        exit(json_encode($message));
    }

//	private function queue()
//	{
//		$page = intval($this->ev->get('page'));
//		$search = $this->ev->get('search');
//		$this->u = '';
//		if($search)
//		{
//			$this->tpl->assign('search',$search);
//			foreach($search as $key => $arg)
//			{
//				$this->u .= "&search[{$key}]={$arg}";
//			}
//		}
//		$this->tpl->assign('search',$search);
//		$this->tpl->assign('u',$this->u);
//		$ceid = $this->ev->get('ceid');
//		$ce = $this->ce->getCeById($ceid);
//		$args = array();
//		$args[] = array("AND","ceqceid = :ceqceid","ceqceid",$ceid);
//		if($search['username'])
//		{
//			$user = $this->_user->getUserByUserName($search['username']);
//			if($user)
//			{
//				$args[] = array("AND","cequserid = :cequserid","cequserid",$user['userid']);
//			}
//		}
//		if($search['ceqstatus'] || $search['ceqstatus'] === '0')
//		{
//			$args[] = array("AND","ceqstatus = :ceqstatus","ceqstatus",$search['ceqstatus']);
//		}
//		if($search['stime'])
//		{
//			$args[] = array("AND","ceqtime >= :sceqtime","sceqtime",strtotime($search['stime']));
//		}
//		if($search['etime'])
//		{
//			$args[] = array("AND","ceqtime <= :eceqtime","eceqtime",strtotime($search['etime']));
//		}
//		$certificates = $this->ce->getCeQueueList($args,$page,10);
//		$this->tpl->assign('certificates',$certificates);
//		$this->tpl->assign('status',array('申请中','已受理','已出证','申请被驳回'));
//		$this->tpl->assign('page',$page);
//		$this->tpl->assign('ce',$ce);
//		$this->tpl->display('certificate_queue');
//	}


    private function queue()  //发放证书
    {
        $ceid = $this->ev->get('ceid');
        $certificates = $this->ce->getEmployeeList(0,10,$ceid);
        $this->tpl->assign('certificates',$certificates);
        $this->tpl->assign('ceid',$ceid);
        $this->tpl->display('certificate_queue');
    }

    private function conditionQuery(){
        if(count($_GET)==2 && isset($_GET['groupname'])&&$_GET['groupname']=='全部'){
            exit(json_encode(['status'=>0,'msg'=>'查询全部'],JSON_UNESCAPED_UNICODE));
        }
        $parArr = [];
        if(isset($_GET['empName'])) $parArr['name'] = $_GET['empName'];
        if(isset($_GET['sexNs'])) $parArr['sex'] = $_GET['sexNs'];
        if(isset($_GET['telphpone'])) $parArr['tel'] = $_GET['telphpone'];
        if(isset($_GET['groupname'])) $parArr['groupname'] = $_GET['groupname'];
        $emplist = $this->db->queryEmpCon($parArr,$_GET['ceid']); //按条件查询员工列表
        $r = ['status'=>1,'emplist'=>$emplist];
        exit(json_encode($r,JSON_UNESCAPED_UNICODE));
    }

    private function getDepts(){
        $depts = $this->ce->getDeptList(); //查询所有部门
        exit(json_encode($depts,JSON_UNESCAPED_UNICODE));
    }

    private function index()
    {
        $page = intval($this->ev->get('page'));
        $certificates = $this->ce->getCeList($page,10);
        $this->tpl->assign('certificates',$certificates);
        $this->tpl->assign('page',$page);
        $this->tpl->display('certificate');
    }
}


?>
