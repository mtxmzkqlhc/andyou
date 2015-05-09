<?= $header ?>
<?= $navi ?>

<div class="row-fluid">
    <div id="content" class="span12">         
        <h3 class="page-title">订单管理</h3>
        <div class="row-fluid sortable">		
            <div class="box span12">
                <div class="box-header" data-original-title>
                	  <h2><i class="halflings-icon align-justify"></i><span class="break"></span><?=$isAddUser?"添加会员":"订单管理"?></h2>
				     <!-- <button data-toggle="modal" role="button" href="#add-box" class="btn-addArea big-addbtn" type="button"> 添加数据</button> -->
                </div>
                <div class="box-content">
                	 <!-- 搜索 -->
				     <div class="row-fluid">
<form id="serform" method="get">
<input type="hidden" value="Bills" name="c">
<input type="hidden" value="<?=$isAddUser?>" name="isAddUser">
单号:<input style="width:100px;height:25px;;" class="spanmalt10" type="text" value="<?=$serbno?>" name="bno" placeholder="单号">
<select name="staffid">
    <option value="0">所有员工</option>
    <?php
    if($staffInfo){
        foreach($staffInfo as $k => $v){
            $seled = $k == $serstaffid ? "selected" : "";
            echo "<option value='{$k}' {$seled}>{$v}</option>";
        }
        
    }?>
</select>
会员ID:<input style="width:100px;height:25px;" class="spanmalt10" type="text" value="<?=$sermemberPhone?>" name="memberPhone" placeholder="会员ID">
<button type="submit" class="btn-ser">查看</button></form></div>
				    
				    <!-- 列表 -->
                    <table class="table table-center table-striped table-bordered bootstrap-datatable ">
                     <thead>
<tr>
<th>单号</th><th>商品总价</th><th>折扣</th>
<?php if(!$isAddUser){?>
 <th>使用余额</th>
<?php }?>
<th>收取金额</th><th>销售员</th><th>消费时间</th>
<?php if(!$isAddUser){?>
<th>会员ID</th>
<?php }?>
<th>备注</th><th>操作</th>
</tr>
</thead>
<tbody>
<?php
if($data) {
   foreach($data as $v) {
       $memName = "-";
       if($v['memberId']){
           $memInfo = Helper_Member::getMemberInfo(array("id"=>$v['memberId']));
           $memName = $memInfo["name"];
       }
       $outStr = '<tr>';
       $outStr.='<td>'.$v['bno'].'</td>';
       //$outStr.='<td style="text-align:left;">'.$v['useScore'].($v['useScore'] ? " <span style='color:#999999'>(".$v['useScoreAsMoney']."元)</span>" : "").'</td>';//
       $outStr.='<td>'.round($v['orgPrice']/100,2).'</td>';
       $outStr.='<td>'.$v['discount'].'</td>';
      if(!$isAddUser){
            $outStr.='<td>'.$v['useCard'].'</td>';
      }
       if($v['priceTrue']){//如果销售员修改了价格，记录
           $outStr.='<td style="color:red;font-weight:bold" title="销售员修改了价格，原价：'.round($v['priceTrue']/100).'">'.round($v['price']/100).'</td>';
       }else{
          $outStr.='<td>'.round($v['price']/100).'</td>';
       }
       $outStr.='<td>'.(isset($staffInfo[$v['staffid']]) ? $staffInfo[$v['staffid']] : '-').'</td>';
       $outStr.='<td>'.date("m-d H:i",$v['tm']).'</td>';
       if(!$isAddUser){
            $outStr.='<td>'.$memName.'</td>';
       }
       if($v['remark']){
         $remark = str_replace(array("'",'"'), "", $v['remark']);
         $outStr.='<td><a title="'.$remark.'" onclick="alert(\''.$remark.'\')" href="javascript:void(0);">有备注</a></td>';
       }else{
            $outStr.='<td>&nbsp;</td>';
       }
       $outStr.='<td rel="'.$v['id'].'" align="left" style="text-align:left;">';
       if(empty($v['memberId']) && $isAddUser){
            $outStr.='<a title="添加用户" class="btn btn-info" href="?c=Member&a=ToAddUserFromBill&bid='.$v['id'].'&bno='.$v['bno'].'" target="_blank" style="color:#ffffff">添加用户</a>';
       }
       $outStr.='<!-- <a title="修改" class="btn btn-info editbtnBills"><i class="halflings-icon white edit"></i></a> -->
       <a title="订单明细" class="btn btn-info" href="?c=BillsItem&bno='.$v['bno'].'" target="_blank"><i class="halflings-icon white  th-list"></i></a>
       <!-- <a title="删除" class="btn btn-danger delbtn"><i class="halflings-icon white trash"></i></a> --></td>';
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
                          <tr><td align="right">单号:</td><td><input type="text"   name="bno" /></td></tr>
                          <tr><td align="right">使用积分:</td><td><input type="text"   name="useScore" /></td></tr>
                          <tr><td align="right">使用卡内余额:</td><td><input type="text"   name="useCard" /></td></tr>
                          <tr><td align="right">收款:</td><td><input type="text"   name="price" /></td></tr>
                          <tr><td align="right">折扣:</td><td><input type="text"   name="discount" /></td></tr>
                          <tr><td align="right">员工ID:</td><td><input type="text"   name="staffid" /></td></tr>
                          <tr><td align="right">员工名:</td><td><input type="text"   name="staffName" /></td></tr>
                          <tr><td align="right">会员ID:</td><td><input type="text"   name="memberId" /></td></tr>

                         </tbody></table></td></tr></tbody></table>
                         <input type="hidden" name="a" value="AddItem">
                         <input type="hidden" name="c" value="Bills">
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
                          <tr><td align="right">单号:</td><td><input type="text" id="bno"  name="bno" /></td></tr>
                          <tr><td align="right">使用积分:</td><td><input type="text" id="useScore"  name="useScore" /></td></tr>
                          <tr><td align="right">使用卡内余额:</td><td><input type="text" id="useCard"  name="useCard" /></td></tr>
                          <tr><td align="right">收款:</td><td><input type="text" id="price"  name="price" /></td></tr>
                          <tr><td align="right">折扣:</td><td><input type="text" id="discount"  name="discount" /></td></tr>
                          <tr><td align="right">员工ID:</td><td><input type="text" id="staffid"  name="staffid" /></td></tr>
                          <tr><td align="right">员工名:</td><td><input type="text" id="staffName"  name="staffName" /></td></tr>
                          <tr><td align="right">会员ID:</td><td><input type="text" id="memberId"  name="memberId" /></td></tr>

                          
                        </tbody></table></td></tr></tbody></table>
				    <input type="hidden" id="dataid" name="dataid" value="">
                    <input type="hidden" name="a" value="UpItem">
                    <input type="hidden" name="c" value="Bills">
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
        <input type="hidden" value="Bills" name="c"/>
		<input type="hidden" name="pageUrl" value="<?=$pageUrl?>">
    </form>
</div>

<?= $footer ?>
<script>

var bkUrl = '<?=$pageUrl?>';

$('.editbtnBills').live('click',function(){
    var id = $(this).parent().attr('rel');
    getdatainfo(id,'Bills',function(dat){
        $('#bno').val(dat['bno']);
        $('#useScore').val(dat['useScore']);
        $('#useCard').val(dat['useCard']);
        $('#price').val(dat['price']);
        $('#discount').val(dat['discount']);
        $('#staffid').val(dat['staffid']);
        $('#staffName').val(dat['staffName']);
        $('#memberId').val(dat['memberId']);
    }); 
 });


//-------------------------------
//用一个按钮修改一种状态的后台
//-------------------------------
var setDataValue = function(id,col,val){
    
   var postArr = {'id':id,'col':col,'val':val};
   $.ajax({
        type: "POST",
        url: "/?c=Bills&a=SetValue",
        data: postArr,
        success:function(){
           document.location=bkUrl?bkUrl:'?c=Bills';
        }
   });
}

</script>
</body>
</html>

