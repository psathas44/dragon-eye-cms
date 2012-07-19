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

class Vote
{

	private $_websites = array();

	private $_checkMethod;

	private $_bannerId = null;

	private $_serverId = null;

	private $_bannerName = null;

	private $_bannerLink = null;

	private $_bannerImage = null;

	private $_logFile = null;

	private $_voteTime = null;

	private $_accountName = null;

	private $_pointsPerVote = 1;
	
	public $vote_points = 0;

	public function __construct($account_name)
	{

		$this->_accountName = $account_name;

		$this->_websites = $GLOBALS['CONFIG_VOTE_WEBSITES'];

		$this->_checkMethod = $GLOBALS['CONFIG_VOTE_CHECK_METHOD'];

		$this->_logFile = sep_path('logs/'.md5(USER_IP).'.txt');

	}

	private function _bannerExists($banner_id)
	{

		if(isset($this->_websites[$banner_id]))
			return true;

	}

	public function setBannerId($banner_id)
	{

		if(isset($_GET['banner']) && $this->_bannerExists($banner_id))
		{

			$this->_bannerId = (int) $banner_id;

			return true;
	
		}

	}

	public function setServerId()
	{

		$this->_serverId = $this->_websites[$this->_bannerId][3];

	}

	public static function getVotePoints($account_name)
	{

		if($GLOBALS['CONFIG_SERVER_TYPE'] == '1')
			return Main::db_result(Main::db_query(sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_VOTE_POINTS'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_ACCOUNT'], Main::db_escape_string($account_name, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']), 0);
		else
			return Main::db_result(Main::db_query(sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_VOTE_POINTS'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], Main::db_escape_string($account_name, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']), 0);

	}

	public function setBannerName()
	{

		$this->_bannerName = $this->_websites[$this->_bannerId][0];

	}

	public function setBannerLink()
	{

		$this->_bannerLink = $this->_websites[$this->_bannerId][2];

	}

	public function setBannerImage()
	{

		$this->_bannerImage = $this->_websites[$this->_bannerId][1];

	}

	public function setPointsPerVote()
	{

		$this->_pointsPerVote = $this->_websites[$this->_bannerId][5];

	}

	public function showBanners()
	{

		$the_banners = '';

		foreach($this->_websites as $id => $v)
			$the_banners .= Template::load('styles/vote_banners.html', array('banner_name' => $v[0], 'banner_image' => $v[1], 'banner_id' => $id), '0');

		return $the_banners;

	}

	private function _checkVoted()
	{

		if(file_exists($this->_logFile))
		{

			if($fh = @fopen($this->_logFile, 'r+'))
			{

				$log_data = array();

				$log_data = unserialize(fread($fh, filesize($this->_logFile)));

				if(isset($log_data[$this->_bannerId]))
				{

					$this->_voteTime = $log_data[$this->_bannerId][0];

					if(time() - $this->_voteTime < $this->_websites[$this->_bannerId][4])
						return true;

				}

				@fclose($fh);

			}

		}

	}

	private function _L2RankingCheck($vote_code)
	{

		if($GLOBALS['CONFIG_VOTE_CHECK_METHOD'] == '1' && extension_loaded('curl'))
		{

			$curl = curl_init();

			curl_setopt($curl, CURLOPT_URL, 'http://l2ranking.com/check_vote.php?sid='.$this->_serverId.'&key='.$vote_code);

			curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

			$response = curl_exec($curl);

			curl_close($curl);

		}
		else
			$response = @file_get_contents('http://l2ranking.com/check_vote.php?sid='.$this->_serverId.'&key='.$vote_code);

		if(strcasecmp($response, 'true') == 0)
			return true;

	}

	private function _makeVote()
	{

		if(!$this->_checkVoted())
		{

			$log_data = array();

			$log_data = unserialize(fread($fh, filesize($this->_logFile)));

			$log_data[$this->_bannerId] = array(time(), USER_IP);

			if(file_exists($this->_logFile))
			{

				if($fh = @fopen($this->_logFile, 'r+'))
				{

					$log_data = unserialize(fread($fh, filesize($this->_logFile)));

					@fclose($fh);

				}

			}

			$log_data[$this->_bannerId] = array(time(), USER_IP);

			file_put_contents($this->_logFile, serialize($log_data));

			return true;

		}

	}

	private function _addReward()
	{

		$this->vote_points += $this->_pointsPerVote;

		$new_points = $this->vote_points;

		if($GLOBALS['CONFIG_SERVER_TYPE'] == '1')
			Main::db_query(sprintf($GLOBALS['DBQUERY_ADD_POINTS'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_ACCOUNT'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_VOTE_POINTS'], Main::db_escape_string($new_points, $GLOBALS['DB_LOGIN_SERVER']), $GLOBALS['DBSTRUCT_L2OFF_USERACC_ACCOUNT'], Main::db_escape_string($this->_accountName, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);
		else
			Main::db_query(sprintf($GLOBALS['DBQUERY_ADD_POINTS'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_VOTE_POINTS'], Main::db_escape_string($new_points, $GLOBALS['DB_LOGIN_SERVER']), $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], Main::db_escape_string($this->_accountName, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

		return true;

	}

	public function showBannerData($banner_id)
	{

		$banner_data_vars = array();

		$banner_data_vars['vote_status'] = null;

		if(isset($_GET['code']))
		{

			if($this->_bannerName == 'L2RANKING')
			{

				if($this->_L2RankingCheck($_GET['code']) && $this->_makeVote() && $this->_addReward())
					$banner_data_vars['vote_status'] = $GLOBALS['LANG_VOTE_SUCCED'];
				else
					$banner_data_vars['vote_status'] = $GLOBALS['LANG_VOTE_FAILED'];

			}

		}

		$voted = $this->_checkVoted();

		$banner_data_vars['last_voted'] = $voted ? date('d/M/Y h:i:s', $this->_voteTime) : 'Vote now';

		$banner_data_vars['vote_delay'] = floor($this->_websites[$this->_bannerId][4] / 3600);

		$banner_data_vars['vote_points'] = $this->vote_points;

		$banner_data_vars['banner_id'] = $this->_bannerId;

		$banner_data_vars['banner_image'] = $this->_bannerImage;

		$banner_data_vars['server_website'] = $this->_bannerLink;

		$banner_data_vars['points_per_vote'] = $this->_pointsPerVote;

		return Template::load('vote_banner.html', $banner_data_vars, 0);

	}

}