<?= $header ?>
<?= $navi ?>

<div class="row-fluid">
    <div id="content" class="span12">         
        <h3 class="page-title">��������</h3>
        <div class="row-fluid sortable">		
            <div class="box span12">
                <div class="box-header" data-original-title>
                	  <h2><i class="halflings-icon align-justify"></i><span class="break"></span><?=$isAddUser?"��ӻ�Ա":"��������"?></h2>
				     <!-- <button data-toggle="modal" role="button" href="#add-box" class="btn-addArea big-addbtn" type="button"> �������</button> -->
                </div>
                <div class="box-content">
                	 <!-- ���� -->
				     <div class="row-fluid">
<form id="serform" method="get">
<input type="hidden" value="Bills" name="c">
<input type="hidden" value="<?=$isAddUser?>" name="isAddUser">
����:<input style="width:100px;height:25px;;" class="spanmalt10" type="text" value="<?=$serbno?>" name="bno" placeholder="����">
<select name="staffid">
    <option value="0">����Ա��</option>
    <?php
    if($staffInfo){
        foreach($staffInfo as $k => $v){
            $seled = $k == $serstaffid ? "selected" : "";
            echo "<option value='{$k}' {$seled}>{$v}</option>";
        }
        
    }?>
</select>
��ԱID:<input style="width:100px;height:25px;" class="spanmalt10" type="text" value="<?=$sermemberPhone?>" name="memberPhone" placeholder="��ԱID">
<button type="submit" class="btn-ser">�鿴</button></form></div>
				    
				    <!-- �б� -->
                    <table class="table table-center table-striped table-bordered bootstrap-datatable ">
                     <thead>
<tr>
<th>����</th><th>��Ʒ�ܼ�</th><th>�ۿ�</th>
<?php if(!$isAddUser){?>
 <th>ʹ�����</th>
<?php }?>
<th>��ȡ���</th><th>����Ա</th><th>����ʱ��</th>
<?php if(!$isAddUser){?>
<th>��ԱID</th>
<?php }?>
<th>��ע</th><th>����</th>
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
       //$outStr.='<td style="text-align:left;">'.$v['useScore'].($v['useScore'] ? " <span style='color:#999999'>(".$v['useScoreAsMoney']."Ԫ)</span>" : "").'</td>';//
       $outStr.='<td>'.round($v['orgPrice']/100,2).'</td>';
       $outStr.='<td>'.$v['discount'].'</td>';
      if(!$isAddUser){
            $outStr.='<td>'.$v['useCard'].'</td>';
      }
       if($v['priceTrue']){//�������Ա�޸��˼۸񣬼�¼
           $outStr.='<td style="color:red;font-weight:bold" title="����Ա�޸��˼۸�ԭ�ۣ�'.round($v['priceTrue']/100).'">'.round($v['price']/100).'</td>';
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
         $outStr.='<td><a title="'.$remark.'" onclick="alert(\''.$remark.'\')" href="javascript:void(0);">�б�ע</a></td>';
       }else{
            $outStr.='<td>&nbsp;</td>';
       }
       $outStr.='<td rel="'.$v['id'].'" align="left" style="text-align:left;">';
       if(empty($v['memberId']) && $isAddUser){
            $outStr.='<a title="����û�" class="btn btn-info" href="?c=Member&a=ToAddUserFromBill&bid='.$v['id'].'&bno='.$v['bno'].'" target="_blank" style="color:#ffffff">����û�</a>';
       }
       $outStr.='<!-- <a title="�޸�" class="btn btn-info editbtnBills"><i class="halflings-icon white edit"></i></a> -->
       <a title="������ϸ" class="btn btn-info" href="?c=BillsItem&bno='.$v['bno'].'" target="_blank"><i class="halflings-icon white  th-list"></i></a>
       <!-- <a title="ɾ��" class="btn btn-danger delbtn"><i class="halflings-icon white trash"></i></a> --></td>';
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
                          <tr><td align="right">����:</td><td><input type="text"   name="bno" /></td></tr>
                          <tr><td align="right">ʹ�û���:</td><td><input type="text"   name="useScore" /></td></tr>
                          <tr><td align="right">ʹ�ÿ������:</td><td><input type="text"   name="useCard" /></td></tr>
                          <tr><td align="right">�տ�:</td><td><input type="text"   name="price" /></td></tr>
                          <tr><td align="right">�ۿ�:</td><td><input type="text"   name="discount" /></td></tr>
                          <tr><td align="right">Ա��ID:</td><td><input type="text"   name="staffid" /></td></tr>
                          <tr><td align="right">Ա����:</td><td><input type="text"   name="staffName" /></td></tr>
                          <tr><td align="right">��ԱID:</td><td><input type="text"   name="memberId" /></td></tr>

                         </tbody></table></td></tr></tbody></table>
                         <input type="hidden" name="a" value="AddItem">
                         <input type="hidden" name="c" value="Bills">
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
                          <tr><td align="right">����:</td><td><input type="text" id="bno"  name="bno" /></td></tr>
                          <tr><td align="right">ʹ�û���:</td><td><input type="text" id="useScore"  name="useScore" /></td></tr>
                          <tr><td align="right">ʹ�ÿ������:</td><td><input type="text" id="useCard"  name="useCard" /></td></tr>
                          <tr><td align="right">�տ�:</td><td><input type="text" id="price"  name="price" /></td></tr>
                          <tr><td align="right">�ۿ�:</td><td><input type="text" id="discount"  name="discount" /></td></tr>
                          <tr><td align="right">Ա��ID:</td><td><input type="text" id="staffid"  name="staffid" /></td></tr>
                          <tr><td align="right">Ա����:</td><td><input type="text" id="staffName"  name="staffName" /></td></tr>
                          <tr><td align="right">��ԱID:</td><td><input type="text" id="memberId"  name="memberId" /></td></tr>

                          
                        </tbody></table></td></tr></tbody></table>
				    <input type="hidden" id="dataid" name="dataid" value="">
                    <input type="hidden" name="a" value="UpItem">
                    <input type="hidden" name="c" value="Bills">
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
//��һ����ť�޸�һ��״̬�ĺ�̨
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

