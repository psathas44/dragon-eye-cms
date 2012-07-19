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

class Mail
{

	public $SMTP_RESPONSE = array();

	private $SMTP_SERVER;

	private $SMTP_PORT;

	private $SMTP_USER;

	private $SMTP_PASS;

	private $MAIL_TYPE;	

	public function __construct()
	{

		$this->MAIL_TYPE = $GLOBALS['CONFIG_MAIL_TYPE'];

		if($this->MAIL_TYPE == '1')
		{

			$this->SMTP_SERVER = $GLOBALS['CONFIG_SMTP_SERVER'];

			$this->SMTP_PORT = $GLOBALS['CONFIG_SMTP_PORT'];

			$this->SMTP_USER = base64_encode($GLOBALS['CONFIG_SMTP_USER']);

			$this->SMTP_PASS = base64_encode($GLOBALS['CONFIG_SMTP_PASS']);

			$this->SMTP_TIMEOUT = $GLOBALS['CONFIG_SMTP_TIMEOUT'];

		}

	}

	public function Send($mail_to, $mail_from, $mail_subject, $mail_body)
	{

		if($this->MAIL_TYPE == '1')
		{

			if(@$SMTP_CONN = fsockopen($this->SMTP_SERVER, $this->SMTP_PORT, $errno, $errstr, $this->SMTP_TIMEOUT)) 
			{

				@fputs($SMTP_CONN, "HELO ".$_SERVER['HTTP_HOST']."\r\n");
				$this->SMTP_RESPONSE['HELO'] = fgets($SMTP_CONN, 1024);

				@fputs($SMTP_CONN, "auth login\r\n");
				$this->SMTP_RESPONSE['AUTH'] = fgets($SMTP_CONN, 1024);

				@fputs($SMTP_CONN, $this->SMTP_USER."\r\n");
				$this->SMTP_RESPONSE['USER'] = fgets($SMTP_CONN, 1024);

				@fputs($SMTP_CONN, $this->SMTP_PASS."\r\n");
				$this->SMTP_RESPONSE['PASS'] = fgets($SMTP_CONN, 256);

				@fputs($SMTP_CONN, "MAIL FROM: <".$mail_from.">\r\n");
				$this->SMTP_RESPONSE['FROM'] = fgets($SMTP_CONN, 1024);

				@fputs($SMTP_CONN, "RCPT TO: <".$mail_to.">\r\n"); 
				$this->SMTP_RESPONSE['TO'] = fgets($SMTP_CONN, 1024);

				@fputs($SMTP_CONN, "DATA\r\n");
				$this->SMTP_RESPONSE['DATA'] = fgets($SMTP_CONN, 1024);

				if(@fputs($SMTP_CONN, "To: <".$mail_to.">\r\nFrom: <".$mail_from.">\r\nSubject:".$mail_subject."\r\n\r\n\r\n".$mail_body."\r\n.\r\n"))
					return true;

				$this->SMTP_RESPONSE['SDATA'] = fgets($SMTP_CONN, 256);

				@fputs($SMTP_CONN, "QUIT\r\n"); 
				@fclose($SMTP_CONN); 

			}

		}
		elseif($this->MAIL_TYPE == '2')
		{

			$headers = "From: ".$mail_from."\r\n";

			if(@mail($mail_to, $mail_subject, $mail_body, $headers))
				return true;

		}

	}

	public function GetResponse($resp)
	{

		if(isset($this->SMTP_RESPONSE[$resp]))
			return $this->SMTP_RESPONSE[$resp];
		else
			return $this->SMTP_RESPONSE;

	}

}