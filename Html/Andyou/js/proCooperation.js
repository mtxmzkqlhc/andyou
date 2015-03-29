function syncContent() {
    //提示通用配置
    var dialogParam = {  //除了content之外其他值不要改变，要用的话请 复制到局部变量中改变
        lock : true, /* 锁屏【遮罩层】  */
        title:'提示消息',
        width : '242px',
        height: '117px',
        icon:'error',  // error,message,message,succeed,warning
        ok: function () {
                        this.close();
                        return false;
                    },
        content:''
    };
    var isSubmit = true;
    //验证内容
    $('.verifyCol').each(function(){
        var self = $(this);
        if(self.val() == ''){
            dialogParam.content =  '请将内容填写完整';
            isSubmit = false;
            dialogParam.ok = function () {
                this.close();
                self.focus();
                return false;
            };
            $.dialog( dialogParam); 
        }
    });
    if(isSubmit){
        $('#editform').submit();
    }
}

