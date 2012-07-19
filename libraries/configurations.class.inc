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

class Configs
{

	private static $config_file;

	private static $configs_list = array();

	private static $configs_vars = array();

	private static function config_exists()
	{

		if(!file_exists(sep_path(CMS_DIR.'/configurations/'.self::$config_file.'.config.php')))
			throw new Dragon_Eye_Exception('Can\'t find configuration file!');

	}

	public static function load($file)
	{

		try
		{

			self::$config_file = $file;

			self::config_exists();

			@require_once(sep_path(CMS_DIR.'/configurations/'.$file.'.config.php'));

		}
		catch (Dragon_Eye_Exception $e)
		{

			echo $e->errorMSG();
			return false;

		}

		return true;

	}

	public static function delete_configs($configs_del, $file)
	{

		try
		{

			self::$config_file = $file;

			self::config_exists();

			self::load_configs();

			self::$configs_vars = $configs_del;

			$count = count(self::$configs_list);

			for($i = 0;$i < $count;++$i)
			{

				if(trim(self::$configs_list[$i]) == '?>')
					self::$configs_list[$i] = '';

				if(substr(self::$configs_list[$i], 0, 8) != '$GLOBALS')
					continue;

				foreach($configs_del as $key)
				{

					if(strncasecmp(self::$configs_list[$i], '$GLOBALS[\''.$key.'\']', 12 + strlen($key)) == 0)
					{

						self::$configs_list[$i] = '';

						unset($configs_del[$key]);

					}
	
				}

			}

			self::save_configs();

		}
		catch(Dragon_Eye_Exception $e)
		{

			echo $e->errorMSG();
			return false;

		}

	}

	public static function update_configs($configs_upd, $file)
	{

		try
		{

			self::$config_file = $file;

			self::config_exists();

			self::load_configs();

			// Array which stores all variables that need update
			self::$configs_vars = $configs_upd;

			$count = count(self::$configs_list);

			for($i = 0;$i < $count;++$i)
			{

				if(trim(self::$configs_list[$i]) == '?>')
					self::$configs_list[$i] = '';

				if(substr(self::$configs_list[$i], 0, 8) != '$GLOBALS')
					continue;

				foreach($configs_upd as $key => $val)
				{

					if(strncasecmp(self::$configs_list[$i], '$GLOBALS[\''.$key.'\']', 12 + strlen($key)) == 0)
					{

						self::$configs_list[$i] = '$GLOBALS[\''.$key.'\'] = '.$val.';'."\n";

						unset($configs_upd[$key]);

					}

				}

			}

			if(!empty($configs_upd))
			{

				self::$configs_list[$i++] = '';

				foreach($configs_upd as $key => $val)
					self::$configs_list[$i++] = '$GLOBALS[\''.$key.'\'] = '.$val.';'."\n";

			}

			self::save_configs();

			return true;

		}
		catch (Dragon_Eye_Exception $e)
		{

			echo $e->errorMSG();
			return false;

		}

	}

	public static function remake_array($arr)
	{

		$array_remake = 'array(';

		if(count($arr) == 0)
			$array_remake .= ')';
		else
		{

			$j = 0;
	
			foreach($arr as $k)
			{

				$array_remake .= 'array(';

				for($i=0;$i<count($k);++$i)
					if(is_array($k[$i]))
					{

						$array_remake .= 'array(';

						for($z=0;$z<count($k[$i]);++$z)
							$array_remake .= '\''.$k[$i][$z].'\''.($z==count($k[$i]) - 1 ? ')' : ', ');

						$array_remake .= ($i == count($k) - 1 ? ')' : ', ');

					}						
					else
						$array_remake .= '\''.$k[$i].'\''.($i==count($k) - 1 ? ')' : ', ');

				$array_remake .= ($j == count($arr) - 1 ? ')' : ', ');

				++$j;

			}

		}

		return $array_remake;

	}

	private static function save_configs()
	{

		$fs = @fopen(sep_path(CMS_DIR.'/configurations/'.self::$config_file.'.config.php'), 'w+');

		if(trim(self::$configs_list[0]) != '<?php')
			fwrite($fs, '<?php'."\n");

		$lines = count(self::$configs_list);

		for($i = 0;$i < $lines - 1;++$i)
			if(self::$configs_list[$i] != '' || @self::$configs_list[$i - 1] != '')
				fwrite($fs, strtr(self::$configs_list[$i], "\r", ''));

		fwrite($fs, self::$configs_list[$i]);

		@fclose($fs);

	}

	private static function load_configs()
	{

		self::$configs_list = @file(sep_path(CMS_DIR.'/configurations/'.self::$config_file.'.config.php'));

		return true;

	}

}