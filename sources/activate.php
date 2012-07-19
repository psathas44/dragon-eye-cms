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

$template_vars['page_title'] .= ' - Account Activation';

if(!$this->logged)
{

	$template_location[] = 'activation.html';

	$template_vars['val_user'] = null;
	$template_vars['val_code'] = null;
	$template_vars['status'] = null;

	if(isset($_GET['uname']))
		$template_vars['val_user'] = htmlspecialchars($_GET['uname']);
	if(isset($_GET['rid']))
		$template_vars['val_code'] = htmlspecialchars($_GET['rid']);

	if(isset($_GET['uname']) && isset($_GET['rid']))
	{

		$act_user = htmlspecialchars(trim($_GET['uname']));
		$act_rid = htmlspecialchars(trim($_GET['rid']));

		if($acc->validate_user($act_user) && $acc->validate_code($act_rid))
		{

			if(Account::activation_check($act_user))
			{

				// Check if account already exists
				if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
					$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_ACCOUNT'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_ACCOUNT'], Main::db_escape_string($act_user, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);
				else
					$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_ACCOUNT'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], Main::db_escape_string($act_user, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

				// If not -> continue
				if(Main::db_rows($query) == 0)
				{

					$query = Main::db_query(sprintf($GLOBALS['DBQUERY_MCHECK_CHECK'], Main::db_escape_string($act_user, $GLOBALS['DB_LOGIN_SERVER']), Main::db_escape_string($act_rid, $GLOBALS['DB_LOGIN_SERVER']), Main::db_escape_string(USER_IP, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

					if(Main::db_rows($query) == 1)
					{

						$query = Main::db_query(sprintf($GLOBALS['DBQUERY_MCHECK_DATA'], Main::db_escape_string($act_user, $GLOBALS['DB_LOGIN_SERVER']), Main::db_escape_string($act_rid, $GLOBALS['DB_LOGIN_SERVER']), Main::db_escape_string(USER_IP, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

						$data = Main::db_fetch_row($query);

						if($GLOBALS['CONFIG_REFER_SYSTEM'] && $acc->validate_refer($data[3]))
						{

							if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
								$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_ACCOUNT'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_ACCOUNT'], Main::db_escape_string($data[3], $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);
							else
								$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_ACCOUNT'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], Main::db_escape_string($data[3], $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

							if(Main::db_rows($query) != 1)
								$data[3] = null;

						}
						else
							$data[3] = null;

						Account::register($data[0], $data[2], $data[1], $data[3]);

						Main::db_query(sprintf($GLOBALS['DBQUERY_MCHECK_DELETE'], Main::db_escape_string($act_user, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

						$mail = new Mail();

						$mail->Send($data[1], $GLOBALS['CONFIG_ADMIN_MAIL'], sprintf($GLOBALS['LANG_REGISTER_MAIL_SUBJECT'], $GLOBALS['CONFIG_WEBSITE_NAME']), sprintf($GLOBALS['LANG_REGISTER_MAIL'], $data[0], $GLOBALS['CONFIG_WEBSITE_NAME']));

						$GLOBALS['the_status'] = $GLOBALS['LANG_REGISTER_SUCCEDED'];

					}
					else
						$GLOBALS['the_status'] = $GLOBALS['LANG_ERROR_ACT_SESSION'];

				}
				else
					$GLOBALS['the_status'] = $GLOBALS['LANG_REGISTER_USERALR'];

			}
			else
				$GLOBALS['the_status'] = $GLOBALS['LANG_ERROR_ACT_EXPIRED'];

		}

	}

	$template_vars['status'] = $GLOBALS['the_status'];

}
else
{

	$template_vars['the_error'] = $GLOBALS['LANG_PAGE_RESTRICTED'];

	$template_location[] = 'errors.html';	

}

$template_location[] = 'footer.html';
