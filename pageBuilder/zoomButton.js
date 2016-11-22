function ExpandButton($elementId)
{
	var buttonNode = document.getElementById($elementId);
	
	buttonNode.style.width = "80px";
	buttonNode.style.height = "40px";
}

function RetractButton($elementId)
{
	var buttonNode = document.getElementById($elementId);
	
	buttonNode.style.width = "40px";
	buttonNode.style.height = "20px";
}
