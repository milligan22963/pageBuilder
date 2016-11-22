function getElementMousePosition(mouseEvent)
{
	var documentXCoord = 0;
	var documentYCoord = 0;
	
	// get the element offset for a given event
	if (mouseEvent.pageX == null)
	{ // IE case
		var d = (document.documentElement && 
				document.documentElement.scrollLeft != null) ?
				document.documentElement : document.body;
		documentXCoord = mouseEvent.clientX + d.scrollLeft;
		documentYCoord = mouseEvent.clientY + d.scrollTop;
	}
	else
	{
		// all other browsers
		documentXCoord = mouseEvent.pageX;
		documentYCoord = mouseEvent.pageY;
	}
	
	var target = mouseEvent.target;
	var offsetLeft = 0;
	var offsetTop = 0;
	while (target != null)
	{
		offsetLeft += target.offsetLeft;// + target.clientLeft;
		offsetTop += target.offsetTop;// + target.clientTop;
		
		target = target.offsetParent;
	}
	var clientX = documentXCoord - offsetLeft;
	var clientY = documentYCoord - offsetTop;

	clientX = Math.max(0, Math.min(clientX, mouseEvent.target.clientWidth - 1));
	clientY = Math.max(0, Math.min(clientY, mouseEvent.target.clientHeight - 1));
		
/*	Target Coordinates: These are coordinates measured within the HTML element that is the target of the event. If you wanted to know where on a element the user clicked, then this is what you'd want.

	Theoretically, the event.offsetX and event.offsetY values are supposed to contain this, but forget it. There are so many bugs and incompatibilities in these values that they are essentially completely useless.

	Instead you'll need to subtract the document coordinates of the target element from the document coordinates of the mouse click. (If you are computing the coordinates of the target element, remember that offsetLeft and offsetTop are relative to offsetParent which is not necessarily the document. You may need to climb the chain of offsetParents adding up offsets as you go.)
	*/ 
	return new Point(clientX, clientY);
}