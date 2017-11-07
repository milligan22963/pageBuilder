<?php
/*
 * did this so it wasn't visible to the masses
 */
 function getConfigData()
 {
	 $dbWord = 'your_pwd';
	 
	$configData =<<<XML_CONFIG_DATA
<?xml version="1.0" encoding="ISO-8859-1"?>
<?xml-stylesheet href="config.xsl" type="text/xsl" ?>
<afm:configuration xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:afm="http://www.afmsoftware.com/config" xsi:schemaLocation="http://www.afmsoftware.com/config config.xsd ">
  <afm:database>
    <afm:option name="DB_USER_NAME" value="user" />
    <afm:option name="DB_PASSWORD" value="$dbWord" />
    <afm:option name="DB_SYSTEM" value="MYSQLi" />
    <afm:option name="DB_HOST_NAME" value="localhost" />
    <afm:option name="DB_VERSION" value="1.0.0.0" />
    <afm:option name="DB_CONFIG_TABLE" value="default" />
  </afm:database>
  <afm:site>
  	<!--  these options are auto defined for you and used throughout the site change the names with caution -->
    <afm:option name="SITE_TITLE" value="PageBuilder" />
    <afm:option name="SITE_TAG_LINE" value="Another day another site..." />
    <afm:option name="SITE_AUTHOR" value="AfmSoftware" />
    <afm:option name="SITE_AUTHOR_LINK" value="http://www.afmsoftware.com/" />

    <!--  this needs to match what is in the install.xml file -->
    <afm:option name="SITE_DB_NAME" value="pb" />
    <afm:option name="SITE_TABLE_PREFIX" value="pbafm_" />
    <afm:option name="SITE_STYLE_SHEET" value="afm.css" />
    
    <!-- this is the default hash salt for passwords -->
    <afm:option name="SITE_HASH_SALT" value="T893reT>l:0234AEIkmdas9*&amp;93:0[99" />
    <!--  this is the default hash salt for usernames in cookies -->
    <afm:option name="SITE_USERNAME_SALT" value="J87#$987:LkwewPdasiesd98^D23[99]" />
    
    <!--  for generating the registration key used to activate a user -->
    <afm:option name="SITE_REGISTRATION_SALT" value="98JreTR$%98:p:qmn091#23j[op]" />

    <!--  this is the email address for user activation and the like.
          while we could use the "admin" account, it may be renamed and there may
          be multiple users with admin permissions hence one email addr here to be specific -->
    <afm:option name="SITE_ADMIN_NAME"  value="admin" />
	<afm:option name="SITE_ADMIN_EMAIL" value="dwm@afmsoftware.com" />
	
	<!-- The main root for the site -->
    <afm:option name="SITE_ROOT_PATH" value="site"></afm:option>
    <afm:option name="SITE_ADMIN_PATH" value="admin" />
    <afm:option name="SITE_CONTENT_PATH" value="assets" />
    
    <!-- theme, user content, and extensions are located under the content path - default of assets
         so this should look something like assets/themes, assets/extensions, assets/content -->
    <afm:option name="SITE_THEME_PATH" value="themes" />
    <afm:option name="SITE_EXTENSION_PATH" value="extensions" />
    <afm:option name="SITE_USER_CONTENT_PATH" value="content" />
    
    <!--  default to a 300 second session time out -->
    <afm:option name="SITE_SESSION_LENGTH" value="900" />
    
    <!-- true to allow new users, false otherwise 
         the exception is the admin can create new users at will if permitted via the next flags -->
    <afm:option name="SITE_ALLOW_NEW_USERS" value="true" />
    <afm:option name="SITE_ALLOW_ADMIN_CREATE_USERS" value="true" />
    <afm:option name="SITE_ALLOW_USER_CONTENT_UPLOAD" value="true" />
    
    <!-- true to allow the user to be activated upon registration 
         false to require manual intervention with registration link to confirm
         or admin enable -->
    <afm:option name="SITE_AUTO_ACTIVATE_REGISTRATION" value="false" />
    
    <!--  this allows the user to activate themselves, if above is false then via email
          otherwise at time of registration.  If false then the admin(s) must activate each user -->
    <afm:option name="SITE_USER_ACTIVATION" value="true" />
    
    <!--  the current page which is set at runtime for use by other parts of the system 
    		the index page will overwrite whatever is in here based on what page is loaded 
    		if no page is specified then this will be the default "home" page -->
    <afm:option name="SITE_CURRENT_PAGE_NAME" value="home" />
    
    <!--  the site extension option for the current extension -->
    <afm:option name="SITE_EXTENSION_OPTION" value="none" />
    <afm:option name="SITE_ACTIVE_EXTENSION" value="none" />

	<!-- EXPERIMENTAL! site compression and speed - default true, false for debugging -->
    <afm:option name="SITE_FLATTEN_JS" value="false" />
    
  </afm:site>
</afm:configuration>
XML_CONFIG_DATA;

  return $configData;
}

?>
