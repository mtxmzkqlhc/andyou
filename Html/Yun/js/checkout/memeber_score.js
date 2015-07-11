/* 
 * 收银，会员相关JS
 */



//搜索会员
iptEnter("#memberPhone",function(){
    doSearchMember();
});
$("#searchMemBtn2").click(function(){
    doSearchMember();
});
//条形码
var doSearchMember = function(){
    $("#addbtn").attr("disabled",true);
    var phone = $("#memberPhone").val();
    if(phone){
        var t = Date.parse(new Date()); 
        var url = "?c=Ajax_Member&a=GetMemberByPhone&phonecard=" + phone+"&t="+t;
        $.getJSON(url,{},function(data){
            if(data && data.name){
                var bls = data.balance ? data.balance : 0;
                $("#memtbl_name").show().html(data.name);
                $("#bill_end_membernm").val(data.name);
                $("#memtbl_score").html(data.score);
                memberAllScore = data.score;
                $("#memtbl_card").html(bls);
                $("#memtbl_cate").html(data.cateName);
                $("#memtbl_disc").html('<a href="javascript:;" target="_self" onclick="showMemDis()">查看具体折扣&gt;&gt;</a>');
                $("#memtbl_remark").html(data.remark);
                $("#memtbl_allsum").html(data.allsum);
                
                if(data.score < minCheckoutScore){
                    alert("该会员的积分未达到最低兑换值，不能进行兑换！");
                    return false;
                }
                $("#listBox").show();
                $("#addbtn").removeAttr("disabled");
    
                $("#bill_member_card").val(bls);
                $("#bill_end_membernm").val(data.name);

                $("#memberId").val(data.id);
                $("#bill_member_card").removeAttr("readonly");
                $("#bill_member_score").removeAttr("readonly");

                $("#bill_card_left").val(bls);
                $("#bill_score_left").val(data.score);
                $(".memextinfo").show();

                memberDisc = data.discount;//会员折扣
                if(memberDisc > 1)memberDisc = 1;
                memberDiscArr = data.discountArr;   
                    
                 $("#bill_disc").val(memberDisc);
                //先关按钮的显示
                $("#removeMemInfo").show();
                $("#proBarCode").focus();
                
                //计算一下金额
                refreshRightTbl();
                calcBillSumInfo();
            }else{
                alert("该会员不存在");
                removeMemInfo();
            }
        });
    }else{
        removeMemInfo();
    }
}
//清除会员相关信息
var removeMemInfo = function(){

    $("#memtbl_name").hide().html("");
    $("#bill_end_membernm").val("");
    $("#memtbl_score").html("");
    $("#memtbl_card").html("");
    $("#memtbl_cate").html("");
    $("#memberId").val(0);
    $("#bill_member_card").attr("readonly","true");
    $("#bill_member_score").attr("readonly","true");
    $("#bill_card_left").val(0);
    $("#bill_score_left").val(0);
    $(".memextinfo").hide();
    $("#memtbl_remark").html("");
    $("#memtbl_disc").html("");
    $("#memtbl_allsum").html("");

    $("#memberPhone").val("");

    //先关按钮的隐藏
    $("#removeMemInfo").hide();
    memberDiscArr = {};
    //计算一下金额
    refreshRightTbl();
    calcBillSumInfo();

}
//清除会员信息
$("#removeMemInfo").click(function(){
    if(confirm("确认清空会员信息吗？")){
        removeMemInfo();
        $("#listBox").hide();
    }
});