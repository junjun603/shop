<style>
	#bg{
		position: fixed;
		top:0;
		left:0;
		right:0;
		bottom:0;
		background: rgba(0, 0, 0, 0.5);
		z-index: 998;
	}
	.waiting_animate{
		width:32px;
		height:32px;
		background:url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIzMiIgaGVpZ2h0PSIzMiIgdmlld0JveD0iMCAwIDMyIDMyIj48cGF0aCBmaWxsPSIjMEI5MUQ0IiBkPSJNMTYgMGgtLjk5M2MtLjU1MiAwLS43NzIgMS40MzUuNzcyIDEuMTA0aDEuMzIzYzcuMDYzIDAgMTIuNjkgNi40IDEyLjY5IDE0LjM0NSAwIDcuOTQzLTUuNjI4IDE0LjM0NC0xMi42OSAxNC4zNDQtNy4wNjIgMC0xMi42OS02LjQtMTIuNjktMTQuMzQ1IDAtMS4zMjUuMTEtMi41NC40NC0zLjc1Mi41NTMtMS41NDUtLjQ0LTIuNTM4LTEuNTQzLTIuODctLjY2Mi0uMjItMS41NDQtLjExLTEuOTg2Ljg4MkMuNDQgMTEuNTg2IDAgMTMuNzkzIDAgMTZjMCA4LjgyOCA3LjE3MiAxNiAxNiAxNnMxNi03LjE3MiAxNi0xNlMyNC44MjggMCAxNiAweiIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyIvPjwvc3ZnPg==) no-repeat;
		transform-origin: center;
		animation: rotate 0.8s linear infinite;
		position: absolute;
		top: 50%;
		left: 50%;
	}
	@keyframes rotate{
		0%{
			transform: rotate(0);
		}
		50%{
			transform:rotate(180deg);
		}
		100%{
			transform: rotate(360deg);
		}
	}

</style>
<div class="container-fluid box" style="margin:0px auto;padding-top:10px;overflow:visible">
	<div class="row-fluid">
		<div class="main">
			<div class="col-xs-3">
				<h1 style="font-size:42px;color:#337AB7;"><img src="app/core/styles/img/logo2.png" style="height:60px;margin-top:-10px;"/></h1>
			</div>
			<div class="col-xs-1">
			</div>
			<div class="col-xs-6" style="padding-top:22px;">
				<div class="form-inline">
					<div class="input-group">
						<div class="input-group-btn">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								新闻 <span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								<li><a href="index.php?content-app">新闻</a></li>
								<li><a href="index.php?course-app">课程</a></li>
								<li><a href="index.php?docs-app">资料</a></li>
							</ul>
						</div>
						<input type="text" style="width:380px;" class="form-control" id="keyword" placeholder="搜索新闻">
					</div>
					<button type="button" class="btn btn-primary" onclick="javascript:window.location='index.php?content-app-search&keyword='+$('#keyword').val();"> 搜 索 </button>
				</div>
			</div>
			<div class="col-xs-2" style="padding-top:22px;">
				<ul class="list-unstyled list-inline">
					<?php if($this->tpl_var['_user']['userid']){ ?>
					<li>
						<div class="btn-group">
							<button type="button" class="btn btn-info"><em class="glyphicon glyphicon-user"></em> <?php echo $this->tpl_var['_user']['username']; ?></button>
							<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<ul class="dropdown-menu">
								<li><a href="index.php?user-center"><em class="glyphicon glyphicon-user"></em> 用户中心</a></li>
								<?php if($this->tpl_var['_user']['teacher_subjects']){ ?><li><a href="index.php?exam-teach"><em class="glyphicon glyphicon-book"></em> 教师管理</a></li><?php } ?>
								<?php if($this->tpl_var['_user']['groupid'] == 1){ ?><li><a href="index.php?core-master"><em class="glyphicon glyphicon-dashboard"></em> 后台管理</a></li><?php } ?>
								<li><a class="ajax" href="index.php?user-app-logout"><em class="glyphicon glyphicon-log-out"></em> 退出</a></li>
							</ul>
						</div>
					</li>
					<?php } else { ?>
					   <?php if($this->tpl_var['flag']==1){ ?>
						<li><a href="javascript:;" id="firstLogin" onclick="javascript:$.loginbox.show(1,<?php echo $this->tpl_var['username']; ?>,'<?php echo $this->tpl_var['password']; ?>');" class="btn btn-default"> 登 录 </a></li>
					<?php } else { ?>
						<li><a href="javascript:;" id="firstLogin" onclick="javascript:$.loginbox.show();" class="btn btn-default"> 登 录 </a></li>
					<?php } ?>
					<!--
					<li><a href="index.php?user-app-register" class="btn btn-default"> 注 册 </a></li>
					-->
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="container-fluid navbar" style="margin-top:0px;margin-bottom:0px;padding-top:10px;background-color:#337AB7;">
	<div class="row-fluid">
		<div class="main">
			<div class="col-xs-12">
				<ul class="list-unstyled list-inline">
					<li class="menu col-xs-1 active"><a href="index.php" class="icon">首页</a></li>
					<li class="menu col-xs-1"><a href="index.php?course">课程</a></li>
					<li class="menu col-xs-1"><a href="index.php?exam">考试</a></li>
					<li class="menu col-xs-1"><a href="index.php?docs">百科</a></li>
					<li class="menu col-xs-1"><a href="index.php?certificate">证书</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<script>
	$(function(){
	    if('<?php echo $this->tpl_var['flag']; ?>'==1){ //sap登录
             $('#firstLogin').click();
             //  console.log($('.forUsername').val()) $('.forPassword').val()
             $('body').append('<div id="bg"><div class="waiting_animate"></div></div>');
			setTimeout(function(){
                $('#bg').remove();
                $('#sapLogin').click();
               // window.location.href = "http://"+document.domain;
                var url = document.URL,
                    URL;
                var num = url.indexOf('?');
                if (num){
                    URL = url.substring(0,num);
                    history.pushState(null,null,URL);
                }
			},500)
        }

	})

</script>