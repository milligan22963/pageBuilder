<?php

function getPageFooter(& $page, $themePath, $displayPosition, $callOrder)
{
	$footerData = null;
	
	if ($displayPosition == DISPLAY_PRE)
	{
		if ($callOrder == FIRST_CALL)
		{
		$footerData = <<<FOOTER_DATA
	<div id="footer">
FOOTER_DATA;
		}
		elseif ($callOrder == SECOND_CALL)
		{
		$footerData = <<<FOOTER_DATA
		Footer
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