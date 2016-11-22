function loginUser(actionUrl, userName, userPassword)
{	
	var userNameValue = document.forms['userloginext'].elements[userName].value;
	var userPasswordValue = document.forms['userloginext'].elements[userPassword].value;
	
//  $j = jQuery.noConflict();//{foo:"bar", bar:"foo"}

  $.post(actionUrl, {option:"login", userName:userNameValue, userPassword:userPasswordValue}, function(retData)
    {
      var results = $(retData).find("results");
      var message = $(retData).find("returnString");
      var userName = $(retData).find("userName");

      if (results.length > 0)
      {
        if (results.text() == 1)
        {
	        // if normal embedded widget
			if ($('#loginextension').length > 0)
			{
	        	var formData = '<div class="loginextension"><form id="userloginext" class="logout" method="post" action="javascript:logoutUser(';
		  		formData += "'" + actionUrl + "');";
		  		formData += '"><label id="logoutUserName">Welcome ' + userName.text() + '</label><br /><input type="submit" value="Logout" /></form>';
		  		formData += '<hr class="extensionline"/> <a href="' + actionUrl + '">Options</a></div>';
	//          alert("Success: " + message.text());
	          $('.loginextension').replaceWith(formData);
	       }
	       else if ($('#loginlink').length > 0)
	       {
		       $('#loginlink').text("Logout");
	       }
  	        location.reload(true);

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

function logoutUser(actionUrl, registerData)
{
	this.m_logoutLink = "javascript:logoutUser('" + actionUrl + "', null);"

	var thisObj = this;
	
  $.post(actionUrl, {option:"login"}, function(retData)
    {
      var results = $(retData).find("results");
      var message = $(retData).find("returnString");

      if (results.length > 0)
      {
        if (results.text() == 1)
        {
	        // if normal embedded widget
			if ($('#loginextension').length > 0)
			{
	        	var formData = '<div class="loginextension"><form id="userloginext" class="login" method="post" action="javascript:loginUser(';
		  		formData += "'" + actionUrl + "', 'loginUserName', 'loginUserPassword');";
		  		formData += '"><label id="loginUserNameLbl">User Name:<input type="text" id="loginUserName" /></label><label id="loginUserPasswordLbl">Password:<input type="password" id="loginUserPassword" name="loginUserPassword"/></label><input id="loginUserButton" type="submit" value="Login" /></form>';
		  		if (registerData != "null")
		  		{
			  		formData += '<hr class="extensionline" /><a href="' + registerData + '">Register</a>';
		  		}
		  		formData += '</div>';
	          $('.loginextension').replaceWith(formData);
	       }
	       else if ($('#loginlink').length > 0)
	       {
		       $('#loginlink').text("Login");
			   $("#loginlink").attr("href", thisObj.m_logutLink);
	       }
		  location.reload(true);
        }
      }
      else
      {
        alert("Error: Unable to process request - " + actionUrl + " message: " + message);
      }
    });
}

function showLoginPopup(actionUrl, targetDialogId)
{
	this.m_targetDialogId = targetDialogId;
	this.m_loginDialog = null;
	this.m_jqueryDialog = null;
	this.m_loginLink = "javascript:showLoginPopup('" + actionUrl + "', 'loginpopup')";
	this.m_logoutLink = "javascript:logoutUser('" + actionUrl + "', null);";
	
	var thisObj = this;
	
	this.issueLoginPost = function()
	{
		var userNameValue = $('#loginUserName').val();
		var userPasswordValue = $('#loginUserPassword').val();
		
		$.post(actionUrl, {option:"login", userName:userNameValue, userPassword:userPasswordValue}, function(data) {thisObj.callbackMethod(data, 'login');});
	};
	this.m_loginDialog = new PopUp(thisObj.m_targetDialogId, "Login", 400, 200);
	this.m_loginDialog.addField('loginUserName', 'User Name', null, 'text', 'CHARACTER', 3, 32);
	this.m_loginDialog.addField('loginUserPassword', 'Password', null, 'password', 'CHARACTER', 3, 32);
	this.m_loginDialog.addField('loginUserWarning', 'Alert: please verify your username and/or password.', null, 'label', 'CHARACTER', 3, 32);
	this.m_loginDialog.addButton('loginBtn', 'Login',  function () {thisObj.issueLoginPost();});

    this.callbackMethod = function(retData, actionName)
    {
      var results = $(retData).find("results");
      var message = $(retData).find("returnString");
      var userName = $(retData).find("userName");

      if (results.length > 0)
      {
        if (results.text() == 1)
        {
	        if (actionName == 'login')
	        {
				$('#loginlink').text("Logout");
				$("#loginlink").attr("href", thisObj.m_logoutLink);
				thisObj.m_jqueryDialog.uiDialog.dialog("close");
				location.reload(true);
			}
			else
			{
				$('#loginlink').text("Login");
				$("#loginlink").attr("href", thisObj.m_loginLink);
			}
	    }
	    else
	    {
		    thisObj.m_jqueryDialog.uiDialog.dialog("option", 'height', 250);
		    $('#loginUserWarning').css("visibility", "visible");
	    }
	  }
	};
	
	this.m_loginDialog.addButton('login_cancel', 'Cancel', function() {thisObj.m_jqueryDialog.uiDialog.dialog( "close" );});
	
	$(this.m_targetDialogId).data('window', this);

	// when the dialog closes, we want to refresh if changes were made
	this.m_loginDialog.closeWindow = function()
	{
	};

	this.m_jqueryDialog = showPopup(this.m_loginDialog, 0);
}