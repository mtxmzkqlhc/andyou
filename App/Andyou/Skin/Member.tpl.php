<?= $header ?>
<?= $navi ?>
<style>
#addCheckExs{padding:3px 4px;margin-bottom:10px;}
</style>
<div class="row-fluid">
    <div id="content" class="span12">         
        <h3 class="page-title">��Ա����</h3>
        <div class="row-fluid sortable">		
            <div class="box span12">
                <div class="box-header" data-original-title>
                	  <h2><i class="halflings-icon align-justify"></i><span class="break"></span>��Ա����</h2>
				     <button data-toggle="modal" role="button" href="#add-box" class="btn-addArea big-addbtn" type="button"> ��ӻ�Ա</button>
                </div>
                <div class="box-content">
                	 <!-- ���� -->
				     <div class="row-fluid">
<form id="serform" method="get">
<input type="hidden" value="Member" name="c">
������<input style="width:100px;height:25px;" class="spanmalt10" type="text" value="<?=$sername?>" name="name">
�ֻ��ţ�<input style="width:120px;height:25px;" class="spanmalt10" type="text" value="<?=$serphone?>" name="phone">
<select name="cateId">
    <option value="0">���з���</option>
    <?php
    if($memberCate){
        foreach($memberCate as $k => $v){
            $seled = $k == $sercateId ? "selected" : "";
            echo "<option value='{$k}' {$seled}>{$v}</option>";
        }
        
    }?>
</select>
<button type="submit" class="btn-ser">�鿴</button></form></div>
				    
				    <!-- �б� -->
                    <table class="table table-center table-striped table-bordered bootstrap-datatable ">
                     <thead>
<tr>
<th>����</th><th>�ֻ���</th><th>����</th><th>����</th><th>����</th><th>���</th><th>������</th><th>���ʱ��</th><th>����</th>
</tr>
</thead>
<tbody>
<?php
if($data) {
   foreach($data as $v) {
       $outStr = '<tr>';
       $outStr.='<td>'.$v['name'].'</td>';
       $outStr.='<td>'.$v['phone'].'</td>';
       $outStr.='<td>'.(isset($memberCate[$v['cateId']]) ? $memberCate[$v['cateId']] : '').'</td>';
       $outStr.='<td>'.$v['byear'].'/'.$v['bmonth'].'/'.$v['bday'].'</td>';
       $outStr.='<td>'.$v['score'].'</td>';
       $outStr.='<td>'.$v['balance'].'</td>';
       $outStr.='<td>'.$v['allsum'].'</td>';
       $outStr.='<td>'.date("Y-m-d",$v['addTm']).'</td>';
           
       $outStr.='<td rel="'.$v['id'].'">
       <a title="�޸�" class="btn btn-info editbtnMember"><i class="halflings-icon white edit"></i></a>
       <a title="�޸Ļ���" class="btn btn-info btnUpScore" style="color:#ffffff;" data-mid="'.$v['id'].'" data-score="'.$v['score'].'">���ֵ���</a>
       <a title="�޸����" class="btn btn-info btnUpCard" style="color:#ffffff;" data-mid="'.$v['id'].'" data-card="'.$v['balance'].'">��Ա��ֵ</a>
       <!-- <a title="ɾ��" class="btn btn-danger delbtn"><i class="halflings-icon white trash"></i></a>��--></td>';
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
                <h4 class="modal-title">��ӻ�Ա</h4>
            </div>
            <div class="modal-body">
                <form id="addform" method="post" action="?">
                    <table>
                        <tbody><tr><td> <table class="item_edit_table"> <tbody>
                        
                          <tr><td align="right">�ֻ���:</td><td><input type="text" name="phone" id="add_phone"/>
                              <span class="btn btn-mini" title="��֤�Ƿ����" id="addCheckExs"><i class="halflings-icon search white"></i></span>
                          </td></tr>
                          <tr><td align="right">����:</td><td><input type="text" name="name" id="add_name"/></td></tr>
                          <tr><td align="right">����:</td><td>
                          <select name="cateId" id="add_cateId"><option value='0'>��ѡ��</option>
                                <?php
                                if ($memberCate) {
                                       foreach ($memberCate as $k=>$v) {
                                           echo '<option value="' . $k . '">' . $v . '</option>' . "\n";
                                       } 
                                   }
                                 ?>
                           </select>    
                          </td></tr>
                          <tr><td align="right">����:</td><td><input type="text" name="byear"  id="add_byear"  style="width:60px" /> �� <input type="text" name="bmonth" id="add_bmonth"  style="width:60px" /> �� <input type="text" name="bday" id="add_bday" style="width:60px" /> ��</td></tr>
                          <tr><td align="right">����:</td><td><input type="text"   name="score"  id="add_score" value='0' /></td></tr>
                          <tr><td align="right">�����:</td><td><input type="text"   name="balance"  id="add_balance" value='0'/></td></tr>
                          <tr><td align="right">��ע:</td><td><textarea  name="remark"  style="width:350px;height:50px"></textarea></td></tr>
                          <tr><td align="right">�������ֻ���:</td><td><input type="text" name="introducer"  id="add_introducer"/></td></tr>


                         </tbody></table></td></tr></tbody></table>
                         <input type="hidden" name="a" value="AddItem">
                         <input type="hidden" name="c" value="Member">
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
                          <tr><td align="right">�ֻ���:</td><td><input type="text" id="phone"  name="phone" /></td></tr>
                          <tr><td align="right">����:</td><td><input type="text" id="name"  name="name" /></td></tr>
                          <tr><td align="right">����:</td><td>
                              <select id="cateId" name="cateId"><option value='0'>��ѡ��</option>
                                <?php
                                if ($memberCate) {
                                       foreach ($memberCate as $k=>$v) {
                                           echo '<option value="' . $k . '">' . $v . '</option>' . "\n";
                                       } 
                                   }
                                 ?>
                                </select>                              
                              </td></tr>
                          <tr><td align="right">����:</td><td><input type="text" id="byear" name="byear" style="width:60px" /> �� <input type="text" id="bmonth"  name="bmonth"  style="width:60px" /> �� <input type="text" id="bday"  name="bday"  style="width:60px" /> ��</td></tr>
                         <!-- <tr><td align="right">����:</td><td><input type="text" id="score"  name="score"/></td></tr>
                          <tr><td align="right">�����:</td><td><input type="text" id="balance"  name="balance" /></td></tr>-->
                          <tr><td align="right">��ע:</td><td><textarea id="remark"  name="remark" style="width:350px;height:50px"></textarea></td></tr>
                          <tr><td align="right">�������ֻ���:</td><td><input type="text" id="introducer"  name="introducer" /></td></tr>
                          
                        </tbody></table></td></tr></tbody></table>
				    <input type="hidden" id="dataid" name="dataid" value="">
                    <input type="hidden" name="a" value="UpItem">
                    <input type="hidden" name="c" value="Member">
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


<div class="modal hide fade" id="edit-box2"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:600px;font-size:12px">
        <div class="modal-content">
            <div class="modal-body">
                <form id="editform" method="post" action="?" onsubmit="return checkUpScore()">
                    <table>
                        <tbody><tr><td> <table class="item_edit_table"><tbody>
                          <tr><td align="right">�û���ǰ����:</td><td id="memNowScore" style="font-weight: bold;color:green;line-height:35px;"></td></tr>
                          <tr><td align="right">����/����:</td><td><select name="direction" id="us_direction"><option value="1">����</option><option value="0">����</option></select></td></tr>
                          <tr><td align="right">����:</td><td><input type="text" id="us_score"  name="score"/></td></tr>
                          <tr><td align="right">��ע:</td><td><textarea id="us_remark"  name="remark" style="width:350px;height:50px"></textarea></td></tr>

                        </tbody></table></td></tr></tbody></table>
                    <input type="hidden" name="mid" value="0" id="us_mid" >
                    <input type="hidden" name="us_allscore" value="0" id="us_allscore" >
                    <input type="hidden" name="a" value="UpScore">
                    <input type="hidden" name="c" value="Member">
					<input type="hidden" name="pageUrl" value="<?=$pageUrl?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="savebtn">ȷ���޸�</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal hide fade" id="edit-box4"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:600px;font-size:12px">
        <div class="modal-content">
            <div class="modal-body">
                <form id="editform" method="post" action="?"  onsubmit="return checkUpCard()">
                    <table>
                        <tbody><tr><td> <table class="item_edit_table"><tbody>
                          <tr><td align="right">�û���ǰ���:</td><td id="memNowCard" style="font-weight: bold;color:green;line-height:35px;"></td></tr>
                          <tr><td align="right">����/����:</td><td><select name="direction" id="uc_direction"><option value="0">����</option><option value="1">����</option></select></td></tr>
                          <tr><td align="right">���:</td><td><input type="text" id="uc_score"  name="card"/></td></tr>
                          <tr><td align="right">��ע:</td><td><textarea id="uc_remark"  name="remark" style="width:350px;height:50px"></textarea></td></tr>

                        </tbody></table></td></tr></tbody></table>
                    <input type="hidden" name="mid" value="0" id="uc_mid" >
                    <input type="hidden" name="uc_allcard" value="0" id="uc_allcard" >
                    <input type="hidden" name="a" value="UpCard">
                    <input type="hidden" name="c" value="Member">
					<input type="hidden" name="pageUrl" value="<?=$pageUrl?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="savebtn">ȷ���޸�</button>
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
        <input type="hidden" value="Member" name="c"/>
		<input type="hidden" name="pageUrl" value="<?=$pageUrl?>">
    </form>
</div>

<?= $footer ?>
<script>

var bkUrl = '<?=$pageUrl?>';

$('.editbtnMember').live('click',function(){
    var id = $(this).parent().attr('rel');
    getdatainfo(id,'Member',function(dat){
        $('#name').val(dat['name']);
        $('#phone').val(dat['phone']);
        $('#cateId').val(dat['cateId']);
        $('#byear').val(dat['byear']);
        $('#bmonth').val(dat['bmonth']);
        $('#bday').val(dat['bday']);
        $('#score').val(dat['score']);
        $('#balance').val(dat['balance']);
        $('#remark').html(dat['remark']);
        $('#introducer').val(dat['introducer']);
    }); 
 });


//-------------------------------
//��һ����ť�޸�һ��״̬�ĺ�̨
//-------------------------------
var setDataValue = function(id,col,val){
    
   var postArr = {'id':id,'col':col,'val':val};
   $.ajax({
        type: "POST",
        url: "/?c=Member&a=SetValue",
        data: postArr,
        success:function(){
           document.location=bkUrl?bkUrl:'?c=Member';
        }
   });
}

$(".btnUpScore").click(function(){
    //data-mid="'.$v['id'].'" data-score
    var mid   = $(this).attr("data-mid");
    var score = $(this).attr("data-score");
    $("#us_score").val(score);
    $("#us_mid").val(mid);
    $("#memNowScore").html(score);
    $("#us_allscore").val(score);
    $("#us_score").focus();
    art.dialog({title: '�����޸�',width:"600px",content: $("#edit-box2").html()});
});
$(".btnUpCard").click(function(){
    //data-mid="'.$v['id'].'" data-score
    var mid   = $(this).attr("data-mid");
    var score = $(this).attr("data-card");
    $("#uc_card").val(score);
    $("#uc_mid").val(mid);
    $("#memNowCard").html(score);
    $("#uc_allcard").val(score);
    
    art.dialog({title: '��Ա������޸�',width:"600px",content: $("#edit-box4").html()});
});
var checkUpCard = function(){
    if($("#uc_direction").val()==1){
        if($("#uc_allcard").val() - $("#uc_score").val() < 0){
            alert("�û��������Կ۳�");
            return false;
        }
    }
    return true;
}
var checkUpScore = function(){
    if($("#us_direction").val()==1){
        if($("#us_allscore").val() - $("#us_score").val() < 0){
            alert("�û��Ļ��ֲ����Կ۳�");
            return false;
        }
    }
    return true;
    
}
var checkHasOneToAdd = function(){
    doSearchMember($("#add_phone").val(),function(d){
        if(d){
            $("#add_name").val(d.name);
            $("#add_cateId").val(d.cateId);
            $("#add_byear").val(d.byear);
            $("#add_bmonth").val(d.bmonth);
            $("#add_bday").val(d.bday);
            $("#add_balance").val(d.balance);
            $("#add_remark").val(d.remark);
            $("#add_score").val(d.score);
            alert("�û�Ա�Ѿ����ڣ�������ӣ�");
        }
    });
}

$("#addCheckExs").click(function(){
    checkHasOneToAdd();
});
$("#add_phone").blur(function(){
    checkHasOneToAdd();
});

//�����û�
var doSearchMember = function(phone,func){
    
    if(phone){
        var t = Date.parse(new Date()); 
        var url = "?c=Ajax_Member&a=GetMemberByPhone&phone=" + phone+"&t="+t;
        $.getJSON(url,{},function(data){
            if(data){                
                func(data);
            }
        });
    }else{
        return false;;
    }
}
</script>
</body>
</html>

