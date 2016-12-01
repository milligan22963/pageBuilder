/*
 * GetScreenDimensions
 * 
 * returns the inner screen dimensions of the browser window
 * requires Point() from common.js
 */
function GetScreenDimensions()
{
	var screenWidth = 0;
	var screenHeight = 0;

	// check for most browser support
	if (window.innerWidth && window.innerHeight)
	{
    	screenWidth = window.innerWidth;
    	screenHeight = window.innerHeight;
    }
  	else if (document.documentElement && document.documentElement.clientWidth && document.documentElement.clientHeight)
  	{ // for IE
    	screenWidth = document.documentElement.clientWidth;
    	screenHeight = document.documentElement.clientHeight;
	}
    return new Point(screenWidth, screenHeight);
}

/*
 * localStorageAvailable
 * 
 * checks to see if we can access local storage and use it opposed to cookies
 * 
 * @return true of available false otherwise
 */
function localStorageAvailable()
{
	var retValue = false;
	try
	{
	    if ('localStorage' in window && window['localStorage'] !== null)
	    {
	    	retValue = true;
	    }
	}
	catch (e)
	{
		retValue = false;
	}
	
	return retValue;
}

/*
 * setCookie
 * 
 * Used to set a cookie via javascript
 * 
 * @param cookieId
 * @param cookieValue
 * @param expiration (can be null) otherwise in numDays
 * @param path - default is '/'
 * 
 * @return none
 */
function setCookie (cookieId, cookieValue, expiration, path)
{
	var expirationDate = "";
	var pathValue = '; path=';
	
    if (expiration != null)
    {
        var date = new Date();
        
        date.setTime(date.getTime() + (expiration * 24 * 3600000));
        
        expirationDate = "; expires=" + date.toUTCString();
    }

    if (path == null)
    {
    	path = '/';
    }
	pathValue += path;
    document.cookie = cookieId + "=" + cookieValue + expirationDate + pathValue;
}

/*
 * getCookie
 * 
 * Used to get a cookie via javascript
 * 
 * @param cookieId
 * 
 * @return cookieValue
 */
function getCookie(cookieId)
{
    var cookieName = cookieId + "=";
    var retValue = null;
    var cookie = document.cookie.split(';');
    
    // split on ';' however extra params could be used to define a cookie
    for (var index = 0; index < cookie.length; index++)
    {
        var c = cookie[index];
        
        // trim white space
        while (c.charAt(0) == ' ')
        {
        	c = c.substring(1, c.length);
        }
        
        // if we have the name= at the beginning, its our cookie
        if (c.indexOf(cookieName) == 0)
        {
        	retValue = c.substring(cookieName.length, c.length);
        }
    }
    return retValue;
}

/*
 * deleteCookie
 * 
 * Used to remove a cookie
 * 
 * @param cookieId - id for the cookie i.e. name
 * 
 * @return none
 */
function deleteCookie(cookieId)
{
	// set the cookie to a blank value and expire it i.e. -1 is before now
    setCookie(cookieId, "", -1);
}

/*
 * saveData
 * 
 * Used to save data via javascript
 * 
 * @param dataId
 * @param dataValue
 * @param expiration (can be null) otherwise in numDays
 * 
 * @return none
 */
function saveData(dataId, dataValue, expiration)
{
	if (localStorageAvailable() == true)
	{
		var expirationDate = "";
		
	    if (expiration != null)
	    {
	        var date = new Date();
	        
	        date.setTime(date.getTime() + (expiration * 24 * 3600000));
	        
	        expirationDate = date.toUTCString();
	    }
	    localStorage[dataId] = dataValue;
	    localStorage[dataId + 'date'] = expirationDate;
	}
	else
	{
		setCookie(dataId, dataValue, expiration, '/');
	}
}

/*
 * getData
 * 
 * Used to get a data value via javascript
 * 
 * @param dataId
 * 
 * @return dataValue, null if not found our expired
 */
function getData(dataId)
{
    var dataValue = null;
    
	if (localStorageAvailable() == true)
	{
		dataValue = localStorage[dataId];
		if (dataValue != null)
		{
			expiration = localStorage[dataId + 'date'];
			var date = new Date();
			var expirationTime = Date.parse(expiration);
			if (expirationTime <= date.getTime())
			{
				dataValue = null;
			}
		}
	}
	else
	{
		dataValue = getCookie(dataId);
	}
    return dataValue;
}

/*
 * deleteData
 * 
 * Used to delete a data value via javascript
 * 
 * @param dataId
 * 
 * @return none
 */
function deleteData(dataId)
{
	if (localStorageAvailable() == true)
	{
		dataValue = localStorage[dataId];
		if (dataValue != null)
		{
			localStorage.removeItem(dataId);
			localStorage.removeItem(dataId + 'date');
		}
	}
	else
	{
		deleteCookie(dataId);
	}
}

function disableLink(e)
{
  e.preventDefault();
  return false;
}

/*
 * vertical - true if vertical, false otherwise
 * elementId - the element to toggle
 * expandId - the element to expand when toggled hidden
 * contractedValue - the desired size when not expanded
 */
function toggle(vertical, elementId, expandId, contractedValue)
{
	var elementInstance = document.getElementById(elementId);
	
	if (elementInstance != null)
	{
		var displayValue = elementInstance.style.display;
		
		/* Assume it is visible if it is currently set to null */
		if ((displayValue == null) || (displayValue != 'none'))
		{
			elementInstance.style.display = 'none';
			
			if (expandId != null)
			{
				var expandElement = document.getElementById(expandId);
				
				if (vertical == true)
				{
					expandElement.style.height = "100%";
				}
				else
				{
					expandElement.style.width = "100%";
				}
			}
		}
		else
		{
			elementInstance.style.display = "";
			if (expandId != null)
			{
				var expandElement = document.getElementById(expandId);
				
				if (vertical == true)
				{
					expandElement.style.height = contractedValue;
				}
				else
				{
					expandElement.style.width = contractedValue;
				}
			}
		}
	}
}
