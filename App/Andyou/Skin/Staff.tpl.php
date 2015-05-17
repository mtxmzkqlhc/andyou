<?= $header ?>
<?= $navi ?>

<div class="row-fluid">
    <div id="content" class="span12">         
        <h3 class="page-title">员工管理</h3>
        <div class="row-fluid sortable">		
            <div class="box span12">
                <div class="box-header" data-original-title>
                	  <h2><i class="halflings-icon align-justify"></i><span class="break"></span>员工管理</h2>
				     <button data-toggle="modal" role="button" href="#add-box" class="btn-addArea big-addbtn" type="button"> 添加员工</button>
                </div>
                <div class="box-content">
                	 <!-- 搜索 -->
				     <div class="row-fluid">
<form id="serform" method="get">
姓名：<input type="hidden" value="Staff" name="c">
<input style="width:100px;height:25px;" class="spanmalt10" type="text" value="<?=$sername?>" name="name" placeholder="姓名">
<select name="cateId">
    <option value="0">所有分类</option>
    <?php
    if($staffCate){
        foreach($staffCate as $k => $v){
            $seled = $k == $sercateId ? "selected" : "";
            echo "<option value='{$k}' {$seled}>{$v}</option>";
        }
        
    }?>
</select>
<button type="submit" class="btn-ser">查看</button></form></div>
				    
				    <!-- 列表 -->
                    <table class="table table-center table-striped table-bordered bootstrap-datatable ">
                     <thead>
<tr>
<th>ID</th><th>姓名</th><th>入职时间</th><th>生日</th><th>分类</th><th>底薪</th><th>提成比例</th><th>操作</th>
</tr>
</thead>
<tbody>
<?php
if($data) {
   foreach($data as $v) {
       $outStr = '<tr>';
       $outStr.='<td>'.$v['id'].'</td>';
       $outStr.='<td>'.$v['name'].'</td>';
       $outStr.='<td>'.$v['ryear'].'/'.$v['rmonth'].'/'.$v['rday'].'</td>';
       $outStr.='<td>'.$v['byear'].'/'.$v['bmonth'].'/'.$v['bday'].'</td>';
       $outStr.='<td>'.(isset($staffCate[$v['cateId']]) ? $staffCate[$v['cateId']] : '').'</td>';
       $outStr.='<td>'.$v['salary'].'</td>';
       $outStr.='<td>'.$v['percentage'].'</td>';
       $outStr.='<td rel="'.$v['id'].'">
       <a title="修改" class="btn btn-info editbtnStaff"><i class="halflings-icon white edit"></i></a>
       <a title="删除" class="btn btn-danger delbtn"><i class="halflings-icon white trash"></i></a></td>';
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
				
                          <tr><td align="right">姓名:</td><td><input type="text"   name="name" /></td></tr>
                          <tr><td align="right">入职时间:</td><td>
                              <input type="text" name="ryear"  style="width:60px" /> 年 <input type="text" name="rmonth"  style="width:60px" /> 月 <input type="text" name="rday"  style="width:60px" /> 日
                              </td></tr>
                          <tr><td align="right">生日:</td><td><input type="text" name="byear"  style="width:60px" /> 年 <input type="text" name="bmonth"  style="width:60px" /> 月 <input type="text" name="bday"  style="width:60px" /> 日</td></tr>

                          <tr><td align="right">分类:</td><td>
                               <select name="cateId"><option value='0'>请选择</option>
                                <?php
                                if ($staffCate) {
                                       foreach ($staffCate as $k=>$v) {
                                           echo '<option value="' . $k . '">' . $v . '</option>' . "\n";
                                       } 
                                   }
                                 ?>
                           </select> 
                              </td></tr>
                          <tr><td align="right">底薪:</td><td><input type="text"   name="salary" /></td></tr>
                          <tr><td align="right">提成比例:</td><td><input type="text"   name="percentage" /></td></tr>

                         </tbody></table></td></tr></tbody></table>
                         <input type="hidden" name="a" value="AddItem">
                         <input type="hidden" name="c" value="Staff">
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
                          <tr><td align="right">姓名:</td><td><input type="text" id="name"  name="name" /></td></tr>
                          <tr><td align="right">入职时间:</td><td>
                                  <input type="text" id="ryear" name="ryear" style="width:60px" /> 年 <input type="text" id="rmonth"  name="rmonth"  style="width:60px" /> 月 <input type="text" id="rday"  name="rday"  style="width:60px" /> 日
                         </td></tr>
                          
                         <tr><td align="right">生日:</td><td><input type="text" id="byear" name="byear" style="width:60px" /> 年 <input type="text" id="bmonth"  name="bmonth"  style="width:60px" /> 月 <input type="text" id="bday"  name="bday"  style="width:60px" /> 日</td></tr>

                          <tr><td align="right">分类:</td><td>
                              <select id="cateId" name="cateId"><option value='0'>请选择</option>
                                <?php
                                if ($staffCate) {
                                       foreach ($staffCate as $k=>$v) {
                                           echo '<option value="' . $k . '">' . $v . '</option>' . "\n";
                                       } 
                                   }
                                 ?>
                                </select> 
                              
                              </td></tr>
                          <tr><td align="right">底薪:</td><td><input type="text" id="salary"  name="salary" /></td></tr>
                          <tr><td align="right">提成比例:</td><td><input type="text" id="percentage"  name="percentage" /></td></tr>

                        </tbody></table></td></tr></tbody></table>
				    <input type="hidden" id="dataid" name="dataid" value="">
                    <input type="hidden" name="a" value="UpItem">
                    <input type="hidden" name="c" value="Staff">
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
        <input type="hidden" value="Staff" name="c"/>
		<input type="hidden" name="pageUrl" value="<?=$pageUrl?>">
    </form>
</div>

<?= $footer ?>
<script>

var bkUrl = '<?=$pageUrl?>';

$('.editbtnStaff').live('click',function(){
    var id = $(this).parent().attr('rel');
    getdatainfo(id,'Staff',function(dat){
        $('#name').val(dat['name']);
        $('#inDate').val(dat['inDate']);
        $('#byear').val(dat['byear']);
        $('#bmonth').val(dat['bmonth']);
        $('#bday').val(dat['bday']);
        $('#ryear').val(dat['ryear']);
        $('#rmonth').val(dat['rmonth']);
        $('#rday').val(dat['rday']);
        $('#cateId').val(dat['cateId']);
        $('#salary').val(dat['salary']);
        $('#percentage').val(dat['percentage']);
    }); 
 });


//-------------------------------
//用一个按钮修改一种状态的后台
//-------------------------------
var setDataValue = function(id,col,val){
    
   var postArr = {'id':id,'col':col,'val':val};
   $.ajax({
        type: "POST",
        url: "/?c=Staff&a=SetValue",
        data: postArr,
        success:function(){
           document.location=bkUrl?bkUrl:'?c=Staff';
        }
   });
}

</script>
</body>
</html>

