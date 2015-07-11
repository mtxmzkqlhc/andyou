/*
 * @description : swfupload handlers
 * @copyright   : http://www.zol.com.cn/
 * @author      : xuzongsheng
 * @version     : 1.0 (2013-04-09 10:09:35)
 * @modifier    : xuzongsheng
 */
//放入队列默认准备状态
function fileQueued(file){
	if(file!= null){
		try{
			var progress = new FileProgress(file,this.customSettings.progressTarget);
			progress.setStatus("等待上传...");
			progress.toggleCancel(true, this);
		}catch(ex){
			this.debug(ex);
		}
	}
}
//上传队列中的文件错误提示
function fileQueueError(file,errorCode,message){
	try{
		if(errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED){
			alert('队列文件数量超过设定值');
			return;
		}
		var progress = new FileProgress(file,this.customSettings.progressTarget);
		progress.setError();
		progress.toggleCancel(false);
		switch(errorCode){
			case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
				progress.setStatus("文件尺寸超过设定值！");
			break;
			case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
				progress.setStatus("请不要上传空文件！");
			break;
			case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
				progress.setStatus("文件类型不合法！");
			break;
			default:
				if(file !== null) progress.setStatus("上传错误，请与管理员联系！");
			break;
		}
	}catch(ex){
		this.debug(ex);
    }
}
//开始上传
function uploadStart(file) {
	this.addPostParam("logo_stat",$CONFIG['logo_stat']);//开始上传的时候动态设置参数
	try{
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setStatus("正在上传请稍后...");
		progress.toggleCancel(true, this);
	}
	catch(ex){
		this.debug(ex);
	}
	return true;
}
//正在上传
function uploadProgress(file, bytesLoaded, bytesTotal){
	try {
		var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setProgress(percent);
		progress.setStatus("正在上传("+percent+" %)请稍后...");
	} catch (ex) {
		this.debug(ex);
	}
}
//上传成功后执行的回调函数
function uploadComplete(file){
	if (this.getStats().files_queued > 0){
		 this.startUpload();
	}
}
//上传成功
function uploadSuccess(file, serverData) {
	try {
		var uploadResult = eval('('+ serverData +')');
		if(uploadResult.result != 1){
			progress.setStatus("上传错误！");
		}
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setComplete();
		progress.setStatus("文件上传成功！");
		progress.toggleCancel(false);
	} catch (ex) {
		this.debug(ex);
	}
}
//上传错误提示
function uploadError(file, errorCode, message) {
	var msg;
	switch (errorCode){
		case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
			msg = "上传错误: " + message;
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
			msg = "上传错误";
			break;
		case SWFUpload.UPLOAD_ERROR.IO_ERROR:
			msg = "服务器 I/O 错误";
			break;
		case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
			msg = "服务器安全认证错误";
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
			msg = "附件安全检测失败，上传终止";
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			msg = '上传取消';
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			msg = '上传终止';
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			msg = '单次上传文件数限制为 '+swfu.settings.file_upload_limit+' 个';
			break;
		default:
			msg = message;
			break;
	}
	var progress = new FileProgress(file,this.customSettings.progressTarget);
	progress.setError();
	progress.setStatus(msg);
}
//队列中的文件上传完成后执行的回调函数
function queueComplete(numFilesUploaded){
	$('#iframeImages').attr('src',$('#iframeImages').attr('src')+'&rand='+Math.random());
}