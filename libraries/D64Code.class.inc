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

class D64Code
{

	private static $elems = array();

	private static $attrbs = null;
	
	private static $req_elems = null;

	private static $elem_attr = array(
	'a' => array(array('href', 'target'), 0),
	'img' => array(array('src', 'border', 'width', 'height', 'alt', 'title'), 0),
	'iframe' => array(array('src', 'frameborder', 'width', 'height', 'scrolling'), 0),
	'table' => array(array('border', 'cellspacing', 'cellpadding', 'width'), 0),
	);

	public static function parse($str)
	{

		self::get_elems();

		foreach(self::$elems as $k => $v)		
			$str = preg_replace($k, $v, $str);

		foreach(self::$elem_attr as $k => $v)
		{

			$z = count($v[0]);

			foreach($v[0] as $x)
			{

				$pattern = '/[@]'.$k.'-';

				for($i=1;$i<=$z;++$i)
					$pattern .= '[a-zA-Z0-9_=;.:\/?&]{1,}-';

				$pattern .= '[:]/ie';

				$str = preg_replace($pattern, 'self::eval_part("$0")', $str);

				--$z;

			}

		}

		return $str;

	}

	public static function decode($str)
	{

		self::get_elems();

		foreach(self::$elems as $k => $v)
		{

			$k = str_replace(array('/[', ']', '[', '/i'), '', $k);

			$str = str_ireplace($v, $k, $str);

		}		

		foreach(self::$elem_attr as $k => $v)
		{

			$z = count($v[0]);
			$y = $z;

			$rz = array();

			for($i=1;$i<=$z;$i++)
			{

				$attrs = str_repeat(' ([a-zA-Z0-9_.:\/?]{1,})[=]["][a-zA-Z0-9_=;.:\/?&]{1,}["]', $i);

				preg_match_all('/'.$k.$attrs.'[>]/', $str, $rz[$i-1]);	// support for '/'

			}

			for($i=0;$i<count($rz);++$i)
				if(isset($rz[$i][0][0]))
				{

					for($l=0;$l<count($rz[$i][0]);++$l)
					{

						$cpat = '/[<]'.$k;

						for($j=1;$j<count($rz[$i]);++$j)
							if(in_array($rz[$i][$j][$l], $v[0]))
								$cpat .= ' '.$rz[$i][$j][$l].'[=]["][a-zA-Z0-9_=;.:\/?&]{1,}["]';

						$cpat .= '[>]/ie';

						$str = preg_replace($cpat, 'self::show_them("$0", "$k")', $str);

					}

				}

		}

		return $str;

	}

	private static function show_them($attrs, $stag)
	{

		$sa = array('@', '<'.$stag.' ', '>', '" ', '"');

		preg_match('/([a-zA-Z0-9_.:\/?]{1,})[=]["][a-zA-Z0-9_=;.:\/?&]{1,}["][>]/i', $attrs, $ltst);	// support for '/'

		$attrs_rec = '<'.$stag;

		foreach(self::$elem_attr[$stag][0] as $k)
		{

			$r = array();

			if(preg_match('/'.$k.'[=]["][a-zA-Z0-9_=;.:\/?&]{1,}["]/i', $attrs, $r))
				$attrs_rec .= ' '.$r[0];
			else
				$attrs_rec .= ' '.$k.'="@"';

			$sa[] = $k.'=';

			if($k == $ltst[1])
				break;

		}

		$attrs_rec .= '>';

		$attrs = str_replace($sa, array('.', '@'.$stag.'-', '-:', '-', ''), $attrs_rec);

		return $attrs;

	}

	private static function val_attr($str)
	{

		if(isset($str) && $str != ':' && $str != '.')
			return true;

	}

	private static function add_atributes($attrb_list)
	{

		foreach($attrb_list as $k => $v)
			if(self::val_attr(self::$req_elems[$k + 1]))
				self::$attrbs .= ' '.$v.'="'.self::$req_elems[$k + 1].'"';

	}

	private static function show_elem($tag)
	{

		self::add_atributes(self::$elem_attr[$tag][0]);

		return '<'.$tag.''.self::$attrbs.''.(self::$elem_attr[$tag][1] ? ' /' : '').'>';

	}

	private static function eval_part($q)
	{

		self::$req_elems = explode('-', $q);

		self::$attrbs = '';

		if(strcasecmp(self::$req_elems[0], '@a') == 0)
			return self::show_elem('a');
		elseif(strcasecmp(self::$req_elems[0], '@img') == 0)
			return self::show_elem('img');
		elseif(strcasecmp(self::$req_elems[0], '@iframe') == 0)
			return self::show_elem('iframe');
		elseif(strcasecmp(self::$req_elems[0], '@table') == 0)
			return self::show_elem('table');

	}

	private static function get_elems()
	{

		$basic_elems = array('b', 'u', 'i', 's', 'sup', 'sub', 'table', 'tr', 'td', 'list', 'li', 'a', 'iframe');
		$single_elems = array('br', 'hr');

		foreach($basic_elems as $v)
		{

			self::$elems['/[@]'.$v.'[:]/i'] = '<'.$v.'>';
			self::$elems['/[:]'.$v.'[@]/i'] = '</'.$v.'>';

		}

		foreach($single_elems as $v)
			self::$elems['/[@]'.$v.'[:]/i'] = '<'.$v.' />';

	}

}