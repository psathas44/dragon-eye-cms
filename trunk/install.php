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

function check_step($step, &$vars, &$step_vars, &$errors_list, &$done_list)
{

	if($step == 1)
		$vars['active_agree'] = 'active';
	elseif($step == 2)
	{

		$vars['active_data'] = 'active';

		$step_vars['val_h_gs'] = $GLOBALS['CONFIG_MYSQL_HOST_GS'];
		$step_vars['val_h_ls'] = $GLOBALS['CONFIG_MYSQL_HOST_LS'];
		$step_vars['val_u_gs'] = $GLOBALS['CONFIG_MYSQL_USER_GS'];
		$step_vars['val_u_ls'] = $GLOBALS['CONFIG_MYSQL_USER_LS'];
		$step_vars['val_d_gs'] = $GLOBALS['CONFIG_MYSQL_NAME_GS'];
		$step_vars['val_d_ls'] = $GLOBALS['CONFIG_MYSQL_NAME_LS'];

		if(isset($_POST['hostname_gs']) && isset($_POST['hostname_ls']) && isset($_POST['hostname_forum']) && isset($_POST['user_gs']) && isset($_POST['user_ls']) && isset($_POST['user_forum']) && isset($_POST['pass_gs']) && isset($_POST['pass_ls']) && isset($_POST['pass_forum']) && isset($_POST['data_gs']) && isset($_POST['data_ls']) && isset($_POST['data_forum']))
		{

			$host_gs = $step_vars['val_h_gs'] = htmlspecialchars($_POST['hostname_gs']);
			$host_ls = $step_vars['val_h_ls'] = htmlspecialchars($_POST['hostname_ls']);
			$host_forum = htmlspecialchars($_POST['hostname_forum']);

			$user_gs = $step_vars['val_u_gs'] = htmlspecialchars($_POST['user_gs']);
			$user_ls = $step_vars['val_u_ls'] = htmlspecialchars($_POST['user_ls']);
			$user_forum = htmlspecialchars($_POST['user_forum']);

			$pass_gs = htmlspecialchars($_POST['pass_gs']);
			$pass_ls = htmlspecialchars($_POST['pass_ls']);
			$pass_forum = htmlspecialchars($_POST['pass_forum']);

			$data_gs = $step_vars['val_d_gs'] = htmlspecialchars($_POST['data_gs']);
			$data_ls = $step_vars['val_d_ls'] = htmlspecialchars($_POST['data_ls']);
			$data_forum = htmlspecialchars($_POST['data_forum']);

			$gs_check = 0;
			$ls_check = 0;

			if(function_exists('sqlsrv_connect') && function_exists('sqlsrv_close'))
			{

				if(sqlsrv_connect($host_gs,
					array(
					'Database' => $data_gs,
					'UID' => $user_gs,
					'PWD' => $pass_gs
					))
				)
					$gs_check = 1;

				if(sqlsrv_connect($host_ls,
					array(
					'Database' => $data_ls,
					'UID' => $user_ls,
					'PWD' => $pass_ls
					))
				)
					$ls_check = 1;

				@sqlsrv_close();

			}
			elseif(function_exists('mssql_connect') && function_exists('mssql_select_db') && function_exists('mssql_close'))
			{

				if(@mssql_connect($host_gs, $user_gs, $pass_gs) && @mssql_select_db($data_gs))
					$gs_check = 1;

				if(@mssql_connect($host_ls, $user_ls, $pass_ls) && @mssql_select_db($data_ls))
					$ls_check = 1;

				@mssql_close();

			}

			if($gs_check == 1 && $ls_check == 1)
				$server_type = 1;
			else
			{

				if(function_exists('mysql_connect') && function_exists('mysql_select_db') && function_exists('mysql_close'))
				{

					if(@mysql_connect($host_gs, $user_gs, $pass_gs) && @mysql_select_db($data_gs))
						$gs_check = 1;
	
					if(@mysql_connect($host_ls, $user_ls, $pass_ls) && @mysql_select_db($data_ls))
						$ls_check = 1;	

				}

				if($gs_check == 1 && $ls_check == 1)
					$server_type = 2;

				@mysql_close();

			}


			if(!isset($server_type))
				$errors_list .= 'Can\'t establish a connection with those details!';
			else
			{

				$dbt = $server_type == 1 ? 'MSSQL' : 'MYSQL';

				if(Configs::update_configs(array('CONFIG_SERVER_TYPE' => '\''.$server_type.'\''), 'main') && Configs::update_configs(array('CONFIG_'.$dbt.'_HOST_GS' => '\''.$host_gs.'\'', 'CONFIG_'.$dbt.'_HOST_LS' => '\''.$host_ls.'\'', 'CONFIG_'.$dbt.'_USER_GS' => '\''.$user_gs.'\'', 'CONFIG_'.$dbt.'_USER_LS' => '\''.$user_ls.'\'', 'CONFIG_'.$dbt.'_PASS_GS' => '\''.$pass_gs.'\'', 'CONFIG_'.$dbt.'_PASS_LS' => '\''.$pass_ls.'\'', 'CONFIG_'.$dbt.'_NAME_GS' => '\''.$data_gs.'\'', 'CONFIG_'.$dbt.'_NAME_LS' => '\''.$data_ls.'\''), 'connection'))
				{

					++$_SESSION['DE_INSTALL_STEP'];
					$done_list .= 'Database Configuration step completed!';

				}
				else
					$errors_list .= 'Error while updating configs.Please report it on forum: <a style="color:#181818;" href="http://dragoneyecms.com">www.dragoneyecms.com</a>';

			}

		}

	}
	elseif($step == 3)
	{

		if(isset($_GET['q']) && isset($_GET['r']) && isset($_GET['p']))
		{

			if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
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

				}
				else
				{

					$GLOBALS['DB_GAME_SERVER_LINK'] = @mssql_pconnect($GLOBALS['CONFIG_MSSQL_HOST_GS'], $GLOBALS['CONFIG_MSSQL_USER_GS'], $GLOBALS['CONFIG_MSSQL_PASS_GS']);
					$GLOBALS['DB_LOGIN_SERVER_LINK'] = @mssql_pconnect($GLOBALS['CONFIG_MSSQL_HOST_LS'], $GLOBALS['CONFIG_MSSQL_USER_LS'], $GLOBALS['CONFIG_MSSQL_PASS_LS']);

					@mssql_select_db($GLOBALS['CONFIG_MSSQL_NAME_GS'], $GLOBALS['DB_GAME_SERVER_LINK']);

					$GLOBALS['CURRENT_DB_P'] = 'S';

					$GLOBALS['CURRENT_DB'] = '1';

				}

				$link = $_GET['p'] == 1 ? 1 : 2;

				if($_GET['r'] == '0')
				{

					if(Main::db_result(Main::db_query(sprintf('IF OBJECT_ID (N\'%s\', N\'U\') IS NOT NULL SELECT 1 AS result ELSE SELECT 0 AS result', $_GET['q']), $link), 0) == 1)
						exit('1');

				}
				else
				{

					$qry = Main::db_query(sprintf('SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.Columns WHERE TABLE_NAME = \'%s\'', $_GET['r']), $link);

					while($row=Main::db_fetch_row($qry))
							if($row[0] == $_GET['q'])
								exit('1');

				}

				if($GLOBALS['CONFIG_USE_SQLSRV'] && extension_loaded('sqlsrv'))
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
			else
			{

				$GLOBALS['DB_GAME_SERVER_LINK'] = @mysql_pconnect($GLOBALS['CONFIG_MYSQL_HOST_GS'], $GLOBALS['CONFIG_MYSQL_USER_GS'], $GLOBALS['CONFIG_MYSQL_PASS_GS']);
				$GLOBALS['DB_LOGIN_SERVER_LINK'] = @mysql_pconnect($GLOBALS['CONFIG_MYSQL_HOST_LS'], $GLOBALS['CONFIG_MYSQL_USER_LS'], $GLOBALS['CONFIG_MYSQL_PASS_LS']);

				@mysql_select_db($GLOBALS['CONFIG_MYSQL_NAME_GS'], $GLOBALS['DB_GAME_SERVER_LINK']);

				$GLOBALS['CURRENT_DB_P'] = 'Y';

				$GLOBALS['CURRENT_DB'] = '1';

				$link = $_GET['p'] == 1 ? 1 : 2;

				if($_GET['r'] == '0')
				{

					if(Main::db_rows(Main::db_query(sprintf('SHOW TABLES LIKE \'%s\'', $_GET['q']), $link)))
						exit('1');

				}
				else
				{

					$qry = Main::db_query(sprintf('SHOW COLUMNS FROM %s', $_GET['r']), $link);

					while($row=Main::db_fetch_array($qry))
						if($row[0] == $_GET['q'])
							exit('1');

				}

				@mysql_close($GLOBALS['DB_GAME_SERVER_LINK']);
				@mysql_close($GLOBALS['DB_LOGIN_SERVER_LINK']);

			}

			exit('2');

		}
		else
		{

			$vars['active_extra'] = 'active';

			$GLOBALS['CONFIG_ACCESS_LEVELS_0'] = $GLOBALS['CONFIG_ACCESS_LEVELS_0'] == 'install' ? ($GLOBALS['CONFIG_SERVER_TYPE'] == 1 ? '-1' : '-1') : $GLOBALS['CONFIG_ACCESS_LEVELS_0'];
			$GLOBALS['CONFIG_ACCESS_LEVELS_1'] = $GLOBALS['CONFIG_ACCESS_LEVELS_1'] == 'install' ? ($GLOBALS['CONFIG_SERVER_TYPE'] == 1 ? '0' : '0') : $GLOBALS['CONFIG_ACCESS_LEVELS_1'];
			$GLOBALS['CONFIG_ACCESS_LEVELS_2'] = $GLOBALS['CONFIG_ACCESS_LEVELS_2'] == 'install' ? ($GLOBALS['CONFIG_SERVER_TYPE'] == 1 ? '5' : '20') : $GLOBALS['CONFIG_ACCESS_LEVELS_2'];
			$GLOBALS['CONFIG_ACCESS_LEVELS_3'] = $GLOBALS['CONFIG_ACCESS_LEVELS_3'] == 'install' ? ($GLOBALS['CONFIG_SERVER_TYPE'] == 1 ? '4' : '50') : $GLOBALS['CONFIG_ACCESS_LEVELS_3'];
			$GLOBALS['CONFIG_ACCESS_LEVELS_4'] = $GLOBALS['CONFIG_ACCESS_LEVELS_4'] == 'install' ? ($GLOBALS['CONFIG_SERVER_TYPE'] == 1 ? '2' : '80') : $GLOBALS['CONFIG_ACCESS_LEVELS_4'];
			$GLOBALS['CONFIG_ACCESS_LEVELS_5'] = $GLOBALS['CONFIG_ACCESS_LEVELS_5'] == 'install' ? ($GLOBALS['CONFIG_SERVER_TYPE'] == 1 ? '1' : '100') : $GLOBALS['CONFIG_ACCESS_LEVELS_5'];

			$step_vars['val_webname'] = $GLOBALS['CONFIG_WEBSITE_NAME'];
			$step_vars['val_weburl'] = $GLOBALS['CONFIG_WEBSITE_URL'];
			$step_vars['val_description'] = $GLOBALS['CONFIG_TEMPLATE_DESCRIPTION'];
			$step_vars['val_keywords'] = $GLOBALS['CONFIG_TEMPLATE_KEYWORDS'];
			$step_vars['val_author'] = $GLOBALS['CONFIG_TEMPLATE_AUTHOR'];
			$step_vars['val_admin_mail'] = $GLOBALS['CONFIG_ADMIN_MAIL'];
			$step_vars['val_smtp_server'] = $GLOBALS['CONFIG_SMTP_SERVER'];
			$step_vars['val_smtp_port'] = $GLOBALS['CONFIG_SMTP_PORT'];
			$step_vars['val_smtp_user'] = $GLOBALS['CONFIG_SMTP_USER'];
			$step_vars['smtp_check_d'] = !isset($_POST['smtp_data']) || (isset($_POST['smtp_data']) && $_POST['smtp_data'] == 0) ? 'checked="checked" ' : null;
			$step_vars['smtp_check_p'] = isset($_POST['smtp_data']) && $_POST['smtp_data'] == 2 ? 'checked="checked" ' : null;
			$step_vars['smtp_check_s'] = isset($_POST['smtp_data']) && $_POST['smtp_data'] == 1 ? 'checked="checked" ' : null;
			$step_vars['smtp_display'] = isset($_POST['smtp_data']) && $_POST['smtp_data'] == 1 ? 'table' : 'none';
			$step_vars['stype'] = $GLOBALS['CONFIG_SERVER_TYPE'];
			$step_vars['db_load_structure'] = $GLOBALS['CONFIG_SERVER_TYPE'] == 1 ? 
			<<<STRUCT
			<input type="radio" name="struct_data" value="0" onclick="change_display('structure_1_userc', 'table', '1')" />User Count
			<input type="radio" name="struct_data" value="0" onclick="change_display('structure_1_ssn', 'table', '1')" />SSN
			<input type="radio" name="struct_data" value="0" onclick="change_display('structure_1_userac', 'table', '1')" />User Account
			<input type="radio" name="struct_data" value="0" onclick="change_display('structure_1_userau', 'table', '1')" />User Auth
			<input type="radio" name="struct_data" value="0" onclick="change_display('structure_1_useri', 'table', '1')" />User Info
			<input type="radio" name="struct_data" value="0" onclick="change_display('structure_1_builder', 'table', '1')" />Builder
			<input type="radio" name="struct_data" value="0" onclick="change_display('structure_1_userdat', 'table', '1')" />User Data
			<input type="radio" name="struct_data" value="0" onclick="change_display('structure_1_pledge', 'table', '1')" />Pledge
			<input type="radio" name="struct_data" value="0" onclick="change_display('structure_1_castle', 'table', '1')" />Castle
			<input type="radio" name="struct_data" value="0" onclick="change_display('structure_1_npcboss', 'table', '1')" />NPC Boss
			<input type="radio" name="struct_data" value="0" onclick="change_display('structure_1_ally', 'table', '1')" />Alliance
			<input type="radio" name="struct_data" value="0" onclick="change_display('structure_1_usernob', 'table', '1')" />User Noblesse
			<input type="radio" name="struct_data" value="0" onclick="change_display('structure_1_social', 'table', '1')" />Sociality
			<input type="radio" name="struct_data" value="0" onclick="change_display('structure_1_skills', 'table', '1')" />Skills
			<input type="radio" name="struct_data" value="0" onclick="change_display('structure_1_items', 'table', '1')" />Items
STRUCT
:
			<<<STRUCT
			<input type="radio" name="struct_data" value="0" onclick="change_display('structure_2_acc', 'table', '1')" />Accounts
			<input type="radio" name="struct_data" value="1" onclick="change_display('structure_2_chars', 'table', '1');" />Characters
			<input type="radio" name="struct_data" value="2" onclick="change_display('structure_2_clan', 'table', '1');" />Clan
			<input type="radio" name="struct_data" value="3" onclick="change_display('structure_2_castle', 'table', '1');" />Castle
			<input type="radio" name="struct_data" value="4" onclick="change_display('structure_2_npc', 'table', '1');" />NPC
			<input type="radio" name="struct_data" value="5" onclick="change_display('structure_2_raids', 'table', '1');" />Raids
			<input type="radio" name="struct_data" value="6" onclick="change_display('structure_2_skills', 'table', '1')" />Skills
			<input type="radio" name="struct_data" value="7" onclick="change_display('structure_2_items', 'table', '1')" />Items
STRUCT;

			if(isset($_POST['website_name']) && isset($_POST['website_url']) && isset($_POST['meta_desc']) && isset($_POST['meta_keywords']) && isset($_POST['meta_author']) && isset($_POST['smtp_data']) && isset($_POST['acc_lvl_0']) && isset($_POST['acc_lvl_1']) && isset($_POST['acc_lvl_2']) && isset($_POST['acc_lvl_3']) && isset($_POST['acc_lvl_4']) && isset($_POST['acc_lvl_5']) && isset($_POST['server_chronicle']) && isset($_POST['server_xp']) && isset($_POST['server_sp']) && isset($_POST['server_drop']) && isset($_POST['server_adena']))
			{

				$web_name = htmlspecialchars($_POST['website_name']);
				$web_url = htmlspecialchars($_POST['website_url']);
				$admin_mail = htmlspecialchars($_POST['admin_mail']);

				$meta_desc = htmlspecialchars($_POST['meta_desc']);
				$meta_keyw = htmlspecialchars($_POST['meta_keywords']);
				$meta_auth = htmlspecialchars($_POST['meta_author']);

				$acc_lvl_0 = intval($_POST['acc_lvl_0']);
				$acc_lvl_1 = intval($_POST['acc_lvl_1']);
				$acc_lvl_2 = intval($_POST['acc_lvl_2']);
				$acc_lvl_3 = intval($_POST['acc_lvl_3']);
				$acc_lvl_4 = intval($_POST['acc_lvl_4']);
				$acc_lvl_5 = intval($_POST['acc_lvl_5']);

				$server_chronicle = htmlspecialchars($_POST['server_chronicle']);
				$server_xp = intval($_POST['server_xp']);
				$server_sp = intval($_POST['server_sp']);
				$server_drop = intval($_POST['server_drop']);
				$server_adena = intval($_POST['server_adena']);

				$smtp_data = intval($_POST['smtp_data']);

				// Update main.config.php file
				Configs::update_configs(array('CONFIG_WEBSITE_NAME' => '\''.$web_name.'\'', 'CONFIG_WEBSITE_URL' => '\''.$web_url.'\'', 'CONFIG_ADMIN_MAIL' => '\''.$admin_mail.'\'', 'CONFIG_MAIL_TYPE' => '\''.$smtp_data.'\'', 'CONFIG_SERVER_CHRONICLE' => '\''.$server_chronicle.'\'', 'CONFIG_SERVER_XP' => '\''.$server_xp.'\'', 'CONFIG_SERVER_SP' => '\''.$server_sp.'\'', 'CONFIG_SERVER_DROP' => '\''.$server_drop.'\'', 'CONFIG_SERVER_ADENA' => '\''.$server_adena.'\''), 'main');

				// Update template.config.php file
				Configs::update_configs(array('CONFIG_TEMPLATE_DESCRIPTION' => '\''.$meta_desc.'\'', 'CONFIG_TEMPLATE_KEYWORDS' => '\''.$meta_keyw.'\'', 'CONFIG_TEMPLATE_AUTHOR' => '\''.$meta_auth.'\''), 'template');

				// Update access_levels.config.php file
				Configs::update_configs(array('CONFIG_ACCESS_LEVELS_0' => '\''.$acc_lvl_0.'\'', 'CONFIG_ACCESS_LEVELS_1' => '\''.$acc_lvl_1.'\'', 'CONFIG_ACCESS_LEVELS_2' => '\''.$acc_lvl_2.'\'', 'CONFIG_ACCESS_LEVELS_3' => '\''.$acc_lvl_3.'\'', 'CONFIG_ACCESS_LEVELS_4' => '\''.$acc_lvl_4.'\'', 'CONFIG_ACCESS_LEVELS_5' => '\''.$acc_lvl_5.'\''), 'access_levels');

				// Update donate_system.config.php file
				Configs::update_configs(array('CONFIG_DONATE_LOGS_EXTRA' => '\''.substr(sha1(base64_encode(rand(10, 999))), 1, 15).'\''), 'donate_system');

				// Update flood.config.php file
				Configs::update_configs(array('CONFIG_FLOOD_LOGS_EXTRA' => '\''.substr(sha1(base64_encode(rand(10, 999))), 1, 15).'\''), 'flood');

				// Update db_structure.config.php file
				if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
				{

					if(isset($_POST['l2off_userc_table']) && isset($_POST['l2off_userc_rtime']) && isset($_POST['l2off_userc_worldu']))
					{

						$l2off_userc_table = htmlspecialchars($_POST['l2off_userc_table']);
						$l2off_userc_rtime = htmlspecialchars($_POST['l2off_userc_rtime']);
						$l2off_userc_worldu = htmlspecialchars($_POST['l2off_userc_worldu']);

						Configs::update_configs(array(
						'DBSTRUCT_L2OFF_USERC_TABLE' => '\''.$l2off_userc_table.'\'',
						'DBSTRUCT_L2OFF_USERC_RTIME' => '\''.$l2off_userc_rtime.'\'',
						'DBSTRUCT_L2OFF_USERC_WOLDU' => '\''.$l2off_userc_worldu.'\'',
						), 'db_structure');

					}

					if(isset($_POST['l2off_ssn_table']) && isset($_POST['l2off_ssn_name']) && isset($_POST['l2off_ssn_mail']) && isset($_POST['l2off_ssn_job']) && isset($_POST['l2off_ssn_phone']) && isset($_POST['l2off_ssn_zip']) && isset($_POST['l2off_ssn_address']) && isset($_POST['l2off_ssn_addetc']) && isset($_POST['l2off_ssn_accnum']))
					{

						$l2off_ssn_table = htmlspecialchars($_POST['l2off_ssn_table']);
						$l2off_ssn_name = htmlspecialchars($_POST['l2off_ssn_name']);
						$l2off_ssn_mail = htmlspecialchars($_POST['l2off_ssn_mail']);
						$l2off_ssn_job = htmlspecialchars($_POST['l2off_ssn_job']);
						$l2off_ssn_phone = htmlspecialchars($_POST['l2off_ssn_phone']);
						$l2off_ssn_zip = htmlspecialchars($_POST['l2off_ssn_zip']);
						$l2off_ssn_address = htmlspecialchars($_POST['l2off_ssn_address']);
						$l2off_ssn_addetc = htmlspecialchars($_POST['l2off_ssn_addetc']);
						$l2off_ssn_accnum = htmlspecialchars($_POST['l2off_ssn_accnum']);

						Configs::update_configs(array(
						'DBSTRUCT_L2OFF_SSN_TABLE' => '\''.$l2off_ssn_table.'\'',
						'DBSTRUCT_L2OFF_SSN_NAME' => '\''.$l2off_ssn_name.'\'',
						'DBSTRUCT_L2OFF_SSN_EMAIL' => '\''.$l2off_ssn_mail.'\'',
						'DBSTRUCT_L2OFF_SSN_JOB' => '\''.$l2off_ssn_job.'\'',
						'DBSTRUCT_L2OFF_SSN_PHONE' => '\''.$l2off_ssn_phone.'\'',
						'DBSTRUCT_L2OFF_SSN_ZIP' => '\''.$l2off_ssn_zip.'\'',
						'DBSTRUCT_L2OFF_SSN_MAD' => '\''.$l2off_ssn_address.'\'',
						'DBSTRUCT_L2OFF_SSN_EAD' => '\''.$l2off_ssn_addetc.'\'',
						'DBSTRUCT_L2OFF_SSN_ACN' => '\''.$l2off_ssn_accnum.'\'',
						), 'db_structure');

					}

					if(isset($_POST['l2off_userac_table']) && isset($_POST['l2off_userac_acc']) && isset($_POST['l2off_userac_paystat']) && isset($_POST['l2off_userac_llogged']))
					{

						$l2off_userac_table = htmlspecialchars($_POST['l2off_userac_table']);
						$l2off_userac_acc = htmlspecialchars($_POST['l2off_userac_acc']);
						$l2off_userac_paystat = htmlspecialchars($_POST['l2off_userac_paystat']);
						$l2off_userac_llogged = htmlspecialchars($_POST['l2off_userac_llogged']);

						Configs::update_configs(array(
						'DBSTRUCT_L2OFF_USERACC_TABLE' => '\''.$l2off_userac_table.'\'',
						'DBSTRUCT_L2OFF_USERACC_ACCOUNT' => '\''.$l2off_userac_acc.'\'',
						'DBSTRUCT_L2OFF_USERACC_PAYSTAT' => '\''.$l2off_userac_paystat.'\'',
						'DBSTRUCT_L2OFF_USERACC_LAST_LOGGED' => '\''.$l2off_userac_llogged.'\'',
						), 'db_structure');

					}

					if(isset($_POST['l2off_useraut_table']) && isset($_POST['l2off_useraut_acc']) && isset($_POST['l2off_useraut_pass']) && isset($_POST['l2off_useraut_quiz1']) && isset($_POST['l2off_useraut_quiz2']) && isset($_POST['l2off_useraut_answ1']) && isset($_POST['l2off_useraut_answ2']))
					{

						$l2off_useraut_table = htmlspecialchars($_POST['l2off_useraut_table']);
						$l2off_useraut_acc = htmlspecialchars($_POST['l2off_useraut_acc']);
						$l2off_useraut_pass = htmlspecialchars($_POST['l2off_useraut_pass']);
						$l2off_useraut_quiz1 = htmlspecialchars($_POST['l2off_useraut_quiz1']);
						$l2off_useraut_quiz2 = htmlspecialchars($_POST['l2off_useraut_quiz2']);
						$l2off_useraut_answ1 = htmlspecialchars($_POST['l2off_useraut_answ1']);
						$l2off_useraut_answ2 = htmlspecialchars($_POST['l2off_useraut_answ2']);

						Configs::update_configs(array(
						'DBSTRUCT_L2OFF_USERAUT_TABLE' => '\''.$l2off_useraut_table.'\'',
						'DBSTRUCT_L2OFF_USERAUT_ACCOUNT' => '\''.$l2off_useraut_acc.'\'',
						'DBSTRUCT_L2OFF_USERAUT_PASS' => '\''.$l2off_useraut_pass.'\'',
						'DBSTRUCT_L2OFF_USERAUT_QUIZ1' => '\''.$l2off_useraut_quiz1.'\'',
						'DBSTRUCT_L2OFF_USERAUT_QUIZ2' => '\''.$l2off_useraut_quiz2.'\'',
						'DBSTRUCT_L2OFF_USERAUT_ANSW1' => '\''.$l2off_useraut_answ1.'\'',
						'DBSTRUCT_L2OFF_USERAUT_ANSW2' => '\''.$l2off_useraut_answ2.'\'',
						), 'db_structure');

					}

					if(isset($_POST['l2off_useri_table']) && isset($_POST['l2off_useri_acc']) && isset($_POST['l2off_useri_ssn']) && isset($_POST['l2off_useri_kind']))
					{

						$l2off_useri_table = htmlspecialchars($_POST['l2off_useri_table']);
						$l2off_useri_acc = htmlspecialchars($_POST['l2off_useri_acc']);
						$l2off_useri_ssn = htmlspecialchars($_POST['l2off_useri_ssn']);
						$l2off_useri_kind = htmlspecialchars($_POST['l2off_useri_kind']);

						Configs::update_configs(array(
						'DBSTRUCT_L2OFF_USERINF_TABLE' => '\''.$l2off_useri_table.'\'',
						'DBSTRUCT_L2OFF_USERINF_ACCOUNT' => '\''.$l2off_useri_acc.'\'',
						'DBSTRUCT_L2OFF_USERINF_SSN' => '\''.$l2off_useri_ssn.'\'',
						'DBSTRUCT_L2OFF_USERINF_KIND' => '\''.$l2off_useri_kind.'\'',
						), 'db_structure');

					}

					if(isset($_POST['l2off_builder_table']) && isset($_POST['l2off_builder_acc']) && isset($_POST['l2off_builder_val']))
					{

						$l2off_builder_table = htmlspecialchars($_POST['l2off_builder_table']);
						$l2off_builder_acc = htmlspecialchars($_POST['l2off_builder_acc']);
						$l2off_builder_val = htmlspecialchars($_POST['l2off_builder_val']);

						Configs::update_configs(array(
						'DBSTRUCT_L2OFF_BUILDER_TABLE' => '\''.$l2off_builder_table.'\'',
						'DBSTRUCT_L2OFF_BUILDER_ACCOUNT' => '\''.$l2off_builder_acc.'\'',
						'DBSTRUCT_L2OFF_BUILDER_VALUE' => '\''.$l2off_builder_val.'\'',
						), 'db_structure');

					}

					if(isset($_POST['l2off_userdat_table']) && isset($_POST['l2off_userdat_id']) && isset($_POST['l2off_userdat_cname']) && isset($_POST['l2off_userdat_accname']) && isset($_POST['l2off_userdat_level']) && isset($_POST['l2off_userdat_pvp']) && isset($_POST['l2off_userdat_pk']) && isset($_POST['l2off_userdat_st_underw']) && isset($_POST['l2off_userdat_st_earr1']) && isset($_POST['l2off_userdat_st_earr2']) && isset($_POST['l2off_userdat_st_neck']) && isset($_POST['l2off_userdat_st_ring1']) && isset($_POST['l2off_userdat_st_ring2']) && isset($_POST['l2off_userdat_st_head']) && isset($_POST['l2off_userdat_st_rhand']) && isset($_POST['l2off_userdat_st_lhand']) && isset($_POST['l2off_userdat_st_gloves']) && isset($_POST['l2off_userdat_st_chest']) && isset($_POST['l2off_userdat_st_legs']) && isset($_POST['l2off_userdat_st_feet']) && isset($_POST['l2off_userdat_st_back']) && isset($_POST['l2off_userdat_st_bhand']) && isset($_POST['l2off_userdat_st_hair']) && isset($_POST['l2off_userdat_st_hdeco']) && isset($_POST['l2off_userdat_st_hall']) && isset($_POST['l2off_userdat_clan']) && isset($_POST['l2off_userdat_class']) && isset($_POST['l2off_userdat_subjob']) && isset($_POST['l2off_userdat_title']) && isset($_POST['l2off_userdat_ontime']) && isset($_POST['l2off_userdat_laccess']))
					{

						$l2off_userdat_table = htmlspecialchars($_POST['l2off_userdat_table']);
						$l2off_userdat_id = htmlspecialchars($_POST['l2off_userdat_id']);
						$l2off_userdat_cname = htmlspecialchars($_POST['l2off_userdat_cname']);
						$l2off_userdat_accname = htmlspecialchars($_POST['l2off_userdat_accname']);
						$l2off_userdat_level = htmlspecialchars($_POST['l2off_userdat_level']);
						$l2off_userdat_pvp = htmlspecialchars($_POST['l2off_userdat_pvp']);
						$l2off_userdat_pk = htmlspecialchars($_POST['l2off_userdat_pk']);
						$l2off_userdat_st_underw = htmlspecialchars($_POST['l2off_userdat_st_underw']);
						$l2off_userdat_st_earr1 = htmlspecialchars($_POST['l2off_userdat_st_earr1']);
						$l2off_userdat_st_earr2 = htmlspecialchars($_POST['l2off_userdat_st_earr2']);
						$l2off_userdat_st_neck = htmlspecialchars($_POST['l2off_userdat_st_neck']);
						$l2off_userdat_st_ring1 = htmlspecialchars($_POST['l2off_userdat_st_ring1']);
						$l2off_userdat_st_ring2 = htmlspecialchars($_POST['l2off_userdat_st_ring2']);
						$l2off_userdat_st_head = htmlspecialchars($_POST['l2off_userdat_st_head']);
						$l2off_userdat_st_rhand = htmlspecialchars($_POST['l2off_userdat_st_rhand']);
						$l2off_userdat_st_lhand = htmlspecialchars($_POST['l2off_userdat_st_lhand']);
						$l2off_userdat_st_gloves = htmlspecialchars($_POST['l2off_userdat_st_gloves']);
						$l2off_userdat_st_chest = htmlspecialchars($_POST['l2off_userdat_st_chest']);
						$l2off_userdat_st_legs = htmlspecialchars($_POST['l2off_userdat_st_legs']);
						$l2off_userdat_st_feet = htmlspecialchars($_POST['l2off_userdat_st_feet']);
						$l2off_userdat_st_back = htmlspecialchars($_POST['l2off_userdat_st_back']);
						$l2off_userdat_st_bhand = htmlspecialchars($_POST['l2off_userdat_st_bhand']);
						$l2off_userdat_st_hair = htmlspecialchars($_POST['l2off_userdat_st_hair']);
						$l2off_userdat_st_hdeco = htmlspecialchars($_POST['l2off_userdat_st_hdeco']);
						$l2off_userdat_st_hall = htmlspecialchars($_POST['l2off_userdat_st_hall']);
						$l2off_userdat_clan = htmlspecialchars($_POST['l2off_userdat_clan']);
						$l2off_userdat_class = htmlspecialchars($_POST['l2off_userdat_class']);
						$l2off_userdat_subjob = htmlspecialchars($_POST['l2off_userdat_subjob']);
						$l2off_userdat_title = htmlspecialchars($_POST['l2off_userdat_title']);
						$l2off_userdat_ontime = htmlspecialchars($_POST['l2off_userdat_ontime']);
						$l2off_userdat_laccess = htmlspecialchars($_POST['l2off_userdat_laccess']);

						Configs::update_configs(array(
						'DBSTRUCT_L2OFF_USERDAT_TABLE' => '\''.$l2off_userdat_table.'\'',
						'DBSTRUCT_L2OFF_USERDAT_ID' => '\''.$l2off_userdat_id.'\'',
						'DBSTRUCT_L2OFF_USERDAT_NAME' => '\''.$l2off_userdat_cname.'\'',
						'DBSTRUCT_L2OFF_USERDAT_ACC' => '\''.$l2off_userdat_accname.'\'',
						'DBSTRUCT_L2OFF_USERDAT_LEVEL' => '\''.$l2off_userdat_level.'\'',
						'DBSTRUCT_L2OFF_USERDAT_PVP' => '\''.$l2off_userdat_pvp.'\'',
						'DBSTRUCT_L2OFF_USERDAT_PK' => '\''.$l2off_userdat_pk.'\'',
						'DBSTRUCT_L2OFF_USERDAT_ST_UNDERW' => '\''.$l2off_userdat_st_underw.'\'',
						'DBSTRUCT_L2OFF_USERDAT_ST_EARR1' => '\''.$l2off_userdat_st_earr1.'\'',
						'DBSTRUCT_L2OFF_USERDAT_ST_EARR2' => '\''.$l2off_userdat_st_earr2.'\'',
						'DBSTRUCT_L2OFF_USERDAT_ST_NECK' => '\''.$l2off_userdat_st_neck.'\'',
						'DBSTRUCT_L2OFF_USERDAT_ST_RING1' => '\''.$l2off_userdat_st_ring1.'\'',
						'DBSTRUCT_L2OFF_USERDAT_ST_RING2' => '\''.$l2off_userdat_st_ring2.'\'',
						'DBSTRUCT_L2OFF_USERDAT_ST_HEAD' => '\''.$l2off_userdat_st_head.'\'',
						'DBSTRUCT_L2OFF_USERDAT_ST_RHAND' => '\''.$l2off_userdat_st_rhand.'\'',
						'DBSTRUCT_L2OFF_USERDAT_ST_LHAND' => '\''.$l2off_userdat_st_lhand.'\'',
						'DBSTRUCT_L2OFF_USERDAT_ST_GLOVES' => '\''.$l2off_userdat_st_gloves.'\'',
						'DBSTRUCT_L2OFF_USERDAT_ST_CHEST' => '\''.$l2off_userdat_st_chest.'\'',
						'DBSTRUCT_L2OFF_USERDAT_ST_LEGS' => '\''.$l2off_userdat_st_legs.'\'',
						'DBSTRUCT_L2OFF_USERDAT_ST_FEET' => '\''.$l2off_userdat_st_feet.'\'',
						'DBSTRUCT_L2OFF_USERDAT_ST_BACK' => '\''.$l2off_userdat_st_back.'\'',
						'DBSTRUCT_L2OFF_USERDAT_ST_BHAND' => '\''.$l2off_userdat_st_bhand.'\'',
						'DBSTRUCT_L2OFF_USERDAT_ST_HAIR' => '\''.$l2off_userdat_st_hair.'\'',
						'DBSTRUCT_L2OFF_USERDAT_ST_HDECO' => '\''.$l2off_userdat_st_hdeco.'\'',
						'DBSTRUCT_L2OFF_USERDAT_ST_HALL' => '\''.$l2off_userdat_st_hall.'\'',
						'DBSTRUCT_L2OFF_USERDAT_CLAN' => '\''.$l2off_userdat_clan.'\'',
						'DBSTRUCT_L2OFF_USERDAT_CLASS' => '\''.$l2off_userdat_class.'\'',
						'DBSTRUCT_L2OFF_USERDAT_SUBJOB' => '\''.$l2off_userdat_subjob.'\'',
						'DBSTRUCT_L2OFF_USERDAT_TITLE' => '\''.$l2off_userdat_title.'\'',
						'DBSTRUCT_L2OFF_USERDAT_ONLINE_TIME' => '\''.$l2off_userdat_ontime.'\'',
						'DBSTRUCT_L2OFF_USERDAT_LAST_ACCESS' => '\''.$l2off_userdat_laccess.'\'',
						), 'db_structure');

					}

					if(isset($_POST['l2off_pledge_table']) && isset($_POST['l2off_pledge_id']) && isset($_POST['l2off_pledge_name']) && isset($_POST['l2off_pledge_level']) && isset($_POST['l2off_pledge_castle']) && isset($_POST['l2off_pledge_ally']) && isset($_POST['l2off_pledge_leader']))
					{

						$l2off_pledge_table = htmlspecialchars($_POST['l2off_pledge_table']);
						$l2off_pledge_id = htmlspecialchars($_POST['l2off_pledge_id']);
						$l2off_pledge_name = htmlspecialchars($_POST['l2off_pledge_name']);
						$l2off_pledge_level = htmlspecialchars($_POST['l2off_pledge_level']);
						$l2off_pledge_castle = htmlspecialchars($_POST['l2off_pledge_castle']);
						$l2off_pledge_ally = htmlspecialchars($_POST['l2off_pledge_ally']);
						$l2off_pledge_leader = htmlspecialchars($_POST['l2off_pledge_leader']);

						Configs::update_configs(array(
						'DBSTRUCT_L2OFF_PLEDGE_TABLE' => '\''.$l2off_pledge_table.'\'',
						'DBSTRUCT_L2OFF_PLEDGE_ID' => '\''.$l2off_pledge_id.'\'',
						'DBSTRUCT_L2OFF_PLEDGE_NAME' => '\''.$l2off_pledge_name.'\'',
						'DBSTRUCT_L2OFF_PLEDGE_LEVEL' => '\''.$l2off_pledge_level.'\'',
						'DBSTRUCT_L2OFF_PLEDGE_CASTLE' => '\''.$l2off_pledge_castle.'\'',
						'DBSTRUCT_L2OFF_PLEDGE_ALLY' => '\''.$l2off_pledge_ally.'\'',
						'DBSTRUCT_L2OFF_PLEDGE_LEADER' => '\''.$l2off_pledge_leader.'\'',
						), 'db_structure');

					}

					if(isset($_POST['l2off_castle_table']) && isset($_POST['l2off_castle_id']) && isset($_POST['l2off_castle_name']) && isset($_POST['l2off_castle_date']))
					{

						$l2off_castle_table = htmlspecialchars($_POST['l2off_castle_table']);
						$l2off_castle_id = htmlspecialchars($_POST['l2off_castle_id']);
						$l2off_castle_name = htmlspecialchars($_POST['l2off_castle_name']);
						$l2off_castle_date = htmlspecialchars($_POST['l2off_castle_date']);

						Configs::update_configs(array(
						'DBSTRUCT_L2OFF_CASTLE_TABLE' => '\''.$l2off_castle_table.'\'',
						'DBSTRUCT_L2OFF_CASTLE_ID' => '\''.$l2off_castle_id.'\'',
						'DBSTRUCT_L2OFF_CASTLE_NAME' => '\''.$l2off_castle_name.'\'',
						'DBSTRUCT_L2OFF_CASTLE_DATE' => '\''.$l2off_castle_date.'\'',
						), 'db_structure');

					}

					if(isset($_POST['l2off_npcboss_table']) && isset($_POST['l2off_npcboss_name']) && isset($_POST['l2off_npcboss_tlow']))
					{

						$l2off_npcboss_table = htmlspecialchars($_POST['l2off_npcboss_table']);
						$l2off_npcboss_name = htmlspecialchars($_POST['l2off_npcboss_name']);
						$l2off_npcboss_tlow = htmlspecialchars($_POST['l2off_npcboss_tlow']);

						Configs::update_configs(array(
						'DBSTRUCT_L2OFF_NPCBOS_TABLE' => '\''.$l2off_npcboss_table.'\'',
						'DBSTRUCT_L2OFF_NPCBOS_NAME' => '\''.$l2off_npcboss_name.'\'',
						'DBSTRUCT_L2OFF_NPCBOS_TLOW' => '\''.$l2off_npcboss_tlow.'\'',
						), 'db_structure');

					}

					if(isset($_POST['l2off_ally_table']) && isset($_POST['l2off_ally_id']) && isset($_POST['l2off_ally_name']))
					{

						$l2off_ally_table = htmlspecialchars($_POST['l2off_ally_table']);
						$l2off_ally_id = htmlspecialchars($_POST['l2off_ally_id']);
						$l2off_ally_name = htmlspecialchars($_POST['l2off_ally_name']);

						Configs::update_configs(array(
						'DBSTRUCT_L2OFF_ALLIANCE_TABLE' => '\''.$l2off_ally_table.'\'',
						'DBSTRUCT_L2OFF_ALLIANCE_ID' => '\''.$l2off_ally_id.'\'',
						'DBSTRUCT_L2OFF_ALLIANCE_NAME' => '\''.$l2off_ally_name.'\'',
						), 'db_structure');

					}

					if(isset($_POST['l2off_usernob_table']) && isset($_POST['l2off_usernob_id']) && isset($_POST['l2off_usernob_noble']) && isset($_POST['l2off_usernob_hero']))
					{

						$l2off_usernob_table = htmlspecialchars($_POST['l2off_usernob_table']);
						$l2off_usernob_id = htmlspecialchars($_POST['l2off_usernob_id']);
						$l2off_usernob_noble = htmlspecialchars($_POST['l2off_usernob_noble']);
						$l2off_usernob_hero = htmlspecialchars($_POST['l2off_usernob_hero']);

						Configs::update_configs(array(
						'DBSTRUCT_L2OFF_UNOBLES_TABLE' => '\''.$l2off_usernob_table.'\'',
						'DBSTRUCT_L2OFF_UNOBLES_ID' => '\''.$l2off_usernob_id.'\'',
						'DBSTRUCT_L2OFF_UNOBLES_NOBLE' => '\''.$l2off_usernob_noble.'\'',
						'DBSTRUCT_L2OFF_UNOBLES_HERO' => '\''.$l2off_usernob_hero.'\'',
						), 'db_structure');

					}

					if(isset($_POST['l2off_social_table']) && isset($_POST['l2off_social_id']) && isset($_POST['l2off_social_name']))
					{

						$l2off_social_table = htmlspecialchars($_POST['l2off_social_table']);
						$l2off_social_id = htmlspecialchars($_POST['l2off_social_id']);
						$l2off_social_name = htmlspecialchars($_POST['l2off_social_name']);

						Configs::update_configs(array(
						'DBSTRUCT_L2OFF_USOCIAL_TABLE' => '\''.$l2off_social_table.'\'',
						'DBSTRUCT_L2OFF_USOCIAL_ID' => '\''.$l2off_social_id.'\'',
						'DBSTRUCT_L2OFF_USOCIAL_NAME' => '\''.$l2off_social_name.'\'',
						), 'db_structure');

					}

					if(isset($_POST['l2off_skills_table']) && isset($_POST['l2off_skills_cid']) && isset($_POST['l2off_skills_id']) && isset($_POST['l2off_skills_level']) && isset($_POST['l2off_skills_subjob']))
					{

						$l2off_skills_table = htmlspecialchars($_POST['l2off_skills_table']);
						$l2off_skills_cid = htmlspecialchars($_POST['l2off_skills_cid']);
						$l2off_skills_id = htmlspecialchars($_POST['l2off_skills_id']);
						$l2off_skills_level = htmlspecialchars($_POST['l2off_skills_level']);
						$l2off_skills_subjob = htmlspecialchars($_POST['l2off_skills_subjob']);

						Configs::update_configs(array(
						'DBSTRUCT_L2OFF_SKILLS_TABLE' => '\''.$l2off_skills_table.'\'',
						'DBSTRUCT_L2OFF_SKILLS_CID' => '\''.$l2off_skills_cid.'\'',
						'DBSTRUCT_L2OFF_SKILLS_ID' => '\''.$l2off_skills_id.'\'',
						'DBSTRUCT_L2OFF_SKILLS_LEVEL' => '\''.$l2off_skills_level.'\'',
						'DBSTRUCT_L2OFF_SKILLS_SUBJOB' => '\''.$l2off_skills_subjob.'\'',
						), 'db_structure');

					}

					if(isset($_POST['l2off_items_table']) && isset($_POST['l2off_items_cid']) && isset($_POST['l2off_items_id']) && isset($_POST['l2off_items_count']) && isset($_POST['l2off_items_enchant']) && isset($_POST['l2off_items_warehouse']) && isset($_POST['l2off_items_type']))
					{

						$l2off_items_table = htmlspecialchars($_POST['l2off_items_table']);
						$l2off_items_cid = htmlspecialchars($_POST['l2off_items_cid']);
						$l2off_items_id = htmlspecialchars($_POST['l2off_items_id']);
						$l2off_items_count = htmlspecialchars($_POST['l2off_items_count']);
						$l2off_items_enchant = htmlspecialchars($_POST['l2off_items_enchant']);
						$l2off_items_warehouse = htmlspecialchars($_POST['l2off_items_warehouse']);
						$l2off_items_type = htmlspecialchars($_POST['l2off_items_type']);

						Configs::update_configs(array(
						'DBSTRUCT_L2OFF_ITEMS_TABLE' => '\''.$l2off_items_table.'\'',
						'DBSTRUCT_L2OFF_ITEMS_CID' => '\''.$l2off_items_cid.'\'',
						'DBSTRUCT_L2OFF_ITEMS_ID' => '\''.$l2off_items_id.'\'',
						'DBSTRUCT_L2OFF_ITEMS_COUNT' => '\''.$l2off_items_count.'\'',
						'DBSTRUCT_L2OFF_ITEMS_ENCHANT' => '\''.$l2off_items_enchant.'\'',
						'DBSTRUCT_L2OFF_ITEMS_WAREHOUSE' => '\''.$l2off_items_warehouse.'\'',
						'DBSTRUCT_L2OFF_ITEMS_TYPE' => '\''.$l2off_items_type.'\'',
						), 'db_structure');

					}

				}
				else
				{

					if(isset($_POST['l2j_accounts_table']) && isset($_POST['l2j_accounts_login']) && isset($_POST['l2j_accounts_pass']) && isset($_POST['l2j_accounts_acc_lvl']) && isset($_POST['l2j_accounts_last_logged']))
					{

						$l2j_acc_table = htmlspecialchars($_POST['l2j_accounts_table']);
						$l2j_acc_login = htmlspecialchars($_POST['l2j_accounts_login']);
						$l2j_acc_pass = htmlspecialchars($_POST['l2j_accounts_pass']);
						$l2j_acc_acc_lvl = htmlspecialchars($_POST['l2j_accounts_acc_lvl']);
						$l2j_acc_last_logged = htmlspecialchars($_POST['l2j_accounts_last_logged']);

						Configs::update_configs(array('DBSTRUCT_L2J_ACCOUNTS_TABLE' => '\''.$l2j_acc_table.'\'', 'DBSTRUCT_L2J_ACCOUNTS_NAME' => '\''.$l2j_acc_login.'\'', 'DBSTRUCT_L2J_ACCOUNTS_PASS' => '\''.$l2j_acc_pass.'\'', 'DBSTRUCT_L2J_ACCOUNTS_ACC_LVL' => '\''.$l2j_acc_acc_lvl.'\'', 'DBSTRUCT_L2J_ACCOUNTS_LAST_LOGGED' => '\''.$l2j_acc_last_logged.'\''), 'db_structure');

					}

					if(isset($_POST['l2j_chars_table']) && isset($_POST['l2j_chars_login']) && isset($_POST['l2j_chars_id']) && isset($_POST['l2j_chars_name']) && isset($_POST['l2j_chars_level']) && isset($_POST['l2j_chars_pvp']) && isset($_POST['l2j_chars_pk']) && isset($_POST['l2j_chars_clanid']) && isset($_POST['l2j_chars_classid']) && isset($_POST['l2j_chars_title']) && isset($_POST['l2j_chars_recs']) && isset($_POST['l2j_chars_online']) && isset($_POST['l2j_chars_ontime']) && isset($_POST['l2j_chars_laccess']) && isset($_POST['l2j_chars_noble']) && isset($_POST['l2j_chars_hero']))
					{

						$l2j_chars_table = htmlspecialchars($_POST['l2j_chars_table']);
						$l2j_chars_login = htmlspecialchars($_POST['l2j_chars_login']);
						$l2j_chars_id = htmlspecialchars($_POST['l2j_chars_id']);
						$l2j_chars_name = htmlspecialchars($_POST['l2j_chars_name']);
						$l2j_chars_level = htmlspecialchars($_POST['l2j_chars_level']);
						$l2j_chars_pvp = htmlspecialchars($_POST['l2j_chars_pvp']);
						$l2j_chars_pk = htmlspecialchars($_POST['l2j_chars_pk']);
						$l2j_chars_clanid = htmlspecialchars($_POST['l2j_chars_clanid']);
						$l2j_chars_classid = htmlspecialchars($_POST['l2j_chars_classid']);
						$l2j_chars_title = htmlspecialchars($_POST['l2j_chars_title']);
						$l2j_chars_recs = htmlspecialchars($_POST['l2j_chars_recs']);
						$l2j_chars_online = htmlspecialchars($_POST['l2j_chars_online']);
						$l2j_chars_ontime = htmlspecialchars($_POST['l2j_chars_ontime']);
						$l2j_chars_laccess = htmlspecialchars($_POST['l2j_chars_laccess']);
						$l2j_chars_noble = htmlspecialchars($_POST['l2j_chars_noble']);
						$l2j_chars_hero = htmlspecialchars($_POST['l2j_chars_hero']);

						Configs::update_configs(array(
						'DBSTRUCT_L2J_CHARS_TABLE' => '\''.$l2j_chars_table.'\'',
						'DBSTRUCT_L2J_CHARS_ACC' => '\''.$l2j_chars_login.'\'',
						'DBSTRUCT_L2J_CHARS_ID' => '\''.$l2j_chars_id.'\'',
						'DBSTRUCT_L2J_CHARS_NAME' => '\''.$l2j_chars_name.'\'',
						'DBSTRUCT_L2J_CHARS_LEVEL' => '\''.$l2j_chars_level.'\'',
						'DBSTRUCT_L2J_CHARS_PVP' => '\''.$l2j_chars_pvp.'\'',
						'DBSTRUCT_L2J_CHARS_PK' => '\''.$l2j_chars_pk.'\'',
						'DBSTRUCT_L2J_CHARS_CLAN' => '\''.$l2j_chars_clanid.'\'',
						'DBSTRUCT_L2J_CHARS_CLASS' => '\''.$l2j_chars_classid.'\'',
						'DBSTRUCT_L2J_CHARS_TITLE' => '\''.$l2j_chars_title.'\'',
						'DBSTRUCT_L2J_CHARS_RECS' => '\''.$l2j_chars_recs.'\'',
						'DBSTRUCT_L2J_CHARS_ONLINE' => '\''.$l2j_chars_online.'\'',
						'DBSTRUCT_L2J_CHARS_ONLINE_TIME' => '\''.$l2j_chars_ontime.'\'',
						'DBSTRUCT_L2J_CHARS_LAST_ACCESS' => '\''.$l2j_chars_laccess.'\'',
						'DBSTRUCT_L2J_CHARS_NOBLE' => '\''.$l2j_chars_noble.'\'',
						'DBSTRUCT_L2J_CHARS_HERO' => '\''.$l2j_chars_hero.'\'',
						), 'db_structure');

					}

					if(isset($_POST['l2j_clan_table']) && isset($_POST['l2j_clan_id']) && isset($_POST['l2j_clan_name']) && isset($_POST['l2j_clan_level']) && isset($_POST['l2j_clan_castle']) && isset($_POST['l2j_clan_ally']) && isset($_POST['l2j_clan_leader']))
					{

						$l2j_clan_table = htmlspecialchars($_POST['l2j_clan_table']);
						$l2j_clan_id = htmlspecialchars($_POST['l2j_clan_id']);
						$l2j_clan_name = htmlspecialchars($_POST['l2j_clan_name']);
						$l2j_clan_level = htmlspecialchars($_POST['l2j_clan_level']);
						$l2j_clan_castle = htmlspecialchars($_POST['l2j_clan_castle']);
						$l2j_clan_ally = htmlspecialchars($_POST['l2j_clan_ally']);
						$l2j_clan_leader = htmlspecialchars($_POST['l2j_clan_leader']);

						Configs::update_configs(array(
						'DBSTRUCT_L2J_CLAN_TABLE' => '\''.$l2j_clan_table.'\'',
						'DBSTRUCT_L2J_CLAN_ID' => '\''.$l2j_clan_id.'\'',
						'DBSTRUCT_L2J_CLAN_NAME' => '\''.$l2j_clan_name.'\'',
						'DBSTRUCT_L2J_CLAN_LEVEL' => '\''.$l2j_clan_level.'\'',
						'DBSTRUCT_L2J_CLAN_CASTLE' => '\''.$l2j_clan_castle.'\'',
						'DBSTRUCT_L2J_CLAN_ALLY' => '\''.$l2j_clan_ally.'\'',
						'DBSTRUCT_L2J_CLAN_LEADER' => '\''.$l2j_clan_leader.'\'',
						), 'db_structure');

					}

					if(isset($_POST['l2j_castle_table']) && isset($_POST['l2j_castle_id']) && isset($_POST['l2j_castle_name']) && isset($_POST['l2j_castle_siege']))
					{

						$l2j_castle_table = htmlspecialchars($_POST['l2j_castle_table']);
						$l2j_castle_id = htmlspecialchars($_POST['l2j_castle_id']);
						$l2j_castle_name = htmlspecialchars($_POST['l2j_castle_name']);
						$l2j_castle_siege = htmlspecialchars($_POST['l2j_castle_siege']);

						Configs::update_configs(array(
						'DBSTRUCT_L2J_CASTLE_TABLE' => '\''.$l2j_castle_table.'\'',
						'DBSTRUCT_L2J_CASTLE_ID' => '\''.$l2j_castle_id.'\'',
						'DBSTRUCT_L2J_CASTLE_NAME' => '\''.$l2j_castle_name.'\'',
						'DBSTRUCT_L2J_CASTLE_DATE' => '\''.$l2j_castle_siege.'\'',
						), 'db_structure');

					}

					if(isset($_POST['l2j_npc_table']) && isset($_POST['l2j_npc_id']) && isset($_POST['l2j_npc_name']) && isset($_POST['l2j_npc_level']))
					{

						$l2j_npc_table = htmlspecialchars($_POST['l2j_npc_table']);
						$l2j_npc_id = htmlspecialchars($_POST['l2j_npc_id']);
						$l2j_npc_name = htmlspecialchars($_POST['l2j_npc_name']);
						$l2j_npc_level = htmlspecialchars($_POST['l2j_npc_level']);

						Configs::update_configs(array(
						'DBSTRUCT_L2J_NPCS_TABLE' => '\''.$l2j_npc_table.'\'',
						'DBSTRUCT_L2J_NPCS_ID' => '\''.$l2j_npc_id.'\'',
						'DBSTRUCT_L2J_NPCS_NAME' => '\''.$l2j_npc_name.'\'',
						'DBSTRUCT_L2J_NPCS_LEVEL' => '\''.$l2j_npc_level.'\'',
						), 'db_structure');

					}

					if(isset($_POST['l2j_raids_table']) && isset($_POST['l2j_raids_id']) && isset($_POST['l2j_raids_respawn']))
					{

						$l2j_raids_table = htmlspecialchars($_POST['l2j_raids_table']);
						$l2j_raids_id = htmlspecialchars($_POST['l2j_raids_id']);
						$l2j_raids_respawn = htmlspecialchars($_POST['l2j_raids_respawn']);

						Configs::update_configs(array(
						'DBSTRUCT_L2J_RAIDS_TABLE' => '\''.$l2j_raids_table.'\'',
						'DBSTRUCT_L2J_RAIDS_ID' => '\''.$l2j_raids_id.'\'',
						'DBSTRUCT_L2J_RAIDS_TIME' => '\''.$l2j_raids_respawn.'\'',
						), 'db_structure');

					}

					if(isset($_POST['l2j_skills_table']) && isset($_POST['l2j_skills_cid']) && isset($_POST['l2j_skills_id']) && isset($_POST['l2j_skills_level']) && isset($_POST['l2j_skills_class']))
					{

						$l2j_skills_table = htmlspecialchars($_POST['l2j_skills_table']);
						$l2j_skills_cid = htmlspecialchars($_POST['l2j_skills_cid']);
						$l2j_skills_id = htmlspecialchars($_POST['l2j_skills_id']);
						$l2j_skills_level = htmlspecialchars($_POST['l2j_skills_level']);
						$l2j_skills_class = htmlspecialchars($_POST['l2j_skills_class']);

						Configs::update_configs(array(
						'DBSTRUCT_L2J_SKILLS_TABLE' => '\''.$l2j_skills_table.'\'',
						'DBSTRUCT_L2J_SKILLS_CID' => '\''.$l2j_skills_cid.'\'',
						'DBSTRUCT_L2J_SKILLS_ID' => '\''.$l2j_skills_id.'\'',
						'DBSTRUCT_L2J_SKILLS_LEVEL' => '\''.$l2j_skills_level.'\'',
						'DBSTRUCT_L2J_SKILLS_CLASS' => '\''.$l2j_skills_class.'\'',
						), 'db_structure');

					}

					if(isset($_POST['l2j_items_table']) && isset($_POST['l2j_items_cid']) && isset($_POST['l2j_items_id']) && isset($_POST['l2j_items_count']) && isset($_POST['l2j_items_enchant']) && isset($_POST['l2j_items_loc']) && isset($_POST['l2j_items_type']))
					{

						$l2j_items_table = htmlspecialchars($_POST['l2j_items_table']);
						$l2j_items_cid = htmlspecialchars($_POST['l2j_items_cid']);
						$l2j_items_id = htmlspecialchars($_POST['l2j_items_id']);
						$l2j_items_count = htmlspecialchars($_POST['l2j_items_count']);
						$l2j_items_enchant = htmlspecialchars($_POST['l2j_items_enchant']);
						$l2j_items_loc = htmlspecialchars($_POST['l2j_items_loc']);
						$l2j_items_type = htmlspecialchars($_POST['l2j_items_type']);

						Configs::update_configs(array(
						'DBSTRUCT_L2J_ITEMS_TABLE' => '\''.$l2j_items_table.'\'',
						'DBSTRUCT_L2J_ITEMS_CID' => '\''.$l2j_items_cid.'\'',
						'DBSTRUCT_L2J_ITEMS_ID' => '\''.$l2j_items_id.'\'',
						'DBSTRUCT_L2J_ITEMS_COUNT' => '\''.$l2j_items_count.'\'',
						'DBSTRUCT_L2J_ITEMS_ENCHANT' => '\''.$l2j_items_enchant.'\'',
						'DBSTRUCT_L2J_ITEMS_LOC' => '\''.$l2j_items_loc.'\'',
						'DBSTRUCT_L2J_ITEMS_TYPE' => '\''.$l2j_items_type.'\'',
						), 'db_structure');

					}

				}

				if($smtp_data == 1)
				{

					if(isset($_POST['smtp_server']) && isset($_POST['smtp_port']) && isset($_POST['smtp_user']) && isset($_POST['smtp_pass']))
					{

						$smtp_server = htmlspecialchars($_POST['smtp_server']);
						$smtp_port = htmlspecialchars($_POST['smtp_port']);
						$smtp_user = htmlspecialchars($_POST['smtp_user']);
						$smtp_pass = htmlspecialchars($_POST['smtp_pass']);

						Configs::update_configs(array('CONFIG_SMTP_SERVER' => '\''.$smtp_server.'\'', 'CONFIG_SMTP_PORT' => '\''.$smtp_port.'\'', 'CONFIG_SMTP_USER' => '\''.$smtp_user.'\'', 'CONFIG_SMTP_PASS' => '\''.$smtp_pass.'\''), 'main');

					}

				}

				++$_SESSION['DE_INSTALL_STEP'];
				$done_list .= 'Step completed!Press next or refresh the page to access next step.';

			}

		}

	}
	elseif($step == 4)
	{

		$vars['active_prog'] = 'active';

		// Simulate all the queryes used by CMS (like register, login etc)

		if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
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

			}
			else
			{

				$GLOBALS['DB_GAME_SERVER_LINK'] = @mssql_connect($GLOBALS['CONFIG_MSSQL_HOST_GS'], $GLOBALS['CONFIG_MSSQL_USER_GS'], $GLOBALS['CONFIG_MSSQL_PASS_GS']);
				$GLOBALS['DB_LOGIN_SERVER_LINK'] = @mssql_connect($GLOBALS['CONFIG_MSSQL_HOST_LS'], $GLOBALS['CONFIG_MSSQL_USER_LS'], $GLOBALS['CONFIG_MSSQL_PASS_LS']);
	
				@mssql_select_db($GLOBALS['CONFIG_MSSQL_NAME_LS'], $GLOBALS['DB_LOGIN_SERVER_LINK']);

				$GLOBALS['CURRENT_DB_P'] = 'S';
	
				$GLOBALS['CURRENT_DB'] = '2';

			}

			$GLOBALS['DB_GAME_SERVER'] = '1';

			$GLOBALS['DB_LOGIN_SERVER'] = '2';

			$failed_queryes = array();

			$qry = Main::db_query(sprintf('SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.Columns WHERE TABLE_NAME = \'%s\'', $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE']), $GLOBALS['DB_LOGIN_SERVER']);

			$qry2 = Main::db_query(sprintf('SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.Columns WHERE TABLE_NAME = \'%s\'', $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE']), $GLOBALS['DB_GAME_SERVER']);

			$to_chk = array($GLOBALS['DBSTRUCT_L2OFF_USERACC_REFER_POINTS'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_VOTE_POINTS'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_DONATE_POINTS'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_FORUM_POINTS'], 'refer', $GLOBALS['DBSTRUCT_L2OFF_USERDAT_VIEWSET'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_SIGNATURE']);

			while($row=Main::db_fetch_row($qry))
			{

				$key = array_search($row[0], $to_chk);

				if(in_array($row[0], $to_chk))
					unset($to_chk[$key]);

			}

			while($row=Main::db_fetch_row($qry2))
			{

				$key = array_search($row[0], $to_chk);

				if(in_array($row[0], $to_chk))
					unset($to_chk[$key]);

			}

			if(in_array($GLOBALS['DBSTRUCT_L2OFF_USERACC_REFER_POINTS'], $to_chk))
				if(!@Main::db_query(sprintf('ALTER TABLE %s ADD %s int NOT NULL DEFAULT ((0))', $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_REFER_POINTS']), $GLOBALS['DB_LOGIN_SERVER']))
					$failed_queryes[] = sprintf('ALTER TABLE %s ADD %s int NOT NULL DEFAULT ((0))', $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_REFER_POINTS']);

			if(in_array($GLOBALS['DBSTRUCT_L2OFF_USERACC_VOTE_POINTS'], $to_chk))
				if(!@Main::db_query(sprintf('ALTER TABLE %s ADD %s int NOT NULL DEFAULT ((0))', $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_VOTE_POINTS']), $GLOBALS['DB_LOGIN_SERVER']))
					$failed_queryes[] = sprintf('ALTER TABLE %s ADD %s int NOT NULL DEFAULT ((0))', $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_VOTE_POINTS']);

			if(in_array($GLOBALS['DBSTRUCT_L2OFF_USERACC_DONATE_POINTS'], $to_chk))
				if(!@Main::db_query(sprintf('ALTER TABLE %s ADD %s decimal(11,5) NOT NULL DEFAULT ((0))', $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_DONATE_POINTS']), $GLOBALS['DB_LOGIN_SERVER']))
					$failed_queryes[] = sprintf('ALTER TABLE %s ADD %s decimal(11,5) NOT NULL DEFAULT ((0))', $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_DONATE_POINTS']);

			if(in_array($GLOBALS['DBSTRUCT_L2OFF_USERACC_FORUM_POINTS'], $to_chk))
				if(!@Main::db_query(sprintf('ALTER TABLE %s ADD %s int NOT NULL DEFAULT ((0))', $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_FORUM_POINTS']), $GLOBALS['DB_LOGIN_SERVER']))
					$failed_queryes[] = sprintf('ALTER TABLE %s ADD %s int NOT NULL DEFAULT ((0))', $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_FORUM_POINTS']);

			if(in_array('refer', $to_chk))
				if(!@Main::db_query(sprintf('ALTER TABLE %s ADD %s varchar(45) NULL', $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], 'refer'), $GLOBALS['DB_LOGIN_SERVER']))
					$failed_queryes[] = sprintf('ALTER TABLE %s ADD %s varchar(45) NULL', $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], 'refer');

			if(in_array($GLOBALS['DBSTRUCT_L2OFF_USERACC_SIGNATURE'], $to_chk))
				if(!@Main::db_query(sprintf('ALTER TABLE %s ADD %s int NOT NULL DEFAULT ((0))', $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_SIGNATURE']), $GLOBALS['DB_LOGIN_SERVER']))
					$failed_queryes[] = sprintf('ALTER TABLE %s ADD %s int NOT NULL DEFAULT ((0))', $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_SIGNATURE']);

			if(in_array($GLOBALS['DBSTRUCT_L2OFF_USERDAT_VIEWSET'], $to_chk))
				if(!@Main::db_query(sprintf('ALTER TABLE %s ADD %s int NOT NULL DEFAULT ((0))', $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_VIEWSET']), $GLOBALS['DB_GAME_SERVER']))
					$failed_queryes[] = sprintf('ALTER TABLE %s ADD %s int NOT NULL DEFAULT ((0))', $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_VIEWSET']);

			$ACTIVATION_QUERY_1 = 'IF EXISTS(SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = \'mail_check\') DROP TABLE mail_check';
			$ACTIVATION_QUERY_2 = <<<DEYE
CREATE TABLE [dbo].[mail_check](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[username] [varchar](17) NULL,
	[email] [varchar](67) NULL,
	[password] [varchar](50) NULL,
	[ip] [char](15) NULL,
	[check_id] [varchar](16) NULL,
	[time] [int] NULL,
	[refer] [varchar](17) NULL,
 CONSTRAINT [PK_mail_check2] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
)
DEYE;

			// Add activation table
			if(!@Main::db_query($ACTIVATION_QUERY_1, $GLOBALS['DB_LOGIN_SERVER']))
				$failed_queryes[] = $ACTIVATION_QUERY_1;
			if(!@Main::db_query($ACTIVATION_QUERY_2, $GLOBALS['DB_LOGIN_SERVER']))
				$failed_queryes[] = $ACTIVATION_QUERY_2;

			if($GLOBALS['CONFIG_USE_SQLSRV'] && extension_loaded('sqlsrv'))
			{

				@sqlsrv_close($gs_link);
				@sqlsrv_close($ls_link);

			}
			else
			{

				@mssql_close($gs_link);
				@mssql_close($ls_link);

			}

			if(count($failed_queryes) == 0)
			{

				$step_vars['failed_queryes'] = '0 failed queryes, install completed.Press finish/refresh in order to access the last step of installation.';

				++$_SESSION['DE_INSTALL_STEP'];

			}
			else
			{

				$step_vars['failed_queryes'] = '<u>'.count($failed_queryes).' failed query(es): </u><br /><br /><br />';

				foreach($failed_queryes as $k)
					$step_vars['failed_queryes'] .= '" '.$k.' "<br /><br />';

				$step_vars['failed_queryes'] .= '<u>Try to fix them by accessing \'Extra\' step or fix them manually.If you still got problems, access our website: <a style="color:#dadada;" href="http://dragoneyecms.com">www.dragoneyecms.com</a>';
			}

		}
		else
		{

			$GLOBALS['DB_GAME_SERVER_LINK'] = @mysql_connect($GLOBALS['CONFIG_MYSQL_HOST_GS'], $GLOBALS['CONFIG_MYSQL_USER_GS'], $GLOBALS['CONFIG_MYSQL_PASS_GS']);
			$GLOBALS['DB_LOGIN_SERVER_LINK'] = @mysql_connect($GLOBALS['CONFIG_MYSQL_HOST_LS'], $GLOBALS['CONFIG_MYSQL_USER_LS'], $GLOBALS['CONFIG_MYSQL_PASS_LS']);

			@mysql_select_db($GLOBALS['CONFIG_MYSQL_NAME_LS'], $GLOBALS['DB_LOGIN_SERVER_LINK']);

			$GLOBALS['CURRENT_DB_P'] = 'Y';

			$GLOBALS['CURRENT_DB'] = '2';

			$GLOBALS['DB_GAME_SERVER'] = '1';

			$GLOBALS['DB_LOGIN_SERVER'] = '2';

			$failed_queryes = array();

			$qry = Main::db_query(sprintf('SHOW COLUMNS FROM %s', $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE']), $GLOBALS['DB_LOGIN_SERVER']);
			$qry2 = Main::db_query(sprintf('SHOW COLUMNS FROM %s', $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE']), $GLOBALS['DB_GAME_SERVER']);

			$to_chk = array($GLOBALS['DBSTRUCT_L2J_ACCOUNTS_MAIL'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_REFER_POINTS'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_VOTE_POINTS'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_DONATE_POINTS'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_FORUM_POINTS'], 'refer', $GLOBALS['DBSTRUCT_L2J_CHARS_VIEWSET'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_SIGNATURE']);

			while($row=@mysql_fetch_array($qry))
			{

				$key = array_search($row[0], $to_chk);

				if(in_array($row[0], $to_chk))
					unset($to_chk[$key]);

			}

			while($row=@mysql_fetch_array($qry2))
			{

				$key = array_search($row[0], $to_chk);

				if(in_array($row[0], $to_chk))
					unset($to_chk[$key]);

			}

			// Add special table fields:

			if(in_array($GLOBALS['DBSTRUCT_L2J_ACCOUNTS_MAIL'], $to_chk))
				if(!@Main::db_query(sprintf('ALTER TABLE %s ADD %s varchar(60) NULL', $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_MAIL']), $GLOBALS['DB_LOGIN_SERVER']))
					$failed_queryes[] = sprintf('ALTER TABLE %s ADD %s varchar(60) NULL', $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_MAIL']);

			if(in_array($GLOBALS['DBSTRUCT_L2J_ACCOUNTS_REFER_POINTS'], $to_chk))
				if(!@Main::db_query(sprintf('ALTER TABLE %s ADD %s INT(11) NOT NULL DEFAULT 0', $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_REFER_POINTS']), $GLOBALS['DB_LOGIN_SERVER']))
					$failed_queryes[] = sprintf('ALTER TABLE %s ADD %s INT(11) NOT NULL DEFAULT 0', $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_REFER_POINTS']);

			if(in_array($GLOBALS['DBSTRUCT_L2J_ACCOUNTS_VOTE_POINTS'], $to_chk))
				if(!@Main::db_query(sprintf('ALTER TABLE %s ADD %s INT(11) NOT NULL DEFAULT 0', $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_VOTE_POINTS']), $GLOBALS['DB_LOGIN_SERVER']))
					$failed_queryes[] = sprintf('ALTER TABLE %s ADD %s INT(11) NOT NULL DEFAULT 0', $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_VOTE_POINTS']);

			if(in_array($GLOBALS['DBSTRUCT_L2J_ACCOUNTS_DONATE_POINTS'], $to_chk))
				if(!@Main::db_query(sprintf('ALTER TABLE %s ADD %s double(11,5) NOT NULL DEFAULT \'0.00000\'', $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_DONATE_POINTS']), $GLOBALS['DB_LOGIN_SERVER']))
					$failed_queryes[] = sprintf('ALTER TABLE %s ADD %s double(11,5) NOT NULL DEFAULT \'0.00000\'', $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_DONATE_POINTS']);

			if(in_array($GLOBALS['DBSTRUCT_L2J_ACCOUNTS_FORUM_POINTS'], $to_chk))
				if(!@Main::db_query(sprintf('ALTER TABLE %s ADD %s INT(11) NOT NULL DEFAULT 0', $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_FORUM_POINTS']), $GLOBALS['DB_LOGIN_SERVER']))
					$failed_queryes[] = sprintf('ALTER TABLE %s ADD %s INT(11) NOT NULL DEFAULT 0', $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_FORUM_POINTS']);

			if(in_array('refer', $to_chk))
				if(!@Main::db_query(sprintf('ALTER TABLE %s ADD %s varchar(45) NULL', $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], 'refer'), $GLOBALS['DB_LOGIN_SERVER']))
					$failed_queryes[] = sprintf('ALTER TABLE %s ADD %s varchar(45) NULL', $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], 'refer');

			if(in_array($GLOBALS['DBSTRUCT_L2J_ACCOUNTS_SIGNATURE'], $to_chk))
				if(!@Main::db_query(sprintf('ALTER TABLE %s ADD %s INT(11) NOT NULL DEFAULT 0', $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_SIGNATURE']), $GLOBALS['DB_LOGIN_SERVER']))
					$failed_queryes[] = sprintf('ALTER TABLE %s ADD %s INT(11) NOT NULL DEFAULT 0', $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_SIGNATURE']);

			if(in_array($GLOBALS['DBSTRUCT_L2J_CHARS_VIEWSET'], $to_chk))
				if(!@Main::db_query(sprintf('ALTER TABLE %s ADD %s INT(5) NOT NULL DEFAULT 0', $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_VIEWSET']), $GLOBALS['DB_GAME_SERVER']))
					$failed_queryes[] = sprintf('ALTER TABLE %s ADD %s INT(5) NOT NULL DEFAULT 0', $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_VIEWSET']);

			$register_username = 'dragoneyeinstalltest1'.rand(1, 9999);
			$register_password = 'dragoneyeinstalltest2';
			$register_email = 'dragoneyeinstalltest3@dragoneyecms.com'.rand(1, 9999);
			$register_refer = 'dragoneyeinstalltest4';
			$register_points = '0';

			$ACTIVATION_QUERY_1 = 'DROP TABLE IF EXISTS mail_check';
			$ACTIVATION_QUERY_2 = <<<DEYE
			CREATE TABLE mail_check (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  username varchar(17) DEFAULT NULL,
  email varchar(67) DEFAULT NULL,
  password varchar(45) DEFAULT NULL,
  ip char(15) DEFAULT NULL,
  check_id varchar(16) DEFAULT NULL,
  time int(10) unsigned DEFAULT NULL,
  refer varchar(17) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
DEYE;

			// Register account query
//			if(!@Main::db_query(sprintf($GLOBALS['DBQUERY_CREATE_ACCOUNT_L2J'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_PASS'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_ACC_LVL'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_MAIL'], 'refer', Main::db_escape_string($register_username, $GLOBALS['DB_LOGIN_SERVER']), Main::db_escape_string($register_password, $GLOBALS['DB_LOGIN_SERVER']), '0', Main::db_escape_string($register_email, $GLOBALS['DB_LOGIN_SERVER']), Main::db_escape_string($register_refer, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']))
	//			$failed_queryes[] = sprintf($GLOBALS['DBQUERY_CREATE_ACCOUNT_L2J'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_PASS'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_ACC_LVL'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_MAIL'], 'refer', Main::db_escape_string($register_username, $GLOBALS['DB_LOGIN_SERVER']), Main::db_escape_string($register_password, $GLOBALS['DB_LOGIN_SERVER']), '0', Main::db_escape_string($register_email, $GLOBALS['DB_LOGIN_SERVER']), Main::db_escape_string($register_refer, $GLOBALS['DB_LOGIN_SERVER']));
//To-Do: query to delete registered test account after check if exists

			// Add activation table
			if(!@Main::db_query($ACTIVATION_QUERY_1, $GLOBALS['DB_LOGIN_SERVER']))
				$failed_queryes[] = $ACTIVATION_QUERY_1;
			if(!@Main::db_query($ACTIVATION_QUERY_2, $GLOBALS['DB_LOGIN_SERVER']))
				$failed_queryes[] = $ACTIVATION_QUERY_2;

			@mysql_close($gs_link);
			@mysql_close($ls_link);

			if(count($failed_queryes) == 0)
			{

				$step_vars['failed_queryes'] = '0 failed queryes, install completed.Press finish/refresh in order to access the last step of installation.';

				++$_SESSION['DE_INSTALL_STEP'];

			}
			else
			{

				$step_vars['failed_queryes'] = '<u>'.count($failed_queryes).' failed query(es): </u><br /><br /><br />';

				foreach($failed_queryes as $k)
					$step_vars['failed_queryes'] .= '" '.$k.' "<br /><br />';

				$step_vars['failed_queryes'] .= '<u>Try to fix them by accessing \'Extra\' step or fix them manually.If you still got problems, access our website: <a style="color:#dadada;" href="http://dragoneyecms.com">www.dragoneyecms.com</a>';
			}

		}

	}
	elseif($step == 5)
	{

		$vars['active_done'] = 'active';

		// Complete installation
		Configs::update_configs(array('CONFIG_CMS_INSTALLED' => '\'1\''), 'main');

		// Send installation data to our server
		$w = explode('://', $GLOBALS['CONFIG_WEBSITE_URL']);

		$step_vars['server_response'] = '<iframe width="500" height="50" allowtransparency="true" frameborder="0" scrolling="no" src="http://stats.dragoneyecms.com/request_add.php?the_server='.$GLOBALS['CONFIG_WEBSITE_NAME'].'&the_type='.$GLOBALS['CONFIG_SERVER_TYPE'].'&the_website='.$w[1].'&ht='.$w[0].'"><p>Error</p></iframe>';

		++$_SESSION['DE_INSTALL_STEP'];

	}
	else
		return false;

}

$install_vars = array();
$step_vars = array();
$errors_list = null;
$done_list = null;

$check_list = array(Main::folder_files('configurations', '.config.php'), 'sources/cdata', 'cache', 'logs', Main::folder_files('sources/cdata', '.xml'), 'sources/cdata/archives', 'templates/default/styles', Main::folder_files('templates/default/styles', '.html'));

Main::check_chmod_write($check_list);

$install_vars['step_max'] = '150';

$step_name = 'install/welcome_page';

if(isset($_GET['get_started']))
	$_SESSION['DE_INSTALL_STEP'] = 1;
elseif(isset($_GET['reinstall']))
	unset($_SESSION['DE_INSTALL_STEP']);
elseif((isset($_SESSION['DE_INSTALL_STEP']) && $_SESSION['DE_INSTALL_STEP'] == 1 && isset($_GET['agree'])) || (isset($_GET['data']) && isset($_SESSION['DE_INSTALL_STEP']) && $_SESSION['DE_INSTALL_STEP'] > 2))
	$_SESSION['DE_INSTALL_STEP'] = 2;
elseif(isset($_SESSION['DE_INSTALL_STEP']) && $_SESSION['DE_INSTALL_STEP'] > 3 && isset($_GET['extra']))
	$_SESSION['DE_INSTALL_STEP'] = 3;

$install_vars['active_welcome'] = $install_vars['active_agree'] = $install_vars['active_data'] = $install_vars['active_extra'] = $install_vars['active_prog'] = $install_vars['active_done'] = 'default';

if(isset($GLOBALS['CHMOD']['REQ_FTP']))
	$step_name = 'ftp_connect';
elseif(isset($_SESSION['DE_INSTALL_STEP']))
{

	$step_name = 'install/step_'.$_SESSION['DE_INSTALL_STEP'];

	$proc = array(10, 20, 30, 30, 10);

	check_step($_SESSION['DE_INSTALL_STEP'], $install_vars, $step_vars, $errors_list, $done_list);

	$t_proc = 0;

	/*
	if(isset($_SESSION['DE_INSTALL_STEP']))
	{

		for($i=1;$i<=$_SESSION['DE_INSTALL_STEP'];$i++)
			$t_proc += $proc[$i - 1];

		$install_vars['install_step'] = $_SESSION['DE_INSTALL_STEP'];
		$install_vars['install_proc'] = ($t_proc / 100) * $install_vars['step_max'];

	}
	*/

}

if(!isset($_SESSION['DE_INSTALL_STEP']))
{
	$install_vars['active_welcome'] = 'active';

	$self_page = dirname('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);;

	$GLOBALS['CONFIG_WEBSITE_URL'] = $self_page;

	Configs::update_configs(array('CONFIG_WEBSITE_URL' => '\''.$self_page.'\''), 'main');

}
else
	if($_SESSION['DE_INSTALL_STEP'] == 6)
		unset($_SESSION['DE_INSTALL_STEP']);

if(count($GLOBALS['CHMOD']['FAIL_LIST']))
{

	$errors_list = 'Your windows it\'s not compatible with CHMOD, so you must set write&read permissions to next files: <br />';

	foreach($GLOBALS['CHMOD']['FAIL_LIST'] as $val)
		$errors_list .= $val.'<br />';

}

$step_vars['step_errors'] = !$errors_list ? null : '<div id="error">'.$errors_list.'</div>';
$step_vars['step_done'] = !$done_list ? null : '<div id="success">'.$done_list.'</div>';

$install_vars['install_step_data'] = Template::load($step_name.'.html', $step_vars, 0);

echo Template::load('install/install.html', $install_vars, 0);
