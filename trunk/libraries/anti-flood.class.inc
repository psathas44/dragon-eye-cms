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

class AFlood
{

	private $file = null;

	private $ltime = null;

	private $rtime = null;

	private $ip_list = array();

	private $section = null;

	private $bypass = null;

	private $ban_time = null;

	public function __construct($section)
	{

		$this->section = strtoupper($section);

		$this->bypass = $GLOBALS['CONFIG_'.$this->section.'_FLOOD_BYPASS'];

		$this->ban_time = $GLOBALS['CONFIG_'.$this->section.'_FLOOD_TIME'];

	}

	private function is_banned()
	{

		if(is_array($this->ip_list))
			foreach($this->ip_list as $key => $val)
			{

				if(trim($val[0]) == USER_IP && trim($val[2]) == $this->section && ((time() - trim($val[1])) < $this->ban_time))
				{

					$this->rtime = time() - trim($val[1]);

					return true;

				}
				elseif(trim($val[0]) == USER_IP && trim($val[2]) == $this->section && ((time() - trim($val[1])) >= $this->ban_time))
						unset($this->ip_list[$key]);

			}

	}

	public function check()
	{

		$this->file = sep_path(CMS_DIR.'/logs/flood_bans'.$GLOBALS['CONFIG_FLOOD_LOGS_EXTRA'].'.txt');

		if(file_exists($this->file))
			$this->ip_list = unserialize(file_get_contents($this->file));
		else
			file_put_contents($this->file, serialize(array()));

		if($this->is_banned())
			return false;
		else
		{

			if(!isset($_SESSION['DE_ANTI_FLOOD_'.$this->section]))
			{

				$_SESSION['DE_ANTI_FLOOD_'.$this->section] = microtime(true);

				$this->ltime = 0;

			}
			else
				$this->ltime = $_SESSION['DE_ANTI_FLOOD_'.$this->section];

			if((microtime(true) - $this->ltime) < $this->bypass)
			{

				// Delete expired bans
				foreach($this->ip_list as $k => $v)
					if((time() - trim($v[1])) >= $this->ban_time)
						unset($this->ip_list[$k]);

				$this->ip_list[] = array(USER_IP, time(), $this->section);

				file_put_contents($this->file, serialize($this->ip_list));

				$this->rtime = 0;

				return false;
				
			}
			else
				$_SESSION['DE_ANTI_FLOOD_'.$this->section] = microtime(true);

			return true;

		}

	}

}