function PopulateFileList(thisObject, dialogId, fileInputId)
{
	if (thisObject.fileCount < thisObject.maxFiles)
	{
		var previousInput = fileInputId + thisObject.fileCount;
		
		// Increment to the the next one
		thisObject.fileCount++;
		
		var newId = fileInputId + thisObject.fileCount;
		 
		var newInputString = '<label for="' + newId + '" class="label ' + newId + '" id="' + newId + 'lbl">Select:<input type="file" id="' + newId;
		newInputString += '" name="' + newId + '" class="file ui-widget-content ui-corner-all"/>';
	
//		$('#' + dialogId).children('form').append(newInputString);
		$('#' + previousInput + 'lbl').append(newInputString);
//		$('#' + previousInput + 'lbl').prepend(newInputString);
		$('#' + newId).bind('change', function() {PopulateFileList(thisObject, dialogId, 'upload_file');});
	}
}

function UploadFileList(dialogId)
{
	$('#' + dialogId).children('form').submit();	
}

function UploadFiles(dialogId, targetUrl)
{
	this.fileCount = 1; // the form already has 1, however we will increment when creating the new id
	this.maxFiles = 10;
	
	// Create our popup form
	this.uploadPopup = new PopUp(dialogId, 'Uploader', 480, 480);
	this.uploadPopup.target = 'upload_file_iframe';
	this.uploadPopup.submitmethod = 'post';

	var ufThis = this;
	this.uploadPopup.addField('upload_file1', 'Select', function() {PopulateFileList(ufThis, dialogId, 'upload_file');}, 'file', 'FILENAME', 3, 256);
	this.uploadPopup.addFieldBreak();
	this.uploadPopup.addField(this.uploadPopup.target, 'target', null, 'iframe', 'NONE', 0, 0);
	this.uploadPopup.addField('iframe', 'Uploading', null, 'hidden', 'NONE', 0 , 0);
	this.uploadPopup.addField('uploading', 'Uploading', null, 'hidden', 'NONE', 0 , 0);
	
	// Add in buttons outside of our form
	this.uploadPopup.addButton('Upload', function() {UploadFileList(dialogId);$( this ).dialog( "close" );});
	this.uploadPopup.addButton('Cancel', function() {$( this ).dialog( "close" );});
	
	return showPopup(this.uploadPopup, 0);	
}
