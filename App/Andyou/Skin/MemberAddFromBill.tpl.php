<?= $header ?>
<?= $navi ?>
<style>
    #addUserTbl {font-size:12px;}
    #addUserTbl td{text-align: left;}
#addCheckExs,#addIntrCheckExs{padding:3px 4px;margin-bottom:10px;}
</style>
<div class="content" style="padding-top:20px;">
        
			<div class="row-fluid">
                
                <div class="box">
					<div class="box-header">
						<h2><i class="halflings-icon list-alt"></i><span class="break"></span>��ӻ�Ա</h2>
						<div class="box-icon">
							<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
							<a href="#" class="btn-close"><i class="halflings-icon remove"></i></a>
						</div>
					</div>
                    <div class="box-content clearfix">
                	 <form id="addform" method="post" action="?" onsubmit="return doCheckIpt()" onkeydown="if(event.keyCode==13)return false;">
                         <table class="table table-center table-striped table-bordered" id="addUserTbl">
                        <tbody>
                         <?php
                          if(!$andCard){
                          ?>
                          <tr><td align="right" style="text-align:right;">����:</td><td style="font-weight:bold;color:green"><?=$billInfo["bno"]?></td></tr>
                          <tr><td align="right" style="text-align:right;">���ѽ��:</td><td><?=round($billInfo["price"]/100,2)?> Ԫ</td></tr>
                          <?php
                          }
                          ?>
                          <tr><td align="right" style="text-align:right;">�ֻ���:</td><td><input type="text"  id="phone" name="phone" class="entrnext" data-tab-index="1"/> <span class="btn btn-mini" title="��֤�Ƿ����" id="addCheckExs"><i class="halflings-icon search white"></i></span></td></tr>
                          <tr><td align="right" style="text-align:right;">����:</td><td><input type="text" id="name"  name="name" class="entrnext" data-tab-index="2"/></td></tr>
                          <tr><td align="right" style="text-align:right;">����:</td><td><input type="text" id="cardno"  name="cardno" class="entrnext" data-tab-index="3"/></td></tr>
                          <tr><td align="right" style="text-align:right;">����:</td><td>
                          <select name="cateId" id="cateId" class="entrnext" data-tab-index="4"><option value='0'>��ѡ��</option>
                                <?php
                                if ($memberCate) {
                                       foreach ($memberCate as $k=>$v) {
                                           echo '<option value="' . $k . '">' . $v . '</option>' . "\n";
                                       } 
                                   }
                                 ?>
                           </select>    
                          </td></tr>
                          <tr><td align="right" style="text-align:right;">����:</td><td><input type="text" name="byear" id="byear"  style="width:60px"  class="entrnext" data-tab-index="5"/> �� <input type="text" name="bmonth"  id="bmonth"  style="width:60px"  class="entrnext" data-tab-index="6"/> �� <input type="text" name="bday"  id="bday" style="width:60px"  class="entrnext" data-tab-index="7"/> ��</td></tr>
                          
                          <tr><td align="right" style="text-align:right;">�������ֻ���:</td><td><input type="text"  name="introducer" id="introducer" value=''  class="entrnext" data-tab-index="9"/> <span class="btn btn-mini" title="��֤�Ƿ����" id="addIntrCheckExs"><i class="halflings-icon search white"></i></span></td></tr>
                          
                          <tr><td align="right" style="text-align:right;">��Ա��ע:</td><td><textarea  name="remark" id="remark"  style="width:350px;height:50px"  class="entrnext" data-tab-index="8"></textarea></td></tr>
                          <?php
                          if($andCard){
                          ?>
                          <tr><td align="right" style="text-align:right;">��ӳ�ֵ�����:</td><td><input type="text"  name="balance"/></td></tr>
                          <tr><td align="right" style="text-align:right;">����Ա:</td><td>
                                  <select name="staffid"><option value='0'>��ѡ��</option>
                                    <?php
                                    if ($staffArr) {
                                           foreach ($staffArr as $k=>$v) {
                                               echo '<option value="' . $k . '">' . $v . '</option>' . "\n";
                                           } 
                                       }
                                     ?>
                               </select> </td></tr>
                          
                          <tr><td align="right" style="text-align:right;">��ֵ��ע:</td><td><textarea  name="remark2" id="remark2"  style="width:350px;height:50px"  class="entrnext" data-tab-index="8"></textarea></td></tr>
                          <?php
                          }else{
                          ?>
                          <tr><td align="right" style="text-align:right;">�ɻ����:</td><td><?=$canGetScore?></td></tr>
                          <?php
                          }
                          ?>
                         <!-- <tr><td align="right">�����:</td><td><input type="text"   name="balance" value='0'/></td></tr> -->
                          <tr><td>&nbsp;</td><td><input type="submit" id="submitbtn" value="<?=$andCard?"ȷ�����":"���ѹ����˻�Ա"?>" class="btn btn-primary entrnext"  data-tab-index="10"/></td></tr>
                          

                         </tbody></table>
                         
                          <?php
                          if($andCard){
                          ?>
                         <input type="hidden" name="a" value="AddUserAndCard">
                          <?php
                          }else{
                          ?>
                         <input type="hidden" name="a" value="AddUserFromBill">
                          <?php
                          }
                          ?>
                         <input type="hidden" name="c" value="Member">
                         <input type="hidden" name="bid" value="<?=$billInfo["id"]?>">
                         <input type="hidden" name="andCard" value="<?=$andCard?>">
                         <input type="hidden" name="billPrice" value="<?=round($billInfo["price"]/100)?>">
                         
                </form>
					
                </div>
            </div>

        </div>

</div>

<?= $footer ?>
<script>
var doCheckIpt = function(){
    var cid = $("#cateId").val();
    if(cid == 0){
        alert("��ѡ�����");
        return false;
    }
    if($("#name").val() == ""){
         alert("����д������");
         return false;
    }
    if($("#phone").val() == ""){  
        alert("����д�ֻ��ţ�");
        return false;
    }
    //��֤�û�
    //$.ajaxSettings.async = false;
    
    return true;
    
}
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
        $('#balance').val(dat['balance']);
        $('#remark').html(dat['remark']);
        $('#cardno').html(dat['cardno']);
    }); 
 });


//-------------------------------
//��һ����ť�޸�һ��״̬�ĺ�̨ introducer
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

var checkHasOneToAdd = function(){
    
    $("#name").val("").removeAttr("readonly");
    $("#cateId").val("").removeAttr("readonly");
    $("#byear").val("").removeAttr("readonly");
    $("#bmonth").val("").removeAttr("readonly");
    $("#bday").val("").removeAttr("readonly");
    $("#balance").val("").removeAttr("readonly");
    $("#remark").val("").removeAttr("readonly");
    $("#score").val("").removeAttr("readonly");
    $("#introducer").val("").removeAttr("readonly");
    $("#cardno").val("").removeAttr("readonly");
            
    doSearchMember($("#phone").val(),function(d){
        if(d && d.name){
            $("#name").val(d.name).attr("readonly",true);
            $("#cateId").val(d.cateId).attr("readonly",true);
            $("#byear").val(d.byear).attr("readonly",true);
            $("#bmonth").val(d.bmonth).attr("readonly",true);
            $("#bday").val(d.bday).attr("readonly",true);
            $("#balance").val(d.balance).attr("readonly",true);
            $("#remark").val(d.remark).attr("readonly",true);
            $("#score").val(d.score).attr("readonly",true);
            $("#introducer").val(d.introducer).attr("readonly",true);
            $("#cardno").val(d.cardno).attr("readonly",true);
            
        }
    });
}
$("#addCheckExs").click(function(){
    checkHasOneToAdd();
})

$("#phone").blur(function(){
    checkHasOneToAdd();
});

$("#addIntrCheckExs").click(function(){
    doSearchMember($("#introducer").val(),function(d){
        if(!d || !d.name){
            alert("�������ֻ��Ų�����");
            $("#introducer").val("");
            
        }
    });
});
$("#introducer").blur(function(){
    if($("#introducer").val() == ""){
        $("#submitbtn").val("ȷ���ύ").attr("disabled",false);
        return true;
    }
    
    $("#submitbtn").val("��֤�������Ƿ������...").attr("disabled",true);
    doSearchMember($("#introducer").val(),function(d){
        if(!d || !d.name){
            alert("�������ֻ��Ų�����");
            $("#introducer").val("");
            $("#submitbtn").val("ȷ���ύ").attr("disabled",false);
        }else{
           $("#submitbtn").val("ȷ���ύ").attr("disabled",false);
        }
    });
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
//

$("form").keydown(function(e){
    if(e.which == 13){
        return false;
    }
});
setTimeout(function(){
   // $.get("?c=Rsync_Member&a=UpNew");//ͬ����Ա��Ϣ
},100);
</script>
</body>
</html>

