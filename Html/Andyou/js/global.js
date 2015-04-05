//输入的回车处理方法
var iptEnter = function(sel,callback){
    $(sel).keydown(function(e){
        if(e.which == 13){
          e.preventDefault();
          callback();
       }
    });
};

//积分转换价格
var scoreToMoney = function(score){
    var rule = 30; //300分 = 10元
    var price = Math.floor(score/rule);
    var leftScore = score - price * rule;
    return {
      price :   price,
      score :   leftScore,
    };
}

