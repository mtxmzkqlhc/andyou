<?= $header ?>
<?= $navi ?>

<div class="row-fluid">
    <div id="content" class="span12">         
        <h3 class="page-title">����¼</h3>
        <div class="row-fluid sortable">		
            <div class="box span12">
                <div class="box-header" data-original-title>
                	  <h2><i class="halflings-icon align-justify"></i><span class="break"></span>����¼</h2>
                </div>
                <div class="box-content">
                	 <!-- ���� -->
				     <div class="row-fluid">
<form id="serform" method="get">
<input type="hidden" value="LogInStorage" name="c">
<select name="cateId">
    <option value="0">���з���</option>
    <?php
    if($proCateArr){
        foreach($proCateArr as $k => $v){
            $seled = $k == $sercateId ? "selected" : "";
            echo "<option value='{$k}' {$seled}>{$v}</option>";
        }
        
    }?>
</select>
��Ʒ����<input style="width:120px;"  type="text" value="<?=$sername?>" name="name">
���룺<input style="width:120px;"  type="text" value="<?=$sercode?>" name="code">
 ʱ�䣺<input style="width:90px;" class="datePlugin" type="text" value="<?=$startTime?>" name="startTime" >                           
 ����<input style="width:90px;" class="datePlugin" type="text" value="<?=$endTime?>" name="endTime" > 

<button type="submit" class="btn-ser">�鿴</button></form></div>
				    
				    <!-- �б� -->
                    <table class="table table-center table-striped table-bordered bootstrap-datatable ">
                     <thead>
<tr>
<th>��ƷID</th><th>��Ʒ��</th><th>��Ʒ����</th><th>�������</th><th>ԭʼ�������</th><th>���ʱ��</th>
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
       <!--<a title="�޸�" class="btn btn-info editbtnLogInStorage"><i class="halflings-icon white edit"></i></a>
       <a title="ɾ��" class="btn btn-danger delbtn"><i class="halflings-icon white trash"></i></a>-->
       </td>';
       $outStr.='</tr>';*/
       echo $outStr;
   }
} ?>
</tbody>
                     
                    </table>
                    
                    <!-- ��ҳ -->
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
                <h4 class="modal-title">�������</h4>
            </div>
            <div class="modal-body">
                <form id="addform" method="post" action="?">
                    <table>
                        <tbody><tr><td> <table class="item_edit_table"> <tbody>						
                          <tr><td align="right">��ƷID:</td><td><input type="text"   name="proId" /></td></tr>
                          <tr><td align="right">����Ա:</td><td><input type="text"   name="adminer" /></td></tr>
                          <tr><td align="right">ְԱID:</td><td><input type="text"   name="staffid" /></td></tr>
                          <tr><td align="right">����ʱ��:</td><td><input type="text"   name="dateTm" /></td></tr>

                         </tbody></table></td></tr></tbody></table>
                         <input type="hidden" name="a" value="AddItem">
                         <input type="hidden" name="c" value="LogInStorage">
						 <input type="hidden" name="pageUrl" value="<?=$pageUrl?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">�ر�</button>
                <button type="button" class="btn btn-primary" id="addbtn">���</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal hide fade" id="edit-box" style="width:760px;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">�༭����</h4>
            </div>
            <div class="modal-body">
                <form id="editform" method="post" action="?">
                    <table>
                        <tbody><tr><td> <table class="item_edit_table"><tbody>
                          <tr><td align="right">��ƷID:</td><td><input type="text" id="proId"  name="proId" /></td></tr>
                          <tr><td align="right">����Ա:</td><td><input type="text" id="adminer"  name="adminer" /></td></tr>
                          <tr><td align="right">ְԱID:</td><td><input type="text" id="staffid"  name="staffid" /></td></tr>
                          <tr><td align="right">����ʱ��:</td><td><input type="text" id="dateTm"  name="dateTm" /></td></tr>
                          <tr><td align="right">���ԭʼ����:</td><td><input type="text" id="orgNum"  name="orgNum" /></td></tr>
                          <tr><td align="right">��������:</td><td><input type="text" id="addNum"  name="addNum" /></td></tr>
                          <tr><td align="right">��Ʒ����:</td><td><input type="text" id="cateId"  name="cateId" /></td></tr>
                          <tr><td align="right">��Ʒ��:</td><td><input type="text" id="name"  name="name" /></td></tr>

                          
                        </tbody></table></td></tr></tbody></table>
				    <input type="hidden" id="dataid" name="dataid" value="">
                    <input type="hidden" name="a" value="UpItem">
                    <input type="hidden" name="c" value="LogInStorage">
					<input type="hidden" name="pageUrl" value="<?=$pageUrl?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">�ر�</button>
                <button type="button" class="btn btn-primary" id="savebtn">����</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="del-box" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4>ɾ��</h4>
    </div>
    <div class="modal-body" >
        �˲���������,ȷ��Ҫɾ��������?
    </div>
    <form id="delform" method="post" action="?">
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">�ر�</button>
            <a class="btn btn-danger" target="_self" id="delbtn">ɾ��</a>
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
//��һ����ť�޸�һ��״̬�ĺ�̨
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
/*���ڿ���*/
$(".datePlugin").datepicker({
    dateFormat: "yy-mm-dd"
});
</script>
</body>
</html>

