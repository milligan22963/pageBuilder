/*
 * showDialog
 * 
 * designed to display a popup window using jQueryUI.  The following assumptions are made:
 * 
 * @param dialogObj - the object that will be the popup window
 * @param timeout - a timeout if the popup needs to fade away, null for modal
 * 
 * @return this object of a jQuery dialog
 */
function showDialog(dialogObj, timeout)
{
	this.dialogObj = dialogObj;
	this.timeout = timeout;
	this.uiDialog = null;
	
	this.primaryObjectId = '#' + dialogObj.getId();

	this.bindAction = function (fieldId, bindName, action)
	{
		$('#' + fieldId).bind(bindName, action);
	};

	this.closeWindow = function()
	{
		if (this.uiDialog != null)
		{
			this.uiDialog.dialog("close");
		}
	};

	if ($(this.primaryObjectId).is(':data(dialog)'))
	{
		this.uiDialog = $(this.primaryObjectId).dialog('open');
		this.uiDialog.dialog('moveToTop');
	}
	else
	{
		/*
		 * If they saved the previous position then restore it
		 */
		if (dialogObj.savePosition == true)
		{
			var xPos = getData(dialogObj.objectId + 'x');
			var yPos = getData(dialogObj.objectId + 'y');
			
			// if xPos and yPos set then override the default position specified
			if (xPos != null && yPos != null)
			{
				dialogObj.position = [parseInt(xPos), parseInt(yPos)];
			}
		}
		
		var savedWidth = getData(this.dialogObj.objectId + 'Xsize');
		var savedHeight = getData(this.dialogObj.objectId + 'Ysize');
		if (savedWidth != null && savedHeight != null)
		{
			dialogObj.height = parseInt(savedHeight);
			dialogObj.width = parseInt(savedWidth);
		}
	
		this.dialogObj.populate(this.dialogObj.getId());

		this.uiDialog = $(this.primaryObjectId).dialog({
			autoOpen: true,
			title: dialogObj.title,
			height: dialogObj.height,
			width: dialogObj.width,
			modal: dialogObj.modal,
			buttons: dialogObj.buttons,
			show: dialogObj.showEffect,
			hide: dialogObj.hideEffect,
			position: dialogObj.position,
			close: function()
			{
				dialogObj.closeWindow();
			}
		});
		$(this.primaryObjectId).data('dialog', 'dialog');

		/*
		 * If they want to perserve the last known position
		 */
		if (dialogObj.savePosition == true)
		{
			var thisObj = this;
			
			this.bindAction(dialogObj.objectId, 'dialogdragstop', function (event, ui) { thisObj.setXYPosition(event, ui);});
			this.bindAction(dialogObj.objectId, 'dialogresizestop', function (event, ui) { thisObj.setXYSize(event, ui);});
		}
		
		if (timeout > 0)
		{
			var timerId = setInterval(function()
			{
				$(objectIdElement).dialog('close');
				clearInterval(timerId);
			}, timeout);
		}
	}

	dialogObj.initCall();
	this.setXYPosition = function (event, ui)
	{
		saveData(this.dialogObj.objectId + 'x', ui.position.left, 1000);
		saveData(this.dialogObj.objectId + 'y', ui.position.top, 1000);
	};
	
	this.setXYSize = function (event, ui)
	{
		saveData(this.dialogObj.objectId + 'Xsize', ui.size.height, 1000);
		saveData(this.dialogObj.objectId + 'Ysize', ui.size.width, 1000);
	};

	return this;
}