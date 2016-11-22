<?php

function getPageFooter(& $page, $themePath, $displayPosition, $callOrder)
{
	$footerData = null;
	
	if ($displayPosition == DISPLAY_PRE)
	{
		if ($callOrder == FIRST_CALL)
		{
			$footerData = <<<FOOTER_DATA
		</section> <!-- end of body container -->
		<footer id="footer" class="ui-widget ui-widget-header ui-corner-all">
FOOTER_DATA;
		}
	}
	else if ($displayPosition == DISPLAY_POST)
	{
		if ($callOrder == SECOND_CALL)
		{
			$systemObject = getSystemObject();
			$siteAuthor = $systemObject->getConfigurationData(SITE_AUTHOR);
			$siteAuthorLink = $systemObject->getConfigurationData(SITE_AUTHOR_LINK);
			$footerData = <<<FOOTER_DATA
			<a id="footerauthor" href="$siteAuthorLink">$siteAuthor</a>
		</footer>
		</section> <!-- end of main section -->
FOOTER_DATA;
		}
	}
	if ($footerData != null)
	{
	  $page->addBodyData($footerData);
	}
}
?>