function NameValuePair(name, value)
{
	this.m_name = name;
	this.m_value = value;
	
	this.getName = function ()
	{
		return this.m_name;
	};
	
	this.getValue = function ()
	{
		return this.m_value;
	};
}

/*
 * dialog field object
 */
function DialogField(objectId, name, action, fieldType, validationType, minLength, maxLength)
{
	this.objectId = objectId;
	this.name = name;
	this.action = action;
	this.fieldType = fieldType;
	this.validationType = validationType;
	this.minLength = minLength;
	this.maxLength = maxLength;
	this.option = 0;
	this.selections = {};
	this.uiOptions = {};
	this.associatedObject = null;
	this.children = new Array();
	this.index = 0;
	
	this.addUIOption = function (option, value)
	{
		this.uiOptions[option] = value;
	};
	
	this.addField = function (objectId, name, action, fieldType, validationType, minLength, maxLength)
	{
		var fieldObj = new DialogField(objectId, name, action, fieldType, validationType, minLength, maxLength);

		this.addChild(fieldObj);

		return fieldObj;
	};

	this.addFieldObj = function(fieldObj)
	{
		this.addChild(fieldObj);
	};

	this.addFieldBreak = function()
	{
		var fieldObj = new DialogField(null, null, null, 'break', null, 0, 0);

		this.addChild(fieldObj);
	};

	this.addChild = function (child)
	{
		this.children[this.index++] = child;
	};
	
	/*
	 * for pre-populating a selection box
	 * expecting name/value objects
	 */
	this.addSelectionType = function(selectionName, selectionValue)
	{
		var nameValue = new NameValuePair(selectionName, selectionValue);
		
		this.selections[this.option++] = nameValue;
	};
	
	this.addSelectionType = function(selectionType)
	{
		var nameValue = new NameValuePair(selectionType, selectionType);
		this.selections[this.option++] = selectionType;
	};
	
	this.addSelectionTypes = function(typeArray)
	{
		for (var index = 0; index < typeArray.length; index++)
		{
			this.selections[this.option++] = new NameValuePair(typeArray[index], typeArray[index]);
		}
	};
	
	/*
	 * runtime selection population
	 */
	this.applySelectionType = function (value, label)
	{
		var option = document.createElement('option');
		
		option.text = label;
		option.value = value;
		
		var selection = document.getElementById(this.objectId);
		try
		{
			selection.add(option, null); // standards compliant; doesn't work in IE
		}
		catch(ex)
		{
			selection.add(option); // IE only
		}
	};
	
	this.validate = function()
	{
		var valid = true;
		
		if (this.validationType != null)
		{
			valid = validateRegX(this.objectId, this.validationType, null);
		}
		if (valid == true)
		{
			valid = validateLength(this.objectId, this.name, this.minLength, this.maxLength);
		}
		return valid;
	};
	this.getValue = function(valueId)
	{
		var variableName = '#' + this.objectId;
		var returnValue = null;
		
		switch (this.fieldType)
		{
			case 'color_picker':
			{
				if (this.associatedObject != null)
				{
					if (valueId == 0) // 0 based, 
					{
						returnValue = this.associatedObject.getCurrentColor();
					}
					else
					{
						returnValue = this.associatedObject.getOpacity();
					}
				}
			}
			break;

			case 'select':
			{
				returnValue = null;
				if ((valueId == undefined) || (valueId == 0))
				{
					returnValue = $(variableName  + ' option:selected').val();
				}
				else
				{
					returnValue = $(variableName + ' option:selected').text();
				}
			}
			break;

			case 'canvas':
			{
				returnValue = '0';
			}
			break;

			case 'slider':
			{
				returnValue = $(variableName).slider("option", "value");
			}
			break;
			
			case 'checkbox':
			{
				returnValue = $(variableName).prop('checked');
			}
			break;
			
			default:
			{
				returnValue = $(variableName).val();
			}
			break;
		}
		return returnValue;
	};
	
	this.setValue = function(value, value2)
	{
		var variableName = '#' + this.objectId;
		
		switch (this.fieldType)
		{
			case 'file':
			{
				$(variableName).html(value);
			}
			break;
			case 'color_picker':
			{
				if (this.associatedObject != null)
				{
					this.associatedObject.setCurrentColor(value);
					this.associatedObject.setOpacity(value2);
				}
			}
			break;
			case 'menu':
			{
				this.associatedObject.addCommand(value, value2); // add this command to the menu
			}
			break;
			case 'select':
			{
				$(variableName + " option[value=" + value + "]").attr("selected",true);
			}
			break;
			case 'slider':
			{
				$(variableName).slider("option", "value", value);
			}
			break;
			case 'checkbox':
			{
				$(variableName).prop('checked', value);
			}
			break;
			case 'canvas':
			{
				// do nothing...
			}
			break;
			default:
			{
				$(variableName).val(value);
			}
		}
	};
	
	this.setUIOption = function (option, value)
	{
		var variableName = '#' + this.objectId;
		
		switch (this.fieldType)
		{
			case 'slider':
			{
				$(variableName).slider("option", option, value);
			}
			break;
			case 'checkbox':
			{
				$(variableName).button("option", option, value);
			}
			break;
			default:
			{
			}
		}
	};
	
	this.hasClass = function (queryClass)
	{
		var variableName = '#' + this.objectId;
		
		return $(variableName).hasClass(queryClass);
	};

	this.addClass = function (newClass)
	{
		var variableName = '#' + this.objectId;
		
		return $(variableName).addClass(newClass);
	};
	
	this.removeClass = function (oldClass)
	{
		var variableName = '#' + this.objectId;
		
		return $(variableName).removeClass(oldClass);
	};

	this.toggleClass = function (toggleClass)
	{
		var variableName = '#' + this.objectId;
		
		return $(variableName).toggleClass(toggleClass);
	};
}


/*
 * PopUp object for a popup window
 */
function PopUp(objectId, title, width, height)
{
	this.objectId = objectId;
	this.title = title;
	this.width = width;
	this.height = height;
	this.target = null;
	this.submitmethod = null;
	this.modal = true;
	this.savePosition = true;
	this.returnValue = 0;
	this.buttons = new Array(); //{};
	this.fields = {};
	this.index = 0;
	this.position = { my: "center", at: "center", of: window };
	this.showEffect = 'slide';
	this.hideEffect = 'slide';
	this.initCall = function () { $.noop();};
	this.closeWindow = function () { $.noop();};
	
	this.addButton = function(id, name, action)
	{
		var fields = {};
		
		if (name != null)
		{
			fields['text'] = name;
		}
		fields['id'] = id;
		fields['click'] = action;
		this.buttons.push(fields);
	};

	this.addIconButton = function(id, name, icon, action)
	{
		var fields = {};
		
		if (name != null)
		{
			fields['text'] = name;
		}
		fields['id'] = id;
		fields['click'] = action;
		fields['icons'] = {primary : icon};
		
		this.buttons.push(fields);
	};
	
	this.addField = function (objectId, name, action, fieldType, validationType, minLength, maxLength)
	{
		var fieldObj = new DialogField(objectId, name, action, fieldType, validationType, minLength, maxLength);
		this.addFieldObj(fieldObj);

		return fieldObj;
	};
	
	this.addFieldObj = function(fieldObj)
	{
		this.fields[this.index++] = fieldObj;
	};
	
	this.addFieldBreak = function()
	{
		this.fields[this.index++] = new DialogField(null, null, null, 'break', null, 0, 0);
	};
	
	this.getValue = function(objectId, valueId)
	{
		var returnValue = null;
		
		for (var index = 0; index < this.index; index++)
		{
			if (this.fields[index].objectId == objectId)
			{
				returnValue = this.fields[index].getValue(valueId);
				break;
			}
			else if (this.fields[index].children.length > 0)
			{
				var valueFound = false;
				var childObj = this.fields[index].children;
				for (var child = 0; child < childObj.length; child++)
				{
					if (childObj[child].objectId == objectId)
					{
						returnValue = childObj[child].getValue(valueId);
						valueFound = true;
						break;
					}
				}
				
				if (valueFound == true)
				{
					break;
				}
			}
		}
		return returnValue;
	};
	
	this.setValue = function(objectId, value, value2)
	{
		for (var index = 0; index < this.index; index++)
		{
			if (this.fields[index].objectId == objectId)
			{
				this.fields[index].setValue(value, value2);
				break;
			}
			else if (this.fields[index].children.length > 0)
			{
				var valueSet = false;
				var childObj = this.fields[index].children;
				for (var child = 0; child < childObj.length; child++)
				{
					if (childObj[child].objectId == objectId)
					{
						childObj[child].setValue(value, value2);
						valueSet = true;
						break;
					}
				}
				
				if (valueSet == true)
				{
					break;
				}
			}
		}
	};
	
	this.setUIOption = function (objectId, option, value)
	{
		for (var index = 0; index < this.index; index++)
		{
			if (this.fields[index].objectId == objectId)
			{
				this.fields[index].setUIOption(option, value);
				break;
			}
		}
	};
}
/*
 * showPopup
 * 
 * designed to display a popup window using jQueryUI.  The following assumptions are made:
 * 
 * @param objectId - the object that will be the popup window
 * @param timeout - a timeout if the popup needs to fade away, null for modal
 */
function showPopup(popupObj, timeout)
{
	this.popupObj = popupObj;
	this.timeout = timeout;
	this.uiDialog = null;
	
	this.primaryObjectId = '#' + popupObj.objectId;
	
	this.addClass = function (newClass)
	{
		$(this.primaryObjectId).dialog('option', 'dialogClass', newClass);
	};
	
	this.bindAction = function (fieldId, bindName, action)
	{
		$('#' + fieldId).bind(bindName, action);
	};
	
	this.addBreak = function (parentId)
	{
		var formEntry = '<br />';

		$(parentId).children('form').append(formEntry);
	};
	this.addHorizontalRule = function(parentId, fieldId, fieldName)
	{
		var formEntry = '<hr id="' + fieldId + '" class="' + fieldName + '" />';

		var hasForm = $(parentId).children('form').length != 0;
		
		if (hasForm == true)
		{
			$(parentId).children('form').append(formEntry);
		}
		else
		{
			$(parentId).append(formEntry);			
		}
	};
	this.addSection = function (parentId, fieldId, fieldName, action)
	{
		var formEntry = '<section id="' + fieldId + '">';
		
		if (fieldName != null)
		{
			formEntry += '<label id="' + fieldId + 'lbl">' + fieldName + '</label>';
		}
		formEntry += '</section>';

		$(parentId).append(formEntry);

		// assign the action of the section if any
		if (action != null)
		{
			$('#' + fieldId).click(action);
		}
	};

	this.addChildSection = function (parentId, fieldId, fieldName, action, children)
	{
		var formEntry = '<section id="' + fieldId + '">';
		
		if (fieldName != null)
		{
			formEntry += '<label id="' + fieldId + 'lbl">' + fieldName + '</label>';
		}
		formEntry += '</section>';

		var hasForm = $(parentId).children('form').length != 0;
		
		if (hasForm == true)
		{
			$(parentId).children('form').append(formEntry);
		}
		else
		{
			$(parentId).append(formEntry);			
		}

		// now append the children
		for (var child = 0; child < children.length; child++)
		{
//			$('#' + fieldId).append('<label>test</label>');			
		}

		// assign the action of the section if any
		if (action != null)
		{
			$('#' + fieldId).click(action);
		}
	};
	
	this.addCanvas = function (parentId, fieldId, action)
	{
		var formEntry = '<canvas class="ui-widget ui-widget-content" id="' + fieldId + '"></canvas>';
		
		$(parentId).children('form').append(formEntry);

		// assign the action of the canvas if any
		if (action != null)
		{
			$('#' + fieldId).click(action);
		}
	};
	
	this.addSlider = function(parentId, fieldId, fieldName, options, action)
	{
		var formEntry = '<div class="' + fieldId + 'container">';
				
		if (fieldName != null)
		{
			formEntry += '<label for="' + fieldId + '" class="' + fieldId + 'label">' + fieldName + '</label>';
		}

		var needValueBox = false;
		var hasRange = false;
		
		if (options["valuebox"] !== undefined )
		{
			needValueBox = true;
		}

		if (options["range"] !== undefined )
		{
			hasRange = options["range"];
		}

		if (hasRange == true)
		{
			formEntry += '<input id="' + fieldId +  'inputhigh"';
			
			if (options["readonly"] !== undefined)
			{
				formEntry += ' readonly="readonly"';
			}
			formEntry += '/>';
		}		
		formEntry += '<div id="' + fieldId + '" class="ui-slider '; //ui-slider-horizontal ui-widget ui-widget-content ui-corner-all">';
		if (options["orientation"] === undefined )
		{
			formEntry += 'ui-slider-horizontal';
		}
		else
		{
			if (options["orientation"] == 'vertical')
			{
				formEntry += 'ui-slider-vertical';
			}
			else
			{
				formEntry += 'ui-slider-horizontal';
			}				
		}
		formEntry += ' ui-widget ui-widget-content ui-corner-all"></div>';
		
		if (needValueBox == true)
		{
			formEntry += '<input id="' + fieldId +  'input';
			if (hasRange == true)
			{
				formEntry += 'low';
			}
			formEntry += '"';
			
			if (options["readonly"] !== undefined)
			{
				formEntry += ' readonly="readonly"';
			}
			formEntry += '/>';
		}
		formEntry += '</div>';

		var hasForm = $(parentId).children('form').length != 0;
		
		if (hasForm == true)
		{
			$(parentId).children('form').append(formEntry);
		}
		else
		{
			$(parentId).append(formEntry);			
		}
		
		$("#" + fieldId).slider(options);

		$("#" + fieldId).bind("slide", action);
	};
	
	this.addButton = function (parentId, fieldId, fieldName, action)
	{
//		formEntry = '<button class="ui-button ui-widget ui-state-default ui-corner-all" id="' + fieldId + '">';
//		formEntry += popupObj.fields[index].name + '</button>';
		var formEntry = '<input type="button" class="ui-button ui-widget ui-state-default ui-corner-all" id="' + fieldId + '"';
		formEntry += 'value="' + fieldName + '"/>';
		
		$(parentId).children('form').append(formEntry);

		// assign the action of the button
		$('#' + fieldId).click(action);
	};
	
	this.addCheckbox = function (parentId, fieldId, fieldName, action)
	{
		var formEntry = '<input type="checkbox" class="ui-button ui-widget ui-state-default ui-corner-all" id="' + fieldId + '"/>';
//		formEntry += 'value="' + fieldName + '"/>';
		
		if (fieldName != null)
		{
			formEntry += "<label>" + fieldName + "</label>";
		}
		$(parentId).children('form').append(formEntry);

		// assign the action of the button
		if (action != null)
		{
			$('#' + fieldId).click(action);
		}
	};

	this.addSelection = function(parentId, fieldId, fieldName, options, numOptions, action)
	{
		var formEntry = '<label for="' + fieldId + '" class="' + fieldId + 'label">';
		formEntry += fieldName  + ':';
		formEntry += '<select id="' + fieldId + '" name="' + fieldId + '">';
		for (var option = 0; option < numOptions; option++)
		{
			formEntry += '<option value="' + options[option].getValue() + '">' + options[option].getName() + '</option>'; 
		}
		formEntry += '</select></label>';
		
		$(parentId).children('form').append(formEntry);
		
		if (action != null)
		{
			$("#" + fieldId).bind("change", action);
		}
	};

	this.addImage = function (parentId, fieldId, fieldName)
	{
		var formEntry = '<img name="' + fieldId + '" id="' + fieldId;
		formEntry += '" class="ui-widget-content ui-corner-all"/>';

		var hasForm = $(parentId).children('form').length != 0;
		
		if (hasForm == true)
		{
			$(parentId).children('form').append(formEntry);
		}
		else
		{
			$(parentId).append(formEntry);			
		}
	};
	
	this.addMenu = function (parentId, fieldId, fieldName)
	{
		var formEntry = '<div id="' + fieldId + '" class=" ui-dialog ui-widget ui-widget-content ui-corner-all"></div>';

		$(parentId).children('form').append(formEntry);
		
		var popMenu = PopupMenu(fieldId, null);

		// create and draw it later
//		popMenu.drawMenu();
		
		return popMenu;
	};
	
	this.addColorPicker = function (parentId, fieldId, fieldName)
	{
		var formEntry = '<div id="' + fieldId + '" class=" hidden ui-dialog ui-widget ui-widget-content ui-corner-all"></div>';

		$(parentId).children('form').append(formEntry);

		// Things to do after this item is in the dom
		var colorPicker = new ColorPicker(fieldId);

		colorPicker.drawColorPicker();
		
		return colorPicker;
	};

	this.addList = function (parentId, fieldId, fieldName)
	{
		var formEntry = '<label for="' + fieldId + '" class="' + fieldId + '">';
		formEntry += fieldName  + ':<ul name="' + fieldId + '" id="' + fieldId;
		formEntry += '" class="ui-widget-content ui-corner-all"/>';
		formEntry += '</label>';

		$(parentId).children('form').append(formEntry);
	};

	this.addIframe = function (parentId, fieldId)
	{
		var formEntry = '<iframe name="' + fieldId + '" id="' + fieldId;
		formEntry += '" class="ui-widget-content ui-corner-all"/>';

		$(parentId).children('form').append(formEntry);
	};
	
	this.addTextArea = function(parentId, fieldId, fieldName, rows, cols)
	{
		var formEntry = '<label for="' + fieldId + '" class="' + fieldId + '">';
		formEntry += fieldName  + ':<input type="textarea" name="';
		formEntry += fieldId + '" id="' + fieldId + '" class="ui-widget-content ui-corner-all"';
		formEntry += ' rows="' + rows + '" cols="' + cols + '" />';
		formEntry += '</label>';

		$(parentId).children('form').append(formEntry);
	};
	
	this.addHidden = function (parentId, fieldId, fieldName, fieldValue)
	{
		var formEntry = '<input type="hidden" name="' + fieldId + '" value="' + fieldValue;
		formEntry += '" id="' + fieldId + '" class="hidden ui-widget-content ui-corner-all"/>';

		$(parentId).children('form').append(formEntry);
	};
	
	this.addLabel = function(parentId, fieldId, fieldName)
	{
		formEntry = '<label class="' + fieldId + 'label" id="' + fieldId + '">';
		formEntry += fieldName + '</label>';
		
		var hasForm = $(parentId).children('form').length != 0;
		
		if (hasForm == true)
		{
			$(parentId).children('form').append(formEntry);
		}
		else
		{
			$(parentId).append(formEntry);			
		}		
	};

	this.addSubmit = function (parentId, fieldId, fieldName)
	{
		var formEntry = '<input type="submit" name="';
		formEntry += fieldId + '" id="' + fieldId + '" class="ui-widget-content ui-corner-all"/>';

		$(parentId).children('form').append(formEntry);
	};
	
	this.addInput = function (parentId, fieldId, fieldType, fieldName, action, readOnly)
	{
		formEntry = '<label for="' + fieldId + '" class="' + fieldId + 'label" id="' + fieldId + 'lbl">';
		formEntry += fieldName  + ':<input type="' + fieldType;
		formEntry += '" name="' + fieldId + '" id="' + fieldId + '" class="ui-widget-content ui-corner-all"';
		if (readOnly == true)
		{
			formEntry += ' readonly="readonly"';
		}
		formEntry += '></label>';

		var hasForm = $(parentId).children('form').length != 0;
		
		if (hasForm == true)
		{
			$(parentId).children('form').append(formEntry);
		}
		else
		{
			$(parentId).append(formEntry);			
		}
		
		if (fieldType == 'file')
		{
			// ensure form has proper encoding time 				//enctype="multipart/form-data"			
			$(parentId).children('form').attr("enctype", "multipart/form-data");
		}
		
		if (action != null)
		{
			$("#" + fieldId).bind("change", action);
		}
	};

	this.addSvg = function(parentId, fieldId, fieldType, fieldName, action)
	{
		var formEntry = '<svg name="' + fieldId + '" id="' + fieldId + '" class="ui-widget-content ui-corner-all"/>';

		var hasForm = $(parentId).children('form').length != 0;
		
		if (hasForm == true)
		{
			$(parentId).children('form').append(formEntry);
		}
		else
		{
			$(parentId).append(formEntry);			
		}

		if (action != null)
		{
			$('#' + fieldId).click(action);
		}
	};
	
	this.populate = function(fieldCount, fieldArray, parentId)
	{
		var fieldId = null;
		var fieldName = null;
		
		// add in each of our fields
		for (var index = 0; index < fieldCount; index++)
		{
			fieldId = fieldArray[index].objectId;
			fieldName = fieldArray[index].name;
			
			switch (fieldArray[index].fieldType)
			{
				case 'break':
				{
					this.addBreak(parentId);
				}
				break;
				case 'hr':
				{
					this.addHorizontalRule(parentId, fieldId, fieldName);
				}
				break;
				case 'canvas':
				{
					this.addCanvas(parentId, fieldId, fieldArray[index].action);
				}
				break;
				case 'slider':
				{
					this.addSlider(parentId, fieldId, fieldName, fieldArray[index].uiOptions, fieldArray[index].action);
				}
				break;
				case 'button':
				{
					this.addButton(parentId, fieldId, fieldName, fieldArray[index].action);
				}
				break;
				case 'checkbox':
				{
					this.addCheckbox(parentId, fieldId, fieldName, fieldArray[index].action);
				}
				break;
				case 'select':
				{
					this.addSelection(parentId, fieldId, fieldName, fieldArray[index].selections, fieldArray[index].option, fieldArray[index].action);
				}
				break;
				case 'section':
				{
					this.addSection(parentId, fieldId, fieldName, fieldArray[index].action);
				}
				break;
				case 'childsection':
				{
					this.addChildSection(parentId, fieldId, fieldName, fieldArray[index].action, fieldArray[index].children);
				}
				break;
				case 'color_picker':
				{
					fieldArray[index].associatedObject = this.addColorPicker(parentId, fieldId, fieldName);
				}
				break;				
				case 'menu':
				{
					fieldArray[index].associatedObject = this.addMenu(parentId, fieldId, fieldName);
				}
				break;
				case 'image':
				{
					this.addImage(parentId, fieldId, fieldName);
				}
				break;
				case 'list':
				{
					this.addList(parentId, fieldId, fieldName);
				}
				break;				
				case 'iframe':
				{
					this.addIframe(parentId, fieldId);
				}
				break;
				case 'label':
				{
					this.addLabel(parentId, fieldId, fieldName);					
				}
				break;
				case 'textarea':
				{
					this.addTextArea(parentId, fieldId, fieldName, fieldArray[index].minLength, fieldArray[index].maxLength);
				}
				break;
				case 'hidden':
				{
					this.addHidden(parentId, fieldId, fieldName, fieldName);
				}
				break;
				case 'submit':
				{
					this.addSubmit(parentId, fieldId, fieldName);
				}
				break;
				case 'readonlytext':
				{
					this.addInput(parentId, fieldId, fieldArray[index].fieldType, fieldName, fieldArray[index].action, true);					
				}
				break;
				case 'svg':
				{
					this.addSvg(parentId, fieldId, fieldArray[index].fieldType, fieldName, fieldArray[index].action);
				}
				break;
				case 'file':
				default:
				{
					this.addInput(parentId, fieldId, fieldArray[index].fieldType, fieldName, fieldArray[index].action, false);
				}
				break;				
			}
			
			if (fieldArray[index].index > 0)
			{
				this.populate(fieldArray[index].index, fieldArray[index].children, '#' + fieldId);
			}
		}
	};
	
	if ($(this.primaryObjectId).is(':data(dialog)'))// == '42')
	{
		this.uiDialog = $(this.primaryObjectId).dialog('open');
		this.uiDialog.dialog('moveToTop');
//		$(this.primaryObjectId).dialog('moveToTop');
	}
	else
	{
		// Remove current fields - assumes that there is only one form but will keep the same id as the original
		var formId = $(this.primaryObjectId).children('form').get();
		if (formId.length == 0)
		{
			$(this.primaryObjectId).append('<form id="' + this.popupObj.objectId + 'form"></form>');
		}
		else
		{
			$(this.primaryObjectId).children('form').replaceWith('<form id="' + formId[0].id + '"></form>');
		}
		
		if (popupObj.target != null)
		{
			$(this.primaryObjectId).children('form').attr('target', popupObj.target);
		}
		
		if (popupObj.submitmethod != null)
		{
			$(this.primaryObjectId).children('form').attr('method', popupObj.submitmethod);
		}
		
		this.populate(popupObj.index, popupObj.fields, this.primaryObjectId);
		
		var objectIdElement = this.primaryObjectId;
	
		/*
		 * If they saved the previous position then restore it
		 */
		if (popupObj.savePosition == true)
		{
			var xPos = getData(popupObj.objectId + 'x');
			var yPos = getData(popupObj.objectId + 'y');
			
			// if xPos and yPos set then override the default position specified
			if (xPos != null && yPos != null)
			{
				popupObj.position = [parseInt(xPos), parseInt(yPos)];
			}
		}
		
		var savedWidth = getData(this.popupObj.objectId + 'Xsize');
		var savedHeight = getData(this.popupObj.objectId + 'Ysize');
		if (savedWidth != null && savedHeight != null)
		{
			popupObj.height = parseInt(savedHeight);
			popupObj.width = parseInt(savedWidth);
		}
	
		this.uiDialog = $(objectIdElement).dialog({
			autoOpen: true,
			title: popupObj.title,
			height: popupObj.height,
			width: popupObj.width,
			modal: popupObj.modal,
			buttons: popupObj.buttons,
			show: popupObj.showEffect,
			hide: popupObj.hideEffect,
			position: popupObj.position,
			close: function()
			{
				popupObj.closeWindow();
			}
		});
		
		/*
		 * If they want to perserve the last known position
		 */
		if (popupObj.savePosition == true)
		{
			var thisObj = this;
			
			this.bindAction(popupObj.objectId, 'dialogdragstop', function (event, ui) { thisObj.setXYPosition(event, ui);});
			this.bindAction(popupObj.objectId, 'dialogresizestop', function (event, ui) { thisObj.setXYSize(event, ui);});
		}
		
//		popupObj.initCall();
		
		if (timeout > 0)
		{
			var timerId = setInterval(function()
			{
				$(objectIdElement).dialog('close');
				clearInterval(timerId);
			}, timeout);
		}
	}
	popupObj.initCall();
	this.setXYPosition = function (event, ui)
	{
		saveData(this.popupObj.objectId + 'x', ui.position.left, 1000);
		saveData(this.popupObj.objectId + 'y', ui.position.top, 1000);
	};
	
	this.setXYSize = function (event, ui)
	{
		saveData(this.popupObj.objectId + 'Xsize', ui.size.height, 1000);
		saveData(this.popupObj.objectId + 'Ysize', ui.size.width, 1000);
	};
		
	return this;
}

/*
 * showColorPicker
 * 
 * to display a dialog for selecting a color
 * 
 * @param objectId - the object id of the parent element
 * @param callback - the method to call on ok
 */
function showColorPicker(objectId, callback)
{
	var objectIdElement = '#' + objectId;

	// does it already exist?
	if ($(objectIdElement).is(':data(dialog)'))
	{
		$(objectIdElement).dialog('open');		
		$(objectIdElement).dialog('moveToTop');
	}
	else
	{
		$(objectIdElement).dialog({
			autoOpen: true,
			title: 'Color Selection',
			height: 250,
			width: 200,
			modal: true,
			buttons: {'Ok' : function () { var event = {}; event.originalEvent = 'selectColor'; callback(event, null); $(objectIdElement).dialog('close');}, 'Cancel' : function () {$(objectIdElement).dialog('close');}},
			close: function() {
			
			}
		});
	}
}
