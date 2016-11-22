/*
 * CompareColors(hexColor1, hexColor2)
 * 
 * @return 1 if hexColor1 is greater, 0 if they are equal, -1 if hexColor2 is greater
 */
function CompareColors(hexColor1, hexColor2)
{
	var color1 = hexColor1.replace('#', "");
	var color2 = hexColor2.replace('#', "");
	var verdict = -1;
	
	var red1 = 0;
	var green1 = 0;
	var blue1 = 0;

	var red2 = 0;
	var green2 = 0;
	var blue2 = 0;
	
	if (color1.length > 3)
	{
		red1 = parseInt(color1.substring(0, 2), 16);
		green1 = parseInt(color1.substring(2, 4), 16);
		blue1 = parseInt(color1.substring(4, 6), 16);
	}
	else
	{
		red1 = parseInt(color1.substring(0, 1), 16);
		green1 = parseInt(color1.substring(1, 2), 16);
		blue1 = parseInt(color1.substring(2, 3), 16);
	}
	
	if (color2.length > 3)
	{
		red2 = parseInt(color2.substring(0, 2), 16);
		green2 = parseInt(color2.substring(2, 4), 16);
		blue2 = parseInt(color2.substring(4, 6), 16);
	}
	else
	{
		red2 = parseInt(color2.substring(0, 1), 16);
		green2 = parseInt(color2.substring(1, 2), 16);
		blue2 = parseInt(color2.substring(2, 3), 16);
	}

	if (red1 == red2)
	{
		if (green1 == green2)
		{
			if (blue1 == blue2)
			{
				verdict = 0;
			}
			else if (blue1 > blue2)
			{
				verdict = 1;
			}
		}
		else if (green1 > green2)
		{
			verdict = 1;
		}
	}
	else if (red1 > red2)
	{
		verdict = 1;
	}
	return verdict;
}

/*
 * HexToRGB
 * 
 * used to convert from hexidecimal color to rgb
 * 
 * @param hexColor - the color value in hexidecimal can start with # or not
 * 
 * @return rgb string such as rgb(R, G, B)
 */

function HexToRGB(hexColor)
{
	// get rid of a leading # if any
	var hexString = hexColor.replace('#', "");
	var red = 0;
	var green = 0;
	var blue = 0;
	
	if (hexString.length > 3)
	{
		red = parseInt(hexString.substring(0, 2), 16);
		green = parseInt(hexString.substring(2, 4), 16);
		blue = parseInt(hexString.substring(4, 6), 16);
	}
	else
	{
		red = parseInt(hexString.substring(0, 1), 16);
		green = parseInt(hexString.substring(1, 2), 16);
		blue = parseInt(hexString.substring(2, 3), 16);
	}
	
	return 'rgb(' + red + ', ' + green + ', ' + blue + ')';
}

/*
 * HexToRGBA
 * 
 * used to convert from hexidecimal color to rgba
 * 
 * @param hexColor - the color value in hexidecimal can start with # or not
 * @param opacity - the opacity in decimal (0.00 to 100.00)
 * 
 * @return rgba string such as rgba(R, G, B, opacity)
 */
function HexToRGBA(hexColor, opacity)
{
	var hexString = HexToRGB(hexColor).replace('rgb', 'rgba');
	var opacityValue = (parseFloat(opacity) / 100);

	return hexString.replace(')', ', ' + opacityValue + ')'); //'rgba(' + red + ', ' + green + ', ' + blue + ', ' + opacity + ')';
}

function RGBToRGBA(rgbColor, opacity)
{
	var hexString = rgbColor.replace('rgb', 'rgba');
	var opacityValue = (parseFloat(opacity) / 100);
	
	return hexString.replace(')', ', ' + opacityValue + ')');
}

function RGBToHex(red, green, blue)
{
	this.convertToHex = function (value)
	{
		var parsedValue = parseInt(value, 10);
		var hexString = "0123456789ABCDEF";
		var retValue = "00";
		
		if (isNaN(parsedValue) == false)
		{
			parsedValue = Math.max(0, Math.min(parsedValue, 255));
			retValue = hexString.charAt(Math.floor(parsedValue / 16)) + hexString.charAt(parsedValue % 16);
		}
		return retValue;
	};
	
	var retString = '#' + this.convertToHex(red) + this.convertToHex(green) + this.convertToHex(blue);
	
	return retString;
}

function RGBStringToHex(rgbString)
{
	//rgb(x, y, z)
	var stringData = rgbString.replace('rgb(', '');
	stringData = stringData.replace(')', '');
	
	var colorArray = stringData.split(',');
	
	return RGBToHex(colorArray[0], colorArray[1], colorArray[2]);
}

function RGBAStringToHex(rgbaString)
{
	//rgba(x, y, z, a)
	var stringData = rgbaString.replace('rgba(', '');
	stringData = stringData.replace(')', '');
	
	var colorArray = stringData.split(',');
	
	return RGBToHex(colorArray[0], colorArray[1], colorArray[2]);
}

function ColorPicker(containerId)
{
	this.containerId = containerId;
	this.enabled = false;
	this.currentColor = '#A0A0A0';
	this.currentOpacity = 100.0;
	this.width = 100;
	this.height = 50;
	this.borderColor = 'rgb(0, 0, 0)';
	this.borderWidth = 2;
	this.gradientSwatchWidth = 40;

	this.setState = function (enabled)
	{
		if (this.enabled != enabled)
		{
			var container = document.getElementById(this.containerId);
			var canvasElement = document.getElementById(this.containerId + '_colorswatch');
			var gradientElement = document.getElementById(this.containerId + '_gradientswatch');
			var colorValue = document.getElementById(this.containerId + '_colorselection');
			var opacityValue = document.getElementById(this.containerId + '_opacityselection');
			var thisObj = this;
			
			if (enabled == true)
			{
				colorValue.onchange = function () { thisObj.changeColor();};
				colorValue.readOnly = false;
				canvasElement.onclick = function (event) { thisObj.selectColor(event);};
				gradientElement.onclick = function (event) { thisObj.selectGradientColor(event);};				
				opacityValue.onchange = function () { thisObj.changeOpacity();};
				opacityValue.readOnly = false;
				container.style.opacity = 1.0;
			}
			else
			{
				colorValue.onchange = null;
				colorValue.readOnly = true;
				canvasElement.onclick = null;
				gradientElement.onclick = null;
				opacityValue.onchange = null;
				opacityValue.readOnly = true;
				container.style.opacity = 0.5;
			}
			this.enabled = enabled;
		}
	};
	
	this.getCurrentColor = function ()
	{
		return this.currentColor;
	};
	
	this.getCurrentColorRGB = function ()
	{
		return HexToRGB(this.currentColor);
	};
	
	this.getCurrentColorRGBA = function ()
	{
		return HexToRGBA(this.currentColor, this.currentOpacity);
	};
	
	this.setCurrentColor = function (hexColor)
	{
		this.currentColor = hexColor;
		this.refresh();
	};
	
	this.setCurrentColorRGB = function (red, green, blue)
	{
		this.currentColor = RGBToHex(red, green, blue);
	};
	
	this.setCurrentColorRGBA = function (red, green, blue, opacity)
	{
		this.setCurrentColorRGB(red, green, blue);
		this.currentOpacity = opacity;
	};
	
	this.getOpacity = function ()
	{
		return this.currentOpacity;
	};
	
	this.setOpacity = function (opacity)
	{
		this.currentOpacity = opacity;
		this.refresh();
	};
	
	this.createGradient = function (color)
	{
		var canvasElement = document.getElementById(this.containerId + '_colorswatch');
		var context = canvasElement.getContext('2d');
		var delta = this.borderWidth * 2;
		var gradient;
		
		gradient = context.createLinearGradient(this.borderWidth, this.height - delta, this.width - delta, this.borderWidth);
		
		// black
		gradient.addColorStop(0.0, 'rgb(0, 0, 0)');

		// color selection
		gradient.addColorStop(0.5, color);

		// white
		gradient.addColorStop(1.0, 'rgb(255, 255, 255)');
		
		return gradient;
	};
	
	this.selectGradientColor = function (event)
//	this.selectGradientColor = function (clientPoint)
	{
		var canvasElement = document.getElementById(this.containerId + '_gradientswatch');
		var context = canvasElement.getContext('2d');
		var clientPoint = getElementMousePosition(event);
		
		var imageData = context.getImageData(clientPoint.x, clientPoint.y, 1, 1);
		
		if (imageData != null)
		{
			// switch to the main color picker
			canvasElement = document.getElementById(this.containerId + '_colorswatch');
			context = canvasElement.getContext('2d');
			context.beginPath();
			context.fillStyle = this.createGradient('rgb(' + imageData.data[0] + ', ' + imageData.data[1] + ', ' + imageData.data[2] + ')');
			context.rect(this.borderWidth, this.borderWidth, this.width - this.borderWidth * 2, this.height - this.borderWidth * 2);
			context.fill();
		}
	};
	
	this.changeColor = function ()
	{
		var colorValue = document.getElementById(this.containerId + '_colorselection');
		this.setCurrentColor(colorValue.value);
		this.refresh();
	};
	
	this.changeOpacity = function ()
	{
		var opacityValue = document.getElementById(this.containerId + '_opacityselection');
		this.setOpacity(opacityValue.value);
		this.refresh();
	};
	
	this.selectColor = function (event)
	{
		var canvasElement = document.getElementById(this.containerId + '_colorswatch');
		var context = canvasElement.getContext('2d');
		var clientPoint = getElementMousePosition(event);
		
		var imageData = context.getImageData(clientPoint.x, clientPoint.y, 1, 1);
		
		if (imageData != null)
		{
			// get the color at the point selected
			this.setCurrentColorRGB(imageData.data[0], imageData.data[1], imageData.data[2]);
			this.refresh();
		}
	};
	
	/*
	 * drawColorPicker
	 * 
	 * used to draw a color picker that can be used
	 * to select an rgb color and opacity
	 */
	this.drawColorPicker = function ()
	{
		var containerItem = document.getElementById(this.containerId);
		var thisObj = this;
		var div = document.createElement("div");
		
		containerItem.appendChild(div);
//		containerItem.style += 'display: block;';
//		containerItem.class += 'ui-dialog ui-widget ui-widget-content ui-corner-all';
//		containerItem.style += 'ui-dialog ui-widget ui-widget-content ui-corner-all';
		
		// we need a canvas
		var canvas = document.createElement("canvas");
		
		canvas.id = this.containerId + '_colorswatch';
		
		canvas.height = this.height;
		canvas.width = this.width;
		div.appendChild(canvas);
		
		var context = canvas.getContext('2d');
		
		context.beginPath();
		context.fillStyle = this.borderColor;
		context.rect(0, 0, this.width, this.height);
		context.fill();
		context.beginPath();
		context.fillStyle = this.createGradient(this.getCurrentColorRGB());
		context.rect(this.borderWidth, this.borderWidth, this.width - this.borderWidth * 2, this.height - this.borderWidth * 2);
		context.fill();

		// a current value of color
		var colorValue = document.createElement("input");
		colorValue.id = this.containerId + '_colorselection';
		colorValue.type = 'text';
		colorValue.style.width = (this.width - 4) + 'px'; // subtract 4 for the border width
		colorValue.style.marginTop = '5px';
		colorValue.style.clear = 'left';
		colorValue.style.border = 'solid 2px black';

		containerItem.appendChild(colorValue);
		
		var gradientCanvas = document.createElement("canvas");
		gradientCanvas.id = this.containerId + '_gradientswatch';
		gradientCanvas.width = this.gradientSwatchWidth;
		gradientCanvas.height = this.height - 4;
		gradientCanvas.style.marginLeft = '5px';
		gradientCanvas.style.border = 'solid 2px black';

		div.appendChild(gradientCanvas);
		
		gradientContext = gradientCanvas.getContext('2d');
		
		gradientContext.beginPath();
		
		var gradient = gradientContext.createLinearGradient(this.gradientSwatchWidth / 2, this.height, this.gradientSwatchWidth / 2, 0);
		gradient.addColorStop(0.0, 'rgb(255, 255, 255)');
		gradient.addColorStop(0.15, 'rgb(0, 255, 255)');
		gradient.addColorStop(0.30, 'rgb(0, 0, 255)');
		gradient.addColorStop(0.45, 'rgb(0, 255, 0)');
		gradient.addColorStop(0.60, 'rgb(255, 255, 0)');
		gradient.addColorStop(0.75, 'rgb(255, 0, 0)');
		gradient.addColorStop(1.0, 'rgb(0, 0, 0)');
		gradientContext.fillStyle = gradient;
		gradientContext.rect(0, 0, this.gradientSwatchWidth, this.height);
		gradientContext.fill();

		// a slider (opacity) with value
		var colorValue = document.createElement("input");
		colorValue.id = this.containerId + '_opacityselection';
		colorValue.type = 'text';
		colorValue.style.width = this.gradientSwatchWidth  + 'px'; // subtract 4 for the border width
		colorValue.style.marginTop = '5px';
		colorValue.style.marginLeft = '5px';
		colorValue.style.clear = 'left';
		colorValue.style.border = 'solid 2px black';
		colorValue.value = this.getOpacity();
		containerItem.appendChild(colorValue);

		this.setState(true);
		this.refresh();
	};
	
	this.refresh = function ()
	{
		var colorValue = document.getElementById(this.containerId + '_colorselection');
		var opacityValue = document.getElementById(this.containerId + '_opacityselection');
		
		if (CompareColors('#7F7F7F', this.getCurrentColor()) == 1)
		{
			opacityValue.style.color = '#FFFFFF';
			colorValue.style.color = '#FFFFFF';
		}
		else
		{
			opacityValue.style.color = '#000000';
			colorValue.style.color = '#000000';
		}
		opacityValue.style.backgroundColor = this.getCurrentColorRGB();
		colorValue.style.backgroundColor = this.getCurrentColorRGBA();
		colorValue.value = this.getCurrentColor();
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
}