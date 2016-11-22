function selectSiteTheme(actionUrl, themeSelection)
{
	var actionString = actionUrl + "?option=theme&settheme=" + themeSelection;
	
  $.get(actionString, function(retData)
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
        alert("Error: Unable to process request - " + actionString);
      }
    });
}