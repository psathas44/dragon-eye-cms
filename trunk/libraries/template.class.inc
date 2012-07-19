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

class Template
{

	private static $vars = array();

	private static $page = null;

	private static $page_contents = null;

	public static function load($page, $vars, $default)
	{

		self::$page = $page;

		if(!self::template_file_exists())
		{

			if($default)
			{

				self::$page = $default;

				if(!self::template_file_exists())
					throw new Dragon_Eye_Exception('Can\'t find '.self::$page.' template file!');
				else
				{

					$fh = @fopen(sep_path(CMS_DIR.'/templates/'.self::used_template().'/'.$page), 'w+');

					if($fh)
					{

						fwrite($fh, file_get_contents(sep_path(CMS_DIR.'/templates/'.self::used_template().'/'.$default)));

						fclose($fh);

					}

				}

			}
			else
				throw new Dragon_Eye_Exception('Can\'t find '.self::$page.' template file!');

		}

		self::$page_contents = self::get_content();
		self::$vars = $vars;

		self::replace_vars(self::$page_contents);

		return self::$page_contents;

	}

	public static function used_template()
	{

		if(@file_exists(sep_path(CMS_DIR.'/templates/'.$GLOBALS['CONFIG_TEMPLATE_SELECTED'].'/'.self::$page)))
			return $GLOBALS['CONFIG_TEMPLATE_SELECTED'];
		elseif(@file_exists(sep_path(CMS_DIR.'/templates/'.$GLOBALS['CONFIG_TEMPLATE_DEFAULT'].'/'.self::$page)))
			return $GLOBALS['CONFIG_TEMPLATE_DEFAULT'];

	}

	private static function template_file_exists()
	{

		if(@file_exists(sep_path(CMS_DIR.'/templates/'.self::used_template().'/'.self::$page)))
			return true;
		elseif(@file_exists(sep_path(CMS_DIR.'/templates/'.$GLOBALS['CONFIG_TEMPLATE_DEFAULT'].'/'.self::$page)))
			return true;

	}

	private static function get_content()
	{

		return @file_get_contents(sep_path(CMS_DIR.'/templates/'.self::used_template().'/'.self::$page));

	}

	private static function get_global_var($var)
	{

		$new_var = null;

		preg_match('/[a-zA-Z0-9_]{1,}/', $var, $new_var);

		if(isset($GLOBALS[strtoupper($new_var[0])]))
			return $GLOBALS[strtoupper($new_var[0])];
		else
			return $var;

	}

	private static function get_image_var($var)
	{

		$new_var = array();

		preg_match('/[a-zA-Z0-9_]{1,}[.](gif|jpg|png|bmp)/', $var, $new_var);

		if(isset($GLOBALS['CONFIG_TEMPLATE_IMAGES']))
			return $GLOBALS['CONFIG_WEBSITE_URL'].'/templates/'.self::used_template().'/'.$GLOBALS['CONFIG_TEMPLATE_IMAGES'].'/'.$new_var[0];
		else
			return $var;

	}

	private static function get_active_page($str)
	{

		$new_str = array();

		preg_match('/[a-zA-Z0-9_-]{1,}/', $str, $new_str);

		$new_str = explode('-', $new_str[0]);

		if(THE_USED_PAGE == $new_str[1])
			return $new_str[0];

	}

	private static function replace_vars(&$page_content)
	{

		foreach (self::$vars as $rep_str => $val)
			// Replace template variables ( {var_name} ) with $template_vars['var_name']
			$page_content = str_replace('{'.$rep_str.'}', (string) $val, $page_content);

		// Replace template variables ( [var_name] ) with $GLOBALS['var_name']
		$page_content = preg_replace('/[[][a-zA-Z0-9_]{1,}[]]/e', 'self::get_global_var("$0")', $page_content);

		// Replace template variables {var_name] with ../template/template_name/$GLOBALS['CONFIG_TEMPLATE_IMAGES']/var_name
		$page_content = preg_replace('/[{][a-zA-Z0-9_]{1,}[.](gif|jpg|png|bmp)[]]/e', 'self::get_image_var("$0")', $page_content);

		// Replace template variables [var_name} with the return of function self::get_active_page(var_name)
		$page_content = preg_replace('/[[][a-zA-Z0-9_-]{1,}[}]/e', 'self::get_active_page("$0")', $page_content);

		$conditions = explode('???', $page_content);

		if(count($conditions) > 1)
			foreach($conditions as $k)
			{

				if(substr($k, 0, 6) == 'logged')
					if(!isset($GLOBALS['template_logged']))
						$page_content = str_replace('???'.$k.'???', '', $page_content);
					else
						$page_content = str_replace('???'.$k.'???', substr($k, 6), $page_content);

				if(substr($k, 0, 5) == 'guest')
					if(isset($GLOBALS['template_logged']))
						$page_content = str_replace('???'.$k.'???', '', $page_content);
					else
						$page_content = str_replace('???'.$k.'???', substr($k, 5), $page_content);

			}

	}

	public static function current_templates()
	{

		$templates_list = array();

		$folder = sep_path(CMS_DIR.'/templates/');

		$dir = opendir($folder);

		while(false !== ($template = readdir($dir)))
			if($template != '.' && $template != '..')
			{

				$template_loc = sep_path($folder.$template.'/');

				if(file_exists($template_loc.'index.php'))
					$templates_list[] = $template;

			}

			return $templates_list;

	}

}