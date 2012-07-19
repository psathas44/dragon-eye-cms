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

class Main
{

	public $page;

	public $server_type;

	public $DB_GAME_SERVER = null;

	public $DB_LOGIN_SERVER = null;

	public $logged = false;

	public $access_level = null;

	final public function __construct($page)
	{

		$this->server_type = $GLOBALS['CONFIG_SERVER_TYPE'];

		$this->page = $page;

		$this->filter_page();

		define('THE_USED_PAGE', $this->page);

		date_default_timezone_set($GLOBALS['CONFIG_DATE_TIMEZONE']);

		if($GLOBALS['CONFIG_QUERY_COUNT'])
			$GLOBALS['DB_QUERY_COUNT'] = 0;

		$GLOBALS['LAST_QRY'] = false;

		$GLOBALS['SQLSRV_ROW_RESULTS'] = array();

		$GLOBALS['DATABASE_CONNECTED'] = false;

		$GLOBALS['DB_GAME_SERVER'] = '1';

		$GLOBALS['DB_LOGIN_SERVER'] = '2';

		if(!file_exists(sep_path(CMS_DIR.'/sources/'.$this->page.'.php')))
			throw new Dragon_Eye_Exception('Can\'t find the page requested!');

	}

	final public function __destruct()
	{

		// Close all MsSql&MySql Connections
		if($this->server_type == 1 && $GLOBALS['DATABASE_CONNECTED'])
		{

			if($GLOBALS['CONFIG_USE_SQLSRV'])
			{

				@sqlsrv_close($GLOBALS['DB_GAME_SERVER_LINK']);
				@sqlsrv_close($GLOBALS['DB_LOGIN_SERVER_LINK']);

			}
			else
			{

				@mssql_close($GLOBALS['DB_GAME_SERVER_LINK']);
				@mssql_close($GLOBALS['DB_LOGIN_SERVER_LINK']);

			}

		}
		elseif($GLOBALS['DATABASE_CONNECTED'])
		{

			@mysql_close($GLOBALS['DB_GAME_SERVER_LINK']);
			@mysql_close($GLOBALS['DB_LOGIN_SERVER_LINK']);

		}

	}

	public static function connect_database()
	{

		if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
		{

			try
			{

				if($GLOBALS['CONFIG_USE_SQLSRV'] && extension_loaded('sqlsrv'))
				{

					$GLOBALS['DB_GAME_SERVER_LINK'] = sqlsrv_connect($GLOBALS['CONFIG_MSSQL_HOST_GS'],
					array(
					'Database' => $GLOBALS['CONFIG_MSSQL_NAME_GS'],
					'UID' => $GLOBALS['CONFIG_MSSQL_USER_GS'],
					'PWD' => $GLOBALS['CONFIG_MSSQL_PASS_GS']
					));

					$GLOBALS['DB_LOGIN_SERVER_LINK'] = sqlsrv_connect($GLOBALS['CONFIG_MSSQL_HOST_LS'],
					array(
					'Database' => $GLOBALS['CONFIG_MSSQL_NAME_LS'],
					'UID' => $GLOBALS['CONFIG_MSSQL_USER_LS'],
					'PWD' => $GLOBALS['CONFIG_MSSQL_PASS_LS']
					));

					if(!$GLOBALS['DB_GAME_SERVER_LINK'] || !$GLOBALS['DB_LOGIN_SERVER_LINK'])
						throw new Dragon_Eye_Exception('Can\'t connect to MsSql Server');

				}
				elseif(!extension_loaded('mssql'))
					throw new Dragon_Eye_Exception('You must enable php_mssql or sqlsrv extension!');
				else
				{

					$GLOBALS['CONFIG_USE_SQLSRV'] = false;

					if($GLOBALS['CONFIG_MSSQL_PCONNECT'])
					{

						$GLOBALS['DB_GAME_SERVER_LINK'] = @mssql_pconnect($GLOBALS['CONFIG_MSSQL_HOST_GS'], $GLOBALS['CONFIG_MSSQL_USER_GS'], $GLOBALS['CONFIG_MSSQL_PASS_GS'], false);

						if(strcasecmp($GLOBALS['CONFIG_MSSQL_HOST_LS'], $GLOBALS['CONFIG_MSSQL_HOST_GS']) == 0 && strcasecmp($GLOBALS['CONFIG_MSSQL_USER_LS'], $GLOBALS['CONFIG_MSSQL_USER_GS']) == 0 && strcasecmp($GLOBALS['CONFIG_MSSQL_PASS_LS'], $GLOBALS['CONFIG_MSSQL_PASS_GS']) == 0)
							$GLOBALS['DB_LOGIN_SERVER_LINK'] = $GLOBALS['DB_GAME_SERVER_LINK'];
						else
							$GLOBALS['DB_LOGIN_SERVER_LINK'] = @mssql_pconnect($GLOBALS['CONFIG_MSSQL_HOST_LS'], $GLOBALS['CONFIG_MSSQL_USER_LS'], $GLOBALS['CONFIG_MSSQL_PASS_LS'], false);

					}
					else
					{

						$GLOBALS['DB_GAME_SERVER_LINK'] = @mssql_connect($GLOBALS['CONFIG_MSSQL_HOST_GS'], $GLOBALS['CONFIG_MSSQL_USER_GS'], $GLOBALS['CONFIG_MSSQL_PASS_GS'], false);

						if(strcasecmp($GLOBALS['CONFIG_MSSQL_HOST_LS'], $GLOBALS['CONFIG_MSSQL_HOST_GS']) == 0 && strcasecmp($GLOBALS['CONFIG_MSSQL_USER_LS'], $GLOBALS['CONFIG_MSSQL_USER_GS']) == 0 && strcasecmp($GLOBALS['CONFIG_MSSQL_PASS_LS'], $GLOBALS['CONFIG_MSSQL_PASS_GS']) == 0)
							$GLOBALS['DB_LOGIN_SERVER_LINK'] = $GLOBALS['DB_GAME_SERVER_LINK'];
						else
							$GLOBALS['DB_LOGIN_SERVER_LINK'] = @mssql_connect($GLOBALS['CONFIG_MSSQL_HOST_LS'], $GLOBALS['CONFIG_MSSQL_USER_LS'], $GLOBALS['CONFIG_MSSQL_PASS_LS'], false);

					}

					if(!$GLOBALS['DB_GAME_SERVER_LINK'] || !$GLOBALS['DB_LOGIN_SERVER_LINK'])
						throw new Dragon_Eye_Exception('Can\'t connect to MsSql Server');

					if(@!mssql_select_db($GLOBALS['CONFIG_MSSQL_NAME_GS'], $GLOBALS['DB_GAME_SERVER_LINK']))
						throw new Dragon_Eye_Exception('Can\'t connect to MsSql Database');

					if(@!mssql_select_db($GLOBALS['CONFIG_MSSQL_NAME_LS'], $GLOBALS['DB_LOGIN_SERVER_LINK']))
						throw new Dragon_Eye_Exception('Can\'t connect to MsSql Database');

					$GLOBALS['CURRENT_DB_P'] = 'S';

					$GLOBALS['CURRENT_DB'] = '2';

				}

			}
			catch (Dragon_Eye_Exception $e)
			{

				echo $e->errorMSG();
				return false;

			}

		}
		else
		{

			try
			{

				if(!extension_loaded('mysql'))
					throw new Dragon_Eye_Exception('You must enable php_mysql extension!');
				else
				{

					if($GLOBALS['CONFIG_MYSQL_PCONNECT'])
					{

						$GLOBALS['DB_GAME_SERVER_LINK'] = @mysql_pconnect($GLOBALS['CONFIG_MYSQL_HOST_GS'], $GLOBALS['CONFIG_MYSQL_USER_GS'], $GLOBALS['CONFIG_MYSQL_PASS_GS'], false);

						if(strcasecmp($GLOBALS['CONFIG_MYSQL_HOST_LS'], $GLOBALS['CONFIG_MYSQL_HOST_GS']) == 0 && strcasecmp($GLOBALS['CONFIG_MYSQL_USER_LS'], $GLOBALS['CONFIG_MYSQL_USER_GS']) == 0 && strcasecmp($GLOBALS['CONFIG_MYSQL_PASS_LS'], $GLOBALS['CONFIG_MYSQL_PASS_GS']) == 0)
							$GLOBALS['DB_LOGIN_SERVER_LINK'] = $GLOBALS['DB_GAME_SERVER_LINK'];
						else
							$GLOBALS['DB_LOGIN_SERVER_LINK'] = @mysql_pconnect($GLOBALS['CONFIG_MYSQL_HOST_LS'], $GLOBALS['CONFIG_MYSQL_USER_LS'], $GLOBALS['CONFIG_MYSQL_PASS_LS'], false);

					}
					else
					{

						$GLOBALS['DB_GAME_SERVER_LINK'] = @mysql_connect($GLOBALS['CONFIG_MYSQL_HOST_GS'], $GLOBALS['CONFIG_MYSQL_USER_GS'], $GLOBALS['CONFIG_MYSQL_PASS_GS'], false);

						if(strcasecmp($GLOBALS['CONFIG_MYSQL_HOST_LS'], $GLOBALS['CONFIG_MYSQL_HOST_GS']) == 0 && strcasecmp($GLOBALS['CONFIG_MYSQL_USER_LS'], $GLOBALS['CONFIG_MYSQL_USER_GS']) == 0 && strcasecmp($GLOBALS['CONFIG_MYSQL_PASS_LS'], $GLOBALS['CONFIG_MYSQL_PASS_GS']) == 0)
							$GLOBALS['DB_LOGIN_SERVER_LINK'] = $GLOBALS['DB_GAME_SERVER_LINK'];
						else
							$GLOBALS['DB_LOGIN_SERVER_LINK'] = @mysql_connect($GLOBALS['CONFIG_MYSQL_HOST_LS'], $GLOBALS['CONFIG_MYSQL_USER_LS'], $GLOBALS['CONFIG_MYSQL_PASS_LS'], false);

					}

					if(!$GLOBALS['DB_GAME_SERVER_LINK'] || !$GLOBALS['DB_LOGIN_SERVER_LINK'])
						throw new Dragon_Eye_Exception('Can\'t connect to MySql Server');

					if(@!mysql_select_db($GLOBALS['CONFIG_MYSQL_NAME_GS'], $GLOBALS['DB_GAME_SERVER_LINK']))
						throw new Dragon_Eye_Exception('Can\'t connect to MySql Database');

					if(@!mysql_select_db($GLOBALS['CONFIG_MYSQL_NAME_LS'], $GLOBALS['DB_LOGIN_SERVER_LINK']))
						throw new Dragon_Eye_Exception('Can\'t connect to MySql Database');

					$GLOBALS['CURRENT_DB_P'] = 'Y';

					$GLOBALS['CURRENT_DB'] = '2';

				}

			}
			catch (Dragon_Eye_Exception $e)
			{

				echo $e->errorMSG();
				return false;

			}

		}

		$GLOBALS['DATABASE_CONNECTED'] = true;

		return true;

	}

	public function load()
	{

		if(!$GLOBALS['CONFIG_CMS_INSTALLED'] && file_exists(sep_path(CMS_DIR.'/install.php')))
			require(sep_path(CMS_DIR.'/install.php'));
		else
		{

			// Because here it's the start of page execution we set a variable which stores the current time.
			if($GLOBALS['CONFIG_EXEC_TIME'])
				$e_start = microtime(true);

			if(!isset($_GET['page']) || isset($_GET['page']) && $_GET['page'] != 'signature')
			{

				$main_flood = new AFlood('main');

				if(!$main_flood->check())
					die('You are banned from accessing this page for a period of time!');

			}

			if($GLOBALS['CONFIG_ATTACK_MODE'])
				$GLOBALS['DB_GAME_SERVER'] = $GLOBALS['DB_LOGIN_SERVER'] = null;

			// Load selected language
			$lang = new Language();

			$template_vars = array();
			$languages_vars = array();
			$template_location = array();

			$template_vars['page_title'] = $GLOBALS['CONFIG_WEBSITE_NAME'];
			$template_vars['admin_panel'] = null;
			$template_vars['e_time'] = null;
			$template_vars['query_count'] = null;
			$template_vars['maintenance'] = null;

			if($GLOBALS['CONFIG_ATTACK_MODE'])
				$template_vars['maintenance'] = $GLOBALS['CONFIG_ATTACK_MESSAGE'];

			// Don't edit/change it
			$template_vars['cms_copyright'] = 'DRAGON EYE CMS '.DragonEye;

			$languages_vars['self_page'] = $this->page;
			$languages_vars['language_list'] = $lang->show_langs();
			$languages_location = 'styles/languages_basic.html';

			$template_vars['language_form'] = Template::load($languages_location, $languages_vars, null);

			// Load meta tags
			$template_vars['meta_description'] = $GLOBALS['CONFIG_TEMPLATE_DESCRIPTION'];
			$template_vars['meta_keywords'] = $GLOBALS['CONFIG_TEMPLATE_KEYWORDS'];
			$template_vars['meta_author'] = $GLOBALS['CONFIG_TEMPLATE_AUTHOR'];

			// Also checks if attack mode it's enabled, so it will not connect to server if under attack
			if(!$GLOBALS['CONFIG_ATTACK_MODE'] && $GLOBALS['CONFIG_ENABLE_STATUS'] == '1')
			{

				$cache_file = sep_path(CMS_DIR.'/cache/server_status.txt');

				$status_info = array();

				if($GLOBALS['CONFIG_STATUS_CACHE'] && file_exists($cache_file) && time() - filemtime($cache_file) < $GLOBALS['CONFIG_STATUS_CACHE'])
					$status_info = unserialize(file_get_contents($cache_file));
				else
				{

					$status_info[0] = $status_info[1] = 0;

					if(@fsockopen($GLOBALS['CONFIG_STATUS_LOGIN_IP'], $GLOBALS['CONFIG_STATUS_LOGIN_PORT'], $errno, $errstr, $GLOBALS['CONFIG_STATUS_TIMEOUT']))
						$status_info[0] = 1;

					if(@fsockopen($GLOBALS['CONFIG_STATUS_SERVER_IP'], $GLOBALS['CONFIG_STATUS_SERVER_PORT'], $errno, $errstr, $GLOBALS['CONFIG_STATUS_TIMEOUT']))
						$status_info[1] = 1;

					if($GLOBALS['CONFIG_STATUS_CACHE'])
						file_put_contents($cache_file, serialize($status_info));

				}

				$template_vars['login_status'] = (int) $status_info[0] == 1 ? @$GLOBALS['LANG_ONLINE'] : @$GLOBALS['LANG_OFFLINE'];

				$template_vars['server_status'] = (int) $status_info[1] == 1 ? @$GLOBALS['LANG_ONLINE'] : @$GLOBALS['LANG_OFFLINE'];

			}
			elseif(!$GLOBALS['CONFIG_ATTACK_MODE'] && $GLOBALS['CONFIG_ENABLE_STATUS'] == '2')
			{

				$cache_file = sep_path(CMS_DIR.'/cache/server_status.txt');

				$status_info = array();

				if($GLOBALS['CONFIG_STATUS_CACHE'] && file_exists($cache_file) && time() - filemtime($cache_file) < $GLOBALS['CONFIG_STATUS_CACHE'])
					$status_info = unserialize(file_get_contents($cache_file));
				else
				{

					$status_info[0] = @file_get_contents('http://dragoneyecms.com/server_status.check.php?server_ip='.$GLOBALS['CONFIG_STATUS_LOGIN_IP'].'&server_port='.$GLOBALS['CONFIG_STATUS_LOGIN_PORT'].'&check_timeout='.$GLOBALS['CONFIG_STATUS_TIMEOUT']);

					$status_info[1] = @file_get_contents('http://dragoneyecms.com/server_status.check.php?server_ip='.$GLOBALS['CONFIG_STATUS_SERVER_IP'].'&server_port='.$GLOBALS['CONFIG_STATUS_SERVER_PORT'].'&check_timeout='.$GLOBALS['CONFIG_STATUS_TIMEOUT']);

					if($GLOBALS['CONFIG_STATUS_CACHE'])
						file_put_contents($cache_file, serialize($status_info));

				}

				$template_vars['login_status'] = $status_info[0] == '1' ? @$GLOBALS['LANG_ONLINE'] : @$GLOBALS['LANG_OFFLINE'];

				$template_vars['server_status'] = $status_info[1] == '1' ? @$GLOBALS['LANG_ONLINE'] : @$GLOBALS['LANG_OFFLINE'];

			}
			else
			{

				$template_vars['login_status'] = @$GLOBALS['LANG_DISABLED'];
				$template_vars['server_status'] = @$GLOBALS['LANG_DISABLED'];

			}

			if($GLOBALS['CONFIG_ENABLE_ONLINE'])
			{

				$cache_file = sep_path(CMS_DIR.'/cache/players_online.txt');

				$cache_time = $GLOBALS['CONFIG_ONLINE_CACHE'];

				if(file_exists($cache_file) && time() - filemtime($cache_file) < $cache_time)
					$template_vars['players_online'] = unserialize(file_get_contents($cache_file));
				else
				{

					if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
						$query = Main::db_query(sprintf($GLOBALS['DBQUERY_L2OFF_PLAYERS_ONLINE'], $GLOBALS['DBSTRUCT_L2OFF_USERC_WOLDU'], $GLOBALS['DBSTRUCT_L2OFF_USERC_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERC_RTIME']), $GLOBALS['DB_LOGIN_SERVER']);
					else
						$query = Main::db_query(sprintf($GLOBALS['DBQUERY_PLAYERS_ONLINE'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_ONLINE'], '1'), $GLOBALS['DB_GAME_SERVER']);

					$template_vars['players_online'] = Main::db_rows($query) ? Main::db_result($query, 0) : '0';

					file_put_contents($cache_file, serialize($template_vars['players_online']));

				}

			}
			else
				$template_vars['players_online'] = @$GLOBALS['LANG_DISABLED'];

			// Include sources page informations
			Configs::load('sources');

			$page_access = @$GLOBALS['CONFIG_'.strtoupper($this->page).'_ACCESS'];

			if($page_access == 0)
			{

				$template_location[] = 'header.html';
				$template_location[] = 'errors.html';
				$template_location[] = 'footer.html';

				$template_vars['the_error'] = $GLOBALS['LANG_PAGE_DISABLED'];

			}
			else
			{

				if(isset($_GET['page']) && $_GET['page'] == 'account' && isset($_GET['logout']))
				{

					Account::logout();

					$template_vars['status'] = $GLOBALS['LANG_LOGIN_OUT'];

					$template_vars['val_user'] = null;
					$template_vars['val_pass'] = null;
					$template_vars['val_remember'] = null;

					$template_location[] = 'header.html';
					$template_location[] = 'login.html';
					$template_location[] = 'footer.html';

				}
				else
				{

					$acc = new Account();

					$logged_cache_file = sep_path(CMS_DIR.'/cache/'.md5($acc->account_username).'.txt');

					$cache_status = false;

					if($GLOBALS['CONFIG_CHECK_LOGGED'] && file_exists($logged_cache_file) && time() - filemtime($logged_cache_file) < $GLOBALS['CONFIG_CHECK_LOGGED'])
						$cache_status = true;

					if($cache_status || $acc->logged())
					{

						$this->logged = true;

						$GLOBALS['template_logged'] = true;

						if($cache_status)
							$this->access_level = file_get_contents($logged_cache_file);
						else
						{

							$this->access_level = $acc->access_level();

							if($GLOBALS['CONFIG_CHECK_LOGGED'])
								file_put_contents($logged_cache_file, $this->access_level);

						}

						$template_vars['username'] = $acc->account_username;

						if($this->access_level >= 5)	// set '>=2' after add privileges (coming in next versions)
							$template_vars['admin_panel'] = @$GLOBALS['LANG_ADMIN_PANEL'];

						if($page_access > $this->access_level)
						{
	
							$template_location[] = 'header.html';
							$template_location[] = 'errors.html';
							$template_location[] = 'footer.html';

							$template_vars['the_error'] = $GLOBALS['LANG_PAGE_RESTRICTED'];

						}
						else
							// Include the current page source
							@require(sep_path(CMS_DIR.'/sources/'.$this->page.'.php'));

					}
					else
						// Include the current page source
						@require(sep_path(CMS_DIR.'/sources/'.$this->page.'.php'));

				}

			}

			// Because here it's the end of page execution we request the page execution time
			if($GLOBALS['CONFIG_EXEC_TIME'])
				$template_vars['e_time'] = sprintf(@$GLOBALS['LANG_EXEC_TIME'], (microtime(true) - $e_start));

			if($GLOBALS['CONFIG_QUERY_COUNT'])
				$template_vars['query_count'] = sprintf(@$GLOBALS['LANG_QUERY_COUNT'], $GLOBALS['DB_QUERY_COUNT']);

			// Try to load template page
			try
			{

				if(method_exists('Template', 'load'))
					foreach ($template_location as $tmpl_loc)
						echo Template::load($tmpl_loc, $template_vars, null);
				else
					throw new Dragon_Eye_Exception('Can\'t load template!');

			}
			catch (Dragon_Eye_Exception $e)
			{

				echo $e->errorMSG();
				return false;

			}

		}

	}

	static public function db_fetch_row($query)
	{

		if($GLOBALS['CONFIG_ATTACK_MODE'])
			return false;

		$result = array();

		switch($GLOBALS['CONFIG_SERVER_TYPE'])
		{

			case 1 : $result = $GLOBALS['CONFIG_USE_SQLSRV'] ? sqlsrv_fetch_array($query, SQLSRV_FETCH_NUMERIC) : mssql_fetch_row($query);	break;
			case 2 : $result = mysql_fetch_row($query);	break;

			default : $result = mysql_fetch_row($query);

		}

		return $result;

	}

	static public function db_fetch_array($query)
	{

		if($GLOBALS['CONFIG_ATTACK_MODE'])
			return false;

		$result = array();

		switch($GLOBALS['CONFIG_SERVER_TYPE'])
		{

			case 1 : $result = $GLOBALS['CONFIG_USE_SQLSRV'] ? sqlsrv_fetch_array($query) : mssql_fetch_array($query);	break;
			case 2 : $result = mysql_fetch_array($query);	break;

			default : $result = mysql_fetch_array($query);

		}

		return $result;

	}

	static public function db_escape_string($string, $db_link)
	{

		if($GLOBALS['CONFIG_ATTACK_MODE'])
			return false;

		if(!$GLOBALS['DATABASE_CONNECTED'] && !Main::connect_database())
			exit();

		$result = null;

		$db_link = $db_link == 1 ? $GLOBALS['DB_GAME_SERVER_LINK'] : $GLOBALS['DB_LOGIN_SERVER_LINK'];

		switch($GLOBALS['CONFIG_SERVER_TYPE'])
		{

			case 1 : $result = addslashes($string);	break;
			case 2 : $result = mysql_real_escape_string($string, $db_link);	break;

			default : $result = mysql_real_escape_string($string, $db_link);

		}

		return $result;

	}

	static public function db_rows($the_query)
	{

		if($GLOBALS['CONFIG_ATTACK_MODE'])
			return false;

		$result = null;

		switch($GLOBALS['CONFIG_SERVER_TYPE'])
		{

			case 1 : $result = $GLOBALS['CONFIG_USE_SQLSRV'] ? sqlsrv_num_rows($the_query) : mssql_num_rows($the_query);	break;
			case 2 : $result = mysql_num_rows($the_query);	break;

			default : $result = mysql_num_rows($the_query);

		}

		return $result;

	}

	static public function db_query($the_query, $db_link)
	{

		if($GLOBALS['CONFIG_ATTACK_MODE'])
			return false;

		if(!$GLOBALS['DATABASE_CONNECTED'] && !Main::connect_database())
			exit();

		$query = null;

		$final_link = $db_link == 1 ? $GLOBALS['DB_GAME_SERVER_LINK'] : $GLOBALS['DB_LOGIN_SERVER_LINK'];

		if($GLOBALS['CONFIG_SERVER_TYPE'] == '1' && $GLOBALS['CONFIG_USE_SQLSRV'])
			$query = sqlsrv_query($final_link, $the_query, array(), array('Scrollable' => SQLSRV_CURSOR_KEYSET));
		else
		{

			if($db_link != $GLOBALS['CURRENT_DB'])
			{

				if($db_link == '1')
				{

					$GLOBALS['CURRENT_DB'] = '1';

					$final_link = $GLOBALS['DB_GAME_SERVER_LINK'];

					if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
						@mssql_select_db($GLOBALS['CONFIG_M'.$GLOBALS['CURRENT_DB_P'].'SQL_NAME_GS'], $GLOBALS['DB_GAME_SERVER_LINK']);
					else
						@mysql_select_db($GLOBALS['CONFIG_M'.$GLOBALS['CURRENT_DB_P'].'SQL_NAME_GS'], $GLOBALS['DB_GAME_SERVER_LINK']);

				}
				else
				{

					$GLOBALS['CURRENT_DB'] = '2';

					$final_link = $GLOBALS['DB_LOGIN_SERVER_LINK'];

					if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
						@mssql_select_db($GLOBALS['CONFIG_M'.$GLOBALS['CURRENT_DB_P'].'SQL_NAME_LS'], $GLOBALS['DB_LOGIN_SERVER_LINK']);
					else
						@mysql_select_db($GLOBALS['CONFIG_M'.$GLOBALS['CURRENT_DB_P'].'SQL_NAME_LS'], $GLOBALS['DB_LOGIN_SERVER_LINK']);

				}

			}

			switch($GLOBALS['CONFIG_SERVER_TYPE'])
			{

				case 1 : $query = mssql_query($the_query, $final_link);	break;
				case 2 : $query = mysql_query($the_query, $final_link);	break;

				default : $query = mysql_query($the_query, $final_link);

			}

		}

		if($GLOBALS['CONFIG_QUERY_COUNT'])
			++$GLOBALS['DB_QUERY_COUNT'];

		return $query;

	}

	static public function db_result($the_query, $row)
	{

		if($GLOBALS['CONFIG_ATTACK_MODE'])
			return false;

		if($GLOBALS['CONFIG_SERVER_TYPE'] == '1' && $GLOBALS['CONFIG_USE_SQLSRV'])
		{

			if($GLOBALS['LAST_QRY'] != $the_query)
			{

				sqlsrv_fetch($the_query);

				$GLOBALS['LAST_QRY'] = $the_query;

			}

			if(!isset($GLOBALS['SQLSRV_ROW_RESULTS'][$row]))
				$GLOBALS['SQLSRV_ROW_RESULTS'][$row] = sqlsrv_get_field($the_query, $row);

			return $GLOBALS['SQLSRV_ROW_RESULTS'][$row];

		}
		else
		{

			switch($GLOBALS['CONFIG_SERVER_TYPE'])
			{

				case 1 : return mssql_result($the_query, 0, $row);	break;
				case 2 : return mysql_result($the_query, $row);	break;

				default : return mysql_result($the_query, $row);	break;

			}

		}

	}

	static public function folder_files($folder, $ext)
	{

		$the_files = array();

		$dir = sep_path(CMS_DIR.'/'.$folder.'/');

		foreach(glob($dir.'*'.$ext) as $the_file)
			$the_files[] = $the_file;

		return $the_files;

	}

	static public function check_chmod_funct($the_file)
	{

		if (!is_writable(CMS_DIR.'/'.$the_file))
		{

			@chmod(CMS_DIR.'/'.$the_file, 0755);

			if (!@chmod(CMS_DIR.'/'.$the_file, 0777) && !is_writable(CMS_DIR.'/'.$the_file))
				$GLOBALS['CHMOD']['FAIL_LIST'][] = $the_file;

		}

	}

	static public function check_chmod_funct2($the_file)
	{

		if(is_dir(CMS_DIR.'/'.$the_file))
			$the_file .= '/index.php';

		@chmod(CMS_DIR.'/'.$the_file, 0777);

		$fh = @fopen(CMS_DIR.'/'.$the_file, 'r+');

		if(!is_resource($fh))
			$fh = @fopen(CMS_DIR.'/'.$the_file, 'w');

		if(!is_resource($fh))
			$GLOBALS['CHMOD']['FAIL_LIST'][] = $the_file;

		@fclose($fh);

	}

	static public function check_chmod_write($the_list)
	{

		$GLOBALS['CHMOD']['FAIL_LIST'] = array();

		if (substr(__FILE__, 1, 2) != ':\\')
			foreach($the_list as $the_file)
				if(is_array($the_file))
					foreach($the_file as $key)
						self::check_chmod_funct($key);
				elseif(!is_writable(CMS_DIR.'/'.$the_file))
					self::check_chmod_funct($the_file);
		else
			foreach($the_list as $the_file)
				if(is_array($the_file))
					foreach($the_file as $key)
						self::check_chmod_funct2($key);
				else
					self::check_chmod_funct2($the_file);

		$status = true;

		if(count($GLOBALS['CHMOD']['FAIL_LIST']) >= 1)
			$status = false;

		if(!$status && substr(__FILE__, 1, 2) == ':\\')
			return false;
		elseif(!$status)
		{

			if(isset($_POST['ftp_server']) && isset($_POST['ftp_port']) && isset($_POST['ftp_user']) && isset($_POST['ftp_pass']) && isset($_POST['ftp_path']))
			{

				$_SESSION['THE_FTP_DATA']['SERVER'] = $_POST['ftp_server'];
				$_SESSION['THE_FTP_DATA']['PORT'] = $_POST['ftp_port'];
				$_SESSION['THE_FTP_DATA']['USER'] = $_POST['ftp_user'];
				$_SESSION['THE_FTP_DATA']['PASS'] = $_POST['ftp_pass'];
				$_SESSION['THE_FTP_DATA']['PATH'] = substr($_POST['ftp_path'], -1) == '/' ? substr($_POST['ftp_path'], 0, -1) : $_POST['ftp_path'];

			}

			if(!isset($_SESSION['THE_FTP_DATA']) && !isset($CHMOD['FTP']))
				$GLOBALS['CHMOD']['REQ_FTP'] = true;
			else
			{

				if(isset($_SESSION['THE_FTP_DATA']) && !isset($CHMOD['FTP']))
				{

					$CHMOD['FTP']['SERVER'] = $_SESSION['THE_FTP_DATA']['SERVER'];
					$CHMOD['FTP']['PORT'] = $_SESSION['THE_FTP_DATA']['PORT'];
					$CHMOD['FTP']['USER'] = $_SESSION['THE_FTP_DATA']['USER'];
					$CHMOD['FTP']['PASS'] = $_SESSION['THE_FTP_DATA']['PASS'];
					$CHMOD['FTP']['PATH'] = $_SESSION['THE_FTP_DATA']['PATH'];

				}

				// To-Do: Finish FTP CHMOD Checking

			}

		}

		return true;

	}

	private function filter_page()
	{

		if(strlen($this->page) > 16 || strlen($this->page) < 2 || !ctype_alnum($this->page))
			$this->page = $GLOBALS['CONFIG_DEFAULT_PAGE'];

	}

	static public function encrypt($str)
	{

		if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
		{

			$key = array();
			$dst = array();
			$i = 0;

			$nBytes = strlen($str);

			while ($i < $nBytes)
			{

				$i++;
				$key[$i] = ord(substr($str, $i - 1, 1));
				$dst[$i] = $key[$i];

			}

			$rslt = $key[1] + $key[2]*256 + $key[3]*65536 + $key[4]*16777216;
			$one = $rslt * 213119 + 2529077;
			$one = $one - intval($one/ 4294967296) * 4294967296;
			$rslt = $key[5] + $key[6]*256 + $key[7]*65536 + $key[8]*16777216;
			$two = $rslt * 213247 + 2529089;
			$two = $two - intval($two/ 4294967296) * 4294967296;
			$rslt = $key[9] + $key[10]*256 + $key[11]*65536 + $key[12]*16777216;
			$three = $rslt * 213203 + 2529589;
			$three = $three - intval($three/ 4294967296) * 4294967296;
			$rslt = $key[13] + $key[14]*256 + $key[15]*65536 + $key[16]*16777216;
			$four = $rslt * 213821 + 2529997;
			$four = $four - intval($four/ 4294967296) * 4294967296;
			$key[4] = intval($one/16777216);
			$key[3] = intval(($one - $key[4] * 16777216) / 65535);
			$key[2] = intval(($one - $key[4] * 16777216 - $key[3] * 65536) / 256);
			$key[1] = intval(($one - $key[4] * 16777216 - $key[3] * 65536 - $key[2] * 256));
			$key[8] = intval($two/16777216);
			$key[7] = intval(($two - $key[8] * 16777216) / 65535);
			$key[6] = intval(($two - $key[8] * 16777216 - $key[7] * 65536) / 256);
			$key[5] = intval(($two - $key[8] * 16777216 - $key[7] * 65536 - $key[6] * 256));
			$key[12] = intval($three/16777216);
			$key[11] = intval(($three - $key[12] * 16777216) / 65535);
			$key[10] = intval(($three - $key[12] * 16777216 - $key[11] * 65536) / 256);
			$key[9] = intval(($three - $key[12] * 16777216 - $key[11] * 65536 - $key[10] * 256));
			$key[16] = intval($four/16777216);
			$key[15] = intval(($four - $key[16] * 16777216) / 65535);
			$key[14] = intval(($four - $key[16] * 16777216 - $key[15] * 65536) / 256);
			$key[13] = intval(($four - $key[16] * 16777216 - $key[15] * 65536 - $key[14] * 256));
			$dst[1] = $dst[1] ^ $key[1];

			$i=1;

			while ($i<16)
			{

				$i++;
				$dst[$i] = $dst[$i] ^ $dst[$i-1] ^ $key[$i];

			}

			$i=0;

			while ($i<16)
			{

				$i++;

				if ($dst[$i] == 0)
					$dst[$i] = 102;

			}

			$encrypt = '0x';
			$i=0;
			
			while ($i<16)
			{

				$i++;

				if ($dst[$i] < 16)
					$encrypt = $encrypt.'0'.dechex($dst[$i]);
				else
					$encrypt = $encrypt.dechex($dst[$i]);

			}

		}
		else
			$encrypt = base64_encode(pack("H*", sha1(utf8_encode($str))));

		return $encrypt;

	}

}