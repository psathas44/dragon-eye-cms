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

$template_vars['page_title'] .= ' - Account';

if(isset($_GET['donate']) && !isset($_GET['shop']) && $GLOBALS['CONFIG_DONATE_ENABLED'])
{

	$template_vars['acc_page'] = null;

	$acc_page_vars = array();

	$template_location[] = 'account.html';

	@require_once(sep_path(CMS_DIR.'/libraries/donate.class.inc'));

	$donate = new Donate();

	if(isset($_GET['ppipn']) && $donate->paypal)
		$donate->paypal_ipn();
	elseif(isset($_GET['mbipn']) && $donate->moneybookers)
		$donate->moneybookers_ipn();
	else
	{

		$donate_page_vars = array();

		$donate_page_vars['val_acc'] = isset($_POST['donate_to']) ? $_POST['donate_to'] : null;

		$donate_page_vars['val_amount'] = isset($_POST['donate_amount']) ? $_POST['donate_amount'] : null;

		$donate_page_vars['currency'] = $donate->currency;

		$acc_page_vars['donate_form'] = Template::load('styles/donate_form.html', $donate_page_vars, 0);

		$acc_page_vars['paypal_method'] = null;

		$acc_page_vars['paypal_points'] = null;

		$acc_page_vars['moneybookers_method'] = null;

		$acc_page_vars['moneybookers_points'] = null;

		if(isset($_GET['payment_done']))
			$GLOBALS['the_status'] = $GLOBALS['LANG_DONATE_SUCCED'];
		elseif(isset($_GET['payment_canceled']))
			$GLOBALS['the_status'] = $GLOBALS['LANG_DONATE_CANCEL'];

		if(isset($_POST['donate_to']) && isset($_POST['donate_amount']) && $_POST['donate_amount'] > 0 && $acc->validate_user($_POST['donate_to']))
		{

			if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
				$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_ACCOUNT'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_ACCOUNT'], Main::db_escape_string(htmlspecialchars($_POST['donate_to']), $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);
			else
				$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_ACCOUNT'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], Main::db_escape_string(htmlspecialchars($_POST['donate_to']), $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

			if(Main::db_rows($query) == 1)
			{

				$method_page_vars = array();

				$method_page_vars['account_name'] = htmlspecialchars($_POST['donate_to']);

				$method_page_vars['amount'] = (double) $_POST['donate_amount'];

				$method_page_vars['currency'] = $donate->currency;

				if($donate->paypal)
				{

					$method_page_vars['paypal_url'] = $donate->paypal_url;

					$method_page_vars['paypal_mail'] = $donate->paypal_mail;

					$method_page_vars['paypal_points'] = (double) round($_POST['donate_amount'] * ($GLOBALS['CONFIG_DONATE_MULTIPLIER'] ? $GLOBALS['CONFIG_DONATE_MULTIPLIER'] : 1) * ($GLOBALS['CONFIG_DONATE_PAYPAL_MULTIPLIER'] ? $GLOBALS['CONFIG_DONATE_PAYPAL_MULTIPLIER'] : 1), 5);

					$acc_page_vars['paypal_method'] = Template::load('styles/paypal_method.html', $method_page_vars, 0);

				}

				if($donate->moneybookers)
				{

					$method_page_vars['mb_mail'] = $donate->moneybookers_mail;

					$method_page_vars['moneybookers_points'] = (double) round($_POST['donate_amount'] * ($GLOBALS['CONFIG_DONATE_MULTIPLIER'] ? $GLOBALS['CONFIG_DONATE_MULTIPLIER'] : 1) * ($GLOBALS['CONFIG_DONATE_MONEYBOOKERS_MULTIPLIER'] ? $GLOBALS['CONFIG_DONATE_MONEYBOOKERS_MULTIPLIER'] : 1), 5);

					$acc_page_vars['moneybookers_method'] = Template::load('styles/moneybookers_method.html', $method_page_vars, 0);

				}

			}
			else
				$GLOBALS['the_status'] = $GLOBALS['LANG_ERROR_USER'];

		}

		$acc_page_vars['donate_status'] = $GLOBALS['the_status'];

		$template_vars['acc_page'] = Template::load('donate_system.html', $acc_page_vars, 0);

	}

}
elseif($this->logged)
{

	$template_vars['acc_page'] = null;

	$acc_page_vars = array();

	$template_location[] = 'account.html';

	if(isset($_GET['refer']) && !isset($_GET['shop']))
	{

		$acc_page_vars['refer_link'] = $GLOBALS['CONFIG_WEBSITE_URL'].'/index.php?register&page=account&refer='.$acc->account_username;

		$acc_page_vars['refer_by'] = $_SESSION['dragon_eye_acc_data']['refer'] ? $_SESSION['dragon_eye_acc_data']['refer'] : $GLOBALS['LANG_REFER_NOONE'];

		$acc_page_vars['refer_points'] = Account::getReferPoints($acc->account_username);

		$template_vars['acc_page'] = Template::load('refer.html', $acc_page_vars, 0);
		
	}
	elseif(isset($_GET['profile']))
	{

		if(isset($_GET['cpass']) && $GLOBALS['CONFIG_CHANGE_PASS'])
		{

			if(isset($_POST['change_password']) && isset($_POST['change_npassword']) && isset($_POST['change_rnpassword']))
			{

				$change_password = $acc_page_vars['val_pass'] = htmlspecialchars($_POST['change_password']);
				$change_npassword = $acc_page_vars['val_npass'] = htmlspecialchars($_POST['change_npassword']);
				$change_rnpassword = $acc_page_vars['val_rnpass']  = htmlspecialchars($_POST['change_rnpassword']);

				if($acc->validate_pass($change_password) && $acc->validate_pass($change_npassword) && $acc->validate_pass($change_rnpassword))
				{

					if($change_npassword != $change_rnpassword)
						$GLOBALS['the_status'] = $GLOBALS['LANG_ERROR_RNPSAME'];
					elseif($change_npassword == $change_password)
						$GLOBALS['the_status'] = $GLOBALS['LANG_ERROR_NPSAME'];
					else
					{

						$old_pass = Main::encrypt($change_password);
						$new_pass = Main::encrypt($change_npassword);

						if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
							$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_LOGIN'], $GLOBALS['DBSTRUCT_L2OFF_USERAUT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERAUT_ACCOUNT'], Main::db_escape_string($acc->account_username, $GLOBALS['DB_LOGIN_SERVER']), $GLOBALS['DBSTRUCT_L2OFF_USERAUT_PASS'], 'CONVERT(binary, '.$old_pass.')'), $GLOBALS['DB_LOGIN_SERVER']);
						else
							$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_LOGIN'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], Main::db_escape_string($acc->account_username, $GLOBALS['DB_LOGIN_SERVER']), $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_PASS'], '\''.$old_pass.'\''), $GLOBALS['DB_LOGIN_SERVER']);

						if(Main::db_rows($query) == 1)
						{

							$cpass_flood = new AFlood('cpass');
	
							if(!$cpass_flood->check())
								$GLOBALS['the_status'] = $GLOBALS['LANG_ERROR_CPASS_TIME'];
							else
							{

								if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
									Main::db_query(sprintf($GLOBALS['DBQUERY_CHANGE_PASSWORD'], $GLOBALS['DBSTRUCT_L2OFF_USERAUT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERAUT_PASS'], 'CONVERT(binary, '.$new_pass.')', $GLOBALS['DBSTRUCT_L2OFF_USERAUT_ACCOUNT'], Main::db_escape_string($acc->account_username, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);
								else
									Main::db_query(sprintf($GLOBALS['DBQUERY_CHANGE_PASSWORD'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_PASS'], '\''.$new_pass.'\'', $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], Main::db_escape_string($acc->account_username, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

								$GLOBALS['the_status'] = $GLOBALS['LANG_PASS_CHANGED'];
	
							}

						}
						else
							$GLOBALS['the_status'] = $GLOBALS['LANG_ERROR_OLD_PASS'];

					}

				}

			}
			else
				$acc_page_vars['val_pass'] = $acc_page_vars['val_npass'] = $acc_page_vars['val_rnpass'] = '';			

			$template_vars['acc_page'] = Template::load('cpass.html', $acc_page_vars, 0);

		}
		elseif(isset($_GET['signature']))
		{

			if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
				$query = Main::db_query(sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_SIGNATURE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_ACCOUNT'], Main::db_escape_string($acc->account_username, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);
			else
				$query = Main::db_query(sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_SIGNATURE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], Main::db_escape_string($acc->account_username, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

			$sig_status = Main::db_result($query, 0);

			if(isset($_POST['accsig_status']) && isset($_POST['save']))
			{

				if($_POST['accsig_status'] == '0')
					$new_status = '0';
				elseif($_POST['accsig_status'] == '1')
					$new_status = '1';
				else
					$new_status = '0';

				if($new_status != $sig_status)
				{

					if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
						Main::db_query(sprintf($GLOBALS['DBQUERY_CHANGE_SIGNATURE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_SIGNATURE'], Main::db_escape_string($new_status, $GLOBALS['DB_LOGIN_SERVER']), $GLOBALS['DBSTRUCT_L2OFF_USERACC_ACCOUNT'], Main::db_escape_string($acc->account_username, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);
					else
						Main::db_query(sprintf($GLOBALS['DBQUERY_CHANGE_SIGNATURE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_SIGNATURE'], Main::db_escape_string($new_status, $GLOBALS['DB_LOGIN_SERVER']), $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], Main::db_escape_string($acc->account_username, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

					$sig_status = $new_status;

				}

			}

			$signature_page_vars['accsig_status'] = $sig_status == 1 ? $GLOBALS['LANG_SIG_ENABLED'].$GLOBALS['LANG_SIG_DISABLED'] : $GLOBALS['LANG_SIG_DISABLED'].$GLOBALS['LANG_SIG_ENABLED'];

			$signature_page_vars['account_signature'] = $GLOBALS['CONFIG_WEBSITE_URL'].'/?page=signature&acc='.$acc->account_username;

			$template_vars['acc_page'] = Template::load('account_signature.html', $signature_page_vars, 0);

		}
		else
		{

			if($this->server_type == 1)
			{

				$last_logged = Main::db_result(Main::db_query(sprintf($GLOBALS['DBQUERY_LAST_LOGGED'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_LAST_LOGGED'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_ACCOUNT'], Main::db_escape_string($acc->account_username, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']), 0);

				$acc_page_vars['last_glogged'] = date($GLOBALS['CONFIG_DATE_FORMAT'], strtotime($last_logged));

			}
			else
			{

				$last_logged = Main::db_result(Main::db_query(sprintf($GLOBALS['DBQUERY_LAST_LOGGED'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_LAST_LOGGED'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], Main::db_escape_string($acc->account_username, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']), 0);

				$acc_page_vars['last_glogged'] = date($GLOBALS['CONFIG_DATE_FORMAT'], $last_logged / 1000);

			}

			$template_vars['acc_page'] = Template::load('profile.html', $acc_page_vars, 0);

		}

	}
	elseif(isset($_GET['characters']))
	{

		if(isset($_GET['id']) && $_GET['id'] != '0')
		{

			$char_id = intval($_GET['id']);

			if(Account::check_char($acc->account_username, $char_id))
			{

				$acc_page_vars['the_content'] = null;

				$_SESSION['dragon_eye_character'] = $char_id;

				$char_name = Account::char_name($char_id);

				$acc_page_vars['char_name'] = $char_name;
				$acc_page_vars['char_id'] = $char_id;

				if(isset($_GET['details']))
				{

					if($this->server_type == 1)
						$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHAR_DATA'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_LEVEL'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_PVP'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_PK'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_CLAN'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_CLASS'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TITLE'], '('.sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2OFF_USOCIAL_NAME'], $GLOBALS['DBSTRUCT_L2OFF_USOCIAL_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USOCIAL_ID'], Main::db_escape_string($char_id, $GLOBALS['DB_GAME_SERVER'])).')', $GLOBALS['DBSTRUCT_L2OFF_USERDAT_ONLINE_TIME'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_LAST_ACCESS'], '('.sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2OFF_UNOBLES_NOBLE'], $GLOBALS['DBSTRUCT_L2OFF_UNOBLES_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_UNOBLES_ID'], Main::db_escape_string($char_id, $GLOBALS['DB_GAME_SERVER'])).')', '('.sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2OFF_UNOBLES_HERO'], $GLOBALS['DBSTRUCT_L2OFF_UNOBLES_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_UNOBLES_ID'], Main::db_escape_string($char_id, $GLOBALS['DB_GAME_SERVER'])).')', $GLOBALS['DBSTRUCT_L2OFF_PLEDGE_NAME'], $GLOBALS['DBSTRUCT_L2OFF_PLEDGE_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_PLEDGE_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_PLEDGE_ID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_CLAN'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], Main::db_escape_string($char_id, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);
					else
						$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHAR_DATA'], $GLOBALS['DBSTRUCT_L2J_CHARS_LEVEL'], $GLOBALS['DBSTRUCT_L2J_CHARS_PVP'], $GLOBALS['DBSTRUCT_L2J_CHARS_PK'], $GLOBALS['DBSTRUCT_L2J_CHARS_CLAN'], $GLOBALS['DBSTRUCT_L2J_CHARS_CLASS'], $GLOBALS['DBSTRUCT_L2J_CHARS_TITLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_RECS'], $GLOBALS['DBSTRUCT_L2J_CHARS_ONLINE_TIME'], $GLOBALS['DBSTRUCT_L2J_CHARS_LAST_ACCESS'], $GLOBALS['DBSTRUCT_L2J_CHARS_NOBLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_HERO'], $GLOBALS['DBSTRUCT_L2J_CLAN_NAME'], $GLOBALS['DBSTRUCT_L2J_CLAN_TABLE'], $GLOBALS['DBSTRUCT_L2J_CLAN_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2J_CLAN_ID'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2J_CHARS_CLAN'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_ID'], Main::db_escape_string($char_id, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);

					$char_data = Main::db_fetch_row($query);

					$char_page_vars['char_level'] = $char_data[0] ? $char_data[0] : '1';
					$char_page_vars['char_pvp'] = $char_data[1] ? $char_data[1] : '0';
					$char_page_vars['char_pk'] = $char_data[2] ? $char_data[2] : '0';
					$char_page_vars['char_clan_id'] = $char_data[3] ? $char_data[3] : '0';					
					$char_page_vars['char_clan_name'] = $char_data[11] ? htmlspecialchars($char_data[11]) : $GLOBALS['LANG_NO_CLAN'];
					$char_page_vars['char_class'] = Account::class_name($char_data[4]);
					$char_page_vars['char_title'] = $char_data[5] ? htmlspecialchars($char_data[5]) : $GLOBALS['LANG_NO_TITLE'];
					$char_page_vars['char_recs'] = $char_data[6] ? $char_data[6] : '0';
					$char_page_vars['char_online_time'] = gmstrftime(bcdiv($char_data[7], 86400).' '.$GLOBALS['LANG_DAYS'].' %H '.$GLOBALS['LANG_HOURS'].' %M '.$GLOBALS['LANG_MINS'], $char_data[7]);
					$char_page_vars['char_last_access'] = $char_data[8] ? date($GLOBALS['CONFIG_DATE_FORMAT'], ($this->server_type == 1 ? strtotime($char_data[8]) : $char_data[8] / 1000)) : $GLOBALS['LANG_NEVER'];
					$char_page_vars['char_noblesse'] = $char_data[9] == '1' ? $GLOBALS['LANG_YES'] : $GLOBALS['LANG_NO'];
					$char_page_vars['char_hero'] = $char_data[10] == '1' ? $GLOBALS['LANG_YES'] : $GLOBALS['LANG_NO'];

					$acc_page_vars['the_content'] = Template::load('char_details.html', $char_page_vars, 0);

				}
				elseif(isset($_GET['settings']))
				{

					$inv = isset($_POST['char_inventory']) ? intval($_POST['char_inventory']) : false;
					$skills = isset($_POST['char_skills']) ? intval($_POST['char_skills']) : false;
					$banner = isset($_POST['char_banner']) ? intval($_POST['char_banner']) : false;

					if($this->server_type == 1)
						$vset = Main::db_result(Main::db_query(sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_VIEWSET'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], Main::db_escape_string($char_id, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']), 0);
					else
						$vset = Main::db_result(Main::db_query(sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2J_CHARS_VIEWSET'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_ID'], Main::db_escape_string($char_id, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']), 0);

					if($inv && !$skills && !$banner)
						$viewset = 1;
					elseif($inv && $skills && !$banner)
						$viewset = 2;
					elseif($inv && !$skills && $banner)
						$viewset = 3;
					elseif(!$inv && $skills && !$banner)
						$viewset = 4;
					elseif(!$inv && $skills && $banner)
						$viewset = 5;
					elseif(!$inv && !$skills && $banner)
						$viewset = 6;
					elseif($inv && $skills && $banner)
						$viewset = 7;
					else
						$viewset = 0;

					if($_POST['save'] && $vset != $viewset)
					{

						if($this->server_type == 1)
							Main::db_query(sprintf($GLOBALS['DBQUERY_CHANGE_VIEWSET'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_VIEWSET'], $viewset, $GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], Main::db_escape_string($char_id, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);
						else
							Main::db_query(sprintf($GLOBALS['DBQUERY_CHANGE_VIEWSET'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_VIEWSET'], $viewset, $GLOBALS['DBSTRUCT_L2J_CHARS_ID'], Main::db_escape_string($char_id, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);

						$vset = $viewset;

						$GLOBALS['the_status'] = $GLOBALS['LANG_SETTINGS_SAVED'];

					}

					$char_page_vars['val_inv'] = (in_array($vset, array('1', '2', '3', '7'))) ? 'checked="checked" ' : null;
					$char_page_vars['val_skills'] = (in_array($vset, array('2', '4', '5', '7'))) ? 'checked="checked" ' : null;
					$char_page_vars['val_banner'] = (in_array($vset, array('3', '5', '6', '7'))) ? 'checked="checked" ' : null;

					$acc_page_vars['the_content'] = Template::load('char_settings.html', $char_page_vars, 0);

				}
				elseif(isset($_GET['signature']))
				{

					$signature_page_vars['player_signature'] = $GLOBALS['CONFIG_WEBSITE_URL'].'/?page=signature&char='.$char_name;

					$acc_page_vars['the_content'] = Template::load('player_signature.html', $signature_page_vars, 0);

				}

				$template_vars['acc_page'] = Template::load('char_data.html', $acc_page_vars, 0);

			}
			else
				$template_vars['acc_page'] = Template::load('errors.html', array('the_error' => $GLOBALS['LANG_ERROR_CHAR_ID']), 0);

		}
		else
		{

			$acc_page_vars['chars_list'] = '';

			$chars = Account::chars($acc->account_username);

			if(count($chars) == 0)
				$chars[$GLOBALS['LANG_NO_CHARS']] = '0';

			foreach($chars as $char => $id)		
				$acc_page_vars['chars_list'] .= Template::load('styles/characters_list.html', array('char_name' => $char, 'char_id' => $id), 0);

			$template_vars['acc_page'] = Template::load('styles/characters_basic.html', $acc_page_vars, 0);

		}

	}
	elseif(isset($_GET['clans']))
	{

		if(isset($_GET['id']) && $_GET['id'] != '0')
		{

			$clan_id = intval($_GET['id']);

			if($this->server_type == 1)
				$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_CLAN'], $GLOBALS['DBSTRUCT_L2OFF_PLEDGE_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_PLEDGE_ID'], Main::db_escape_string($clan_id, $GLOBALS['DB_GAME_SERVER']), $GLOBALS['DBSTRUCT_L2OFF_PLEDGE_ID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_CLAN'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ACC'], Main::db_escape_string($acc->account_username, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);
			else
				$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_CLAN'], $GLOBALS['DBSTRUCT_L2J_CLAN_TABLE'], $GLOBALS['DBSTRUCT_L2J_CLAN_ID'], Main::db_escape_string($clan_id, $GLOBALS['DB_GAME_SERVER']), $GLOBALS['DBSTRUCT_L2J_CLAN_ID'], $GLOBALS['DBSTRUCT_L2J_CHARS_CLAN'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2J_CHARS_ACC'], Main::db_escape_string($acc->account_username, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);

			if(Main::db_rows($query) == 1)
			{

				if(isset($_GET['members']))
				{

					$members_page_vars = array();

					$acc_page_vars['members_list'] = '';

					$acc_page_vars['clan_id'] = $clan_id;

					if($this->server_type == 1)
						$query = Main::db_query(sprintf($GLOBALS['DBQUERY_MEMBERS_DATA'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_NAME'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_LEVEL'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_CLASS'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_CLAN'], Main::db_escape_string($clan_id, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);
					else
						$query = Main::db_query(sprintf($GLOBALS['DBQUERY_MEMBERS_DATA'], $GLOBALS['DBSTRUCT_L2J_CHARS_NAME'], $GLOBALS['DBSTRUCT_L2J_CHARS_LEVEL'], $GLOBALS['DBSTRUCT_L2J_CHARS_CLASS'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_CLAN'], Main::db_escape_string($clan_id, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);

					while($members_data=Main::db_fetch_row($query))
					{

						$members_page_vars['member_name'] = htmlspecialchars($members_data[0]);
						$members_page_vars['member_level'] = $members_data[1] ? $members_data[1] : '1';
						$members_page_vars['member_class'] = Account::class_name($members_data[2]);

						$acc_page_vars['members_list'] .= Template::load('styles/clan_members_list.html', $members_page_vars, 0);

					}

					$template_vars['acc_page'] = Template::load('clan_members.html', $acc_page_vars, 0);

				}
				else
				{

					if($this->server_type == 1)
						$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CLAN_DATA'], $GLOBALS['DBSTRUCT_L2OFF_PLEDGE_NAME'], $GLOBALS['DBSTRUCT_L2OFF_PLEDGE_LEVEL'], $GLOBALS['DBSTRUCT_L2OFF_PLEDGE_CASTLE'], '('.sprintf($GLOBALS['DBQUERY_1_1N'], $GLOBALS['DBSTRUCT_L2OFF_ALLIANCE_NAME'], $GLOBALS['DBSTRUCT_L2OFF_ALLIANCE_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_ALLIANCE_ID'], $GLOBALS['DBSTRUCT_L2OFF_PLEDGE_ALLY']).')', $GLOBALS['DBSTRUCT_L2OFF_USERDAT_NAME'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], $GLOBALS['DBSTRUCT_L2OFF_PLEDGE_LEADER'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_CLAN'], $GLOBALS['DBSTRUCT_L2OFF_PLEDGE_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_PLEDGE_ID'], $GLOBALS['DBSTRUCT_L2OFF_PLEDGE_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_PLEDGE_ID'], Main::db_escape_string($clan_id, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);
					else
						$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CLAN_DATA'], $GLOBALS['DBSTRUCT_L2J_CLAN_NAME'], $GLOBALS['DBSTRUCT_L2J_CLAN_LEVEL'], $GLOBALS['DBSTRUCT_L2J_CLAN_CASTLE'], $GLOBALS['DBSTRUCT_L2J_CLAN_ALLY'], $GLOBALS['DBSTRUCT_L2J_CHARS_NAME'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2J_CHARS_ID'], $GLOBALS['DBSTRUCT_L2J_CLAN_LEADER'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2J_CHARS_CLAN'], $GLOBALS['DBSTRUCT_L2J_CLAN_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2J_CLAN_ID'], $GLOBALS['DBSTRUCT_L2J_CLAN_TABLE'], $GLOBALS['DBSTRUCT_L2J_CLAN_ID'], Main::db_escape_string($clan_id, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);

					$clan_data = Main::db_fetch_row($query);

					$acc_page_vars['clan_name'] = htmlspecialchars($clan_data[0]);
					$acc_page_vars['clan_level'] = $clan_data[1] ? $clan_data[1] : '1';
					$acc_page_vars['clan_leader'] = htmlspecialchars($clan_data[4]);
					$acc_page_vars['clan_ally'] = $clan_data[3] ? htmlspecialchars($clan_data[3]) : $GLOBALS['LANG_NO_ALLY'];
					$acc_page_vars['clan_castle'] = Account::castle_name($clan_data[2]);
					$acc_page_vars['clan_members'] = $clan_data[5];
					$acc_page_vars['clan_id'] = $clan_id;

					$template_vars['acc_page'] = Template::load('clan_details.html', $acc_page_vars, 0);

				}

			}
			else
				$template_vars['acc_page'] = Template::load('errors.html', array('the_error' => $GLOBALS['LANG_ERROR_CLAN_ID']), 0);

		}

	}
	elseif(isset($_GET['select_char']))
	{

		$select_char_vars = array();

		$select_char_vars['select_list'] = '';
		$select_char_vars['select_status'] = '';

		if(isset($_GET['id']) && $_GET['id'] != '0')
		{

			$char_id = intval($_GET['id']);

			if(Account::check_char($acc->account_username, $char_id))
			{

				$select_char_vars['select_status'] = 'Character Selected! Press <a href="?page=account&shop">here</a> to access shop if you are not redirected in 5 seconds!</a><meta http-equiv="refresh" content="5;url=?page=account&shop">';

				$_SESSION['dragon_eye_character'] = $char_id;

			}
			else
				$select_char_vars['select_status'] = $GLOBALS['LANG_ERROR_CHAR_ID'];

		}

		$chars = Account::chars($acc->account_username);

		if(count($chars) == 0)
			$chars[$GLOBALS['LANG_NO_CHARS']] = '0';

		foreach($chars as $char => $id)		
			$select_char_vars['select_list'] .= Template::load('styles/select_char_list.html', array('char_name' => $char, 'char_id' => $id), 0);

		$template_vars['acc_page'] = Template::load('select_character.html', $select_char_vars, 0);

	}
	elseif(isset($_GET['shop']) && $GLOBALS['CONFIG_SHOP_ENABLED'])
	{

		@require_once(sep_path(CMS_DIR.'/libraries/shop.class.inc'));
		@require_once(sep_path(CMS_DIR.'/libraries/vote.class.inc'));
		@require_once(sep_path(CMS_DIR.'/libraries/donate.class.inc'));

		if(isset($_GET['refer']) && $GLOBALS['CONFIG_SHOP_REFER_ENABLED'])
		{

			$shop = new Shop('refer', $GLOBALS['CONFIG_SHOP_REFER'], Account::getReferPoints($acc->account_username), $acc->account_username);

			$shop_page_vars = $shop->load();

			$template_vars['acc_page'] = Template::load('shop_page.html', $shop_page_vars, 0);

		}
		elseif(isset($_GET['vote']) && $GLOBALS['CONFIG_SHOP_VOTE_ENABLED'])
		{

			$shop = new Shop('vote', $GLOBALS['CONFIG_SHOP_VOTE'], Vote::getVotePoints($acc->account_username), $acc->account_username);

			$shop_page_vars = $shop->load();

			$template_vars['acc_page'] = Template::load('shop_page.html', $shop_page_vars, 0);

		}
		elseif(isset($_GET['donate']) && $GLOBALS['CONFIG_SHOP_DONATE_ENABLED'])
		{

			$shop = new Shop('donate', $GLOBALS['CONFIG_SHOP_DONATE'], Donate::getDonatePoints($acc->account_username), $acc->account_username);

			$shop_page_vars = $shop->load();

			$template_vars['acc_page'] = Template::load('shop_page.html', $shop_page_vars, 0);

		}
		elseif(isset($_GET['forum']) && $GLOBALS['CONFIG_SHOP_FORUM_ENABLED'])
		{

			$shop = new Shop('forum', $GLOBALS['CONFIG_SHOP_FORUM'], 0, $acc->account_username);

			$shop_page_vars = $shop->load();

			$template_vars['acc_page'] = Template::load('shop_page.html', $shop_page_vars, 0);

		}
		else
			$template_vars['acc_page'] = Template::load('shop.html', array(), 0);

	}
	elseif(isset($_GET['vote']) && $GLOBALS['CONFIG_VOTE_ENABLED'])
	{

		@require_once(sep_path(CMS_DIR.'/libraries/vote.class.inc'));

		$vote = new Vote($acc->account_username);

		if($vote->setBannerId($_GET['banner']))
		{

			$vote->setServerId();

			$vote->setBannerName();

			$vote->setBannerLink();

			$vote->setBannerImage();

			$vote->setPointsPerVote();

			$vote->vote_points = Vote::getVotePoints($acc->account_username);

			$acc_page_vars['the_system'] = $vote->showBannerData();

		}
		else
			$acc_page_vars['the_system'] = $vote->showBanners();

		$template_vars['acc_page'] = Template::load('vote_system.html', $acc_page_vars, 0);

	}
	else
		$template_vars['acc_page'] = Template::load('account_welcome.html', array('username' => $acc->account_username), 0);

}
elseif(isset($_GET['register']))
{

	$template_vars['val_user'] = isset($_POST['register_username']) ? $_POST['register_username'] : '';
	$template_vars['val_pass'] = isset($_POST['register_password']) ? $_POST['register_password'] : '';
	$template_vars['val_rpass'] = isset($_POST['register_rpassword']) ? $_POST['register_rpassword'] : '';
	$template_vars['val_email'] = isset($_POST['register_email']) ? $_POST['register_email'] : '';
	$template_vars['val_remail'] = isset($_POST['register_remail']) ? $_POST['register_remail'] : '';
	$template_vars['val_refer'] = isset($_GET['refer']) ? '&refer='.$_GET['refer'] : '';

	$template_location[] = 'register.html';

}
elseif(isset($_GET['pass_recover']) && $GLOBALS['CONFIG_RECOVER_PASS'])
{

	$template_location[] = 'pass_recover.html';


	$template_vars['val_mail'] = isset($_POST['recover_mail']) ? $_POST['recover_mail'] : '';

	if(isset($_POST['recover_mail']) && isset($_POST['recover_submit']) && $acc->validate_email($_POST['recover_mail']))
	{

		$recover_mail = htmlspecialchars($_POST['recover_mail']);

		if($this->server_type == 1)
			$query = Main::db_query(sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2OFF_SSN_NAME'], $GLOBALS['DBSTRUCT_L2OFF_SSN_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_SSN_EMAIL'], Main::db_escape_string($recover_mail, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);
		else
			$query = Main::db_query(sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_MAIL'], Main::db_escape_string($recover_mail, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

		if(Main::db_rows($query) == 1)
		{

			$recover_account = Main::db_result($query, 0);

			if(Account::recover_check($recover_account))
			{

				$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_ACCOUNT'], 'mail_check', 'user', Main::db_escape_string($recover_account, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);
				$query2 = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_EMAIL'], 'mail_check', 'email', Main::db_escape_string($recover_mail, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

				if(Main::db_rows($query) == 0 && Main::db_rows($query2) == 0)
				{

					$mail = new Mail();

					$recover_flood = new AFlood('recover');

					if(!$recover_flood->check())
						$GLOBALS['the_status'] = $GLOBALS['LANG_ERROR_RECOVER_TIME'];
					else
					{

						$recover_id = substr(sha1(base64_encode(rand(10, 999))), 1, 15);
						$recover_page = $GLOBALS['CONFIG_WEBSITE_URL'].'/index.php?page=recover&uname='.$recover_account;
						$recover_link = $recover_page.'&rid='.$recover_id;

						$generated_pass = substr(sha1(base64_encode(rand(10, 999))), 1, 8);

						Main::db_query(sprintf($GLOBALS['DBQUERY_MCHECK_CREATE'], Main::db_escape_string($recover_account, $GLOBALS['DB_LOGIN_SERVER']), Main::db_escape_string($recover_mail, $GLOBALS['DB_LOGIN_SERVER']), $generated_pass, Main::db_escape_string(USER_IP, $GLOBALS['DB_LOGIN_SERVER']), $recover_id, time(), null), $GLOBALS['DB_LOGIN_SERVER']);

						$mail->Send($recover_mail, $GLOBALS['CONFIG_ADMIN_MAIL'], sprintf($GLOBALS['LANG_RECOVER_MAIL_SUBJECT'], $GLOBALS['CONFIG_WEBSITE_NAME']), sprintf($GLOBALS['LANG_RECOVER_MAIL'], $recover_account, $recover_link, $recover_id, $recover_page, $GLOBALS['CONFIG_WEBSITE_NAME']));

						$GLOBALS['the_status'] = sprintf($GLOBALS['LANG_RECOVER_ACTIVATE'], $recover_mail);

					}

				}
				else
					$GLOBALS['the_status'] = $GLOBALS['LANG_ERROR_MAIL'];

			}

		}
		else
			$GLOBALS['the_status'] = $GLOBALS['LANG_ERROR_MAIL'];

	}

}
else
{

	$template_vars['val_user'] = isset($_POST['login_username']) ? $_POST['login_username'] : '';
	$template_vars['val_pass'] = isset($_POST['login_password']) ? $_POST['login_password'] : '';
	$template_vars['val_remember'] = isset($_POST['login_remember']) ? 'checked="checked" ' : '';

	$template_location[] = 'login.html';

}

$template_vars['status'] = $GLOBALS['the_status'];

$template_location[] = 'footer.html';
