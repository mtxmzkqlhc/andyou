//����Ļس�������
var iptEnter = function(sel,callback){
    $(sel).keydown(function(e){
        if(e.which == 13){
          e.preventDefault();
          callback();
       }
    });
};

//����ת���۸�
var scoreToMoney = function(score){
    var rule = 30; //300�� = 10Ԫ
    var price = Math.floor(score/rule);
    var leftScore = score - price * rule;
    return {
      price :   price,
      score :   leftScore,
    };
}

