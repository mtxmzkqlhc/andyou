function syncContent() {
    //��ʾͨ������
    var dialogParam = {  //����content֮������ֵ��Ҫ�ı䣬Ҫ�õĻ��� ���Ƶ��ֲ������иı�
        lock : true, /* ���������ֲ㡿  */
        title:'��ʾ��Ϣ',
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
    //��֤����
    $('.verifyCol').each(function(){
        var self = $(this);
        if(self.val() == ''){
            dialogParam.content =  '�뽫������д����';
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

