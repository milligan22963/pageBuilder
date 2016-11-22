function registerUser(actionUrl, userName, userPassword, userEmail)
{	
	var userNameValue = document.forms['userregister'].elements[userName].value;
	var userPasswordValue = document.forms['userregister'].elements[userPassword].value;
	var userEmailValue = document.forms['userregister'].elements[userEmail].value;
	
//  $j = jQuery.noConflict();//{foo:"bar", bar:"foo"}

  $.post(actionUrl, {option:"register", userName:userNameValue, userPassword:userPasswordValue, userEmail:userEmailValue}, function(retData)
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