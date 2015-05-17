<?= $header ?>
<?= $navi ?>
<style>
#addCheckExs{padding:3px 4px;margin-bottom:10px;}
</style>
<div class="row-fluid">
    <div id="content" class="span12">         
        <h3 class="page-title">会员管理</h3>
        <div class="row-fluid sortable">		
            <div class="box span12">
                <div class="box-header" data-original-title>
                	  <h2><i class="halflings-icon align-justify"></i><span class="break"></span>会员管理</h2>
				     <button data-toggle="modal" role="button" href="#add-box" class="btn-addArea big-addbtn" type="button"> 添加会员</button>
                </div>
                <div class="box-content">
                	 <!-- 搜索 -->
				     <div class="row-fluid">
<form id="serform" method="get">
<input type="hidden" value="Member" name="c">
姓名：<input style="width:100px;height:25px;" class="spanmalt10" type="text" value="<?=$sername?>" name="name">
手机号：<input style="width:120px;height:25px;" class="spanmalt10" type="text" value="<?=$serphone?>" name="phone">
<select name="cateId">
    <option value="0">所有分类</option>
    <?php
    if($memberCate){
        foreach($memberCate as $k => $v){
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
<th>姓名</th><th>手机号</th><th>分类</th><th>生日</th><th>积分</th><th>余额</th><th>总消费</th><th>添加时间</th><th>操作</th>
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
       <a title="修改" class="btn btn-info editbtnMember"><i class="halflings-icon white edit"></i></a>
       <a title="修改积分" class="btn btn-info btnUpScore" style="color:#ffffff;" data-mid="'.$v['id'].'" data-score="'.$v['score'].'">积分调整</a>
       <a title="修改余额" class="btn btn-info btnUpCard" style="color:#ffffff;" data-mid="'.$v['id'].'" data-card="'.$v['balance'].'">会员充值</a>
       <!-- <a title="删除" class="btn btn-danger delbtn"><i class="halflings-icon white trash"></i></a>　--></td>';
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
                <h4 class="modal-title">添加会员</h4>
            </div>
            <div class="modal-body">
                <form id="addform" method="post" action="?">
                    <table>
                        <tbody><tr><td> <table class="item_edit_table"> <tbody>
                        
                          <tr><td align="right">手机号:</td><td><input type="text" name="phone" id="add_phone"/>
                              <span class="btn btn-mini" title="验证是否存在" id="addCheckExs"><i class="halflings-icon search white"></i></span>
                          </td></tr>
                          <tr><td align="right">姓名:</td><td><input type="text" name="name" id="add_name"/></td></tr>
                          <tr><td align="right">分类:</td><td>
                          <select name="cateId" id="add_cateId"><option value='0'>请选择</option>
                                <?php
                                if ($memberCate) {
                                       foreach ($memberCate as $k=>$v) {
                                           echo '<option value="' . $k . '">' . $v . '</option>' . "\n";
                                       } 
                                   }
                                 ?>
                           </select>    
                          </td></tr>
                          <tr><td align="right">生日:</td><td><input type="text" name="byear"  id="add_byear"  style="width:60px" /> 年 <input type="text" name="bmonth" id="add_bmonth"  style="width:60px" /> 月 <input type="text" name="bday" id="add_bday" style="width:60px" /> 日</td></tr>
                          <tr><td align="right">积分:</td><td><input type="text"   name="score"  id="add_score" value='0' /></td></tr>
                          <tr><td align="right">卡余额:</td><td><input type="text"   name="balance"  id="add_balance" value='0'/></td></tr>
                          <tr><td align="right">备注:</td><td><textarea  name="remark"  style="width:350px;height:50px"></textarea></td></tr>
                          <tr><td align="right">介绍人手机号:</td><td><input type="text" name="introducer"  id="add_introducer"/></td></tr>


                         </tbody></table></td></tr></tbody></table>
                         <input type="hidden" name="a" value="AddItem">
                         <input type="hidden" name="c" value="Member">
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
                          <tr><td align="right">手机号:</td><td><input type="text" id="phone"  name="phone" /></td></tr>
                          <tr><td align="right">姓名:</td><td><input type="text" id="name"  name="name" /></td></tr>
                          <tr><td align="right">分类:</td><td>
                              <select id="cateId" name="cateId"><option value='0'>请选择</option>
                                <?php
                                if ($memberCate) {
                                       foreach ($memberCate as $k=>$v) {
                                           echo '<option value="' . $k . '">' . $v . '</option>' . "\n";
                                       } 
                                   }
                                 ?>
                                </select>                              
                              </td></tr>
                          <tr><td align="right">生日:</td><td><input type="text" id="byear" name="byear" style="width:60px" /> 年 <input type="text" id="bmonth"  name="bmonth"  style="width:60px" /> 月 <input type="text" id="bday"  name="bday"  style="width:60px" /> 日</td></tr>
                         <!-- <tr><td align="right">积分:</td><td><input type="text" id="score"  name="score"/></td></tr>
                          <tr><td align="right">卡余额:</td><td><input type="text" id="balance"  name="balance" /></td></tr>-->
                          <tr><td align="right">备注:</td><td><textarea id="remark"  name="remark" style="width:350px;height:50px"></textarea></td></tr>
                          <tr><td align="right">介绍人手机号:</td><td><input type="text" id="introducer"  name="introducer" /></td></tr>
                          
                        </tbody></table></td></tr></tbody></table>
				    <input type="hidden" id="dataid" name="dataid" value="">
                    <input type="hidden" name="a" value="UpItem">
                    <input type="hidden" name="c" value="Member">
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


<div class="modal hide fade" id="edit-box2"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:600px;font-size:12px">
        <div class="modal-content">
            <div class="modal-body">
                <form id="editform" method="post" action="?" onsubmit="return checkUpScore()">
                    <table>
                        <tbody><tr><td> <table class="item_edit_table"><tbody>
                          <tr><td align="right">用户当前积分:</td><td id="memNowScore" style="font-weight: bold;color:green;line-height:35px;"></td></tr>
                          <tr><td align="right">增加/减少:</td><td><select name="direction" id="us_direction"><option value="1">减少</option><option value="0">增加</option></select></td></tr>
                          <tr><td align="right">积分:</td><td><input type="text" id="us_score"  name="score"/></td></tr>
                          <tr><td align="right">备注:</td><td><textarea id="us_remark"  name="remark" style="width:350px;height:50px"></textarea></td></tr>

                        </tbody></table></td></tr></tbody></table>
                    <input type="hidden" name="mid" value="0" id="us_mid" >
                    <input type="hidden" name="us_allscore" value="0" id="us_allscore" >
                    <input type="hidden" name="a" value="UpScore">
                    <input type="hidden" name="c" value="Member">
					<input type="hidden" name="pageUrl" value="<?=$pageUrl?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="savebtn">确认修改</button>
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
                          <tr><td align="right">用户当前余额:</td><td id="memNowCard" style="font-weight: bold;color:green;line-height:35px;"></td></tr>
                          <tr><td align="right">增加/减少:</td><td><select name="direction" id="uc_direction"><option value="0">增加</option><option value="1">减少</option></select></td></tr>
                          <tr><td align="right">金额:</td><td><input type="text" id="uc_score"  name="card"/></td></tr>
                          <tr><td align="right">备注:</td><td><textarea id="uc_remark"  name="remark" style="width:350px;height:50px"></textarea></td></tr>

                        </tbody></table></td></tr></tbody></table>
                    <input type="hidden" name="mid" value="0" id="uc_mid" >
                    <input type="hidden" name="uc_allcard" value="0" id="uc_allcard" >
                    <input type="hidden" name="a" value="UpCard">
                    <input type="hidden" name="c" value="Member">
					<input type="hidden" name="pageUrl" value="<?=$pageUrl?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="savebtn">确认修改</button>
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
//用一个按钮修改一种状态的后台
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
    art.dialog({title: '积分修改',width:"600px",content: $("#edit-box2").html()});
});
$(".btnUpCard").click(function(){
    //data-mid="'.$v['id'].'" data-score
    var mid   = $(this).attr("data-mid");
    var score = $(this).attr("data-card");
    $("#uc_card").val(score);
    $("#uc_mid").val(mid);
    $("#memNowCard").html(score);
    $("#uc_allcard").val(score);
    
    art.dialog({title: '会员卡余额修改',width:"600px",content: $("#edit-box4").html()});
});
var checkUpCard = function(){
    if($("#uc_direction").val()==1){
        if($("#uc_allcard").val() - $("#uc_score").val() < 0){
            alert("用户的余额不足以扣除");
            return false;
        }
    }
    return true;
}
var checkUpScore = function(){
    if($("#us_direction").val()==1){
        if($("#us_allscore").val() - $("#us_score").val() < 0){
            alert("用户的积分不足以扣除");
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
            alert("该会员已经存在，不能添加！");
        }
    });
}

$("#addCheckExs").click(function(){
    checkHasOneToAdd();
});
$("#add_phone").blur(function(){
    checkHasOneToAdd();
});

//搜索用户
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

