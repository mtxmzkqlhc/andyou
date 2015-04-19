/* 
 * 收银，会员相关JS
 */



//搜索会员
iptEnter("#memberPhone",function(){
    doSearchMember();
});
$("#searchMemBtn").click(function(){
    doSearchMember();
});
//条形码
var doSearchMember = function(){
    var phone = $("#memberPhone").val();
    if(phone){
        var url = "?c=Ajax_Member&a=GetMemberByPhone&phone=" + phone;
        $.getJSON(url,{},function(data){
            if(data){
                var bls = data.balance ? data.balance : 0;
                $("#memtbl_name").html(data.name);
                $("#bill_end_membernm").val(data.name);
                $("#memtbl_score").html(data.score);
                $("#memtbl_card").html(bls);
                $("#memtbl_cate").html(data.cateName);
                $("#memtbl_disc").html(data.discount);
                $("#memtbl_remark").html(data.remark);
                $("#bill_member_card").val(bls);

                $("#memberId").val(data.id);
                $("#bill_member_card").removeAttr("readonly");
                $("#bill_member_score").removeAttr("readonly");

                $("#bill_card_left").val(bls);
                $("#bill_score_left").val(data.score);
                $(".memextinfo").show();

                memberDisc = data.discount;//会员折扣
                if(memberDisc > 1)memberDisc = 1;

                //先关按钮的显示
                $("#removeMemInfo").show();
            }
        });
    }else{
        removeMemInfo();
    }
}
//清除会员相关信息
var removeMemInfo = function(){

    $("#memtbl_name").html("");
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

    $("#memberPhone").val("");

    //先关按钮的隐藏
    $("#removeMemInfo").hide();

}
//清除会员信息
$("#removeMemInfo").click(function(){
    if(confirm("确认清空会员信息吗？")){
        removeMemInfo();
    }
});