/*
 * by default refresh on a screen rotation
 */
$(window).on("orientationchange",function(event)
{
	location.reload(true);
});

/*
 *  common javascript code to be used when/where/etc
 */
function CallBack(object, func)
{          
    // arguments[0] == object
    // arguments[1] == func
    
    
    var argsToPass = []; // empty array
    
    // copy all other arguments we want to "pass through"
    for(var i = 2; i < arguments.length; i++)
    {
    	argsToPass.push(arguments[i]);
    }

    func.apply(object, argsToPass);
}

function Point(x, y)
{
	this.x = x;
	this.y = y;
}

/*
 * file include (http://forums.digitalpoint.com/showthread.php?t=146094)
 */
function include(filename)
{
	var found = false;
	
	var scriptElements = document.getElementsByTagName('script');
	for (var index = 0; index < scriptElements.length; index++)
	{
		if (scriptElements[index].src == filename)
		{
			found = true;
			break;
		}
	}
	
	if (found == false)
	{
		var head = document.getElementsByTagName('head')[0];
		script = document.createElement('script');
		script.src = filename;
		script.type = 'text/javascript';
		
		head.appendChild(script);
//		DisplaySomething('that should do it: ' + filename);
	}
	else
		{
		alert('already loaded!');
		}
}

/*
 * mergeObject
 * 
 * used to merge two objects into a third
 * 
 * @param object1
 * @param object2
 * 
 * @return object1 + object2 = object3
 */
function mergeObject(object1, object2)
{
    var object3 = {};
    
    for (item in object1)
    {
    	object3[item] = object1[item];
    }
    for (item in object2)
    {
    	object3[item] = object2[item];
    }
    return object3;
}

/*
 * Used to show a hint on the screen related to a domObject
 * 
 * 
 * @param objectId - the object to update
 * @param text - the text to display
 * @param highlight - the css style to apply, defaults to "highlight"
 * @param timeout - the duration of the styling/text change and/or dialog
 * 
 * requires jQuery
 * requires jQuery UI
 */
function showHint(objectId, text, highlight, timeout)
{
	if (objectId != null)
	{
		var objectIdElement = '#' + objectId;
		var oldText = $(objectIdElement).text();
		var inputElement = false;
		
		if ((oldText == null) || (oldText == ""))
		{
			inputElement = true;
			oldText = $(objectIdElement).val();
		}
		
		if (highlight == null)
		{
			highlight = 'highlight';
		}
		
		$(objectIdElement).toggleClass(highlight);
		if (text != null)
		{
			if (inputElement == true)
			{
				$(objectIdElement).val(text);
			}
			else
			{
				$(objectIdElement).text(text);
			}
		}
		var timerId = setInterval(function ()
		{
			$(objectIdElement).toggleClass(highlight);
			if (inputElement == true)
			{
				$(objectIdElement).val(oldText);
			}
			else
			{
				$(objectIdElement).text(oldText);
			}
			clearInterval(timerId);
		}, timeout);
	}
}

/*
 * validateLength * 
 * @param objectId - the object id for the variable
 * @param displayName - the name associated with the object
 * @param minValue - the minimum value
 * @param maxValue - the maximum value 
 */
function validateLength(objectId, displayName, minValue , maxValue)
{
	var success = true;
	var objectIdElement = '#' + objectId;

	var variableName = $(objectIdElement);
	
	if (variableName.val().length > maxValue || variableName.val().length < minValue)
	{
		var userMessage = "The length of " + displayName + " must be between: " + minValue + " and " + maxValue;
		showHint(objectId, userMessage, null, 1500);
		success = false;
	}
	return success;
}

function validateRegX(objectId, regularExpression, displayText)
{
	var success = true;
	var objectIdElement = '#' + objectId;

	var variableName = $(objectIdElement);
	
	// The following three "regExp" are the well knows.  More to be added as needed.
	// they are taken from the jQueryUI example for a dialog and have been updated for the password.
	switch (regularExpression)
	{
	case 'USER_NAME':
		regularExpression = /^[a-zA-Z]([0-9a-zA-Z_])+$/i;
		displayText = "User names must be alpha-numeric and may contain an underscore but must start with a letter.";
		break;
	case 'EMAIL': // this is from the jQueryUI example take from From jquery.validate.js (by joern), contributed by Scott Gonzalez: http://projects.scottsplayground.com/email_address_validation/
		regularExpression = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i;
		displayText = "Has to be username AT somewhere such as me@example.com";
		break;
	case 'PASSWORD':
		regularExpression = /^([0-9a-zA-Z@#$%^&!*])+$/;
		displayText = "Passwords have to be alpha-numeric and can include @#$%^&!*.";
		break;
	case 'CHARACTER':
		regularExpression = /^([a-zA-Z])+$/i;
		displayText = "Must enter a valid alphabetic string.";
		break;
	case 'FILENAME':
		regularExpression = /^([a-zA-Z0-9].[a-zA-Z0-9])+$/i;
		displayText = "Must enter a valid filename of format file.ext";
		break;
	case 'NUMERIC':
		regularExpression = /^([0-9])+$/i;
		displayText = "Must enter a numerical value";
		break;
		
	case 'NONE':
		return success; // get out
		break;
	}
	
	if (regularExpression.test(variableName.val()) != true)
	{
		showHint(objectId, displayText, null, 1500);
		success = false;
	}
	return success;
}


function issueExtensionPost(actionUrl, extensionName, extensionOption, params, callback)
{
	var paramList = {extension:extensionName, extensionOption:extensionOption};
	
	if (params != null)
	{
		for (item in params)
		{
			paramList[item] = params[item];
		}
	}
  $.post(actionUrl, paramList, function(retData)
    {
	    if (callback != null)
	    {
		    callback(retData);
	    }
    });
}

function issueExtensionGet(actionUrl, extensionName, extensionOption, params, callback)
{
	var paramList = {extension:extensionName, extensionOption:extensionOption};
	
	if (params != null)
	{
		for (item in params)
		{
			paramList[item] = params[item];
		}
	}
  $.get(actionUrl, paramList, function(retData)
    {
	    if (callback != null)
	    {
		    callback(retData);
	    }
    });
}
