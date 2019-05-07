{x2;if:!$userhash}
{x2;include:header}
<style>
	.table > tbody > tr > td{
		border-top: 0;
	}
	#deptName,#sex-s{
		height:34px;
		padding: 6px 12px;
		font-size: 14px;
		line-height: 1.42857143;
		color: #555;
		background-color: #fff;
		background-image: none;
		border: 1px solid #ccc;
		border-radius: 4px;
	}
</style>
<body>
{x2;include:nav}
<div class="container-fluid">
	<div class="row-fluid">
		<div class="main">
			<div class="col-xs-2" style="padding-top:10px;margin-bottom:0px;">
				{x2;include:menu}
			</div>
			<div class="col-xs-10" id="datacontent">
				{x2;endif}
				<div class="box itembox" style="margin-bottom:0px;border-bottom:1px solid #CCCCCC;">
					<div class="col-xs-12">
						<ol class="breadcrumb">
							<li><a href="index.php?{x2;$_app}-master">{x2;$apps[$_app]['appname']}</a></li>
							<li><a href="index.php?{x2;$_app}-master-certificate">证书管理</a></li>
							<!--
							<li class="active">申请列表</li>
							-->
							<li class="active">员工列表</li>
						</ol>
					</div>
				</div>
				<div class="box itembox" style="padding-top:10px;margin-bottom:0px;overflow:visible">
					<h4 class="title" style="padding:10px;">
						{x2;$ce['cetitle']}
						<!-- 申请列表  -->
						<a class="btn btn-primary pull-right" href="index.php?certificate-master-certificate">证书管理</a>
					</h4>
					<!--
					<form action="index.php?certificate-master-certificate-queue&ceid={x2;$ce['ceid']}" method="post" class="form-inline">

						<table class="table">
					        <tr>
								<td>
									身份证号：
								</td>
								<td>
									<input name="search[username]" class="form-control" size="15" type="text" class="idcard" value="{x2;$search['username']}"/>
								</td>
								<td>
									申请时间：
								</td>
								<td>
									<input class="form-control datetimepicker" data-date="{x2;date:TIME,'Y-m-d'}" data-date-format="yyyy-mm-dd" type="text" name="search[stime]" size="10" id="stime" value="{x2;$search['stime']}"/> - <input class="form-control datetimepicker" data-date="{x2;date:TIME,'Y-m-d'}" data-date-format="yyyy-mm-dd" size="10" type="text" name="search[etime]" id="etime" value="{x2;$search['etime']}"/>
								</td>
					        	<td>
									状态：
								</td>
								<td>
									<select name="search[ceqstatus]" class="form-control">
								  		<option value="">不限</option>
								  		<option value="0"{x2;if:$search['ceqstatus'] === '0'} selected{x2;endif}>申请中</option>
								  		<option value="1"{x2;if:$search['ceqstatus'] == 1} selected{x2;endif}>已受理</option>
								  		<option value="2"{x2;if:$search['ceqstatus'] == 2} selected{x2;endif}>已出证</option>
								  		<option value="3"{x2;if:$search['ceqstatus'] == 3} selected{x2;endif}>已驳回</option>
								  	</select>
								</td>
								<td>
									<button class="btn btn-primary" type="submit">提交</button>
									<a class="btn btn-primary ajax" href="index.php?certificate-master-certificate-outdata&ceid={x2;$ce['ceid']}{x2;$u}">导出</a>
								</td>
					        </tr>
						</table>
						-->

					<form action="index.php?certificate-master-certificate-queue&ceid={x2;$ce['ceid']}" method="post" class="form-inline">
						<table class="table">
							<tr>
								<td>
									员工姓名：
								</td>
								<td>
									<input name="search[name]" class="form-control" size="15" type="text" id="empName" value=""/>
								</td>
								<td>性别：</td>
								<td>
									<select name="search[sex]" id="sex-s">
										<option value="">不限</option>
										<option value="男">男</option>
										<option value="女">女</option>
									</select>
								</td>
							</tr>
							<tr>
								<td>电话：</td>
								<td>
									<input name="search[tel]" class="form-control" size="15" type="tel" id="telphpone" value="{x2;$search['tel']}"/>
								</td>
								<td>部门：</td>
								<td>
									<select name="" id="deptName">
									</select>
								</td>
								<td>
									<button class="btn btn-primary submit-s" type="submit">提交</button>
								</td>
							</tr>
						</table>
						<div class="input">
							<input type="hidden" value="1" name="search[argsmodel]" />
						</div>
					</form>
					<table class="table table-hover table-bordered">
						<thead>
						<tr class="info">
							<th width="60">ID</th>
							<!--<th width="120">照片</th>-->
							<th width="80">姓名</th>
							<th width="100">身份证号</th>
							<th width="50">性别</th>
							<!--<th width="90">文化程度</th> -->
							<th width="110">联系方式</th>
							<!--<th width="100">申请时间</th>-->
							<th width="80">部门名称</th>
							<th width="80">状态</th>
							<th width="140">操作</th>
						</tr>
						</thead>
						<tbody id="empListBody">
						<!--
			            	{x2;tree:$certificates['data'],certificate,cid}
			            	<tr>
			                    <td>{x2;v:certificate['ceqid']}</td>
			                    <td><img src="{x2;v:certificate['ceqinfo']['photo']}" width="120"/></td>
			                    <td>{x2;v:certificate['ceqinfo']['usertruename']}</td>
			                    <td>
			                        {x2;v:certificate['ceqinfo']['username']}
			                    </td>
			                    <td>
			                        {x2;v:certificate['ceqinfo']['usersex']}
			                    </td>
			                    <td>
			                        {x2;v:certificate['ceqinfo']['userdegree']}
			                    </td>
			                    <td>
			                        {x2;v:certificate['ceqinfo']['userphone']}<br />{x2;v:certificate['ceqinfo']['useraddress']}
			                    </td>
			                    <td>
			                    	{x2;date:v:certificate['ceqtime'],'Y-m-d'}
			                    </td>
			                    <td>
			                    	{x2;$status[v:certificate['ceqstatus']]}
			                    </td>
			                    <td class="actions">
			                    	<div class="btn-group">
			                    		<a class="btn ajax" href="index.php?certificate-master-certificate-modifyqueue&ceqid={x2;v:certificate['ceqid']}&status=1" title="设为已受理"><em class="glyphicon glyphicon-random"></em></a>
			                    		<a class="btn ajax" href="index.php?certificate-master-certificate-modifyqueue&ceqid={x2;v:certificate['ceqid']}&status=2" title="设为已出证"><em class="glyphicon glyphicon-ok"></em></a>
										<a class="btn ajax" href="index.php?certificate-master-certificate-modifyqueue&ceqid={x2;v:certificate['ceqid']}&status=3" title="设为被驳回"><em class="glyphicon glyphicon-remove"></em></a>
			                    	</div>
			                    </td>
			                </tr>
			                {x2;endtree}
			                -->

						{x2;tree:$certificates,certificate,cid}
						<tr>
							<td>{x2;v:certificate['id']}</td>
							<td>{x2;v:certificate['emp_code']}</td>
							<td>
								{x2;v:certificate['idcard']}
							</td>
							<td>
								{x2;v:certificate['sex']}
							</td>
							<td>
								{x2;v:certificate['tel']}
							</td>
							<td>
								{x2;v:certificate['dname']}
							</td>
							<td class="cerStatus">
								{x2;v:certificate['status']}
							</td>
							<td class="actions" style="text-align: center;">
								<div class="btn-group">
									<span class="btn ajax btn-primary handleCer" href="javascript:;"  usrid="{x2;v:certificate['id']}" title="发证">
									   发证
									</span>
								</div>
							</td>
						</tr>
						{x2;endtree}


						</tbody>
					</table>
					<ul class="pagination pull-right">
						{x2;$certificates['pages']}
					</ul>
				</div>
			</div>
			{x2;if:!$userhash}
		</div>
	</div>
</div>
{x2;include:footer}
</body>
<script>
    $(function(){
        $.ajax({
            type:"get",
            url:'index.php?certificate-master-certificate-getDepts',
            success:function(res){
                var resObj = JSON.parse(res);
                var allDeptStr = '<option value="-1">全部</option>';
                for(var i=0;i<resObj.length;i++){
                    allDeptStr += '<option value="'+resObj[i]['id']+'">'+resObj[i]['dname']+'</option>';
                }
                $('#deptName').html(allDeptStr);
            },
            error:function(){
                toast('网络错误');
            }
        })

        $('td.cerStatus').each(function() {
            if ($.trim($(this).text()) == '已发') {
                $(this).next().find('span').removeClass('handleCer btn-primary');
                $(this).next().find('span').addClass('btn-default');
            }
        })

        $(document).keydown(function(e) {
            if (e.keyCode == 13) {
            }
        })

        var ceId = {x2;$ceid}; //证书id
        $(document).on('click','.handleCer',function(){  //发证处理
            var uid = $(this).attr('usrid');

            $.ajax({
                type:"get",
                url:'index.php?certificate-master-certificate-issueCer&id='+uid+'&cerId='+ceId,
                success:function(res){
                   // console.log(res);
                    var resObj = JSON.parse(res);
                    if(resObj['status']==1){
                        toast(resObj['msg']);
                        setTimeout(function(){
                            window.location.reload();
						},800)

                    }else{
                        toast(resObj['msg']);
                    }
                },
                error:function(){
                    toast('网络错误');
                }
            })
        })

        function toast(txt, fun) { //吐丝提示
            $('.toast').remove();
            var div = $('<div style="background: #333333;max-width: 85%;min-height: 77px;min-width: 270px;position: absolute;left: -1000px;top: -1000px;text-align: center;border-radius:10px;"><span style="color: #ffffff;line-height: 77px;font-size: 23px;">' + txt + '</span></div>');
            $('body').append(div);
            div.css('zIndex', 9999999);
            div.css('left', parseInt(($(window).width() - div.width()) / 2));
            var top = parseInt($(window).scrollTop() + ($(window).height() - div.height()) / 2);
            div.css('top', top);
            setTimeout(function () {
                div.remove();
                if (fun) {
                    fun();
                }
            }, 2000);
        }


        $('.submit-s').on('click',function (event) { //搜索提交按钮
            event.preventDefault();
            var data = new Object();
            data.ceid = ceId
            if($('#empName').val()) data.empName = $('#empName').val();
            if($('#sex-s').val()) data.sexNs = $('#sex-s').val();
            if($('#telphpone').val())  data.telphpone = $('#telphpone').val();
            if($('#deptName').val()) data.groupname = $('#deptName').find("option:selected").text();;
            //console.log(data);
            $.ajax({
                type:"get",
                url:'index.php?certificate-master-certificate-conditionQuery',
                data:data,
                success:function(res){
                    // console.log(res);
                    var resObj = JSON.parse(res);
                    if(resObj['status']==1){
                        if(resObj['emplist'].length<1){
                            toast('没查到记录');
                            setTimeout(function (){
                                    window.location.reload();
								},
								1000)

                        }else{
                            var bodyList = '';
                            for(var i=0;i<resObj['emplist'].length;i++){
                                var curO = resObj['emplist'][i];
                                bodyList +='<tr>';
                                bodyList +='<td>'+curO["id"]+'</td>';
                                bodyList +='<td>'+curO["name"]+'</td>';
                                bodyList +='<td>'+curO['idcard']+'</td>';
                                bodyList +='<td>'+curO['sex']+'</td>';
                                bodyList +='<td>'+curO['tel']+'</td>';
                                bodyList +='<td>'+curO['dname']+'</td>';
                                bodyList +='<td>'+(curO['status']?curO['status']:'')+'</td>';
                                bodyList +='<td class="actions" style="text-align: center;">';
                                if(curO['status']=='已发'){
                                    bodyList +='<div class="btn-group"><span class="btn ajax btn-default" usrid="'+curO['id']+'" href="javascript:;" title="发证">发证</span></div>';
                                }else{
                                    bodyList +='<div class="btn-group"><span class="btn ajax btn-primary handleCer" usrid="'+curO['id']+'" href="javascript:;" title="发证">发证</span></div>';
                                }

                                bodyList +='</td>';
                                bodyList +='</tr>';
                            }
                            $('#empListBody').html(bodyList);
                        }
                    }else{
                        window.location.reload();
                    }
                },
                error:function(){
                    toast('网络错误');
                }
            })
        });
    })
</script>
</html>
{x2;endif}