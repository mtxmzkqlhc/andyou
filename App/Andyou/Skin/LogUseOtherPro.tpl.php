<?= $header ?>
<?= $navi ?>

<div class="row-fluid">
    <div id="content" class="span12">         
        <h3 class="page-title">������Ʒ���Ѽ�¼</h3>
        <div class="row-fluid sortable">		
            <div class="box span12">
                <div class="box-header" data-original-title>
                	  <h2><i class="halflings-icon align-justify"></i><span class="break"></span>������Ʒ���Ѽ�¼</h2>
                </div>
                <div class="box-content">
                	 <!-- ���� -->
				     <div class="row-fluid">
<form id="serform" method="get">
<input type="hidden" value="LogUseOtherPro" name="c">
��Ա��<input style="width:120px;" type="text" value="<?=$member?>" name="member">
<button type="submit" class="btn-ser">�鿴</button></form></div>
				    
				    <!-- �б� -->
                    <table class="table table-center table-striped table-bordered bootstrap-datatable ">
                     <thead>
                        <tr>
                        <th>��Ա</th><th>����</th><th>����</th><th>֮ǰ����ֵ</th><th>ʱ��</th><th>��Ʒ����</th><th>�����Ա</th><th>����ID</th><th>��ע</th><th>����</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if($data) {
                           foreach($data as $v) {
                               $memInfo = Helper_Member::getMemberInfo(array('id' => $v['memberId'] ));
                               $outStr = '<tr>';
                               $outStr.='<td>'.($memInfo ? $memInfo["name"] : "").'</td>';
                               $outStr.='<td>'.$v['name'].'</td>';
                               $outStr.='<td>'.($v['direction']?"<font color='blue'>-</font> ":"+ ");
                               $outStr.= $v['cvalue'].'</td>';
                               $outStr.='<td>'.$v['orgcvalue'].'</td>';
                               $outStr.='<td>'.date("Y-m-d H:i",$v['dateTm']).'</td>';
                               $outStr.='<td>'.$proCtypeArr[$v['ctype']]['name'].'</td>';
                               $outStr.='<td>'.$staffArr[$v['staffid']].'</td>';
                               $outStr.='<td>'.$v['bno'].'</td>';
                               $outStr.='<td>'.$v['remark'].'</td>';
                               $outStr.='<td rel="'.$v['id'].'">
                               <a title="�޸�" class="btn btn-info editbtnLogUseOtherPro"><i class="halflings-icon white edit"></i></a>
                               <a title="ɾ��" class="btn btn-danger delbtn"><i class="halflings-icon white trash"></i></a></td>';
                               $outStr.='</tr>';
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
                          <tr><td align="right">��ԱID:</td><td><input type="text"   name="memberId" /></td></tr>
                          <tr><td align="right">��Ʒ��ID:</td><td><input type="text"   name="otherproId" /></td></tr>
                          <tr><td align="right">���� 1 ���� 0 ��:</td><td><input type="text"   name="direction" /></td></tr>
                          <tr><td align="right">��������:</td><td><input type="text"   name="cvalue" /></td></tr>

                         </tbody></table></td></tr></tbody></table>
                         <input type="hidden" name="a" value="AddItem">
                         <input type="hidden" name="c" value="LogUseOtherPro">
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
                          <tr><td align="right">��ԱID:</td><td><input type="text" id="memberId"  name="memberId" /></td></tr>
                          <tr><td align="right">��Ʒ��ID:</td><td><input type="text" id="otherproId"  name="otherproId" /></td></tr>
                          <tr><td align="right">���� 1 ���� 0 ��:</td><td><input type="text" id="direction"  name="direction" /></td></tr>
                          <tr><td align="right">��������:</td><td><input type="text" id="cvalue"  name="cvalue" /></td></tr>
                          <tr><td align="right">֮ǰ����ֵ:</td><td><input type="text" id="orgcvalue"  name="orgcvalue" /></td></tr>
                          <tr><td align="right">ʱ��:</td><td><input type="text" id="dateTm"  name="dateTm" /></td></tr>
                          <tr><td align="right">��Ʒ����:</td><td><input type="text" id="ctype"  name="ctype" /></td></tr>

                          
                        </tbody></table></td></tr></tbody></table>
				    <input type="hidden" id="dataid" name="dataid" value="">
                    <input type="hidden" name="a" value="UpItem">
                    <input type="hidden" name="c" value="LogUseOtherPro">
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
        <input type="hidden" value="LogUseOtherPro" name="c"/>
		<input type="hidden" name="pageUrl" value="<?=$pageUrl?>">
    </form>
</div>

<?= $footer ?>
<script>

var bkUrl = '<?=$pageUrl?>';

$('.editbtnLogUseOtherPro').live('click',function(){
    var id = $(this).parent().attr('rel');
    getdatainfo(id,'LogUseOtherPro',function(dat){
        $('#memberId').val(dat['memberId']);
        $('#otherproId').val(dat['otherproId']);
        $('#direction').val(dat['direction']);
        $('#cvalue').val(dat['cvalue']);
        $('#orgcvalue').val(dat['orgcvalue']);
        $('#dateTm').val(dat['dateTm']);
        $('#ctype').val(dat['ctype']);
    }); 
 });


//-------------------------------
//��һ����ť�޸�һ��״̬�ĺ�̨
//-------------------------------
var setDataValue = function(id,col,val){
    
   var postArr = {'id':id,'col':col,'val':val};
   $.ajax({
        type: "POST",
        url: "/?c=LogUseOtherPro&a=SetValue",
        data: postArr,
        success:function(){
           document.location=bkUrl?bkUrl:'?c=LogUseOtherPro';
        }
   });
}

</script>
</body>
</html>

