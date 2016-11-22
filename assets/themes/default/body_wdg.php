<?php
function getBodyWidget(& $page, $themePath, $displayPosition, $callOrder)
{
	$bodyData = null;

	if (($displayPosition == DISPLAY_PRE) || ($displayPosition == DISPLAY_POST))
//	if ($displayPosition == DISPLAY_POST)
	{
		$bodywidgetclass = "bodywidgetpost";
		if ($displayPosition == DISPLAY_PRE)
		{
			$bodywidgetclass = "bodywidgetpre";
		}
		
		if ($callOrder == FIRST_CALL)
		{
			$bodyData = <<<BODY_DATA
	<section id="${bodywidgetclass}" class="ui-widget ui-widget-content ui-corner-all">
BODY_DATA;
		}
		elseif ($callOrder == SECOND_CALL)
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