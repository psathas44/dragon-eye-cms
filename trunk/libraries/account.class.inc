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

class Account
{

	public $account_username = null;

	private $account_password = null;

	public $server_type = null;

	public function __construct()
	{

		$this->server_type = $GLOBALS['CONFIG_SERVER_TYPE'];

		if(!isset($_SESSION['dragon_eye_acc_user']) && isset($_COOKIE['de_login_username']) && isset($_COOKIE['de_login_password']))
		{

			$this->account_username = htmlspecialchars($_COOKIE['de_login_username']);
			$this->account_password = htmlspecialchars($_COOKIE['de_login_password']);

			if(!$this->do_login())
			{

				$this->account_username = null;
				$this->account_password = null;

			}

		}

		if(isset($_SESSION['dragon_eye_acc_user']))
		{

			$this->account_username = htmlspecialchars($_SESSION['dragon_eye_acc_user']);
			$this->account_password = null;

		}
		else
		{

			if(isset($_POST['login_username']) && isset($_POST['login_password']) && isset($_POST['login_submit']))
			{

				$account_user = htmlspecialchars($_POST['login_username']);
				$account_pass = htmlspecialchars($_POST['login_password']);

				$this->account_username = null;
				$this->account_password = null;

				if($this->validate_user($account_user) && $this->validate_pass($account_pass))
				{

					$this->account_username = $account_user;
					$this->account_password = @Main::encrypt($account_pass);

					if(!$this->do_login())
					{

						$this->account_username = null;
						$this->account_password = null;

					}

				}

			}
			elseif(isset($_POST['register_username']) && isset($_POST['register_password']) && isset($_POST['register_rpassword']) && isset($_POST['register_email']) && isset($_POST['register_remail']) && isset($_POST['register_submit']))
			{

				$register_username = htmlspecialchars($_POST['register_username']);
				$register_password = htmlspecialchars($_POST['register_password']);
				$register_rpassword = htmlspecialchars($_POST['register_rpassword']);
				$register_email = $_POST['register_email'];
				$register_remail = $_POST['register_remail'];

				// Validate all inputs
				if($this->validate_user($register_username) && $this->validate_pass($register_password) && $this->validate_email($register_email))
				{

					// Check if passwords and emails are the same
					if($register_password != $register_rpassword || $register_email != $register_remail)
						$GLOBALS['the_status'] = $GLOBALS['LANG_ERROR_SAME'];
					else
					{

						if(Account::activation_check($register_username))
						{

							// Encrypt the entered password
							$register_password = @Main::encrypt($register_password);

							// Check if account already exists
							$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_ACCOUNT'], 'mail_check', 'username', Main::db_escape_string($register_username, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

							// If not -> continue
							if(!$this->_checkUser($register_username) && Main::db_rows($query) == 0)
							{

								// Check if email already exists
								if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
									$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_EMAIL'], $GLOBALS['DBSTRUCT_L2OFF_SSN_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_SSN_EMAIL'], Main::db_escape_string($register_email, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);
								else
									$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_EMAIL'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_MAIL'], Main::db_escape_string($register_email, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

								$query2 = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_EMAIL'], 'mail_check', 'email', Main::db_escape_string($register_email, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

								// If not -> continue
								if(Main::db_rows($query) == 0 && Main::db_rows($query2) == 0)
								{

									$mail = new Mail();
									$register_flood = new AFlood('register');

									if(!$register_flood->check())
										$GLOBALS['the_status'] = $GLOBALS['LANG_ERROR_REGISTER_TIME'];
									else
									{

										$refer = null;
										
										if(isset($_GET['refer']))
										{

											$refer = htmlspecialchars($_GET['refer']);

											if($GLOBALS['CONFIG_REFER_SYSTEM'] && $this->validate_refer($refer))
											{

												if(!$this->_checkUser($refer))
													$refer = null;

											}
											else
												$refer = null;

										}

										if($GLOBALS['CONFIG_REGISTER_ACTIVATION'])
										{

											$activate_id = substr(sha1(base64_encode(rand(10, 999))), 1, 15);
											$activate_page = $GLOBALS['CONFIG_WEBSITE_URL'].'/index.php?page=activate&uname='.$register_username;
											$activate_link = $activate_page.'&rid='.$activate_id;	

											Main::db_query(sprintf($GLOBALS['DBQUERY_MCHECK_CREATE'], Main::db_escape_string($register_username, $GLOBALS['DB_LOGIN_SERVER']), Main::db_escape_string($register_email, $GLOBALS['DB_LOGIN_SERVER']), Main::db_escape_string($register_password, $GLOBALS['DB_LOGIN_SERVER']), Main::db_escape_string(USER_IP, $GLOBALS['DB_LOGIN_SERVER']), $activate_id, time(), Main::db_escape_string($refer, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

											$mail->Send($register_email, $GLOBALS['CONFIG_ADMIN_MAIL'], sprintf($GLOBALS['LANG_ACTIVATE_MAIL_SUBJECT'], $GLOBALS['CONFIG_WEBSITE_NAME']), sprintf($GLOBALS['LANG_ACTIVATE_MAIL'], $register_username, $activate_link, $activate_id, $activate_page, $GLOBALS['CONFIG_WEBSITE_NAME']));

											$resend_link = sep_path(CMS_DIR.'/index.php?page=activate&resend');

											$GLOBALS['the_status'] = sprintf($GLOBALS['LANG_REGISTER_ACTIVATE'], $register_email);

										}
										else
										{

											$this->register($register_username, $register_password, $register_email, $refer);

											$mail->Send($register_email, $GLOBALS['CONFIG_ADMIN_MAIL'], sprintf($GLOBALS['LANG_REGISTER_MAIL_SUBJECT'], $GLOBALS['CONFIG_WEBSITE_NAME']), sprintf($GLOBALS['LANG_REGISTER_MAIL'], $register_username, $GLOBALS['CONFIG_WEBSITE_NAME']));

											$GLOBALS['the_status'] = $GLOBALS['LANG_REGISTER_SUCCEDED'];

										}

									}

								}
								else
									$GLOBALS['the_status'] = $GLOBALS['LANG_REGISTER_MAILALR'];

							}
							else
								$GLOBALS['the_status'] = $GLOBALS['LANG_REGISTER_USERALR'];

						}
						else
							$GLOBALS['the_status'] = $GLOBALS['LANG_ERROR_ACT_EXPIRED'];

					}

				}

			}

		}

	}

	public static function getReferPoints($account_name)
	{

		if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
	 		return Main::db_result(Main::db_query(sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_REFER_POINTS'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_ACCOUNT'], Main::db_escape_string($account_name, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']), 0);
		else
			return Main::db_result(Main::db_query(sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_REFER_POINTS'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], Main::db_escape_string($account_name, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']), 0);

	}

	public static function _checkUser($acc)
	{

		if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
			$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_ACCOUNT'], $GLOBALS['DBSTRUCT_L2OFF_USERAUT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERAUT_ACCOUNT'], Main::db_escape_string($acc, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);
		else
			$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_ACCOUNT'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], Main::db_escape_string($acc, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

		if(Main::db_rows($query) == 1)
			return true;

		return false;

	}

	final public static function register($register_username, $register_password, $register_email, $register_refer)
	{	

		if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
		{

			$answer1 = 'nothing';
			$answer2 = 'nothing';
			$question1 = 'nothing';
			$question2 = 'nothing';
			$ssn1 = mt_rand(1000000,9999999);
			$ssn2 = mt_rand(100000,999999);
			$ssn = $ssn1.$ssn2;

			// Execute DB Queryes required to create an account for L2OFF
			Main::db_query(sprintf($GLOBALS['DBQUERY_CREATE_ACCOUNT_L2OFF_SSN'], $GLOBALS['DBSTRUCT_L2OFF_SSN_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_SSN_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_SSN_NAME'], $GLOBALS['DBSTRUCT_L2OFF_SSN_EMAIL'], $GLOBALS['DBSTRUCT_L2OFF_SSN_JOB'], $GLOBALS['DBSTRUCT_L2OFF_SSN_PHONE'], $GLOBALS['DBSTRUCT_L2OFF_SSN_ZIP'], $GLOBALS['DBSTRUCT_L2OFF_SSN_MAD'], $GLOBALS['DBSTRUCT_L2OFF_SSN_EAD'], $GLOBALS['DBSTRUCT_L2OFF_SSN_ACN'], $ssn, Main::db_escape_string($register_username, $GLOBALS['DB_LOGIN_SERVER']), Main::db_escape_string($register_email, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);
			Main::db_query(sprintf($GLOBALS['DBQUERY_CREATE_ACCOUNT_L2OFF_UAC'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_ACCOUNT'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_PAYSTAT'], 'refer', Main::db_escape_string($register_username, $GLOBALS['DB_LOGIN_SERVER']), Main::db_escape_string($register_refer, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);
			Main::db_query(sprintf($GLOBALS['DBQUERY_CREATE_ACCOUNT_L2OFF_UAU'], $GLOBALS['DBSTRUCT_L2OFF_USERAUT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERAUT_ACCOUNT'], $GLOBALS['DBSTRUCT_L2OFF_USERAUT_PASS'], $GLOBALS['DBSTRUCT_L2OFF_USERAUT_QUIZ1'], $GLOBALS['DBSTRUCT_L2OFF_USERAUT_QUIZ2'], $GLOBALS['DBSTRUCT_L2OFF_USERAUT_ANSW1'], $GLOBALS['DBSTRUCT_L2OFF_USERAUT_ANSW2'], Main::db_escape_string($register_username, $GLOBALS['DB_LOGIN_SERVER']), Main::db_escape_string($register_password, $GLOBALS['DB_LOGIN_SERVER']), $question1, $question2, $answer1, $answer2), $GLOBALS['DB_LOGIN_SERVER']);
			Main::db_query(sprintf($GLOBALS['DBQUERY_CREATE_ACCOUNT_L2OFF_UIF'], $GLOBALS['DBSTRUCT_L2OFF_USERINF_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERINF_ACCOUNT'], $GLOBALS['DBSTRUCT_L2OFF_USERINF_SSN'], $GLOBALS['DBSTRUCT_L2OFF_USERINF_KIND'], Main::db_escape_string($register_username, $GLOBALS['DB_LOGIN_SERVER']), $ssn), $GLOBALS['DB_LOGIN_SERVER']);

		}
		else
			// Execute DB Query required to create an account for L2J
			Main::db_query(sprintf($GLOBALS['DBQUERY_CREATE_ACCOUNT_L2J'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_PASS'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_ACC_LVL'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_MAIL'], 'refer', Main::db_escape_string($register_username, $GLOBALS['DB_LOGIN_SERVER']), Main::db_escape_string($register_password, $GLOBALS['DB_LOGIN_SERVER']), '0', Main::db_escape_string($register_email, $GLOBALS['DB_LOGIN_SERVER']), Main::db_escape_string($register_refer, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

		if(!empty($register_refer))
			if(Account::_checkUser($register_refer))
				Main::db_query(sprintf($GLOBALS['DBQUERY_ADD_REFER_POINTS'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_REFER_POINTS'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_REFER_POINTS'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], Main::db_escape_string($register_refer, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

		return true;	

	}

	final public function validate_email($acc_mail)
	{

		// Validate the entered email
		if(!isset($acc_mail[65]))
			if(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i', $acc_mail))
				return true;

		$GLOBALS['the_status'] = $GLOBALS['LANG_ERROR_MAIL'];

	}

	final public function validate_user($acc_user)
	{

		// Validate the entered username
		if(isset($acc_user[3]) && !isset($acc_user[16]) && ctype_alnum($acc_user))
			return true;

		$GLOBALS['the_status'] = $GLOBALS['LANG_ERROR_USER'];

	}

	final public function validate_refer($refer)
	{

		if(isset($refer[3]) && !isset($refer[16]) && ctype_alnum($refer))
			return true;

	}

	final public function validate_code($act_code)
	{

		if(isset($act_code[14]) && !isset($act_code[15]))
			return true;

		$GLOBALS['the_status'] = $GLOBALS['LANG_ERROR_ACT_CODE'];

	}

	final public function validate_pass($acc_pass)
	{

		// Validate the entered password
		if(isset($acc_pass[3]) && !isset($acc_pass[16]))
			return true;

		$GLOBALS['the_status'] = $GLOBALS['LANG_ERROR_PASS'];

	}

	final public function logged()
	{

		// Check if the username from $_SESSION['dragon_eye_acc_user'] exists
		// If yes, it means that we're already logged in so return true
		if($this->account_username && !$this->account_password)
		{

			if($this->_checkUser($this->account_username))
				return true;
			else
				self::logout();

		}

	}

	final private function do_login()
	{

		// Check whether entered login data it's good or not
		if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
			$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_LOGIN'], $GLOBALS['DBSTRUCT_L2OFF_USERAUT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERAUT_ACCOUNT'], Main::db_escape_string($this->account_username, $GLOBALS['DB_LOGIN_SERVER']), $GLOBALS['DBSTRUCT_L2OFF_USERAUT_PASS'], 'CONVERT(binary, '.Main::db_escape_string($this->account_password, $GLOBALS['DB_LOGIN_SERVER']).')'), $GLOBALS['DB_LOGIN_SERVER']);
		else
			$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_LOGIN'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], Main::db_escape_string($this->account_username, $GLOBALS['DB_LOGIN_SERVER']), $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_PASS'], '\''.Main::db_escape_string($this->account_password, $GLOBALS['DB_LOGIN_SERVER']).'\''), $GLOBALS['DB_LOGIN_SERVER']);

		// If login data it's good -> login the user else show an error
		if(Main::db_rows($query) == 1)
		{

			if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
				$acc_data = Main::db_fetch_row(Main::db_query(sprintf($GLOBALS['DBQUERY_ACCOUNT_DATA'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_ACCOUNT'], sprintf($GLOBALS['DBQUERY_L2OFF_GET_MAIL'], $GLOBALS['DBSTRUCT_L2OFF_SSN_EMAIL'], $GLOBALS['DBSTRUCT_L2OFF_SSN_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_SSN_NAME'], Main::db_escape_string($this->account_username, $GLOBALS['DB_LOGIN_SERVER'])), 'refer', $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_ACCOUNT'], Main::db_escape_string($this->account_username, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']));
			else
				$acc_data = Main::db_fetch_row(Main::db_query(sprintf($GLOBALS['DBQUERY_ACCOUNT_DATA'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_MAIL'], 'refer', $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], Main::db_escape_string($this->account_username, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']));

			$this->account_username = $acc_data[0];

			$_SESSION['dragon_eye_acc_data']['mail'] = $acc_data[1];
			$_SESSION['dragon_eye_acc_data']['refer'] = $acc_data[2];

			$_SESSION['dragon_eye_acc_user'] = $this->account_username;

			if(isset($_POST['login_remember']))
			{

				$expire = time() + 3600 * 24 * 30;

				// Set login remember cookies
				setcookie('de_login_username', $this->account_username, $expire);
				setcookie('de_login_password', $this->account_password, $expire);

			}

			$GLOBALS['the_status'] = $GLOBALS['LANG_LOGIN_LOGGED'];

			return true;

		}
		else
			$GLOBALS['the_status'] = $GLOBALS['LANG_ERROR_USPA'];

	}

	final public function access_level()
	{

		switch($this->server_type)
		{

			case 1 : $query = Main::db_query(sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2OFF_BUILDER_VALUE'], $GLOBALS['DBSTRUCT_L2OFF_BUILDER_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_BUILDER_ACCOUNT'], Main::db_escape_string($this->account_username, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);	break;
			case 2 : $query = Main::db_query(sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_ACC_LVL'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], Main::db_escape_string($this->account_username, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);	break;

			default : $query = Main::db_query(sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_ACC_LVL'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], Main::db_escape_string($this->account_username, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

		}

		if($GLOBALS['CONFIG_SERVER_TYPE'] == 1 && Main::db_rows($query) == 0)
			return 1;
		else
		{

			$row = Main::db_fetch_row($query);

			$access = null;
			$i = 0;

			while(isset($GLOBALS['CONFIG_ACCESS_LEVELS_'.$i]))
			{

				if($GLOBALS['CONFIG_ACCESS_LEVELS_'.$i] == $row[0])
				{

					$access = $i;

					break;

				}

				++$i;

			}

			return $access;

		}

	}

	final public static function activation_check($act_user)
	{

		$time_offset = time() - $GLOBALS['CONFIG_REGISTER_ACTIVATION_SESS_EXPIRE'];

		$query = Main::db_query(sprintf($GLOBALS['DBQUERY_MCHECK_CHECK_EXPIRED'], Main::db_escape_string($act_user, $GLOBALS['DB_LOGIN_SERVER']), $time_offset), $GLOBALS['DB_LOGIN_SERVER']);

		if(Main::db_rows($query) == 0)
			return true;
		else
		{

			Main::db_query(sprintf($GLOBALS['DBQUERY_MCHECK_DELETE'], Main::db_escape_string($act_user, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

			return false;

		}

	}

	final public static function recover_check($rec_user)
	{

		$time_offset = time() - $GLOBALS['CONFIG_RECOVER_SESS_EXPIRE'];

		$query = Main::db_query(sprintf($GLOBALS['DBQUERY_MCHECK_CHECK_EXPIRED'], Main::db_escape_string($rec_user, $GLOBALS['DB_LOGIN_SERVER']), $time_offset), $GLOBALS['DB_LOGIN_SERVER']);

		if(Main::db_rows($query) == 0)
			return true;
		else
		{

			Main::db_query(sprintf($GLOBALS['DBQUERY_MCHECK_DELETE'], Main::db_escape_string($rec_user, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

			return false;

		}

	}

	final public static function check_char($acc_name, $char_id)
	{

		if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
			$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_CHAR'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], Main::db_escape_string($char_id, $GLOBALS['DB_GAME_SERVER']), $GLOBALS['DBSTRUCT_L2OFF_USERDAT_ACC'], Main::db_escape_string($acc_name, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);
		else
			$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_CHAR'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_ID'], Main::db_escape_string($char_id, $GLOBALS['DB_GAME_SERVER']), $GLOBALS['DBSTRUCT_L2J_CHARS_ACC'], Main::db_escape_string($acc_name, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);

		if(Main::db_rows($query) == 1)
			return true;

	}

	final public static function character_selected($acc_name)
	{

		if(isset($_SESSION['dragon_eye_character']) && Account::check_char($acc_name, $_SESSION['dragon_eye_character']))
			return true;

	}

	final public static function chars($acc, $id)
	{

		$chars = array();

		if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
			$query = Main::db_query(sprintf($GLOBALS['DBQUERY_2_1'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_NAME'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_ACC'], Main::db_escape_string($acc, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);
		else
			$query = Main::db_query(sprintf($GLOBALS['DBQUERY_2_1'], $GLOBALS['DBSTRUCT_L2J_CHARS_NAME'], $GLOBALS['DBSTRUCT_L2J_CHARS_ID'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_ACC'], Main::db_escape_string($acc, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);

		while($row=Main::db_fetch_row($query))
			$chars[$row[0]] = $row[1];

		return $chars;

	}

	final public static function char_name($char_id)
	{

		if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
			$query = Main::db_query(sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_NAME'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], Main::db_escape_string($char_id, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);
		else
			$query = Main::db_query(sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2J_CHARS_NAME'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_ID'], Main::db_escape_string($char_id, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);

		return Main::db_result($query, 0);

	}

	final public static function select_char($return)
	{


	}

	final public static function clan_name($clan_id)
	{

		if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
			$query = Main::db_query(sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2OFF_PLEDGE_NAME'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], Main::db_escape_string($char_id, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);
		else
			$query = Main::db_query(sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2J_CLAN_NAME'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_ID'], Main::db_escape_string($char_id, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);

		$name = Main::db_fetch_row($query);

		return $name[0];

	}

	final public static function class_name($class_id)
	{

		switch($class_id)
		{

			case 0 : return 'Human Fighter';	break;
			case 1 : return 'Human Warrior';	break;
			case 2 : return 'Gladiator';	break;
			case 3 : return 'Warlord';	break;
			case 4 : return 'Human Knight';	break;
			case 5 : return 'Paladin';	break;
			case 6 : return 'Dark Avenger';	break;
			case 7 : return 'Rogue';	break;
			case 8 : return 'Treasure Hunter';	break;
			case 9 : return 'Hawkeye';	break;
			case 10 : return 'Human Mage';	break;
			case 11 : return 'Human Wizard';	break;
			case 12 : return 'Sorcerer';	break;
			case 13 : return 'Necromancer';	break;
			case 14 : return 'Warlock';	break;
			case 15 : return 'Cleric';	break;
			case 16 : return 'Bishop';	break;
			case 17 : return 'Prophet';	break;
			case 18 : return 'Elven Fighter';	break;
			case 19 : return 'Elven Knight';	break;
			case 20 : return 'Temple Knight';	break;
			case 21 : return 'Swordsinger';	break;
			case 22 : return 'Elven Scout';	break;
			case 23 : return 'Plainswalker';	break;
			case 24 : return 'Silver Ranger';	break;
			case 25 : return 'Elven Mage';	break;
			case 26 : return 'Elven Wizard';	break;
			case 27 : return 'Spellsinger';	break;
			case 28 : return 'Elemental Summoner';	break;
			case 29 : return 'Elven Oracle';	break;
			case 30 : return 'Elven Elder';	break;
			case 31 : return 'Dark Elven Fighter';	break;
			case 32 : return 'Pallus Knight';	break;
			case 33 : return 'Shillien Knight';	break;
			case 34 : return 'Bladedancer';	break;
			case 35 : return 'Assasin';	break;
			case 36 : return 'Abyss Walker';	break;
			case 37 : return 'Phantom Ranger';	break;
			case 38 : return 'Dark Elven Mage';	break;
			case 39 : return 'Dark Wizard';	break;
			case 40 : return 'Spellhowler';	break;
			case 41 : return 'Phantom Summoner';	break;
			case 42 : return 'Shillien Oracle';	break;
			case 43 : return 'Shillien Elder';	break;
			case 44 : return 'Orc Fighter';	break;
			case 45 : return 'Orc Raider';	break;
			case 46 : return 'Destroyer';	break;
			case 47 : return 'Monk';	break;
			case 48 : return 'Tyrant';	break;
			case 49 : return 'Orc Mage';	break;
			case 50 : return 'Orc Shaman';	break;
			case 51 : return 'Overlord';	break;
			case 52 : return 'Warcryer';	break;
			case 53 : return 'Dwarven Fighter';	break;
			case 54 : return 'Scavenger';	break;
			case 55 : return 'Bounty Hunter';	break;
			case 56 : return 'Artisan';	break;
			case 57 : return 'Warsmith';	break;
			case 88 : return 'Duelist';	break;
			case 89 : return 'Dread Nought';	break;
			case 90 : return 'Phoenix Knight';	break;
			case 91 : return 'Hell Knight';	break;
			case 92 : return 'Sagittarius';	break;
			case 93 : return 'Adventurer';	break;
			case 94 : return 'Archmage';	break;
			case 95 : return 'Soul Traker';	break;
			case 96 : return 'Arcane Lord';	break;
			case 97 : return 'Cardinal';	break;
			case 98 : return 'Hierophant';	break;
			case 99 : return 'Evas Templar';	break;
			case 100 : return 'Sword Muse';	break;
			case 101 : return 'Wind Rider';	break;
			case 102 : return 'Moonlight Sentinel';	break;
			case 103 : return 'Mystic Muse';	break;
			case 104 : return 'Elemental Master';	break;
			case 105 : return 'Evas Saint';	break;
			case 106 : return 'Shillien Templar';	break;
			case 107 : return 'Spectral Dancer';	break;
			case 108 : return 'Ghost Hunter';	break;
			case 109 : return 'Ghost Sentinel';	break;
			case 110 : return 'Storm Screamer';	break;
			case 111 : return 'Spectral Master';	break;
			case 112 : return 'Shillien Saint';	break;
			case 113 : return 'Titan';	break;
			case 114 : return 'Grand Khauatari';	break;
			case 115 : return 'Dominator';	break;
			case 116 : return 'Doomcryer';	break;
			case 117 : return 'Fortune Seeker';	break;
			case 118 : return 'Maestro';	break;

			default : return 'Unknown';

		}

	}

	final public static function castle_name($castle_id)
	{

		switch($castle_id)
		{

			case 1 : return 'Gludio';	break;
			case 2 : return 'Dion';	break;
			case 3 : return 'Giran';	break;
			case 4 : return 'Oren';	break;
			case 5 : return 'Aden';	break;
			case 6 : return 'Innadril';	break;
			case 7 : return 'Goddard';	break;
			case 8 : return 'Rune';	break;
			case 9 : return 'Schuttgart';	break;

			default : return 'Unknown';

		}

	}

	public static function logout()
	{

		if(isset($_SESSION['dragon_eye_acc_user']))
			unset($_SESSION['dragon_eye_acc_user']);	// Destroy user login session

		if(isset($_COOKIE['de_login_username']) || isset($_COOKIE['de_login_password']))
		{

			// Destroy login remember cookies
			setcookie('de_login_username', '', time() - 3600);
			setcookie('de_login_password', '', time() - 3600);

		}

	}

}