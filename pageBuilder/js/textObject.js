function TextObject(canvasElement)
{
	this.canvasElement = canvasElement;
	this.textId = 0;
	this.userId = 0;
	this.userKey = null;
	this.langId = 'en_US';
	this.textColor = '#000000';
	this.textOpacity = 100.00;
	this.textString = "";
	this.fontObject = null;
	this.textAlign = 'start';
	this.textBaseline = 'top';
	this.textPosition = 'middle';
	this.baseOffsetX = 0;
	this.baseOffsetY = 0;
	this.aspectRatio = 1.0; // default to 1 to 1 size
	
	this.extractData = function (element)
	{
		this.setTextId($(element).attr("id"));
		this.setUserId($(element).attr("userId"));
		this.setUserKey($(element).attr("userKey"));
		this.setLanguageId($(element).attr("langId"));
		this.setTextColorHex('#' + $(element).attr("textColor"));
		this.setTextOpacity(parseFloat($(element).attr("textOpacity")));
		this.setTextString($(element).attr("textString"));
		this.setTextPosition($(element).attr("position"));
		
		var fontElem = $(element).find("font");
		if (fontElem.length != 0)
		{
			$(fontElem).attr('fontSize', $(element).attr("fontSize"));
			$(fontElem).attr('fontDecoration', $(element).attr("fontDecoration"));
			this.fontObject = new FontObject();
			this.fontObject.extractData(fontElem);
		}
		this.setOffsetX(parseInt($(element).attr("offsetX")));
		this.setOffsetY(parseInt($(element).attr("offsetY")));		
	};
	
	this.setAspectRatio = function (aspectRatio)
	{
		this.aspectRatio = aspectRatio;
	};
	
	this.setUserId = function (userId)
	{
		this.userId = userId;
	};
	
	this.getUserId = function ()
	{
		return this.userId;
	};
	
	this.setUserKey = function (userKey)
	{
		this.userKey = userKey;
	};
	
	this.getUserKey = function ()
	{
		return this.userKey;
	};
	
	this.setLanguageId = function (languageId)
	{
		this.langId = languageId;
	};
	
	this.getLanguageId = function ()
	{
		return this.langId;
	};
	
	this.setTextId = function (textId)
	{
		this.textId = textId;
	};
	
	this.getTextId = function ()
	{
		return this.textId;
	};
	
	this.setTextColorHex = function (hexColor)
	{
		this.textColor = hexColor;
	};
	
	this.getTextColorHex = function ()
	{
		return this.textColor;
	};
	
	this.getTextColorRGB = function ()
	{
		return HexToRGB(this.textColor);
	};
	
	this.getTextColorRGBA = function ()
	{
		return HexToRGBA(this.textColor, this.textOpacity);
	};
	
	this.setTextOpacity = function (opacity)
	{
		this.textOpacity = opacity;
	};
	
	this.getTextOpacity = function ()
	{
		return this.textOpacity;
	};
	
	this.setTextString = function (textString)
	{
		this.textString = textString;
	};
	
	this.getTextString = function ()
	{
		return this.textString;
	};
	
	this.setFontId = function (fontId)
	{
		if (this.fontObject != null)
		{
			this.fontObject.setId(fontId);
		}
	};
	
	this.getFontId = function ()
	{
		var fontId = 0;
		
		if (this.fontObject != null)
		{
			fontId = this.fontObject.getId();
		}
		return fontId;
	};
	
	this.setFontName = function (fontName)
	{
		if (this.fontObject != null)
		{
			this.fontObject.setName(fontName);
		}
	};
	
	this.getFontName = function ()
	{
		var fontName = "Times";
		if (this.fontObject != null)
		{
			fontName = this.fontObject.getName();
		}
		return fontName;
	};
	
	this.getFontCSSName = function()
	{
		var fontName = "Times";
		if (this.fontObject != null)
		{
			fontName = this.fontObject.getFontName();
		}
		return fontName;
	};
	
	this.setFontSize = function (fontSize)
	{
		if (this.fontObject != null)
		{
			this.fontObject.setSize(fontSize);
		}
	};
	
	this.getFontSize = function ()
	{
		var fontSize = 12; // decent default
		if (this.fontObject != null)
		{
			fontSize = this.fontObject.getSize();
		}
		return fontSize;
	};
	
	this.setFontDecoration = function (fontDecoration)
	{
		if (this.fontObject != null)
		{
			this.fontObject.setDecoration(fontDecoration);
		}
	};
	
	this.getFontDecoration = function ()
	{
		var fontDecoration = ""; // decent default
		if (this.fontObject != null)
		{
			fontDecoration = this.fontObject.getDecoration();
		}
		return fontDecoration;
	};
	
	this.getWidth = function (context)
	{
		context.font = this.getFontDecoration() + ' ' + this.getFontSize() + 'pt ' + this.getFontCSSName();

		// return width for selected font/size
		var textMetric = context.measureText(this.getTextString());
		
		return Math.ceil(textMetric.width * this.aspectRatio);
	};
	
	this.getHeight = function (context)
	{
		var ems = this.getFontSize() / 12; // 1 em is 12 pt which is 16px based on an internet search...
		
		/*
		context.font = this.getFontDecoration() + ' ' + this.getFontSize() + 'pt ' + this.getFontName();

		// return height for selected font/size
		var textMetric = context.measureText(this.getTextString());
		return textMetric.width;
		*/
//		alert(Math.floor(ems * 16));
		return Math.floor((ems * 16) * this.aspectRatio);
	};
	
	this.setTextPosition = function (position)
	{
		this.textPosition = position;
	};
	
	this.getTextPosition = function ()
	{
		return this.textPosition;
	};

	this.setOffsetX = function (offsetX)
	{
		this.baseOffsetX = offsetX;
	};
	
	this.getOffsetX = function ()
	{
		return this.baseOffsetX;
	};
	
	this.setOffsetY = function (offsetY)
	{
		this.baseOffsetY = offsetY;
	};

	this.getOffsetY = function ()
	{
		return this.baseOffsetY;
	};
	
	/*
	 * drawText
	 * 
	 * used to draw text on a canvas
	 * 
	 * @param - context of canvas to use or if null then the canvas provided
	 * @param - width - overall width of the canvas
	 * @param - height - overall height of the canvas
	 * 
	 * @return none
	 */
	this.drawText = function (context, startX, startY, width, height)
	{
		if (context == null)
		{
			context = this.canvasElement.getContext('2d');
		}
		
		var textWidth = this.getWidth(context);
		var textHeight = this.getHeight(context);
		var offsetX = this.getOffsetX() + startX;
		var offsetY = this.getOffsetY() + startY;

		switch (this.textPosition)
		{
			case 'middleleft':
			{
				offsetY += (height  - textHeight) / 2;
			}
			break;
			case 'middleright':
			{
				offsetX += width - textWidth;
				offsetY += (height  - textHeight) / 2;
			}
			break;
			case 'middle':
			{
				offsetX += (width - textWidth) / 2;
				offsetY += (height  - textHeight) / 2;
			}
			break;
			case 'topleft':
			{
				// default
			}
			break;
			case 'topright':
			{
				offsetX += width - textWidth;
			}
			break;
			case 'top':
			{
				offsetX += (width - textWidth) / 2;
			}
			break;
			case 'bottomleft':
			{
				offsetY += height  - textHeight;
			}
			break;
			case 'bottomright':
			{
				offsetX += width - textWidth;
				offsetY += height  - textHeight;
			}
			break;
			case 'bottom':
			{
				offsetX += (width - textWidth) / 2;
				offsetY += height  - textHeight;
			}
			break;
		}

		context.beginPath();
		
		var fontSize = Math.ceil(this.getFontSize() * this.aspectRatio);

		context.font = this.getFontDecoration() + ' ' + fontSize + 'pt ' + this.getFontCSSName();
		context.textAlign = this.textAlign;
		context.textBaseline = this.textBaseline;

		context.fillStyle = this.getTextColorRGBA();
//		context.strokeStyle = this.getTextColorRGBA();
//		context.strokeText(this.getTextString(), 40, 100);
//		context.stroke();
//		context.fillText(fontSize, offsetX, offsetY);
		context.fillText(this.getTextString(), offsetX, offsetY);
		context.fill();
		context.closePath();
	};
}