<?= $header ?>
<?= $navi ?>

<div class="row-fluid">
    <div id="content" class="span12">         
        <h3 class="page-title">积分记录</h3>
        <div class="row-fluid sortable">		
            <div class="box span12">
                <div class="box-header" data-original-title>
                	  <h2><i class="halflings-icon align-justify"></i><span class="break"></span>积分记录</h2>
                </div>
                <div class="box-content">
                	 <!-- 搜索 -->
				     <div class="row-fluid">
<form id="serform" method="get">
<input type="hidden" value="LogScoreChange" name="c">
会员电话：<input style="width:120px;" class="spanmalt10" type="text" value="<?=$memberPhone?>" name="memberPhone" placeholder="">
会员姓名：<input style="width:120px;" class="spanmalt10" type="text" value="<?=$memberNm?>" name="memberNm" placeholder="">
<button type="submit" class="btn-ser">查看</button></form></div>
				    
				    <!-- 列表 -->
                    <table class="table table-center table-striped table-bordered bootstrap-datatable ">
                     <thead>
<tr>
<th>会员名</th><th>会员手机</th><th>变化积分</th><th>原积分</th><th>备注</th><th>订单号</th><th>时间</th><th>操作人</th>
</tr>
</thead>
<tbody>
<?php
if($data) {
   foreach($data as $v) {
       $memberId = $v['memberId'];
       $memInfo = Helper_Member::getMemberInfo(array('id'=>$memberId));
       $outStr = '<tr>';
       $outStr.='<td>'.$memInfo["name"].'</td>';
       $outStr.='<td>'.$memInfo["phone"].'</td>';
       $outStr.='<td>'.($v['direction']?"<font color='blue'>-</font>":"+");
       $outStr.=' '.$v['score'].'</td>';
       $outStr.='<td>'.$v['orgScore'].'</td>';
       $outStr.='<td>'.$v['remark'].'</td>';
       $outStr.='<td>'.$v['bno'].'</td>';
       $outStr.='<td>'.date("Y-m-d H:i",$v['dateTm']).'</td>';
       $outStr.='<td>'.$v['adminer'].'&nbsp;</td>';
       $outStr.='</tr>';
       echo $outStr;
   }
} ?>
</tbody>
                     
                    </table>
                    
                    <!-- 分页 -->
					<?php if ($pageBar) { ?>
                    <div class="row-fluid"><div class="span12 center"><div class="dataTables_paginate paging_bootstrap pagination"><ul><?=$pageBar?></ul></div></div></div>
					<?php } ?>

					
                </div>
            </div>

        </div>

    </div>
</div>

<div class="modal hide fade" id="add-box" style="width:760px;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">添加数据</h4>
            </div>
            <div class="modal-body">
                <form id="addform" method="post" action="?">
                    <table>
                        <tbody><tr><td> <table class="item_edit_table"> <tbody>						
                          <tr><td align="right">会员ID:</td><td><input type="text"   name="memberId" /></td></tr>
                          <tr><td align="right">加/减:</td><td><input type="text"   name="direction" /></td></tr>
                          <tr><td align="right">变化的分数:</td><td><input type="text"   name="score" /></td></tr>
                          <tr><td align="right">原来的分数:</td><td><input type="text"   name="orgScore" /></td></tr>

                         </tbody></table></td></tr></tbody></table>
                         <input type="hidden" name="a" value="AddItem">
                         <input type="hidden" name="c" value="LogScoreChange">
						 <input type="hidden" name="pageUrl" value="<?=$pageUrl?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" id="addbtn">添加</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal hide fade" id="edit-box" style="width:760px;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">编辑数据</h4>
            </div>
            <div class="modal-body">
                <form id="editform" method="post" action="?">
                    <table>
                        <tbody><tr><td> <table class="item_edit_table"><tbody>
                          <tr><td align="right">会员ID:</td><td><input type="text" id="memberId"  name="memberId" /></td></tr>
                          <tr><td align="right">加/减:</td><td><input type="text" id="direction"  name="direction" /></td></tr>
                          <tr><td align="right">变化的分数:</td><td><input type="text" id="score"  name="score" /></td></tr>
                          <tr><td align="right">原来的分数:</td><td><input type="text" id="orgScore"  name="orgScore" /></td></tr>
                          <tr><td align="right">时间:</td><td><input type="text" id="dateTm"  name="dateTm" /></td></tr>
                          <tr><td align="right">操作人:</td><td><input type="text" id="adminer"  name="adminer" /></td></tr>
                          <tr><td align="right">备注:</td><td><input type="text" id="remark"  name="remark" /></td></tr>
                          <tr><td align="right">订单号:</td><td><input type="text" id="bno"  name="bno" /></td></tr>

                          
                        </tbody></table></td></tr></tbody></table>
				    <input type="hidden" id="dataid" name="dataid" value="">
                    <input type="hidden" name="a" value="UpItem">
                    <input type="hidden" name="c" value="LogScoreChange">
					<input type="hidden" name="pageUrl" value="<?=$pageUrl?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" id="savebtn">保存</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="del-box" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4>删除</h4>
    </div>
    <div class="modal-body" >
        此操作不可逆,确定要删除数据吗?
    </div>
    <form id="delform" method="post" action="?">
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            <a class="btn btn-danger" target="_self" id="delbtn">删除</a>
        </div>
        <input type="hidden" id="deldataid" name="dataid" value="">
        <input type="hidden" value="DelItem" name="a"/>
        <input type="hidden" value="LogScoreChange" name="c"/>
		<input type="hidden" name="pageUrl" value="<?=$pageUrl?>">
    </form>
</div>

<?= $footer ?>
<script>

var bkUrl = '<?=$pageUrl?>';

$('.editbtnLogScoreChange').live('click',function(){
    var id = $(this).parent().attr('rel');
    getdatainfo(id,'LogScoreChange',function(dat){
        $('#memberId').val(dat['memberId']);
        $('#direction').val(dat['direction']);
        $('#score').val(dat['score']);
        $('#orgScore').val(dat['orgScore']);
        $('#dateTm').val(dat['dateTm']);
        $('#adminer').val(dat['adminer']);
        $('#remark').val(dat['remark']);
        $('#bno').val(dat['bno']);
    }); 
 });


//-------------------------------
//用一个按钮修改一种状态的后台
//-------------------------------
var setDataValue = function(id,col,val){
    
   var postArr = {'id':id,'col':col,'val':val};
   $.ajax({
        type: "POST",
        url: "/?c=LogScoreChange&a=SetValue",
        data: postArr,
        success:function(){
           document.location=bkUrl?bkUrl:'?c=LogScoreChange';
        }
   });
}

</script>
</body>
</html>

