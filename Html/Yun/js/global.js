//����Ļس�������
var iptEnter = function(sel,callback){
    $(sel).keydown(function(e){
        if(e.which == 13){
          e.preventDefault();
          callback();
       }
    });
};


