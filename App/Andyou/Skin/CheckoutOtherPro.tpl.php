<?= $header ?>
<?= $navi ?>
<style>
.clearfix:after {content:"."; display: block; visibility: hidden; clear: both; height:0; font-size:0}
.clearfix {*zoom:1}
.box{margin-bottom: 10px;}
.box-l{float:left;width:290px;}
.box-l2{float:left;width:500px;}
.box-r{float:left;margin-left:10px;border-left:1px solid #ccc;padding-left:35px;}
.box-r2{float:left;margin-left:40px;border-left:1px solid #ccc;padding-left:35px;}
.box-r2 dl{display: block;margin-bottom:8px;}
.box-r2 dt{float:left;width:50px;padding-right: 15px;line-height: 20px;}
.box-r2 dd{float:left}
.box-r2 dd input{width:140px;}
.box-r2 dd select{width:148px;}

.box-l dl{display: block;}
.box-l dt{float:left;width:70px;text-align: right;padding-right: 15px;line-height: 20px;}
.box-l dd{float:left}
.box-l dd input{width:140px;}
.box-l dd select{width:148px;}
.mtbl_l{width:80px;font-weight: bold;}
.mtbl_r{text-align:left;padding-left:8px;min-width: 40px;}
#memberInfoTbl .mtbl_r{width:120px;}
#billContent dl{margin-bottom:3px;margin-top:2px;}
.tblProNum{width:20px;margin-bottom:0px;}
#barScanDiv{padding:10px 0 15px 0;margin-bottom:10px;}
#proBarCode{width:300px;}
#proListTbody input{margin-bottom:0px;width:25px;}
#proListTbody .btn-small{padding:0 5px 0 3px;}
#searchMemBtn,#searchProBtn,#setDiscBtn,#removeGoodsBtn,#removeMemInfo{padding:3px 4px;margin-bottom:10px;}
#billContent .memextinfo{display:none}
#proAddBoxTbody td{padding-bottom:4px;}
#proAddBoxTbody .btn-info{padding:1px 4px;}
.aui_content{height:400px;overflow-y: scroll}
h2{font-weight: bold;}
#rightTbl{display:none;padding-bottom: 40px;}
#hasUsedOtherPro{margin-bottom:25px;}
#hasUsedOtherPro .btn{padding:3px 4px;margin-bottom:0px;}
</style>
<div id="content">

			<div class="row-fluid">
                
                <div class="box">
                    <div class="box-content clearfix"  style="padding:15px 10px 8px;">
                        <div class="box-l clearfix">
                            <dl>
                                <dt>��Ա</dt>
                                <dd><input type="text" value="" id="memberPhone"><span class="btn btn-mini" title="��ѯ�û�" id="searchMemBtn"><i class="halflings-icon search white"></i></span>
                                </dd>
                            </dl><dl>
                                <dt>&nbsp;</dt>
                                <dd style="text-align:right;width: 180px;">
                                <span class="btn btn-mini btn-info" title="���������ѡ�����Ʒ" id="removeMemInfo" style="display:none;"><i class="halflings-icon star-empty white"></i>���</span>
                                </dd>
                            </dl>
                        </div>
                        <div class="box-r">
                            <table width="100%" id="memberInfoTbl">
                                <tr>
                                    <td class="mtbl_l">��Ա����</td><td class="mtbl_r"><span class="label label-success" id="memtbl_name" style="display:none"></span></td>
                                    <td class="mtbl_l">��Ա����</td><td class="mtbl_r" id="memtbl_cate"></td>
                                    <td class="mtbl_l"> </td><td class="mtbl_r" id="memtbl_disc"></td>
                                </tr>
                                <tr>
                                    <td class="mtbl_l">�ۼ�����</td><td class="mtbl_r" id="memtbl_allsum"></td>
                                    <td class="mtbl_l">��ǰ����</td><td class="mtbl_r" id="memtbl_score"></td>
                                    <td class="mtbl_l">�������</td><td class="mtbl_r" id="memtbl_card"></td>
                                </tr>
                                <tr><td class="mtbl_l">��ע</td><td class="mtbl_r" id="memtbl_remark" colspan="8"> </td></tr>
                            </table>
                        </div>
                    </div>
                    
                </div>
            </div>
    
    
    
            <!--   �˵�����     -->
    
			<div class="row-fluid">
                
                <div class="box">
					<div class="box-header">
						<h2>������Ϣ</h2>
					</div>
                    <div class="box-content clearfix" id="billContent2">
                        <div class="box-l2 clearfix">
                            <div>
                                <h2>�����ܷ��������</h2>
                                <table class="table table-striped table-bordered" id="proListTable">
                                    <thead><tr role="row"><th>������Ŀ</th><th  style="width:90px;">ʣ�����</th><th style="width:80px;">����</th></tr> </thead>   
						  
                                    <tbody id="proListTbody">
                                        <tr><td colspan="10" style="text-align: center;color:#666666;padding:100px 0 100px;background:#ffffff;">-- ���޿��÷��� --</td></tr>
                                    </tbody>
                                
                                </table>
                            </div>
                        </div>
                        <div class="box-r2" style="width:300px;" id="rightTbl">
                            <form method="POST" action="?" onsubmit="return doCheckIpt()" onkeydown="if(event.keyCode==13)return false;" >
                             <h2>����ʹ�÷���</h2>
                             <table class="table table-striped table-bordered" id="hasUsedOtherPro">						  
                                    <tbody id="useProListTbody">
                                        <tr><td colspan="10" style="text-align: center;color:#666666;padding:20px 0 20px;background:#ffffff;">-- ���޿��÷��� --</td></tr>
                                    </tbody>
                                
                             </table>
                             <div>
                                <dl class="clearfix">
                                    <dt>�����Ա</dt>
                                    <dd>
                                        <select name="staffid" id="staffid" name="staffid"><option value='0'>��ѡ��</option>
                                           <?php
                                           if ($staffArr) {
                                                  foreach ($staffArr as $k=>$v) {
                                                      echo '<option value="' . $k . '">' . $v . '</option>' . "\n";
                                                  } 
                                              }
                                            ?>
                                      </select> 
                                </dl> 
                                <dl class="clearfix">
                                    <dt>��ע˵��</dt>
                                    <dd><textarea style="width:140px;height:50px;" name="remark"></textarea></dd>
                                </dl>
                                
                             </div>
                            <div style="text-align:center;">
                                <input type="submit" value="ȷ���ύ" class="btn btn-primary" id="addbtn"/>
                                <input type="hidden" value="CheckoutOtherPro" name="c"/>
                                <input type="hidden" value="Done" name="a"/>
                                <input type="hidden" value="0" name="memberId" id="memberId"/>
                            </div>
                             
                            </form>
                        </div>
                        
                    </div>
                    
                    
</div>



<?= $footer ?>
<script type="text/javascript" src="js/global.js"></script>
<script type="text/javascript" src="js/juicer-min.js"></script>


<!--  ���Ʒ��ѡ  -->
<script id="proSelectTableTr" type="text/template">
     {@each list as pro,index}
            <tr>
                <td>${pro.name}</td><td id="opro_num_${pro.id}" style="width:80px;">${pro.num}</td>
                <td style="width:80px;"><span class="btn btn-small btn-info" onclick="boxSelectPro(${pro.id},${pro.proId},'${pro.name}')"><i class="halflings-icon ok white "></i>ѡ��</span>                      
                </td> 
            </tr>
     {@/each}
</script>
<script id="proSelectTableTr2" type="text/template">
    <tr id="uo_tr_${id}"><td>${name}</td><td style="text-align:center;width:50px;">1�� <input type="hidden" class="otherProIpt" name="otherProId[]" value="${id}"/></td><td style="text-align:center;width:40px;"><span class="btn btn-mini" onclick="removeUseOtherPro(${id})"><i class="halflings-icon remove white "></i></span></td></tr>
</script>
<script>
$("#memberPhone").focus(); 
var ctype = "<?=$ctype?>";    
    
var proSelectTableTr = document.getElementById('proSelectTableTr').innerHTML;
var proSelectJuicer  = juicer(proSelectTableTr);
var appendSelectProTable = function(list){
    var data = {list:list};
    var html = proSelectJuicer.render(data);
    $("#proListTbody").empty();
    $("#proListTbody").append(html);
};   

//�Ҳ�
var proSelectTableTr2 = document.getElementById('proSelectTableTr2').innerHTML;
var proSelectJuicer2  = juicer(proSelectTableTr2);
var hasSel = false;
var boxSelectPro = function(id,proId,name){
    var nowNum = getLeftProNum(id);
    if(nowNum < 1){
        alert("�÷�����ʹ����");
        return false;
    }
    if(!hasSel){
        $("#useProListTbody").empty();
    }
    var data = {name:name,proId:proId,id:id};
    var html = proSelectJuicer2.render(data);
    $("#useProListTbody").append(html);
    hasSel = true;
    leftProNumChg(2,id);
}
var getLeftProNum   = function(id){
    var id = "#opro_num_"+id;
    var org = parseInt($(id).html(),10);
    return org;
}
var leftProNumChg = function(i,id){
    var ids = "#opro_num_"+id;
    var org = getLeftProNum(id);
    if(i==1){//��
        $(ids).html(org+1);
    }else{
        $(ids).html(org-1);
    }
}

var removeUseOtherPro = function(pid){
    $("#uo_tr_"+pid).remove();
    leftProNumChg(1,pid);
}
    
//������Ա
iptEnter("#memberPhone",function(){
    doSearchMember();
});
$("#searchMemBtn").click(function(){
    doSearchMember();
});


//�����ύ��֤
var doCheckIpt = function(){

    //�Ƿ�ѡ����Ʒ����֤
    var rightRows = $(".otherProIpt").size();
    if(rightRows < 1){
        alert("������ӷ���");
        return false;
    }


    //��֤����Ա�Ƿ��Ѿ�ѡ��
    var staffid = parseInt($("#staffid").val(),10);
    if(staffid == 0){
        alert("��ѡ�������Ա");
        return false;
    }

    var cfmStr = "ȷ��ʹ�ò���ӡСƱ��";
    

    if(!confirm(cfmStr)){
        return false;
    }

    return true;
}



//������
var doSearchMember = function(){
    var phone = $("#memberPhone").val();
    if(phone){
        var t = Date.parse(new Date()); 
        
        //��û�Ա��������Ʒ���б� doGetMemberOtherPro
        var url = "?c=Ajax_Member&a=GetMemberOtherPro&phonecard=" + phone+"&t="+t;
        $.getJSON(url,{},function(data){
            if(data){
                appendSelectProTable(data);
                $("#rightTbl").show();
            }else{
                alert("�û�Ա����Ӧ����");
            }
        });
        
        //��û�Ա����Ϣ
        var url = "?c=Ajax_Member&a=GetMemberByPhone&phonecard=" + phone+"&t="+t;
        $.getJSON(url,{},function(data){
            if(data && data.name){
                var bls = data.balance ? data.balance : 0;
                $("#memtbl_name").show().html(data.name);
                $("#bill_end_membernm").val(data.name);
                $("#memtbl_score").html(data.score);
                $("#memtbl_card").html(bls);
                $("#memtbl_cate").html(data.cateName);
                $("#memtbl_remark").html(data.remark);
                $("#memtbl_allsum").html(data.allsum);

                $("#memberId").val(data.id);
                //�ȹذ�ť����ʾ
                $("#removeMemInfo").show();
            }else{  
                alert("�û�Ա������");
                removeMemInfo();
            }
        });
    }else{
        removeMemInfo();
    }
}
//�����Ա�����Ϣ
var removeMemInfo = function(){

    $("#memtbl_name").hide().html("");
    $("#memtbl_score").html("");
    $("#memtbl_card").html("");
    $("#memtbl_cate").html("");
    $("#memberId").val(0);
    
    $("#memtbl_remark").html("");
    $("#memtbl_disc").html("");
    $("#memtbl_allsum").html("");

    $("#memberPhone").val("");

    //�ȹذ�ť������
    $("#removeMemInfo").hide();
    

}
//�����Ա��Ϣ
$("#removeMemInfo").click(function(){
    if(confirm("ȷ����ջ�Ա��Ϣ��")){
        removeMemInfo();
    }
});

</script>


</body>
</html>