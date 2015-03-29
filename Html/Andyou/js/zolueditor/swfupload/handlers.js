/*
 * @description : swfupload handlers
 * @copyright   : http://www.zol.com.cn/
 * @author      : xuzongsheng
 * @version     : 1.0 (2013-04-09 10:09:35)
 * @modifier    : xuzongsheng
 */
//�������Ĭ��׼��״̬
function fileQueued(file){
	if(file!= null){
		try{
			var progress = new FileProgress(file,this.customSettings.progressTarget);
			progress.setStatus("�ȴ��ϴ�...");
			progress.toggleCancel(true, this);
		}catch(ex){
			this.debug(ex);
		}
	}
}
//�ϴ������е��ļ�������ʾ
function fileQueueError(file,errorCode,message){
	try{
		if(errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED){
			alert('�����ļ����������趨ֵ');
			return;
		}
		var progress = new FileProgress(file,this.customSettings.progressTarget);
		progress.setError();
		progress.toggleCancel(false);
		switch(errorCode){
			case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
				progress.setStatus("�ļ��ߴ糬���趨ֵ��");
			break;
			case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
				progress.setStatus("�벻Ҫ�ϴ����ļ���");
			break;
			case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
				progress.setStatus("�ļ����Ͳ��Ϸ���");
			break;
			default:
				if(file !== null) progress.setStatus("�ϴ������������Ա��ϵ��");
			break;
		}
	}catch(ex){
		this.debug(ex);
    }
}
//��ʼ�ϴ�
function uploadStart(file) {
	this.addPostParam("logo_stat",$CONFIG['logo_stat']);//��ʼ�ϴ���ʱ��̬���ò���
	try{
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setStatus("�����ϴ����Ժ�...");
		progress.toggleCancel(true, this);
	}
	catch(ex){
		this.debug(ex);
	}
	return true;
}
//�����ϴ�
function uploadProgress(file, bytesLoaded, bytesTotal){
	try {
		var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setProgress(percent);
		progress.setStatus("�����ϴ�("+percent+" %)���Ժ�...");
	} catch (ex) {
		this.debug(ex);
	}
}
//�ϴ��ɹ���ִ�еĻص�����
function uploadComplete(file){
	if (this.getStats().files_queued > 0){
		 this.startUpload();
	}
}
//�ϴ��ɹ�
function uploadSuccess(file, serverData) {
	try {
		var uploadResult = eval('('+ serverData +')');
		if(uploadResult.result != 1){
			progress.setStatus("�ϴ�����");
		}
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setComplete();
		progress.setStatus("�ļ��ϴ��ɹ���");
		progress.toggleCancel(false);
	} catch (ex) {
		this.debug(ex);
	}
}
//�ϴ�������ʾ
function uploadError(file, errorCode, message) {
	var msg;
	switch (errorCode){
		case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
			msg = "�ϴ�����: " + message;
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
			msg = "�ϴ�����";
			break;
		case SWFUpload.UPLOAD_ERROR.IO_ERROR:
			msg = "������ I/O ����";
			break;
		case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
			msg = "��������ȫ��֤����";
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
			msg = "������ȫ���ʧ�ܣ��ϴ���ֹ";
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			msg = '�ϴ�ȡ��';
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			msg = '�ϴ���ֹ';
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			msg = '�����ϴ��ļ�������Ϊ '+swfu.settings.file_upload_limit+' ��';
			break;
		default:
			msg = message;
			break;
	}
	var progress = new FileProgress(file,this.customSettings.progressTarget);
	progress.setError();
	progress.setStatus(msg);
}
//�����е��ļ��ϴ���ɺ�ִ�еĻص�����
function queueComplete(numFilesUploaded){
	$('#iframeImages').attr('src',$('#iframeImages').attr('src')+'&rand='+Math.random());
}