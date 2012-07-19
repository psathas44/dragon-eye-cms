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

class Donate
{

	public $currency = 'USD';

	public $paypal = true;

	public $moneybookers = false;

	public $paypal_url = null;

	public $paypal_mail = null;

	public $moneybookers_mail = null;

	public function __construct()
	{

		$this->currency = $GLOBALS['CONFIG_DONATE_CURRENCY'] == 1 ? 'EUR' : 'USD';

		$this->paypal = $GLOBALS['CONFIG_DONATE_PAYPAL_ENABLE'];

		$this->moneybookers = $GLOBALS['CONFIG_DONATE_MONEYBOOKERS_ENABLE'];

		$this->paypal_url = $GLOBALS['CONFIG_DONATE_PAYPAL_TEST'] ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';

		$this->paypal_mail = $GLOBALS['CONFIG_DONATE_PAYPAL_MAIL'];

		$this->moneybookers_mail = $GLOBALS['CONFIG_DONATE_MONEYBOOKERS_MAIL'];

	}

	private function _checkUser($acc)
	{

		if($GLOBALS['CONFIG_SERVER_TYPE'] == '1')
			$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_ACCOUNT'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_ACCOUNT'], Main::db_escape_string($acc, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);
		else
			$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_ACCOUNT'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], Main::db_escape_string($acc, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

		if(Main::db_rows($query) == 1)
			return true;

		return false;

	}

	private function _addPoints($acc, $val)
	{

		$donate_points = $this->getDonatePoints($acc);

		$new_points = $donate_points + round($val * ($GLOBALS['CONFIG_DONATE_MULTIPLIER'] ? $GLOBALS['CONFIG_DONATE_MULTIPLIER'] : 1), 5);

		if($this->_checkUser(htmlspecialchars($acc)))
			if($GLOBALS['CONFIG_SERVER_TYPE'] == '1' && Main::db_query(sprintf($GLOBALS['DBQUERY_ADD_POINTS'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_DONATE_POINTS'], Main::db_escape_string($new_points, $GLOBALS['DB_LOGIN_SERVER']), $GLOBALS['DBSTRUCT_L2OFF_USERACC_ACCOUNT'], Main::db_escape_string($acc, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']))
					return true;
			elseif(Main::db_query(sprintf($GLOBALS['DBQUERY_ADD_POINTS'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_DONATE_POINTS'], Main::db_escape_string($new_points, $GLOBALS['DB_LOGIN_SERVER']), $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], Main::db_escape_string($acc, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']))
				return true;

		return false;

	}

	private function _checkTransaction($txn_id)
	{

		$log_file = sep_path(CMS_DIR.'/logs/'.md5('donate_paypal_ok'.$GLOBALS['CONFIG_DONATE_LOGS_EXTRA']).'.txt');

		if(file_exists($log_file))
		{

			$log_data = array();

			$log_data = unserialize(@file_get_contents($log_file));

			foreach($log_data as $v)
			{

				foreach($v as $x => $z)
					if($x == 'txn_id' && $z == $txn_id)
					{

						return false;

						break;

					}

			}

		}

		return true;

	}

	private function _logDonations($type, $acc, $donp)
	{

		switch($type)
		{

			case 1 : $et = 'ok';	break;
			case 2 : $et = 'inv';	break;
			case 3 : $et = 'http';	break;

			default : break;

		}

		$log_file = sep_path(CMS_DIR.'/logs/'.md5('donate_'.$donp.'_'.$et.$GLOBALS['CONFIG_DONATE_LOGS_EXTRA']).'.txt');

		$log_data = array();

		if(file_exists($log_file))
			$log_data = unserialize(@file_get_contents($log_file));

		$d_data = $_POST;

		$d_data['dragon_eye_receiver'] = $acc;

		$log_data[] = $d_data;

		@file_put_contents($log_file, serialize($log_data));

	}

	public static function getDonatePoints($account_name)
	{

		if($GLOBALS['CONFIG_SERVER_TYPE'] == '1')
			return Main::db_result(Main::db_query(sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_DONATE_POINTS'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_ACCOUNT'], Main::db_escape_string($account_name, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']), 0);
		else
			return Main::db_result(Main::db_query(sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_DONATE_POINTS'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], Main::db_escape_string($account_name, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']), 0);

	}

	public function paypal_ipn()
	{

		$req = 'cmd=_notify-validate';

		$paypal_url = $GLOBALS['CONFIG_DONATE_PAYPAL_TEST'] ? 'ssl://www.sandbox.paypal.com' : 'ssl://www.paypal.com';

		foreach($_POST as $key => $value)
		{

			$value = urlencode(stripslashes($value));
			$req .= '&'.$key.'='.$value;

		}

		$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: ".strlen($req)."\r\n\r\n";

		$fp = @fsockopen($paypal_url, 443, $errno, $errstr, 30);

		if(!$fp)
			$this->_logDonations(3, $_GET['ppipn'], 'paypal');
		else
		{

			fputs($fp, $header.$req);

			while(!feof($fp))
			{

				$res = fgets($fp, 1024);

				if(strcmp($res, "VERIFIED") == 0 && @$_POST['payment_status'] == 'Completed' && $this->_checkTransaction(@$_POST['txn_id']) && @$_POST['receiver_email'] == $this->paypal_mail && @$_POST['mc_currency'] == $this->currency && $this->_addPoints($_GET['ppipn'], $_POST['mc_gross'] * ($GLOBALS['CONFIG_DONATE_PAYPAL_MULTIPLIER'] ? $GLOBALS['CONFIG_DONATE_PAYPAL_MULTIPLIER'] : 1)))
					$this->_logDonations(1, $_GET['ppipn'], 'paypal');
				elseif(strcmp($res, "INVALID") == 0)
					$this->_logDonations(2, $_GET['ppipn'], 'paypal');

			}

			fclose($fp);

		}

	}

	public function moneybookers_ipn()
	{

		$fieldscode = @$_POST['merchant_id'].@$_POST['transaction_id'].strtoupper(md5($CONFIG['CONFIG_DONATE_MONEYBOOKERS_SECRET_WORD'])).@$_POST['mb_amount'].@$_POST['mb_currency'].@$_POST['status'];

		if(@$_POST['mb_currency'] == $this->currency && strtoupper(md5($fieldscode)) == @$_POST['md5sig'] && $_POST['status'] == 2 && $_POST['pay_to_email'] == $this->moneybookers_mail && $this->_addPoints($_GET['acc'], $_POST['mb_amount'] * ($GLOBALS['CONFIG_DONATE_MONEYBOOKERS_MULTIPLIER'] ? $GLOBALS['CONFIG_DONATE_MONEYBOOKERS_MULTIPLIER'] : 1)))
			$this->_logDonations(1, $_GET['mbipn'], 'mb');
		else
			$this->_logDonations(2, $_GET['mbipn'], 'mb');

	}

}