/*
 * popmenu.js
 * 
 * used to create a popup menu
 * 
 * @param containerId which should be a div container
 * @param commandArray - an array of command objects containing the link and display text along with a description
 * 
 * @return a popupmenu object;
 */
function CommandObject(actionUrl, name, description)
{
	this.actionUrl = actionUrl;
	this.name = name;
	this.description = description;
	
	this.getName = function ()
	{
		return this.name;
	};
	
	this.setName = function (name)
	{
		this.name = name;
	};
	
	this.getDescription = function ()
	{
		return this.description;
	};
	
	this.setDescription = function (description)
	{
		this.description = description;
	};
	
	this.getActionUrl = function ()
	{
		return this.actionUrl;
	};
	
	this.setActionUrl = function (actionUrl)
	{
		this.actionUrl = actionUrl;
	};
	
	return this;
}

function PopupMenu(containerId, commandArray)
{
	this.containerId = containerId;
	this.commandArray = commandArray;
	
	this.addCommand = function (commandObject)
	{
		if (this.commandArray == null)
		{
			this.commandArray = new Array();
		}
		this.commandArray.push(commandObject);
	};
	
	this.drawMenu = function ()
	{
		var containerItem = document.getElementById(this.containerId);
		var thisObj = this;
		var div = document.createElement("div");
		div.id = this.containerId + "_wrapper";
		
		containerItem.appendChild(div);
		
		// create menu structure
		var list = document.createElement("ul");
		list.id = this.containerId + "_menu";
		div.appendChild(list);
		
		if (this.commandArray != null)
		{
			for (var index = 0; index < this.commandArray.length; index++)
			{
				var commandObject = this.commandArray[index];
				var anchor = document.createElement("a");
				anchor.href = commandObject.getActionUrl();
				anchor.name = commandObject.getName();
				anchor.innerHTML = commandObject.getName();
				var commandElement = document.createElement("li");
				commandElement.appendChild(anchor);
				list.appendChild(commandElement);
			}
		}
	};
	
	this.enable = function ()
	{
	};
	
	this.disable = function ()
	{
	};
	
	this.destroy = function ()
	{
		var containerItem = document.getElementById(this.containerId);
		
		// remove all of the children from the container that was specified

		while (containerItem.firstChild)
		{
		    containerItem.removeChild(containerItem.firstChild);
		};
	};
	
	return this;
}