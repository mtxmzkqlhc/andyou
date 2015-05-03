<?= $header ?>
<?= $navi ?>

<div class="row-fluid">
    <div id="content" class="span12">         
        <h3 class="page-title">订单明细管理</h3>
        <div class="row-fluid sortable">		
            <div class="box span12">
                <div class="box-header" data-original-title>
                	  <h2><i class="halflings-icon align-justify"></i><span class="break"></span>订单明细管理</h2>
				     <button data-toggle="modal" role="button" href="#add-box" class="btn-addArea big-addbtn" type="button"> 添加数据</button>
                </div>
                <div class="box-content">
                	 <!-- 搜索 -->
				     <div class="row-fluid">
<form id="serform" method="get">
<input type="hidden" value="BillsItem" name="c">
订单ID：<input style="width:100px;height:25px;" class="spanmalt10" type="text" value="<?=$serbid?>" name="bid" placeholder="订单ID">
单号：<input style="width:100px;height:25px;" class="spanmalt10" type="text" value="<?=$serbno?>" name="bno" placeholder="单号">
产品ID：<input style="width:100px;height:25px;" class="spanmalt10" type="text" value="<?=$serproId?>" name="proId" placeholder="产品ID">
员工：<input style="width:100px;height:25px;" class="spanmalt10" type="text" value="<?=$serstaffid?>" name="staffid" placeholder="员工">
会员：<input style="width:100px;height:25px;" class="spanmalt10" type="text" value="<?=$sermemberId?>" name="memberId" placeholder="会员">
<button type="submit" class="btn-ser">查看</button></form></div>
				    
				    <!-- 列表 -->
                    <table class="table table-center table-striped table-bordered bootstrap-datatable ">
                     <thead>
<tr>
<th>单号</th><th>产品</th><th>原价</th><th>数量</th><th>折扣</th><th>售价</th><th>销售员</th><th>会员</th><th>消费时间</th><th>操作</th>
</tr>
</thead>
<tbody>
<?php
if($data) {
   $proArr = array();
   foreach($data as $v) {
       $memName = "-";
       if($v['memberId']){
           $memInfo = Helper_Member::getMemberInfo(array("id"=>$v['memberId']));
           $memName = $memInfo["name"];
       }
       //获得产品信息
       $proId = (int)$v['proId'];
       $proName = "";
       $proPrice = 0;
       if(!isset($proArr[$proId])){
           $proArr[$proId] = Helper_Product::getProductInfo(array('id'=>$proId));
       }
       if( $proArr[$proId]){
           $proName = $proArr[$proId]["name"];
           $proPrice = $proArr[$proId]["price"];
       }
       
       $outStr = '<tr>';
       $outStr.='<td algin="left">'.$v['bno'].'</td>';
       $outStr.='<td style="text-align:left">'.$proName.'</td>';
       $outStr.='<td>'.($proPrice).'</td>';
       $outStr.='<td>'.$v['num'].'</td>';
       $outStr.='<td>'.$v['discount'].'</td>';
       $outStr.='<td>'.($v['price']/100).'</td>';
       $outStr.='<td>'.(isset($staffInfo[$v['staffid']]) ? $staffInfo[$v['staffid']] : '-').'</td>';
       $outStr.='<td>'.$memName.'</td>';
       $outStr.='<td>'.date("Y-m-d H:i",$v['tm']).'</td>';
       $outStr.='<td rel="'.$v['id'].'">
       <a title="修改" class="btn btn-info editbtnBillsItem"><i class="halflings-icon white edit"></i></a>
       </td>';
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
                          <tr><td align="right">订单ID:</td><td><input type="text"   name="bid" /></td></tr>
                          <tr><td align="right">单号:</td><td><input type="text"   name="bno" /></td></tr>
                          <tr><td align="right">产品ID:</td><td><input type="text"   name="proId" /></td></tr>
                          <tr><td align="right">数量:</td><td><input type="text"   name="num" /></td></tr>
                          <tr><td align="right">折扣:</td><td><input type="text"   name="discount" /></td></tr>
                          <tr><td align="right">价格:</td><td><input type="text"   name="price" /></td></tr>
                          <tr><td align="right">员工:</td><td><input type="text"   name="staffid" /></td></tr>
                          <tr><td align="right">会员:</td><td><input type="text"   name="memberId" /></td></tr>

                         </tbody></table></td></tr></tbody></table>
                         <input type="hidden" name="a" value="AddItem">
                         <input type="hidden" name="c" value="BillsItem">
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
                          <tr><td align="right">订单ID:</td><td><input type="text" id="bid"  name="bid" /></td></tr>
                          <tr><td align="right">单号:</td><td><input type="text" id="bno"  name="bno" /></td></tr>
                          <tr><td align="right">产品ID:</td><td><input type="text" id="proId"  name="proId" /></td></tr>
                          <tr><td align="right">数量:</td><td><input type="text" id="num"  name="num" /></td></tr>
                          <tr><td align="right">折扣:</td><td><input type="text" id="discount"  name="discount" /></td></tr>
                          <tr><td align="right">价格:</td><td><input type="text" id="price"  name="price" /></td></tr>
                          <tr><td align="right">员工:</td><td><input type="text" id="staffid"  name="staffid" /></td></tr>
                          <tr><td align="right">会员:</td><td><input type="text" id="memberId"  name="memberId" /></td></tr>

                          
                        </tbody></table></td></tr></tbody></table>
				    <input type="hidden" id="dataid" name="dataid" value="">
                    <input type="hidden" name="a" value="UpItem">
                    <input type="hidden" name="c" value="BillsItem">
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
        <input type="hidden" value="BillsItem" name="c"/>
		<input type="hidden" name="pageUrl" value="<?=$pageUrl?>">
    </form>
</div>

<?= $footer ?>
<script>

var bkUrl = '<?=$pageUrl?>';

$('.editbtnBillsItem').live('click',function(){
    var id = $(this).parent().attr('rel');
    getdatainfo(id,'BillsItem',function(dat){
        $('#bid').val(dat['bid']);
        $('#bno').val(dat['bno']);
        $('#proId').val(dat['proId']);
        $('#num').val(dat['num']);
        $('#discount').val(dat['discount']);
        $('#price').val(dat['price']);
        $('#staffid').val(dat['staffid']);
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
        url: "/?c=BillsItem&a=SetValue",
        data: postArr,
        success:function(){
           document.location=bkUrl?bkUrl:'?c=BillsItem';
        }
   });
}

</script>
</body>
</html>

