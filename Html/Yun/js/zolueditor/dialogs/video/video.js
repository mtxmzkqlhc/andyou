/**
 * Created by JetBrains PhpStorm.
 * User: taoqili
 * Date: 12-2-20
 * Time: 上午11:19
 * To change this template use File | Settings | File Templates.
 */
var video = {};

(function(){
    video.init = function(){
        switchTab("videoTab");
        addUrlChangeListener($G("videoUrl"));
        addOkListener();

        //编辑视频时初始化相关信息
        (function(){
            var img = editor.selection.getRange().getClosedNode(),url,flashvars;
            if(img && img.className == "edui-faked-video"){
                $G("videoUrl").value = url = img.getAttribute("_url");
                $G("videoFlashvars").value = flashvars = img.getAttribute("_flashvars");
                $G("videoWidth").value = img.width;
                $G("videoHeight").value = img.height;
            }
            createPreviewVideo(url,flashvars);
        })();
    };
    /**
     * 监听确认和取消两个按钮事件，用户执行插入或者清空正在播放的视频实例操作
     */
    function addOkListener(){
        dialog.onok = function(){
            $G("preview").innerHTML = "";
            var currentTab =  findFocus("tabHeads","tabSrc");
            switch(currentTab){
                case "video":
                    return insertSingle();
                    break;
            }
        };
        dialog.oncancel = function(){
            $G("preview").innerHTML = "";
        };
    }

    function selectTxt(node){
        if(node.select){
            node.select();
        }else{
            var r = node.createTextRange && node.createTextRange();
            r.select();
        }
    }

function in_array(array,key)
{
	for(i=0;i<array.length;i++)
	{
		if(array[i] == key)
		return true;
	}
	return false;
}

    /**
     * 将单个视频信息插入编辑器中
     */
    function insertSingle(){
    	var zolvid = null;
        var width = $G("videoWidth"),
            height = $G("videoHeight"),
            url=$G('videoUrl').value,
            flashvars = $G('videoFlashvars').value,
            align = 'center';
        if(!url) return false;
        if ( !checkNum( [width, height] ) ) return false;
        var isFnFlag = true;
        try{
        	isFnFlag = in_array(window.parent.$CONFIG['fengniao_classid'],window.parent.$CONFIG['classid']);
        }catch(e){}
        if(width.value>510 && !isFnFlag){
        	alert('视频宽度不要大于500');
        	return false;
        }
        if(/(^\d{6,}$)/.test(url)){
			//flashvars = 'movieId='+ url +'&open_window=0&show_ffbutton=1&channel_str=article.zol.com.cn&product_pic_url=http://icon.zol-img.com.cn/public/noimg.jpg';
			//url = 'http://v.zol.com.cn/flash/meatPlayer.swf';
			zolvid = url;
    	}else{
    		var zolvideo = url.match(/zol\.com\.cn\/video(\d+)/);//适配ZOL视频
        	if(zolvideo) zolvid = zolvideo[1];
    	}
    	if(zolvid){
			UE.ajax.request('http://article.zol.com.cn/admin/ueditor/plugins/video.php',{
			    method:'POST',
			    //可以自定义参数
			    vid:zolvid,
			    type:3,
			    width:width,
			    height:height,
			    async:false,
			    onsuccess:function(xhr){
			        url = xhr.responseText;
			        if(url == 'Error'){
			        	url = 'http://v.zol.com.cn/flash/meatPlayer.swf';
			        	flashvars = 'movieId='+ zolvid +'&open_window=0&show_ffbutton=1&channel_str=article.zol.com.cn&product_pic_url=http://icon.zol-img.com.cn/public/noimg.jpg';
			        }
			        editor.execCommand('insertvideo', {
			            url: convert_url(url),
			            width: width.value,
			            height: height.value,
			            flashvars: flashvars,
			            align: align
			        });
			    },
			    onerror:function(xhr){
					alert('网络错误！');
					return false;
			    }
			})
    	} else {
	        editor.execCommand('insertvideo', {
	            url: convert_url(url),
	            width: width.value,
	            height: height.value,
	            flashvars: flashvars,
	            align: align
	        });
    	}
    }

    /**
     * 找到id下具有focus类的节点并返回该节点下的某个属性
     * @param id
     * @param returnProperty
     */
    function findFocus( id, returnProperty ) {
        var tabs = $G( id ).children,
                property;
        for ( var i = 0, ci; ci = tabs[i++]; ) {
            if ( ci.className=="focus" ) {
                property = ci.getAttribute( returnProperty );
                break;
            }
        }
        return property;
    }
    function convert_url(s){
        return s.replace(/http:\/\/www\.tudou\.com\/programs\/view\/([\w\-]+)\/?/i,"http://www.tudou.com/v/$1")
            .replace(/http:\/\/www\.youtube\.com\/watch\?v=([\w\-]+)/i,"http://www.youtube.com/v/$1")
            .replace(/http:\/\/v\.youku\.com\/v_show\/id_([\w\-=]+)\.html/i,"http://player.youku.com/player.php/sid/$1")
            .replace(/http:\/\/www\.56\.com\/u\d+\/v_([\w\-]+)\.html/i, "http://player.56.com/v_$1.swf")
            .replace(/http:\/\/www.56.com\/w\d+\/play_album\-aid\-\d+_vid\-([^.]+)\.html/i, "http://player.56.com/v_$1.swf")
            .replace(/http:\/\/v\.ku6\.com\/.+\/([^.]+)\.html/i, "http://player.ku6.com/refer/$1/v.swf")
            .replace(/http:\/\/v\.zol\.com\.cn\/video(\d+)\.html/i,"http://v.zol.com.cn/v$1/video.swf");//增加ZOL视频匹配
    }

    /**
      * 检测传入的所有input框中输入的长宽是否是正数
      * @param nodes input框集合，
      */
     function checkNum( nodes ) {
         for ( var i = 0, ci; ci = nodes[i++]; ) {
             var value = ci.value;
             if ( !isNumber( value ) && value) {
                 alert( lang.numError );
                 ci.value = "";
                 ci.focus();
                 return false;
             }
         }
         return true;
     }

    /**
     * 数字判断
     * @param value
     */
    function isNumber( value ) {
        return /(0|^[1-9]\d*$)/.test( value );
    }

    /**
     * tab切换
     * @param tabParentId
     * @param keepFocus   当此值为真时，切换按钮上会保留focus的样式
     */
    function switchTab( tabParentId,keepFocus ) {

    }

    /**
     * 选择切换
     * @param selectParentId
     */
    function switchSelect( selectParentId ) {
        var selects = $G( selectParentId ).children;
        for ( var i = 0, ci; ci = selects[i++]; ) {
            domUtils.on( ci, "click", function () {
                for ( var j = 0, cj; cj = selects[j++]; ) {
                    cj.className = "";
                    cj.removeAttribute && cj.removeAttribute( "class" );
                }
                this.className = "focus";
            } )
        }
    }

    /**
     * 监听url改变事件
     * @param url
     */
    function addUrlChangeListener(url){
        if (browser.ie) {
            url.onpropertychange = function () {
                createPreviewVideo( this.value , '');
            }
        } else {
            url.addEventListener( "input", function () {
                createPreviewVideo( this.value , '');
            }, false );
        }
    }

    /**
     * 根据url生成视频预览
     * @param url
     */
    function createPreviewVideo(url,flashvars){
        if ( !url )return;
		var matches = url.match(/youtu.be\/(\w+)$/) || url.match(/youtube\.com\/watch\?v=(\w+)/) || url.match(/youtube.com\/v\/(\w+)/),
            youku = url.match(/youku\.com\/v_show\/id_(\w+)/),
            youkuPlay = /player\.youku\.com/ig.test(url);
        var zolvideo = url.match(/zol\.com\.cn\/video(\d+)/);//适配ZOL视频
        if(!youkuPlay){
            if (matches){
                url = "https://www.youtube.com/v/" + matches[1] + "?version=3&feature=player_embedded";
            }else if(youku){
                url = "http://player.youku.com/player.php/sid/"+youku[1]+"/v.swf"
            }else{
            	var zolvid = null;
        		if(/(^\d{6,}$)/.test(url)){
					zolvid = url;
    			}else{
    				var zolvideo = url.match(/zol\.com\.cn\/video(\d+)/);//适配ZOL视频
        			if(zolvideo) zolvid = zolvideo[1];
    			}
    			if(zolvid && zolvid.length==6){
    				UE.ajax.request('http://article.zol.com.cn/admin/ueditor/plugins/video.php',{
					    method:'POST',
					    vid:zolvid,
					    type:3,
					    async:false,
					    onsuccess:function(xhr){
					        url = xhr.responseText;
				            if(url == 'Error'){
				        		url = 'http://v.zol.com.cn/flash/meatPlayer.swf';
				        		flashvars = 'movieId='+ zolvideo[1] +'&open_window=0&show_ffbutton=1&channel_str=article.zol.com.cn&product_pic_url=http://icon.zol-img.com.cn/public/noimg.jpg';
				            }
$G("preview").innerHTML = '<embed type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"' +
            ' src="' + url + '"' +
            ' width="' + 420  + '"' +
            ' height="' + 280  + '"' +
            ' wmode="opaque" play="true" flashvars="'+ flashvars +'" loop="false" menu="false" allowscriptaccess="never" allowfullscreen="true" ></embed>';
					    },
					    onerror:function(xhr){
					    }
					})
    			}
            }
        }else {
        	url = url.replace(/\?f=.*/,"");
        }
        $G("preview").innerHTML = '<embed type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"' +
            ' src="' + url + '"' +
            ' width="' + 420  + '"' +
            ' height="' + 280  + '"' +
            ' wmode="opaque" play="true" flashvars="'+ flashvars +'" loop="false" menu="false" allowscriptaccess="never" allowfullscreen="true" ></embed>';
    }

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
     * 改变对象o的选中状态
     * @param o
     */
    function changeSelected(o){
        if ( o.getAttribute( "selected" ) ) {
            o.removeAttribute( "selected" );
            o.style.cssText = "filter:alpha(Opacity=100);-moz-opacity:1;opacity: 1;border: 2px solid #fff";
        } else {
            o.setAttribute( "selected", "true" );
            o.style.cssText = "filter:alpha(Opacity=50);-moz-opacity:0.5;opacity: 0.5;border:2px solid blue;";
        }
    }
})();