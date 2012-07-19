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

class CData
{

	public $current_page = 1;

	public $total_pages;

	public $first_page = null;

	public $prev_page = null;

	public $next_page = null;

	public $last_page = null;

	public $the_page;

	private $the_parser;

	private $offset;

	private $total_results;

	private $data = array();

	private $data2 = array();

	public $results_pp = null;

	public function __construct($the_page)
	{

		$this->the_page = strtolower($the_page);

		$this->file_data = sep_path(CMS_DIR.'/sources/cdata/'.$this->the_page.'.xml');

		$this->load();

		$this->results_pp = $GLOBALS['CONFIG_'.strtoupper($this->the_page).'_RES_PER_PAGE'];

		$this->total_pages = ceil($this->total_results / $this->results_pp);

		if(isset($_GET['res']) && ctype_digit($_GET['res']))
			$this->current_page = ($_GET['res'] >= 1 && $_GET['res'] <= $this->total_pages) ? intval($_GET['res']) : '1';
		else
			$this->current_page = 1;

		if($this->current_page != $this->total_pages)
			$this->last_page = $this->total_pages;

		if($this->current_page != 1)
		{

			if($this->current_page >= 3)
				$this->prev_page = $this->current_page - 1;

			$this->first_page = 1;

		}

		$this->next_page = $this->current_page + 1;

		if($this->next_page > $this->total_pages)
			$this->next_page = null;

		$this->offset = $this->results_pp * $this->current_page;

	}

	public function load()
	{

		if(!file_exists($this->file_data))
		{

			if(!($fh = fopen($this->file_data, 'w')))
				throw new Dragon_Eye_Exception('Can\'t create content file');

			fclose($fh);

		}

		if (!($fh = fopen($this->file_data, "r")))
			throw new Dragon_Eye_Exception('Can\'t load content data!');

		$data = fread($fh, filesize($this->file_data));

		$this->the_parser = xml_parser_create('UTF-8');

		xml_set_object($this->the_parser, $this);
		xml_parser_set_option($this->the_parser, XML_OPTION_SKIP_WHITE, 1);
		xml_set_element_handler($this->the_parser, 'tag_open', 'tag_close');
		xml_set_character_data_handler($this->the_parser, 'cdata');

		$this->data = xml_parse($this->the_parser, $data) ? $this->data['child'] : array();

		xml_parser_free($this->the_parser);

		$this->total_results = count($this->data['CONTENTS'][0]['child']['CONTENT']);

		fclose($fh);

	}

	private function tag_open($the_parser, $tag, $attr)
	{

		$this->data['child'][$tag][] = array('data' => '', 'child' => array());
		$this->data2[] = &$this->data;
		$this->data = &$this->data['child'][$tag][count($this->data['child'][$tag])-1];

	}

	private function tag_close($parser, $tag)
	{

		$this->data = &$this->data2[count($this->data2)-1];

		array_pop($this->data2);

	}

	private function cdata($the_parser, $cdata)
	{

		$this->data['data'] .= $cdata;

	}

	public function used_vars()
	{

		$cdata_vars = array();

		$prev_get = 'page='.$_GET['page'];

		if(isset($_GET['content']))
			$prev_get .= '&content';

		if(isset($_GET['cpage']))
			$prev_get .= '&cpage='.$_GET['cpage'];

		$cdata_vars['cdata_list'] = $this->show_content();
		$cdata_vars['cdata_currentp'] = $this->current_page;
		$cdata_vars['cdata_totalp'] = $this->total_pages;
		$cdata_vars['cdata_firstp'] = $this->first_page ? sprintf($GLOBALS['LANG_FIRSTP'], $prev_get, $this->first_page) : '';
		$cdata_vars['cdata_prevp'] = $this->prev_page ? sprintf($GLOBALS['LANG_PREVP'], $prev_get, $this->prev_page) : '';
		$cdata_vars['cdata_nextp'] = $this->next_page ? sprintf($GLOBALS['LANG_NEXTP'], $prev_get, $this->next_page) : '';
		$cdata_vars['cdata_lastp'] = $this->last_page ? sprintf($GLOBALS['LANG_LASTP'], $prev_get, $this->last_page) : '';

		return $cdata_vars;

	}

	public function edit_content($id, $title, $content)
	{

		$this->data['CONTENTS'][0]['child']['CONTENT'][$id]['child']['TITLE'][0]['data'] = $title;
		$this->data['CONTENTS'][0]['child']['CONTENT'][$id]['child']['MESSAGE'][0]['data'] = $content;

		$this->save_content();

		return true;

	}

	public function del_content($id)
	{

		// Delete the content
		
		$count = count($this->data['CONTENTS'][0]['child']['CONTENT']);

		for($i=$id;$i<$count;$i++)
			$this->data['CONTENTS'][0]['child']['CONTENT'][$i] = $this->data['CONTENTS'][0]['child']['CONTENT'][$i+1];

		unset($this->data['CONTENTS'][0]['child']['CONTENT'][$count - 1]);

		$this->save_content();

		return true;

	}

	public function archive_content($date, $title, $author, $content)
	{

		$archive = new CData('archives/'.$this->the_page);

		for($i = ($archive->total_results - 1);$i >= 0;--$i)
		{

			if($i == ($GLOBALS['CONFIG_'.strtoupper($this->the_page).'_TOTAL_ARCHIVED'] - 1))
				unset($archive->data['CONTENTS'][0]['child']['CONTENT'][$i]);
			else
				$archive->data['CONTENTS'][0]['child']['CONTENT'][$i + 1] = $archive->data['CONTENTS'][0]['child']['CONTENT'][$i];

		}

		$archive->data['CONTENTS'][0]['child']['CONTENT'][0]['child']['DATE'][0]['data'] = $date;
		$archive->data['CONTENTS'][0]['child']['CONTENT'][0]['child']['TITLE'][0]['data'] = $title;
		$archive->data['CONTENTS'][0]['child']['CONTENT'][0]['child']['AUTHOR'][0]['data'] = $author;
		$archive->data['CONTENTS'][0]['child']['CONTENT'][0]['child']['MESSAGE'][0]['data'] = $content;

		$archive->save_content();

	}

	public function add_content($title, $author, $content)
	{

		for($i = ($this->total_results - 1);$i >= 0;--$i)
		{

			if($i == ($GLOBALS['CONFIG_'.strtoupper($this->the_page).'_TOTAL_RESULTS'] - 1))
			{

				$this->archive_content($this->data['CONTENTS'][0]['child']['CONTENT'][$i]['child']['DATE'][0]['data'], $this->data['CONTENTS'][0]['child']['CONTENT'][$i]['child']['TITLE'][0]['data'], $this->data['CONTENTS'][0]['child']['CONTENT'][$i]['child']['AUTHOR'][0]['data'], $this->data['CONTENTS'][0]['child']['CONTENT'][$i]['child']['MESSAGE'][0]['data']);

				unset($this->data['CONTENTS'][0]['child']['CONTENT'][$i]);

			}
			else
				$this->data['CONTENTS'][0]['child']['CONTENT'][$i + 1] = $this->data['CONTENTS'][0]['child']['CONTENT'][$i];

		}

		$this->data['CONTENTS'][0]['child']['CONTENT'][0]['child']['DATE'][0]['data'] = date('d/m/Y');
		$this->data['CONTENTS'][0]['child']['CONTENT'][0]['child']['TITLE'][0]['data'] = $title;
		$this->data['CONTENTS'][0]['child']['CONTENT'][0]['child']['AUTHOR'][0]['data'] = $author;
		$this->data['CONTENTS'][0]['child']['CONTENT'][0]['child']['MESSAGE'][0]['data'] = $content;

		$this->save_content();

	}

	private function save_content()
	{

		$str = '<?xml version="1.0" encoding="UTF-8"?>
<contents>';

		foreach($this->data['CONTENTS'][0]['child']['CONTENT'] as $key)
		{

			$the_author = $key['child']['AUTHOR'][0]['data'];
			$the_title = htmlspecialchars($key['child']['TITLE'][0]['data']);
			$the_date = $key['child']['DATE'][0]['data'];
			$the_content = trim(htmlspecialchars($key['child']['MESSAGE'][0]['data']));

			$str .= '
	<content>
		<author>'.$the_author.'</author>
		<title>'.$the_title.'</title>
		<date>'.$the_date.'</date>
		<message>'.$the_content.'</message>
	</content>';

		}

		$str .= '
</contents>';

		if (!($fh = fopen($this->file_data, "w")))
			throw new Dragon_Eye_Exception('Can\'t write content data!');

		fwrite($fh, $str);

		fclose($fh);

	}

	public function used_content()
	{

		$used_content = array();

		$i = 1;

		foreach($this->data['CONTENTS'][0]['child']['CONTENT'] as $val => $key)
		{

			if($i > $this->offset)
				break;

			if($i > ($this->offset - $this->results_pp))
			{

				$used_content[$i]['cdata_id'] = $val;
				$used_content[$i]['cdata_date'] = $key['child']['DATE'][0]['data'];
				$used_content[$i]['cdata_subject'] = $key['child']['TITLE'][0]['data'];
				$used_content[$i]['cdata_author'] = $key['child']['AUTHOR'][0]['data'];
				$used_content[$i]['cdata_content'] = $key['child']['MESSAGE'][0]['data'];

			}

			++$i;

		}

		return $used_content;

	}

	public static function convert_whitespaces($string)
	{

		return str_replace('  ', '&nbsp; &nbsp; ', $string);

	}

	public function show_content()
	{

		$cdata_vars = $this->used_content();
		$the_ret = '';

		if($this->total_results)
			foreach($cdata_vars as $used_content)
			{

				$used_content['cdata_content'] = nl2br(self::convert_whitespaces($used_content['cdata_content']));
				$the_ret .= Template::load('styles/'.$this->the_page.'_list.html', $used_content, 'styles/list_default.html');

			}
		else
		{

			$the_ret = $GLOBALS['LANG_NOCONTENTS'];
			$this->current_page = 0;

		}

		return $the_ret;

	}

	public static function content_pages()
	{

		$all = array();
		$cpages = array();

		preg_match_all('/CONFIG_[a-zA-Z0-9_]{1,}_ACCESS/', file_get_contents(sep_path(CMS_DIR.'/configurations/sources.config.php')), $resp);

		foreach($resp[0] as $cp)
		{

			$n = explode('_', $cp);

			if(isset($GLOBALS['CONFIG_'.$n[1].'_RES_PER_PAGE']) && isset($GLOBALS['CONFIG_'.$n[1].'_TOTAL_RESULTS']) && isset($GLOBALS['CONFIG_'.$n[1].'_TOTAL_ARCHIVED']))
				$cpages[] = $n[1];

		}

		return $cpages;

	}

}