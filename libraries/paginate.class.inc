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

class Paginate
{

	private $the_query;

	private $query_results;

	private $order_by;

	private $order_type;

	private $db_link;

	private $total_results;

	private $cache_file;

	private $cache_time;

	public $results_per_page;

	public $final_results_per_page;

	public $offset;

	public $total_pages;

	public $current_page = 1;

	public $first_page = null;

	public $prev_page = null;

	public $next_page = null;

	public $last_page = null;

	public $final_results = array();

	public function __construct($query, $order_by, $order_type, $link, $totalr, $rpp, $cache, $cache_name)
	{

		$this->db_link = $link;

		$this->the_query = $query;

		$this->cache_file = sep_path(CMS_DIR.'/cache/'.$cache_name.'.txt');

		$this->cache_time = $cache;

		if($this->cache_time && file_exists($this->cache_file) && time() - filemtime($this->cache_file) < $this->cache_time)
		{

			$cache_results = unserialize(file_get_contents($this->cache_file));

			$cache_tresults = array_keys($cache_results);

			$this->query_results = $cache_tresults[0];

		}
		else
			$this->query_results = Main::db_rows(Main::db_query($this->the_query, $this->db_link));

		$this->order_by = $order_by;

		$this->order_type = $order_type;

		$this->total_results = $totalr;

		$this->results_per_page = $rpp;

		$this->total_pages();

		$this->current_page();

		$this->last_page();

		$this->prev_page();

		$this->next_page();

		$this->first_page();

		$this->final_results_per_page = $this->results_per_page - (($this->current_page * $this->results_per_page > $this->total_results ? $this->total_pages * $this->results_per_page - $this->total_results : 0));

		$this->offset = $this->results_per_page * $this->current_page - $this->results_per_page;

	}

	public function load()
	{

		$the_results = array();

		if($this->cache_time && file_exists($this->cache_file) && time() - filemtime($this->cache_file) < $this->cache_time)
			$the_results = unserialize(file_get_contents($this->cache_file));
		else
		{

			$query = Main::db_query($this->the_query.' ORDER BY '.$this->order_by.' '.($this->order_type ? 'DESC' : 'ASC').';', $this->db_link);

			$i = 0;

			while($arr_res=Main::db_fetch_row($query))
			{

				if($i < $this->total_results)
				{

					$the_results[$this->query_results][] = $arr_res;

					++$i;

				}

			}

			if($this->cache_time)
				file_put_contents($this->cache_file, serialize($the_results));

		}

		$i = $j = 0;

		foreach($the_results[$this->query_results] as $x)
		{

			if($i >= $this->offset && $j < $this->final_results_per_page)
			{

				$this->final_results[] = $x;

				++$j;

			}

			++$i;

		}

	}

	public function results()
	{

		return $this->final_results;

	}

	private function total_pages()
	{

		$this->total_pages = ceil(($this->query_results > $this->total_results ? $this->total_results : $this->query_results) / $this->results_per_page);

	}

	private function current_page()
	{

		if(isset($_GET['res']) && ctype_digit($_GET['res']))
			$this->current_page = ($_GET['res'] >= 1 && $_GET['res'] <= $this->total_pages) ? intval($_GET['res']) : '1';
		else
			$this->current_page = 1;

	}

	private function last_page()
	{

		if($this->current_page != $this->total_pages)
			$this->last_page = $this->total_pages;

	}

	private function prev_page()
	{

		if($this->current_page >= 3)
			$this->prev_page = $this->current_page - 1;

	}

	private function next_page()
	{

		$this->next_page = $this->current_page + 1;

		if($this->next_page > $this->total_pages)
			$this->next_page = null;

	}

	private function first_page()
	{

		if($this->current_page != 1)
			$this->first_page = 1;

	}

}