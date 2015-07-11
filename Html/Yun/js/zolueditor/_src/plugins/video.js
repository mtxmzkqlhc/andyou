///import core
///import plugins/inserthtml.js
///commands 视频
///commandsName InsertVideo
///commandsTitle  插入视频
///commandsDialog  dialogs\video
UE.plugins['video'] = function (){
    var me =this,
        div;
    /**
     * 末尾字符检测
     * @param str
     * @param endStrArr
     */
    function endWith(str,endStrArr){
        for(var i=0,len = endStrArr.length;i<len;i++){
            var tmp = endStrArr[i];
            if(str.length - tmp.length<0) return false;

            if(str.substring(str.length-tmp.length)==tmp){
                return true;
            }
        }
        return false;
    }
    /**
     * 创建插入视频字符窜
     * @param url 视频地址
     * @param width 视频宽度
     * @param height 视频高度
     * @param align 视频对齐
     * @param toEmbed 是否以flash代替显示
     */
    function creatInsertStr(url,width,height,align,toEmbed,flashvars){
    	if(endWith(url,[".mp4",".wmv"])){
    		return  !toEmbed ?
                '<img align="center" width="'+ width +'" height="' + height + '" _url="'+url+'" class="edui-faked-video"' +
                ' src="' + me.options.UEDITOR_HOME_URL+'themes/default/images/spacer.gif" style="background:url('+me.options.UEDITOR_HOME_URL+'themes/default/images/videologo.gif) no-repeat center center; border:1px solid gray;" />' : '<embed src="' + url + '" width="' + width  + '" height="' + height +'">';
    	}else{
        	return  !toEmbed ?
                '<img align="center" width="'+ width +'" height="' + height + '" _url="'+url+'" _flashvars="'+ flashvars +'" class="edui-faked-video"' +
                ' src="' + me.options.UEDITOR_HOME_URL+'themes/default/images/spacer.gif" style="background:url('+me.options.UEDITOR_HOME_URL+'themes/default/images/videologo.gif) no-repeat center center; border:1px solid gray;" />'
                :
                '<embed type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"' +
                ' src="' + url + '" width="' + width  + '" height="' + height +
                '" wmode="opaque" play="true" loop="false" menu="false" flashvars="'+ flashvars +'" allowscriptaccess="never" allowfullscreen="true">';
    	}
    }

    function switchImgAndEmbed(img2embed){
        var tmpdiv,
            nodes =domUtils.getElementsByTagName(me.document, !img2embed ? "embed" : "img");
        for(var i=0,node;node = nodes[i++];){
            if(node.className!="edui-faked-video" && node.tagName!='EMBED'){
                continue;
            }
            tmpdiv = me.document.createElement("div");
            tmpdiv.innerHTML = creatInsertStr(img2embed ? node.getAttribute("_url"):node.getAttribute("src"),node.width,node.height,'center',img2embed,img2embed ? node.getAttribute("_flashvars"):node.getAttribute("flashvars"));
            node.parentNode.replaceChild(tmpdiv.firstChild,node);
        }
    }
    
    me.addListener("beforegetcontent",function(){
        switchImgAndEmbed(true);
    });
    me.addListener('aftersetcontent',function(){
        switchImgAndEmbed(false);
    });
    me.addListener('aftergetcontent',function(cmdName){
        if(cmdName == 'aftergetcontent' && me.queryCommandState('source')){
            return;
        }
        switchImgAndEmbed(false);
    });

    me.commands["insertvideo"] = {
        execCommand: function (cmd, videoObjs){
            videoObjs = utils.isArray(videoObjs)?videoObjs:[videoObjs];
            var html = [];
            for(var i=0,vi,len = videoObjs.length;i<len;i++){
                 vi = videoObjs[i];
                 html.push('<p style="text-align:center">'+creatInsertStr( vi.url, vi.width || 480,  vi.height || 390, vi.align||"none",false,vi.flashvars)+'</p>');
            }
            me.execCommand("inserthtml",html.join(""));
        },
        queryCommandState : function(){
            var img = me.selection.getRange().getClosedNode(),
                flag = img && (img.className == "edui-faked-video");
            return flag ? 1 : 0;
        }
    };
};