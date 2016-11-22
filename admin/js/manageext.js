function manageExtension(actionUrl, objectId, extensionName, activity, selposition, sellocation)
{	
	var extensionNameValue = eval("document.forms['" + objectId + "'].elements['" + extensionName + "'].value");
	var activityValue = eval("document.forms['" + objectId + "'].elements['" + activity + "'].value");
	var positionValue = eval("document.forms['" + objectId + "'].elements['" + selposition + "'].value");
	var locationValue = eval("document.forms['" + objectId + "'].elements['" + sellocation + "'].value");
	
  $.post(actionUrl, {option:"extension", extension:extensionNameValue, action:activityValue, position:positionValue, location:locationValue}, function(retData)
    {
      var results = $(retData).find("results");
      var message = $(retData).find("returnString");

      if (results.length > 0)
      {
        if (results.text() == 1)
        {
          alert("Success: " + message.text());
        }
        else
        {
          alert("Error: " + message.text());
        }
      }
      else
      {
        alert("Error: Unable to process request - " + actionUrl);
      }
    });
}