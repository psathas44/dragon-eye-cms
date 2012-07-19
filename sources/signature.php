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

function draw_text($the_image, $size, $x, $y, $color, $alpha, $text)
{

	$alpha = $alpha ? $alpha : 127;

	$shadow_color = imagecolorallocate($the_image, 0, 0, 0);

	$alpha_color = imagecolorallocatealpha($the_image, 0, 0, 0, $alpha);

	$font = sep_path(CMS_DIR.'/templates/'.Template::used_template().'/fonts/verdana.ttf');

	imagettftext($the_image, $size, 0, $x + 1, $y + 2, $shadow_color, $font, $text);

	imagettftext($the_image, $size, 0, $x, $y, $color, $font, $text);

	imagettftext($the_image, $size, 0, $x, $y, $alpha_color, $font, $text);

}

if(isset($_GET['char']) && $GLOBALS['CONFIG_STATS_PLAYER_SIGNATURE_ENABLED'] && ($GLOBALS['CONFIG_STATS_PLAYER_SIGNATURE_GUESTS'] || $this->logged))
{

	$char_name = htmlspecialchars(trim($_GET['char']));

	$image_cache = sep_path(CMS_DIR.'/cache/player_'.$char_name.'.png');

	if(file_exists($image_cache) && (time() - filemtime($image_cache)) < $GLOBALS['CONFIG_STATS_PLAYER_SIGNATURE_CACHE'])
	{

		header('Content-type: image/png');

		imagecreatefrompng($image_cache);

		imagepng(imagecreatefrompng($image_cache));

	}
	else
	{

		if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
			$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_STATS_ACCESS'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_NAME'], Main::db_escape_string($char_name, $GLOBALS['DB_GAME_SERVER']), $GLOBALS['DBSTRUCT_L2OFF_USERDAT_VIEWSET'], '3', '5', '6'), $GLOBALS['DB_GAME_SERVER']);
		else
			$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_STATS_ACCESS'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_NAME'], Main::db_escape_string($char_name, $GLOBALS['DB_GAME_SERVER']), $GLOBALS['DBSTRUCT_L2J_CHARS_VIEWSET'], '3', '5', '6'), $GLOBALS['DB_GAME_SERVER']);

		if(Main::db_rows($query) == 1 || ($this->logged && strcasecmp(Main::db_result(Main::db_query(($GLOBALS['CONFIG_SERVER_TYPE'] == 1 ? sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_ACC'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_NAME'], Main::db_escape_string($char_name, $GLOBALS['DB_GAME_SERVER'])) : sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2J_CHARS_ACC'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_NAME'], Main::db_escape_string($char_name, $GLOBALS['DB_GAME_SERVER']))), $GLOBALS['DB_GAME_SERVER']), 0), $acc->account_username) == 0))
		{

			if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
				$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHAR_DATA'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_LEVEL'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_PVP'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_PK'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_CLAN'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_CLASS'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TITLE'], '0', $GLOBALS['DBSTRUCT_L2OFF_USERDAT_ONLINE_TIME'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_LAST_ACCESS'], '0', '0', $GLOBALS['DBSTRUCT_L2OFF_PLEDGE_NAME'], $GLOBALS['DBSTRUCT_L2OFF_PLEDGE_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_PLEDGE_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_PLEDGE_ID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_CLAN'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_NAME'], Main::db_escape_string($char_name, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);
			else
				$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHAR_DATA'], $GLOBALS['DBSTRUCT_L2J_CHARS_LEVEL'], $GLOBALS['DBSTRUCT_L2J_CHARS_PVP'], $GLOBALS['DBSTRUCT_L2J_CHARS_PK'], $GLOBALS['DBSTRUCT_L2J_CHARS_CLAN'], $GLOBALS['DBSTRUCT_L2J_CHARS_CLASS'], $GLOBALS['DBSTRUCT_L2J_CHARS_TITLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_RECS'], $GLOBALS['DBSTRUCT_L2J_CHARS_ONLINE_TIME'], $GLOBALS['DBSTRUCT_L2J_CHARS_LAST_ACCESS'], $GLOBALS['DBSTRUCT_L2J_CHARS_NOBLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_HERO'], $GLOBALS['DBSTRUCT_L2J_CLAN_NAME'], $GLOBALS['DBSTRUCT_L2J_CLAN_TABLE'], $GLOBALS['DBSTRUCT_L2J_CLAN_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2J_CLAN_ID'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2J_CHARS_CLAN'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_NAME'], Main::db_escape_string($char_name, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);

			$char_data = Main::db_fetch_row($query);

			header('Content-type: image/png');

			$image_path = sep_path(CMS_DIR.'/templates/'.Template::used_template().'/'.$GLOBALS['CONFIG_TEMPLATE_IMAGES'].'/player_signature.png');

			$the_image = imagecreatefrompng($image_path);

			$color_1 = imagecolorallocate($the_image, 234, 234, 174);

			$color_2 = imagecolorallocate($the_image, 255, 255, 255);

			$color_3 = imagecolorallocate($the_image, 151, 162, 120);

			$shadow_color = imagecolorallocate($the_image, 0, 0, 0);

			$shading_color = imagecolorallocatealpha($the_image, 0, 0, 0, 100);

			draw_text($the_image, 17, 15, 23, $color_1, 110, $GLOBALS['CONFIG_WEBSITE_NAME']);

			draw_text($the_image, 14, 20, 50, $color_2, 120, $GLOBALS['LANG_CHAR']);

			draw_text($the_image, 10, 65, 65, $color_2, 120, '- '.$char_name);

			draw_text($the_image, 14, 20, 85, $color_2, 120, $GLOBALS['LANG_LEVEL']);

			draw_text($the_image, 10, 65, 100, $color_2, 120, '- '.$char_data[0]);

			draw_text($the_image, 14, 300, 35, $color_2, 120, $GLOBALS['LANG_PVP']);

			draw_text($the_image, 10, 345, 50, $color_2, 120, '- '.$char_data[1]);

			draw_text($the_image, 14, 300, 70, $color_2, 120, $GLOBALS['LANG_PK']);

			draw_text($the_image, 10, 345, 85, $color_2, 120, '- '.$char_data[2]);

			draw_text($the_image, 9, 280, 110, $color_1, null, $GLOBALS['CONFIG_WEBSITE_URL']);

			imagepng($the_image);

			imagepng($the_image, $image_cache);

			imagedestroy($the_image);

		}

	}

}
elseif(isset($_GET['acc']) && $GLOBALS['CONFIG_STATS_ACCOUNT_SIGNATURE_ENABLED'] && ($GLOBALS['CONFIG_STATS_ACCOUNT_SIGNATURE_GUESTS'] || $this->logged))
{

	$acc_name = htmlspecialchars(trim($_GET['acc']));

	$image_cache = sep_path(CMS_DIR.'/cache/account_'.$acc_name.'.png');

	if(file_exists($image_cache) && (time() - filemtime($image_cache)) < $GLOBALS['CONFIG_STATS_ACCOUNT_SIGNATURE_CACHE'])
	{

		header('Content-type: image/png');

		imagecreatefrompng($image_cache);

		imagepng(imagecreatefrompng($image_cache));

	}
	else
	{

		if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
			$query = Main::db_query(sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_SIGNATURE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_ACCOUNT'], Main::db_escape_string($acc_name, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);
		else
			$query = Main::db_query(sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_SIGNATURE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], Main::db_escape_string($acc_name, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

		if(Main::db_result($query, 0) == 1 || ($this->logged && strcasecmp($acc_name, $acc->account_username) == 0))
		{

			if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
				$query = Main::db_query(sprintf($GLOBALS['DBQUERY_SIGNATURE_ACC'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_ONLINE_TIME'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_PVP'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_PK'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_ACC'], Main::db_escape_string($acc_name, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_GAME_SERVER']);
			else
				$query = Main::db_query(sprintf($GLOBALS['DBQUERY_SIGNATURE_ACC'], $GLOBALS['DBSTRUCT_L2J_CHARS_ONLINE_TIME'], $GLOBALS['DBSTRUCT_L2J_CHARS_PVP'], $GLOBALS['DBSTRUCT_L2J_CHARS_PK'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_ACC'], Main::db_escape_string($acc_name, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

			$acc_data = Main::db_fetch_row($query);

			header('Content-type: image/png');

			$image_path = sep_path(CMS_DIR.'/templates/'.Template::used_template().'/'.$GLOBALS['CONFIG_TEMPLATE_IMAGES'].'/account_signature.png');

			$the_image = imagecreatefrompng($image_path);

			$color_1 = imagecolorallocate($the_image, 234, 234, 174);

			$color_2 = imagecolorallocate($the_image, 255, 255, 255);

			$color_3 = imagecolorallocate($the_image, 151, 162, 120);

			$shadow_color = imagecolorallocate($the_image, 0, 0, 0);

			$shading_color = imagecolorallocatealpha($the_image, 0, 0, 0, 100);

			draw_text($the_image, 17, 15, 23, $color_1, 110, $GLOBALS['CONFIG_WEBSITE_NAME']);

			draw_text($the_image, 14, 20, 50, $color_2, 120, $GLOBALS['LANG_USER']);

			draw_text($the_image, 10, 65, 65, $color_2, 120, '- '.$acc_name);

			draw_text($the_image, 14, 20, 85, $color_2, 120, $GLOBALS['LANG_ON_TIME']);

			draw_text($the_image, 10, 20, 100, $color_2, 120, '- '.gmstrftime(bcdiv($acc_data[0], 86400).' '.$GLOBALS['LANG_DAYS'].' %H '.$GLOBALS['LANG_HOURS'].' %M '.$GLOBALS['LANG_MINS'], $acc_data[0]));

			draw_text($the_image, 14, 300, 35, $color_2, 120, $GLOBALS['LANG_PVP']);

			draw_text($the_image, 10, 345, 50, $color_2, 120, '- '.$acc_data[1]);

			draw_text($the_image, 14, 300, 70, $color_2, 120, $GLOBALS['LANG_PK']);

			draw_text($the_image, 10, 345, 85, $color_2, 120, '- '.$acc_data[2]);

			draw_text($the_image, 9, 280, 110, $color_1, null, $GLOBALS['CONFIG_WEBSITE_URL']);

			imagepng($the_image);

			imagepng($the_image, $image_cache);

			imagedestroy($the_image);

		}

	}

}