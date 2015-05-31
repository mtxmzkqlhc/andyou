<?= $header ?>
<?= $navi ?>

<div class="row-fluid">
    <div id="content" class="span12">         
        <h3 class="page-title">入库记录</h3>
        <div class="row-fluid sortable">		
            <div class="box span12">
                <div class="box-header" data-original-title>
                	  <h2><i class="halflings-icon align-justify"></i><span class="break"></span>入库记录</h2>
                </div>
                <div class="box-content">
                	 <!-- 搜索 -->
				     <div class="row-fluid">
<form id="serform" method="get">
<input type="hidden" value="LogInStorage" name="c">
<select name="cateId">
    <option value="0">所有分类</option>
    <?php
    if($proCateArr){
        foreach($proCateArr as $k => $v){
            $seled = $k == $sercateId ? "selected" : "";
            echo "<option value='{$k}' {$seled}>{$v}</option>";
        }
        
    }?>
</select>
产品名：<input style="width:120px;"  type="text" value="<?=$sername?>" name="name">
条码：<input style="width:120px;"  type="text" value="<?=$sercode?>" name="code">
 时间：<input style="width:90px;" class="datePlugin" type="text" value="<?=$startTime?>" name="startTime" >                           
 至：<input style="width:90px;" class="datePlugin" type="text" value="<?=$endTime?>" name="endTime" > 

<button type="submit" class="btn-ser">查看</button></form></div>
				    
				    <!-- 列表 -->
                    <table class="table table-center table-striped table-bordered bootstrap-datatable ">
                     <thead>
<tr>
<th>产品ID</th><th>产品名</th><th>产品子类</th><th>入库数量</th><th>原始库存数量</th><th>入库时间</th>
</tr>
</thead>
<tbody>
<?php
if($data) {
   foreach($data as $v) {
       $outStr = '<tr>';
       $outStr.='<td>'.$v['proId'].'</td>';
       $outStr.='<td>'.$v['name'].'</td>';
       $outStr.='<td>'.$proCateArr[$v['cateId']].'</td>';
       $outStr.='<td>'.$v['addNum'].'</td>';
       $outStr.='<td>'.$v['orgNum'].'</td>';
       $outStr.='<td>'.date("Y-m-d H:i",$v['dateTm']).'</td>';
       /*$outStr.='<td rel="'.$v['id'].'">
       <!--<a title="修改" class="btn btn-info editbtnLogInStorage"><i class="halflings-icon white edit"></i></a>
       <a title="删除" class="btn btn-danger delbtn"><i class="halflings-icon white trash"></i></a>-->
       </td>';
       $outStr.='</tr>';*/
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
                          <tr><td align="right">产品ID:</td><td><input type="text"   name="proId" /></td></tr>
                          <tr><td align="right">管理员:</td><td><input type="text"   name="adminer" /></td></tr>
                          <tr><td align="right">职员ID:</td><td><input type="text"   name="staffid" /></td></tr>
                          <tr><td align="right">更新时间:</td><td><input type="text"   name="dateTm" /></td></tr>

                         </tbody></table></td></tr></tbody></table>
                         <input type="hidden" name="a" value="AddItem">
                         <input type="hidden" name="c" value="LogInStorage">
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
                          <tr><td align="right">产品ID:</td><td><input type="text" id="proId"  name="proId" /></td></tr>
                          <tr><td align="right">管理员:</td><td><input type="text" id="adminer"  name="adminer" /></td></tr>
                          <tr><td align="right">职员ID:</td><td><input type="text" id="staffid"  name="staffid" /></td></tr>
                          <tr><td align="right">更新时间:</td><td><input type="text" id="dateTm"  name="dateTm" /></td></tr>
                          <tr><td align="right">库存原始数量:</td><td><input type="text" id="orgNum"  name="orgNum" /></td></tr>
                          <tr><td align="right">增加数量:</td><td><input type="text" id="addNum"  name="addNum" /></td></tr>
                          <tr><td align="right">产品子类:</td><td><input type="text" id="cateId"  name="cateId" /></td></tr>
                          <tr><td align="right">产品名:</td><td><input type="text" id="name"  name="name" /></td></tr>

                          
                        </tbody></table></td></tr></tbody></table>
				    <input type="hidden" id="dataid" name="dataid" value="">
                    <input type="hidden" name="a" value="UpItem">
                    <input type="hidden" name="c" value="LogInStorage">
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
        <input type="hidden" value="LogInStorage" name="c"/>
		<input type="hidden" name="pageUrl" value="<?=$pageUrl?>">
    </form>
</div>

<?= $footer ?>
<script>

var bkUrl = '<?=$pageUrl?>';

$('.editbtnLogInStorage').live('click',function(){
    var id = $(this).parent().attr('rel');
    getdatainfo(id,'LogInStorage',function(dat){
        $('#proId').val(dat['proId']);
        $('#adminer').val(dat['adminer']);
        $('#staffid').val(dat['staffid']);
        $('#dateTm').val(dat['dateTm']);
        $('#orgNum').val(dat['orgNum']);
        $('#addNum').val(dat['addNum']);
        $('#cateId').val(dat['cateId']);
        $('#name').val(dat['name']);
    }); 
 });


//-------------------------------
//用一个按钮修改一种状态的后台
//-------------------------------
var setDataValue = function(id,col,val){
    
   var postArr = {'id':id,'col':col,'val':val};
   $.ajax({
        type: "POST",
        url: "/?c=LogInStorage&a=SetValue",
        data: postArr,
        success:function(){
           document.location=bkUrl?bkUrl:'?c=LogInStorage';
        }
   });
}
/*日期开启*/
$(".datePlugin").datepicker({
    dateFormat: "yy-mm-dd"
});
</script>
</body>
</html>

