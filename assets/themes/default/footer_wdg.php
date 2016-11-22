<?php
function getFooterWidget(& $page, $themePath, $displayPosition, $callOrder)
{
	$footerData = null;
	
	if ($displayPosition == DISPLAY_POST)
	{
		if ($callOrder == FIRST_CALL)
		{
/*			if (function_exists("getWidgetHeader"))
			{
				$footerData = getWidgetHeader("<div class=\"widget\">testing</div>");
			}*/
			$footerData = <<<FOOTER_DATA
	<div id="footerwidget" class="ui-widget ui-widget-content ui-corner-all">
FOOTER_DATA;
		}
		elseif ($callOrder == SECOND_CALL)
		{
			$footerData = <<<FOOTER_DATA
		footer_widget
	</div>
FOOTER_DATA;
		}
	}
  if ($footerData != null)
  {
  	$page->addBodyData($footerData);
  }
}
?>