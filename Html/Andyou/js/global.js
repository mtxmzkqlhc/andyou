//输入的回车处理方法
var iptEnter = function(sel,callback){
    $(sel).keydown(function(e){
        if(e.which == 13){
          e.preventDefault();
          callback();
       }
    });
};


