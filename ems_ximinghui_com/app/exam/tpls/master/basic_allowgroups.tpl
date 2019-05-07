<style>
	.pagination-right ul li{
		list-style: none;
		display: inline-block;
		width: 20px;
		height: 20px;
		margin: 0 3px;
	}

	.pagination-right ul li:first-child,.pagination-right ul li:last-child{
		list-style: none;
		display: inline-block;
		border: none;
		margin: 0 3px;
	}
	.pagination-right ul li:last-child{
		width: 100px;
	}

	.pagination-right ul li a{
		border: 1px solid #d5d5d5;;
		width: 100%;
		height: 100%;
		display: inline-block;
		text-align: center;
		line-height: 20px;
		border-radius: 4px;
	}
</style>
<script type="text/javascript">
	    	function selectexams(o,d){
	    		d = $('#'+d);
	    		s = d.val();
	    		if(s == '')s= ',';
	    		else
	    		s = ','+s+',';
	    		if($(o).is(':checked')){
					if(s.indexOf(','+$(o).val()+',') < 0){
						s = s+$(o).val()+',';
						s = s.substring(1,s.length-1);
					}
				}
				else{
					if(s.indexOf(','+$(o).val()+',') >= 0){
						var t = eval('/,'+$(o).val()+',/');
						s = s.replace(t,',');
						s = s.substring(1,s.length-1);
					}
				}
				if(s == ',' || s == ',,')s = '';
				d.val(s);
	    	}

	    	function markSelectedExams(n,o)
	    	{
	    		$("[name='"+n+"']").each(function(){if((','+$('#'+o).val()+',').indexOf(','+$(this).val()+',') >= 0)$(this).attr('checked',true);});
	    	}

	    	function selectall(obj,a){
	    		$(".sbox").prop('checked', $(obj).is(':checked'));
	    		$(".sbox").each(function(){
	    			selectexams(this,a);
	    		});
	    	}
	    	</script>
	        <table class="table table-hover table-bordered">
				<thead>
					<tr class="info">
	                    <th>ID</th>
				        <th>角色名</th>
	                </tr>
	            </thead>
	            <tbody>
                    {x2;tree:$actors['data'],actor,aid}
			        <tr>
						<td>
							<input rel="1" class="sbox" type="checkbox" name="ids[]" value="{x2;v:actor['groupid']}" onclick="javascript:selectexams(this,'{x2;$target}')"/>
						</td>
						<td>
							{x2;v:actor['groupname']}
						</td>
			        </tr>
			        {x2;endtree}
	        	</tbody>
	        </table>
	        <div class="pagination pagination-right">
	            <ul>{x2;$actors['pages']}</ul>
	        </div>
	        <script type="text/javascript">
	    		jQuery(function($) {
					markSelectedExams('ids[]','{x2;$target}');

					// $('.pagination-right ul li').each(function(){
					// 	$(this).on('click',function(){
					// 		$(this).siblings('li').css('background','#fff');
					// 		$(this).siblings('li').css('color','#333');
					// 		$(this).css('background','#125eab');
					// 		$(this).css('color','#fff');
					//
					// 	})
					// })

	    		});
	    	</script>