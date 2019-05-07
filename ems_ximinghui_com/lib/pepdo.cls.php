<?php
/*
 * Created on 2014-12-10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class pepdo
{
    public $G;
    private $queryid = 0;
    private $linkid = 0;
    private $log = 1;

    public function __construct(&$G)
    {
        $this->G = $G;
        $this->sql = $this->G->make('pdosql');
    }

    private function _log($sql,$query)
    {
        if($this->log)
        {
            $fp = fopen('data/error.log','a');
            fputs($fp,print_r($sql,true).print_r($query->errorInfo(),true));
            fclose($fp);
        }
    }

    public function connect($host = DH,$dbuser = DU,$password = DP,$dbname = DB,$dbcode = HE)
    {
        $dsn="mysql:host={$host};dbname={$dbname};";
        $this->linkid = new PDO($dsn,$dbuser,$password);
        if(HE == 'utf-8')
            $this->linkid->query("set names utf8");
        else
            $this->linkid->query("set names gbk");
    }

    public function commit()
    {
        if(!$this->linkid)$this->connect();
        $this->linkid->commit();
    }

    public function beginTransaction()
    {
        if(!$this->linkid)$this->connect();
        $this->linkid->beginTransaction();
    }

    public function rollback()
    {
        if(!$this->linkid)$this->connect();
        $this->linkid->rollback();
    }

    public function fetchAll($sql,$index = false,$unserialize = false)
    {
        if(!is_array($sql))return false;
        if(!$this->linkid)$this->connect();
        $query = $this->linkid->prepare($sql['sql']);
        $rs = $query->execute($sql['v']);
        $this->_log($sql,$query);
        if ($rs) {
            $query->setFetchMode(PDO::FETCH_ASSOC);
            //return $query->fetchAll();
            $r = array();
            while($tmp = $query->fetch())
            {
                if($unserialize)
                {
                    if(is_array($unserialize))
                    {
                        foreach($unserialize as $value)
                        {
                            $tmp[$value] = unserialize($tmp[$value]);
                        }
                    }
                    else $tmp[$unserialize] = unserialize($tmp[$unserialize]);
                }
                if($index)
                {
                    $r[$tmp[$index]] = $tmp;
                }
                else
                    $r[] = $tmp;
            }
            return $r;
        }
        else
            return false;
    }

    public function fetch($sql,$unserialize = false)
    {
        if(!is_array($sql))return false;
        if(!$this->linkid)$this->connect();
        $query = $this->linkid->prepare($sql['sql']);
        $rs = $query->execute($sql['v']);
        $this->_log($sql,$query);
        if ($rs) {
            $query->setFetchMode(PDO::FETCH_ASSOC);
            $tmp = $query->fetch();
            if($tmp)
            {
                if($unserialize)
                {
                    if(is_array($unserialize))
                    {
                        foreach($unserialize as $value)
                        {
                            $tmp[$value] = unserialize($tmp[$value]);
                        }
                    }
                    else $tmp[$unserialize] = unserialize($tmp[$unserialize]);
                }
            }
            return $tmp;
        }
        else
            return false;
    }

    public function query($sql)
    {
        if(!$sql)return false;
        if(!$this->linkid)$this->connect();
        return $this->linkid->query($sql);
    }

    public function exec($sql)
    {
        $this->affectedRows = 0;
        if(!is_array($sql))return false;
        if(!$this->linkid)$this->connect();
        if($sql['dim'])
            return $this->dimexec($sql);
        else
            $query = $this->linkid->prepare($sql['sql']);
        $rs = $query->execute($sql['v']);
        $this->_log($sql,$query);
        $this->affectedRows = $rs;
        return $rs;
    }




    public function dimexec($sql)
    {
        if(!is_array($sql))return false;
        if(!$this->linkid)$this->connect();
        $query = $this->linkid->prepare($sql['sql']);
        foreach($sql['v'] as $p)
            $rs = $query->execute($p);
        //if($stmt->errorInfo())print_r($stmt->errorInfo());
        //else
        return $rs;
    }

    public function lastInsertId()
    {
        return $this->linkid->lastInsertId();
    }

    public function insertElement($args)
    {
        $data = array($args['table'],$args['query']);
        $sql = $this->sql->makeInsert($data);
        $this->exec($sql);
        return $this->lastInsertId();
    }

    public function queryEmpList($beginIndex=0,$count=20,$ceid)  //获取所有员工列表
    {
        $sql = 'SELECT e.userid id,e.real_name emp_code,e.userpassport idcard,o.groupname dname,e.usergender sex,e.userphone tel,u.status FROM ems_user e left join (select g.* from ( SELECT cer_id,STATUS,id,uid from ems_user_cer where cer_id='.$ceid.' order by id desc ) as g GROUP BY g.uid)  u on u.uid=e.userid join  ems_user_group o on o.groupid  = e.usergroupid limit '.$beginIndex.','.$count;

      //  $sql = 'select e.id,e.emp_code,e.name,e.idcard,e.dname,e.sex,e.tel,u.status from x2_employee e left join (select g.* from ( SELECT cer_id,STATUS,id,uid from x2_user_cer where cer_id='.$ceid.' order by id desc ) as g GROUP BY g.uid)  u on u.uid=e.id limit '.$beginIndex.','.$count;	//定义SQL语句
        $result=$this->linkid->prepare($sql);			//准备查询语句
        $result->execute();						//执行查询语句，并返回结果集
        $res=$result->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }


    public function authCourse($uid,$courlist){ //可允许开通的考场
        $sql = 'select obbasicid from ems_openbasics where obuserid='.$uid;
        $result=$this->linkid->prepare($sql);			//准备查询语句
        $result->execute();						//执行查询语句，并返回结果集
        $allowIds=$result->fetchAll(PDO::FETCH_ASSOC);
        $allowIds = array_column($allowIds,'obbasicid');
        $resArr = [];
        foreach ($courlist as $k=>$v){
            if(in_array($v['basicid'],$allowIds)){
                $resArr[] = $v;
            }
        }
        return $resArr;
    }

    public function batchAddRole($roleArr){ //批量添加角色

        $sql = 'select groupid from ems_user_group where groupname="'.$roleArr[0].'"';
        $result=$this->linkid->prepare($sql);			//准备查询语句
        $result->execute();						//执行查询语句，并返回结果集
        $rows=$result->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $k=>$v){
            $sql  = "DELETE FROM ems_user_group WHERE groupid = ".$v['groupid'];
            $stmt = $this->linkid->prepare($sql);
            $stmt->execute();
        }

        $sql = "insert into ems_user_group (groupname,groupmoduleid,groupdescribe,groupright) values(?,?,?,?)";
        $stmt = $this->linkid->prepare($sql);
        $stmt->bindValue(1,$roleArr[0]);
        $stmt->bindValue(2,$roleArr[1]);
        $stmt->bindValue(3,$roleArr[2]);
        $stmt->bindValue(4,'');
        $stmt->execute();
        $insert_id = $this->linkid->lastInsertId();
        if($insert_id) return 1;
        else return 0;
    }

    public function selRoleOrEmp($flag)  //获取所有员工列表 或者所有角色列表
    {
        if($flag==1){
            $sql = 'select groupid as id,groupname as name from ems_user_group';	//定义SQL语句
        }elseif($flag==2){
            $sql = 'select userid as id,username as name from ems_user';	//定义SQL语句
        }else {
            return false;
        }

        $result=$this->linkid->prepare($sql);			//准备查询语句
        $result->execute();						//执行查询语句，并返回结果集
        $res=$result->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }


    public function queryEmpCon($parArr,$ceid){  //按条件查询员工列表
        $sql = 'select * from ( select e.userid id,e.username emp_code,e.real_name name,e.userpassport idcard,e.groupname dname,e.usergender sex,e.userphone tel,g.status from (';
        $sql .= 'SELECT * FROM ems_user r join ems_user_group t on t.groupid =r.usergroupid WHERE ';	//定义SQL语句
        ksort($parArr, SORT_NATURAL);
        foreach ($parArr as $k=>$val){
            if($k=='groupname'){
                if($val=='全部'){
                    $sql .= ' 1=1 ';
                    continue;
                }
                $sql .= $k.'="'.$val.'"';
                continue;
            }

            $sql .= 'and '.$k.' like "%'.$val.'%"';
        }
        $sql .= ') e left join ( SELECT cer_id, STATUS, id, uid FROM ems_user_cer WHERE cer_id = '.$ceid.' ORDER BY id DESC )  AS g on g.uid=e.userid order by g.id desc) c group BY c.id';
        $result=$this->linkid->prepare($sql);	//准备查询语句
        $result->execute();						//执行查询语句，并返回结果集
        $res=$result->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    public function  queryDeptList(){  //获取所有部门
       // $sql = 'select id,dname from x2_employee GROUP BY dname';	//定义SQL语句
        $sql = 'select groupid id,groupname dname from ems_user_group GROUP BY groupname';
        $result=$this->linkid->prepare($sql);			//准备查询语句
        $result->execute();						//执行查询语句，并返回结果集
        $res=$result->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    public function issuanceCer($empId,$cerId,$status){ //发放证书
        $sql = "insert into ems_user_cer (cer_id,uid,status) values(?,?,?)";
        $stmt = $this->linkid->prepare($sql);
        $stmt->bindValue(1,$cerId);
        $stmt->bindValue(2,$empId);
        $stmt->bindValue(3,$status);
        $stmt->execute();
        $insert_id = $this->linkid->lastInsertId();
        if($insert_id) return 1;
        else return 0;
    }

    public function loginByUsername($empcode){ //根据用户的工号查询用户信息
        $sql = 'select id,pwd from ems_employee where emp_code = '.$empcode;
        $result=$this->linkid->prepare($sql);
        $result->execute();
        $res=$result->fetch(PDO::FETCH_ASSOC);
        return $res;
    }



    public function listElements($page,$number = 20,$args,$tablepre = DTH)
    {
        if(!is_array($args))return false;
        $pg = $this->G->make('pg');
        $page = $page > 0?$page:1;
        $r = array();
        $data = array($args['select'],$args['table'],$args['query'],$args['groupby'],$args['orderby'],array(intval($page-1)*$number,$number));
        $sql = $this->sql->makeSelect($data,$tablepre);
        $r['data'] = $this->fetchAll($sql,$args['index'],$args['serial']);
        $data = array('count(*) AS number',$args['table'],$args['query']);
        $sql = $this->sql->makeSelect($data,$tablepre);
        $t = $this->fetch($sql);
        $pages = $pg->outPage($pg->getPagesNumber($t['number'],$number),$page);
        $r['pages'] = $pages;
        $r['number'] = $t['number'];
        return $r;
    }

   //查询我的证书
    public function getMyCeList($uid){
        $sql = 'select u.issue_time,c.ceid,c.cethumb,c.cetitle,c.cedescribe from  ems_user_cer u join ems_certificate c on c.ceid=u.cer_id and u.uid='.$uid;	//定义SQL语句
        $result=$this->linkid->prepare($sql);			//准备查询语句
        $result->execute();						//执行查询语句，并返回结果集
        $res=$result->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    //考场列表
    public function courList(){
        $sql = 'select ceid,cetitle from ems_certificate';	//定义SQL语句
        $result=$this->linkid->prepare($sql);			//准备查询语句
        $result->execute();						//执行查询语句，并返回结果集
        $res=$result->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    //获取考场名称
    public function getCourseName($cid){
        $sql = 'select basic from ems_basic where basicid = (select  cebasic from ems_certificate where ceid= '.$cid.')';
        $result=$this->linkid->prepare($sql);
        $result->execute();
        $res=$result->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }


    public function delElement($args)
    {
        $data = array($args['table'],$args['query'],$args['orderby'],$args['limit']);
        $sql = $this->sql->makeDelete($data);
        return $this->exec($sql);
        //return $this->affectedRows();
    }

    public function updateElement($args)
    {
        $data = array($args['table'],$args['value'],$args['query'],$args['limit']);
        $sql = $this->sql->makeUpdate($data);
        return $this->exec($sql);
        //$this->affectedRows();
    }

    public function affectedRows()
    {
        return $this->affectedRows;
    }


    public function xmhPdo(){
        $db = array(
            'dsn' => 'mysql:host=rm-2zeemr2mdf945inqy.mysql.rds.aliyuncs.com;dbname=xmhshop20141223',
            'host' => 'rm-2zeemr2mdf945inqy.mysql.rds.aliyuncs.com',
            'port' => '3306',
            'dbname' => 'xmhshop20141223',
            'username' => 'xmh_shop',
            'password' => 'xmh@@#_shop071207',
            'charset' => 'utf8',
        );

        try{
            $pdo = new PDO($db['dsn'], $db['username'], $db['password']);
        }catch(PDOException $e){
            die('数据库连接失败:' . $e->getMessage());
        }
        $sql = 'select d.d_name,h.idcard,h.sex,h.emp_code,name,pwd,tel,address,h.status from hr_employee h left join hr_department d on h.d_id=d.id';
        $stmt = $pdo->query($sql); //返回一个PDOStatement对象
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }


    public function syncTimer() //定时查看ems和新明辉的员工信息
    {
        $allArr = [];
       // $allArr['xmhUl'] = $this->xmhPdo(); //远程连接
        $allArr['xmhUl'] = $this->xmhUserList(); //本地用
        $allArr['emsUl'] = $this->emsUserList();
        return $allArr;
    }

    private function xmhUserList(){ // 新明辉所有员工列表
        $sql = 'select d.d_name,h.idcard,h.sex,h.emp_code,name,pwd,tel,address,h.status from xmhshop20141223.hr_employee h left join xmhshop20141223.hr_department d on h.d_id=d.id';
        $result = $this->linkid->prepare($sql);
        $result->execute();
        $res=$result->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    private function emsUserList()  //ems所有员工列表
    {
        $sql = 'select userid,username,real_name,userpassword,userphone,useraddress from ems_user';
        $result=$this->linkid->prepare($sql);
        $result->execute();
        $res=$result->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    public function updateEmpInfo($userInfo)  //更新员工信息
    {
        if(count($userInfo)<1){
            return false;
        }
        elseif (isset($userInfo['op'])){
           // delete from
            $sql  = "DELETE FROM ems_user WHERE userid = ".$userInfo['userid'];
            $stmt = $this->linkid->prepare($sql);
            return $stmt->execute();
        }else{
            $sql = "UPDATE ems_user SET  `userpassword` = '".$userInfo['userpassword']."',`userphone` =  '".$userInfo['userphone']."',`useraddress` = '".$userInfo['useraddress']."' WHERE `userid` = ".$userInfo['userid'];
            $stmt = $this->linkid->prepare($sql);
            return $stmt->execute();
        }

    }

    public function insertNewEmployeeInfo($newUserInfo) //插入员工信息
    {
        if(isset($newUserInfo['d_name'])){
            //查看部门是否存在
            $sql = 'select groupid from ems_user_group where groupname = "'.$newUserInfo['d_name'].'"';
            $result=$this->linkid->prepare($sql);
            $result->execute();
            $idArr=$result->fetchAll(PDO::FETCH_ASSOC);
            if(count($idArr)>0){
               $idSingle = array_column($idArr,'groupid');
            }else{
                $groupInsert = "INSERT INTO `ems_user_group`(`groupname`, `groupmoduleid`,
                                 `groupdescribe`, `groupright`, `groupmoduledefault`, `groupdefault`) 
                                 VALUES ('".$newUserInfo['d_name']."', 12, '部门描述', '', 0, 0)";
                $stmt = $this->linkid->prepare($groupInsert);
                $stmt->execute();
                $insert_id = $this->linkid->lastInsertId();
                $idSingle = [$insert_id] ;
            }
            $userSql = "INSERT INTO `ems_user` (`username`,`useremail`,`userpassword`,`usergroupid`,`usermoduleid`,`userpassport`,`usergender`,`userphone`,`useraddress`,`real_name`,
                         `useropenid`, `manager_apps`, `teacher_subjects`,`userprofile`,`userdegree`,`userphoto`
                        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $stmt1 = $this->linkid->prepare($userSql);
            $stmt1->bindValue(1,$newUserInfo['emp_code']);
            $stmt1->bindValue(2,$newUserInfo['tel']);
            $stmt1->bindValue(3,$newUserInfo['pwd']);
            $stmt1->bindValue(4,$idSingle[0]);
            $stmt1->bindValue(5,0);
            $stmt1->bindValue(6,$newUserInfo['idcard']);
            $stmt1->bindValue(7,$newUserInfo['sex']);
            $stmt1->bindValue(8,$newUserInfo['tel']);
            $stmt1->bindValue(9,$newUserInfo['address']?$newUserInfo['address']:'');
            $stmt1->bindValue(10,$newUserInfo['name']);
            $stmt1->bindValue(11,'');
            $stmt1->bindValue(12,'');
            $stmt1->bindValue(13,'');
            $stmt1->bindValue(14,'');
            $stmt1->bindValue(15,'');
            $stmt1->bindValue(16,'');
            $stmt1->execute();
            $insert_id = $this->linkid->lastInsertId();
            if($insert_id) return 1;
            else return 0;
        }
    }

    public function getSyncTime($type){
        if($type=='get'){
            $sql = 'select usertruename from ems_user where userid =1';
            $result=$this->linkid->prepare($sql);
            $result->execute();
            $res=$result->fetchAll(PDO::FETCH_ASSOC);
            return array_column($res,'usertruename','userverifytime');
        }elseif ($type=='set'){
            $sql = "UPDATE `ems_user` SET `usertruename`= UNIX_TIMESTAMP() WHERE `userid` = 1";
            $stmt = $this->linkid->prepare($sql);
            return $stmt->execute();
        }

    }

}
?>
