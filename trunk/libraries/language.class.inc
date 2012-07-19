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

class Language
{

	private $page = null;

	private $langs = array();

	private $lang_selected;

	public function __construct()
	{

		$this->get_langs();

		if(isset($_GET['lang']))
			$this->select_lang();

		$this->selected_language();

		$this->load();

	}

	public function load()
	{

		if(!$this->language_file_exists())
			throw new Dragon_Eye_Exception('Can\'t load default language file!');

		@require_once(sep_path(CMS_DIR.'/templates/'.$this->used_template().'/languages/'.$this->page.'.language.php'));

	}

	private function selected_language()
	{

		if($this->lang_selected)
			$this->lang_selected = false;
		elseif(isset($_COOKIE['de_selected_language']))
		{

			$this->page = $_COOKIE['de_selected_language'];

			if(!$this->language_file_exists())
				$this->page = $GLOBALS['CONFIG_TEMPLATE_DEFAULT_LANG'];

		}
		else
			$this->page = $GLOBALS['CONFIG_TEMPLATE_DEFAULT_LANG'];

	}

	private function used_template()
	{

		if(@file_exists(sep_path(CMS_DIR.'/templates/'.$GLOBALS['CONFIG_TEMPLATE_SELECTED'].'/languages/index.php')))
			return $GLOBALS['CONFIG_TEMPLATE_SELECTED'];
		elseif(@file_exists(sep_path(CMS_DIR.'/templates/'.$GLOBALS['CONFIG_TEMPLATE_DEFAULT'].'/languages/index.php')))
			return $GLOBALS['CONFIG_TEMPLATE_DEFAULT'];

	}

	private function language_file_exists()
	{

		if(@file_exists(sep_path(CMS_DIR.'/templates/'.$this->used_template().'/languages/'.$this->page.'.language.php')))
			return true;
		elseif(@file_exists(sep_path(CMS_DIR.'/templates/'.$GLOBALS['CONFIG_TEMPLATE_DEFAULT'].'/languages/'.$this->page.'.language.php')))
			return true;

	}

	private function get_langs()
	{

		$dir = sep_path(CMS_DIR.'/templates/'.$this->used_template().'/languages/');

		$this->selected_language();

		$langs_array = array();

		foreach(glob($dir.'*.language.php') as $langname)
			$langs_array[] = strtoupper(basename($langname, '.language.php'));

		$this->langs = $langs_array;

	}

	public function select_lang()
	{

		$lang = null;

		if(isset($_GET['lang']) && ctype_digit($_GET['lang']))
		{

			$lang = $_GET['lang'];

			setcookie('de_selected_language', strtolower(@$this->langs[$lang]), time() + 3600 * 24 * 30);

			$this->page = @strtolower($this->langs[$lang]);

			$this->lang_selected = true;

		}

	}

	public function show_langs()
	{

		$vals = array();

		$vals['lang_id'] = 'selected';
		$vals['lang_name'] = strtoupper($this->page);

		$langs_list = Template::load('styles/languages_list.html', $vals, null);

		foreach($this->langs as $vals['lang_id'] => $vals['lang_name'])
		{

			if($this->page != strtolower($vals['lang_name']))
				$langs_list .= Template::load('styles/languages_list.html', $vals, null);

		}

		return $langs_list;

	}

}