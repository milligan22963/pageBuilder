<?php
function getBodyWidget(& $page, $themePath, $displayPosition, $callOrder)
{
	$bodyData = null;
	
	if ($displayPosition == DISPLAY_PRE)
	{
		if ($callOrder == FIRST_CALL)
		{
		$bodyData = <<<BODY_DATA
	<div id="bodywidgetpre">
BODY_DATA;
		}
		elseif ($callOrder == SECOND_CALL)
		{
			$bodyData = <<<BODY_DATA
	</div>
BODY_DATA;
		}
	}
	else 
	{
		if ($callOrder == FIRST_CALL)
		{
		$bodyData = <<<BODY_DATA
	<div id="bodywidgetpost">
BODY_DATA;
		}
		elseif ($callOrder == SECOND_CALL)
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