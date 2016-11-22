var databaseCreated = false;

function createDatabase(actionUrl, replaceDatabase)
{
	var actionString = actionUrl;
	
	if (replaceDatabase)
	{
		actionString = actionUrl + "?replace=true";
	}
  $.get(actionString, function(retData)
    {
      var results = $(retData).find("results");
      var message = $(retData).find("returnString");

      if (results.length > 0)
      {
        if (results.text() == 1)
        {
          alert("Good: " + message.text());
          databaseCreated = true;
        }
        else
        {
          if (results.text() == -1)
          {
            var overwriteDB  = confirm(message.text());
            if (overwriteDB)
            {
              createDatabase(actionUrl, true);
            }
            else
            {
              databaseCreated = true; /* already exists so we are good */
            }
          }
          else
          {
            alert("Error: " + message.text());
          }
        }
      }
      else
      {
        alert("Error: Unable to process request.");
      }

      if (databaseCreated == true)
      {
        $(".hiddentable").css("visibility", "visible");
      }
    });
}

function createUserTable(actionUrl, replace, tableName)
{
	var actionString = actionUrl + "?tableName=" + tableName;
	
	if (replace == true)
	{
		actionString += "&replace=true"
	}
  $.get(actionString, function(retData)
    {
      var results = $(retData).find("results");
      var message = $(retData).find("returnString");
      if (results.length > 0)
      {
        if (results.text() == 1)
        {
          alert("Good: " + message.text());
        }
        else
        {
          if (results.text() == -1)
          {
            var overwriteTable  = confirm(message.text());
            if (overwriteTable)
            {
              createUserTable(actionUrl , true, tableName);
            }
          }
          else
          {
            alert("Error: " + message.text());
          }
        }
      }
      else
      {
        alert("Error: Unable to process request.");
      }
    });
}
