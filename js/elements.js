/**
 * DialogObject
 * 
 * NOTE: requires inheritance.js
 */
function DialogObject()
{
	this.parent = new Array(); // we only want one of these inherited
	this.isPopulated = false;
	this.objectId = 0;
	this.text = null;
	this.action = null;
	this.children = null; // create in initialize
	this.properties = {};
	this.classData = null; // create in initialize
	this.debugMode = false;
}

DialogObject.prototype = 
{
	getId: function()
	{
		return this.objectId;
	},

	initialize: function(objectId, text, action)
	{
		this.objectId = objectId;
		this.text = text;
		this.action = action;
		this.children = new Array();
		this.classData = new Array();
		this.properties = {};
	},

	addChild: function(childField)
	{
		this.children.push(childField);
	},

	populate: function(parentId)
	{
		// populate my children
		if (this.isPopulated == false)
		{
			if (this.debugMode == true)
			{
				console.log('I am: ' + this.constructor.name);
			}
			for (var index = 0; index < this.children.length; index++)
			{
				this.children[index].populate(parentId);			
			}
			for (var classIndex = 0; classIndex < this.classData.length; classIndex++)
			{
				$('#' + this.objectId).addClass(this.classData[classIndex]);
			}

			// if any properties, then assign them
			if ($.isEmptyObject(this.properties) == false)
			{
				$('#' + this.objectId).prop(this.properties);
			}
		}
		this.isPopulated = true;
	},

	setProperty: function (property, value)
	{
		if (this.isPopulated == true)
		{
			$('#' + this.objectId).prop(property, value);
		}
		else
		{
			this.properties[property] = value;
		} 
	},

	hasClass: function (queryClass)
	{
		var variableName = '#' + this.objectId;
		
		return $(variableName).hasClass(queryClass);
	},

	addClass: function (newClass)
	{
		if (this.isPopulated == true)
		{
			var variableName = '#' + this.objectId;

			return $(variableName).addClass(newClass);
		}
		else
		{
			this.classData.push(newClass);
		}
	},
	
	removeClass: function (oldClass)
	{
		if (this.isPopulated == true)
		{
			var variableName = '#' + this.objectId;
			
			return $(variableName).removeClass(oldClass);
		}
		else
		{
			//???
			this.classData.remove(oldClass);
		}
	},

	toggleClass: function (toggleClass)
	{
		if (this.isPopulated == true)
		{
			var variableName = '#' + this.objectId;
			
			return $(variableName).toggleClass(toggleClass);
		}
	}
};

/**
 * Label
 */
function Label(objectId, text, action)
{
	this.initialize(objectId, text, action);
}
Label.inheritsFrom(DialogObject);
Label.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		// create myself first
		var childEntry = '<label class="' + this.objectId + '_label" id="' + this.objectId + '">';
		childEntry += this.text + '</label>';

		$('#' + parentId).append(childEntry);	

		// add my children to myself
		this.parent[this['Label']].populate.call(this, this.objectId);
	}
};

/**
 * Form
 */
function Form(objectId, action)
{
	this.initialize(objectId, 'none', action);
}
Form.inheritsFrom(DialogObject);
Form.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		// create myself first
		var childEntry = '<form class="' + this.objectId + '_form" id="' + this.objectId + '"></form>';
		$('#' + parentId).append(childEntry);

		// add my children to myself
		this.parent[this['Form']].populate.call(this, this.objectId);
	}
};

/**
 * Button
 */
function Button(objectId, text, action)
{
	this.initialize(objectId, text, action);
}
Button.inheritsFrom(DialogObject);
Button.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		var childEntry = '<input type="button" class="' + this.objectId + '_button" id="' + this.objectId + '"';
		childEntry += 'value="' + this.text + '"/>';
		$('#' + parentId).append(childEntry);

		// add my children to myself
		this.parent[this['Button']].populate.call(this, this.objectId);

		if (this.action != null)
		{
			// assign the action of the button
			$('#' + this.objectId).click(this.action);
		}
	}
};

/**
 * Break
 */
function Break(objectId)
{
	this.initialize(objectId, 'none', null);
}
Break.inheritsFrom(DialogObject);
Break.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		var childEntry = '<br class="' + this.objectId + '_break" id="' + this.objectId + '"/>';
		$('#' + parentId).append(childEntry);

		// add my children to myself
		this.parent[this['Break']].populate.call(this, this.objectId);
	}
};

/**
 * HorizontalRule
 */
function HorizontalRule(objectId)
{
	this.initialize(objectId, 'none', null);
}
HorizontalRule.inheritsFrom(DialogObject);
HorizontalRule.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		var childEntry = '<hr class="' + this.objectId + '_hr" id="' + this.objectId + '"/>';
		$('#' + parentId).append(childEntry);

		// add my children to myself
		this.parent[this['HorizontalRule']].populate.call(this, this.objectId);
	}
};

/**
 * Canvas
 */
function Canvas(objectId, action)
{
	this.initialize(objectId, 'none', action);
}
Canvas.inheritsFrom(DialogObject);
Canvas.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		var childEntry = '<canvas class="' + this.objectId + '_canvas" id="' + this.objectId + '"></canvas>';
		$('#' + parentId).append(childEntry);

		// add my children to myself
		this.parent[this['Canvas']].populate.call(this, this.objectId);
	}
};

/**
 * Slider
 * 
 * Requires jquery UI
 */
function Slider(objectId, text, action)
{
	this.initialize(objectId, text, action);
}
Slider.inheritsFrom(DialogObject);
Slider.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		var childEntry = '<div class="' + this.objectId + '_slidecontainer">';
				
		if (this.text != null)
		{
			childEntry += '<label for="' + this.objectId + '" class="' + this.objectId + '_label">' + this.text + '</label>';
		}

		var needValueBox = false;
		var hasRange = false;
		
		if (this.properties["valuebox"] !== undefined )
		{
			needValueBox = true;
		}

		if (this.properties["range"] !== undefined )
		{
			hasRange = this.properties["range"];
		}

		if (hasRange == true)
		{
			childEntry += '<input id="' + this.objectId +  'inputhigh"';
			
			if (this.properties["readonly"] !== undefined)
			{
				childEntry += ' readonly="readonly"';
			}
			childEntry += '/>';
		}		
		childEntry += '<div id="' + this.objectId + '" class="ui-slider ';
		if (this.properties["orientation"] === undefined )
		{
			childEntry += 'ui-slider-horizontal';
		}
		else
		{
			if (this.properties["orientation"] == 'vertical')
			{
				childEntry += 'ui-slider-vertical';
			}
			else
			{
				childEntry += 'ui-slider-horizontal';
			}				
		}
		childEntry += ' ui-widget ui-widget-content ui-corner-all"></div>';
		
		if (needValueBox == true)
		{
			childEntry += '<input id="' + this.objectId +  'input';
			if (hasRange == true)
			{
				childEntry += 'low';
			}
			childEntry += '"';
			
			if (this.properties["readonly"] !== undefined)
			{
				childEntry += ' readonly="readonly"';
			}
			childEntry += '/>';
		}
		childEntry += '</div>';

		$("#" + this.objectId).slider(this.properties);

		$("#" + this.objectId).bind("slide", this.action);
		$('#' + parentId).append(childEntry);

		this.properties = {}; // already handled, clear them

		// add my children to myself
		this.parent[this['Slider']].populate.call(this, this.objectId);
	}
};

/**
 * CheckBox
 */
function CheckBox(objectId, text, action)
{
	this.initialize(objectId, text, action);
}
CheckBox.inheritsFrom(DialogObject);
CheckBox.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		var childEntry = '<input type="checkbox" class="' + this.objectId +
		'_checkbox" id="' + this.objectId + '"/>';

		if (this.text != null)
		{
			childEntry += '<label class="' + this.objectId + '_label" for="' + this.objectId + '">' + this.text + '</label>';
		}

		$('#' + parentId).append(childEntry);

		// add my children to myself
		this.parent[this['CheckBox']].populate.call(this, this.objectId);

		if (this.action != null)
		{
			// assign the action of the button
			$('#' + this.objectId).click(this.action);
		}
	}
};

/**
 * Selection
 */
function Selection(objectId, text, action)
{
	this.selections = new Array();

	this.initialize(objectId, text, action);
}
Selection.inheritsFrom(DialogObject);
Selection.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		var childEntry = '<label for="' + this.objectId + '" class="' + this.objectId + '_label" id="' + this.objectId + '_label">';
		childEntry += this.text  + ':</label>';
		childEntry += '<select class="' + this.objectId + '_selection" id="' + this.objectId + '" name="' + this.objectId + '">';
		for (var option = 0; option < this.selections.length; option++)
		{
			childEntry += '<option value="' + this.selections[option].value + '">' + this.selections[option].name + '</option>'; 
		}
		childEntry += '</select>';
		

		$('#' + parentId).append(childEntry);

		// add my children to myself
		this.parent[this['Selection']].populate.call(this, this.objectId);

		if (this.action != null)
		{
			$("#" + this.objectId).bind("change", this.action);
		}
	}
};

Selection.prototype.addSelection = function(name, value)
{
	if (this.isPopulated == false)
	{
		var selectionObject = {'name': name, 'value': value};
		this.selections.push(selectionObject);
	}
	else
	{
		$('#' + this.objectId).append('<option value="' + value + '">' + name + '</option>');
	}
};

/**
 * Section
 */

var sectionCount = 0;
function Section(objectId, text, action)
{
	this.initialize(objectId, text, action);
}
Section.inheritsFrom(DialogObject);
Section.prototype.populate = function(parentId)
{
	sectionCount++;

	if (sectionCount > 10)
	{
		return;
	}
	if (this.isPopulated == false)
	{
		var childEntry = '<section id="' + this.objectId + '" class="' + this.objectId + '_section">';

		if (this.text != null)
		{
			childEntry += "<label class='" + this.objectId + "_label'>" + this.text + "</label>";
		}

		childEntry += '</section>';

		$('#' + parentId).append(childEntry);

		// add my children to myself
		this.parent[this['Section']].populate.call(this, this.objectId);

		if (this.action != null)
		{
			// assign the action of the button
			$('#' + this.objectId).click(this.action);
		}
	}
};

/**
 * Svg
 */
function Svg(objectId, action)
{
	this.initialize(objectId, null, action);
}
Svg.inheritsFrom(DialogObject);
Svg.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		var childEntry = '<svg id="' + this.objectId + '" class="' + this.objectId + '_svg"/>';

		$('#' + parentId).append(childEntry);

		// add my children to myself
		this.parent[this['Svg']].populate.call(this, this.objectId);

		if (this.action != null)
		{
			// assign the action of the button
			$('#' + this.objectId).click(this.action);
		}
	}
};

/**
 * Input
 */
function Input(objectId, text, inputType, action)
{
	this.inputType = inputType;

	this.initialize(objectId, text, action);
}
Input.inheritsFrom(DialogObject);
Input.prototype.setType = function(type) { this.inputType = type;};
Input.prototype.setReadOnly = function(readOnly) { this.setOption("readonly", true);};
Input.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		var childEntry = '<label for="' + this.objectId + '" class="' + this.objectId + '_label" id="' + this.objectId + '_label">';
		childEntry += this.text  + ':</label><input type="' + this.inputType;
		childEntry += '"  id="' + this.objectId + '" class="' + this.objectId + '_input"/>';

		$('#' + parentId).append(childEntry);

		// add my children to myself
		this.parent[this['Input']].populate.call(this, this.objectId);

		if (this.action != null)
		{
			// assign the action of the button
			$('#' + this.objectId).bind("change", this.action);
		}
	}
};

/**
 * FileInput
 */
function FileInput(objectId, text, parentFormId, action)
{
	this.parentFormId = parentFormId;

	this.initialize(objectId, text, action);

	this.setType("file");
}
FileInput.inheritsFrom(Input);
FileInput.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		// create the input field
		this.parent[this['FileInput']].populate.call(this, this.objectId);

		// ensure form has proper encoding type 				//enctype="multipart/form-data"			
		$('#' + this.parentFormId).attr("enctype", "multipart/form-data");

		if (this.action != null)
		{
			// assign the action of the button
			$('#' + this.objectId).bind("change", this.action);
		}
	}
};

/**
 * TextArea
 */
function TextArea(objectId, text, action)
{
	this.initialize(objectId, text, action);

	this.setType("textarea");
}
TextArea.inheritsFrom(Input);

/**
 * TODO
 * 
 * We need to be able to specify where text aligns around an element such as n,e,w,s or the like
 */

/**
 * Dialog object for a dialog window
 * 
 * @param objectId - the id of the object which this dialog is utilizing i.e. a hidden div
 * @param title - the title of the dialog
 * @param width - the width of the dialog
 * @param height - the height of the dialog
 */
function Dialog(objectId, title, width, height)
{
	this.objectId = objectId;
	this.title = title;
	this.width = width;
	this.height = height;
	this.target = null;
	this.submitmethod = null;
	this.modal = true;
	this.savePosition = false;
	this.returnValue = 0;
	this.buttons = new Array();
	this.position = { my: "center", at: "center", of: window };
	this.showEffect = 'slide';
	this.hideEffect = 'slide';
	this.initCall = function () { $.noop();};
	this.closeWindow = function () { $.noop();};
	
	this.initialize(objectId, title, null);

	// specialized buttons outside of the primary dialog
	// which in a number of cases will be a form for submitting data 
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

	this.setShowEffect = function(showEffect)
	{
		this.showEffect = showEffect;
	};

	this.setHideEffect = function(hideEffect)
	{
		this.hideEffect = hideEffect;
	};

}
Dialog.inheritsFrom(DialogObject);
Dialog.prototype.populate = function(parentId)
{
	$('#' + this.objectId).empty();
	this.parent[this['Dialog']].populate.call(this, this.objectId);
};

/**
 * test
 * 
 * for testing out the module aspects related to this module
 */
function test_elements(targetId)
{
	// does it already exist? we may need to eradicate it
	// unless we have a reset method
	var dialogObj = new Dialog(targetId, "Test", 400, 400);

	$('#' + targetId).empty();

	var form = new Form("orville", null);

	dialogObj.addChild(form);

	var label = new Label("orvlab", "Label Data", null);
	form.addChild(label);

	var breakObj = new Break("orvbreak");
	form.addChild(breakObj);

	form.addChild(new HorizontalRule("orvhr"));

	var buttonObj = new Button("orvbutt", "Press", function() { alert('pressed');});
	buttonObj.addClass("ui-button ui-widget ui-state-default ui-corner-all");
	form.addChild(buttonObj);

	form.addChild(new Canvas("orvcanvas", null));
	form.addChild(new Slider("orvslide", "Slider", null));
	form.addChild(new Break("orvbreak2"));
	form.addChild(new CheckBox("orvcheck", "Hello", null));

	var sectionObject = new Section("orvsection", "Section", null);
	form.addChild(sectionObject);

	var selectionObject = new Selection("orvselect", "Test Selection", null);
	selectionObject.addSelection('1', "One");
	selectionObject.addSelection('2', "Two");
	selectionObject.addSelection('3', "Three");	
	sectionObject.addChild(selectionObject);

	var secondSectionObject = new Section("orvsection2", "Section2", null);

	secondSectionObject.addChild(new Break("orvbreak3"));
	secondSectionObject.addChild(new Input("orvin", "InputText", "text", null));
	secondSectionObject.addChild(new FileInput("orvfile", "File", 'orville', null));

	sectionObject.addChild(secondSectionObject);

	dialogObj.populate(targetId);
}
