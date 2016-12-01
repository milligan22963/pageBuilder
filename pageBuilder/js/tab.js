/**
 * tab - used to create a tabbed window interface
 */
function TabObject(objectId, name)
{
	this.id = objectId;
	this.name = name;
	
	this.show = function ()
	{
		var thisObject = document.getElementById(this.id);
		var tabObject = document.getElementById(this.id + '_tab');
		
		if (thisObject != null)
		{
			if (thisObject.classList.contains("tabHide"))
			{
				thisObject.classList.remove("tabHide");
			}
			if (thisObject.classList.contains("tabShow") == false)
			{
				thisObject.classList.add("tabShow");
			}
		}
		if (tabObject != null)
		{
			// tab should be shown as active when shown, inactive otherwise
			if (tabObject.classList.contains("tabInactive"))
			{
				tabObject.classList.remove("tabInactive");
			}
			if (tabObject.classList.contains("tabActive") == false)
			{
				tabObject.classList.add("tabActive");
			}
			if (tabObject.classList.contains("tabShow") == false)
			{
				tabObject.classList.add("tabShow");
			}
			// in case it is now showing after being disabled
			if (tabObject.classList.contains("tabDisable"))
			{
				tabObject.classList.remove("tabDisable");
			}
		}
	};
	
	this.hide = function ()
	{
		var thisObject = document.getElementById(this.id);
		var tabObject = document.getElementById(this.id + '_tab');
		
		if (thisObject != null)
		{
			if (thisObject.classList.contains("tabShow"))
			{
				thisObject.classList.remove("tabShow");
			}
			if (thisObject.classList.contains("tabHide") == false)
			{
				thisObject.classList.add("tabHide");
			}
		}
		
		if (tabObject != null)
		{
			// tab should be shown as active when shown, inactive otherwise
			if (tabObject.classList.contains("tabActive"))
			{
				tabObject.classList.remove("tabActive");
			}
			if (tabObject.classList.contains("tabInactive") == false)
			{
				tabObject.classList.add("tabInactive");
			}
			if (tabObject.classList.contains("tabShow") == false)
			{
				tabObject.classList.add("tabShow");
			}
		}
	};
	
	this.disable = function ()
	{
		var thisObject = document.getElementById(this.id);
		var tabObject = document.getElementById(this.id + '_tab');
		
		alert("Disable");
		if (thisObject != null)
		{
			if (thisObject.classList.contains("tabHide"))
			{
				thisObject.classList.remove("tabHide");
			}
			if (thisObject.classList.contains("tabShow") == false)
			{
				thisObject.classList.add("tabShow");
			}
			if (thisObject.classList.contains("tabDisable") == false)
			{
				thisObject.classList.add("tabDisable");
			}
		}
		if (tabObject != null)
		{
			// tab should be enabled when hidden and disabled when showing
			if (tabObject.classList.contains("tabDisable") == false)
			{
				tabObject.classList.add("tabDisable");
			}
			if (tabObject.classList.contains("tabShow") == false)
			{
				tabObject.classList.add("tabShow");
			}
		}
	};
}

function TabWindow(containerId)
{
	this.tabs = new Array(); // array of tab windows
	this.activeTab = null; // which tab is active
	this.containerId = containerId;
	this.menuBar = null;
	this.visualArea = null;
	this.maxWidth = 0;
	this.maxHeight = 0;
	
	// are my classes part of this document? (tabShow, tabHide, tabDisable) ?
	this.initialize = function ()
	{
		var parentContainer = document.getElementById(this.containerId);
		var menuBar = document.getElementById("tabMenuBar");
		var visualArea = document.getElementById("tabVisualArea");
		
		if (parentContainer != null)
		{
			if (visualArea)
			{
				parentContainer.removeChild(visualArea);
				this.visualArea = null;
			}
			visualArea = document.createElement("div");
			
			visualArea.id = "tabVisualArea";
			visualArea.classList.add("tabContainer");
			parentContainer.appendChild(visualArea);
			
			if (menuBar)
			{
				parentContainer.removeChild(menuBar);
				this.menuBar = null;
			}
			menuBar = document.createElement("div");
			
			menuBar.id = "tabMenuBar";
			parentContainer.appendChild(menuBar);			
		}
		this.menuBar = menuBar;
		this.visualArea = visualArea;
	};
	
	this.addTab = function (tabObject, makeActive)
	{
		this.tabs.push(tabObject);
		var thisObj = this;

		// move this into the visual area of the tab
		if (this.visualArea != null)
		{
			var tabVisual = document.getElementById(tabObject.id);
			
			if (tabVisual != null)
			{				
				// check width/height
				if (tabVisual.offsetHeight - tabVisual.offsetTop > this.maxHeight)
				{
					this.maxHeight = tabVisual.offsetHeight - tabVisual.offsetTop;
//					this.visualArea.style.minHeight = this.maxHeight + "px";
				}
				
				if (tabVisual.offsetWidth - tabVisual.offsetLeft > this.maxWidth)
				{
					this.maxWidth = tabVisual.offsetWidth - tabVisual.offsetLeft;
//					this.visualArea.style.minWidth = this.maxWidth + "px";
				}
				tabVisual.parentNode.removeChild(tabVisual);
				
				this.visualArea.appendChild(tabVisual);
			}
		}
		
		// Add a menu item for this one
		if (this.menuBar != null)
		{
			var menuItem = document.createElement("div");
			var anchor = document.createElement("a");
			
			this.menuBar.appendChild(menuItem);
			menuItem.classList.add("tab");
			menuItem.classList.add("tabEdge");
			menuItem.id = tabObject.id + '_tab';

			menuItem.appendChild(anchor);
			anchor.onclick = function () {thisObj.activateTab(tabObject.id);};
			anchor.setAttribute('name', "tab");
			anchor.appendChild(document.createTextNode(tabObject.name));
		}
		if (makeActive == true)
		{
			this.activateTab(tabObject.id); // set this one as active
		}
		else
		{
			tabObject.hide();
		}
	};
	
	this.activateTab = function (tabId)
	{
		for (var index = 0; index < this.tabs.length; index++)
		{
			if (this.tabs[index].id == tabId)
			{
				if (this.activeTab != null)
				{
					this.activeTab.hide();
				}
				this.activeTab = this.tabs[index];
				this.activeTab.show();
				break;
			}
		}
	};
	
	this.disableTab = function(tabId)
	{
		for (var index = 0; index < this.tabs.length; index++)
		{
			if (this.tabs[index].id = tabId)
			{
				this.tabs[index].disable();
				break;
			}
		}
	};
}