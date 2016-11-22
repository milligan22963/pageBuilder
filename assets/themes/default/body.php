<?php

function getPageBody(& $page, $themePath, $displayPosition, $callOrder)
{
	$bodyData = null;
	
	if ($displayPosition == DISPLAY_PRE)
	{
		if ($callOrder == FIRST_CALL)
		{				
		$bodyData = <<<BODY_DATA
	<section id="bodysection" class="ui-widget ui-widget-content ui-corner-all">
BODY_DATA;
		}
	}
	else if ($displayPosition == DISPLAY_POST)
	{
		if ($callOrder == SECOND_CALL)
		{
			$bodyData = <<<BODY_DATA
	</section>
BODY_DATA;
		}
	}
  if ($bodyData != null)
  {
  	$page->addBodyData($bodyData);
  }
}
?>