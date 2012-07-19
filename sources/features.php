<?php

/* ****************************************************************** /
	 -----------------------------------------------------------
	 -                                                         -
	- ######################################################### -
	- Dragon Eye CMS System							            -
	- Visit www.DragonEyeCMS.com system in order to report BUGS -
	- For contact, send an email at michael9ufo@yahoo.co.uk		-
	- Note* This is an open-source project (free for anyone)    -
	- In order to use it you must only keep our copyright       -
	- ######################################################### -
	-                                                           -
	- Best regards,                                             -
	 -		Michael9ufo                                        -
	 -----------------------------------------------------------
/* ***************************************************************** */

// Check if this page it's accessed in the right way
if (!defined('DragonEye')) die('Sorry, but you are not allowed to access this page from this location!');

$template_location[] = 'header.html';
$template_location[] = 'features.html';
$template_location[] = 'footer.html';

$template_vars['page_title'] .= ' - Features';

if(isset($GLOBALS['CONFIG_'.strtoupper($this->page).'_RES_PER_PAGE']) && isset($GLOBALS['CONFIG_'.strtoupper($this->page).'_TOTAL_RESULTS']) && isset($GLOBALS['CONFIG_'.strtoupper($this->page).'_TOTAL_ARCHIVED']))
	$cdata = new CData($this->page);

$template_vars['cdata'] = is_object($cdata) ? Template::load('styles/'.$this->page.'_basic.html', $cdata->used_vars(), 'styles/basic_default.html') : $GLOBALS['LANG_DISABLED_CONTENT'];
