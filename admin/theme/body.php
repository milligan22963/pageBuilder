<?php

function getPageBody(& $page, $themePath, $displayPosition, $callOrder)
{
	$bodyData = null;
	
	if ($displayPosition == DISPLAY_PRE)
	{
		if ($callOrder == FIRST_CALL)
		{
			$bodyData = <<<BODY_DATA
	<div id="body">
		
BODY_DATA;
		}
	}
	else
	{
		if ($callOrder == SECOND_CALL)
		{
			$bodyData = <<<BODY_DATA

		</div>
BODY_DATA;
		}
	}
  if ($bodyData != null)
  {
  	$page->addBodyData($bodyData);
  }
}
?>