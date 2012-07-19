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

// Check if this page it's accessed in the right way
if (!defined('DragonEye')) die('Sorry, but you are not allowed to access this page from this location!');

$template_location[] = 'header.html';
$template_location[] = 'statistics.html';
$template_location[] = 'footer.html';

$template_vars['page_title'] .= ' - Statistics';

$template_vars['stat_page'] = null;

if(isset($_GET['inventory']) && $GLOBALS['CONFIG_STATS_INVENTORY_ENABLED'])
{

	if(!$GLOBALS['CONFIG_STATS_INVENTORY_GUESTS'] && !$this->logged)
		$GLOBALS['the_status'] = $GLOBALS['LANG_PAGE_LOGGED'];
	else
	{

		$search_page_vars['get_stat'] = 'inventory';

		$search_page_vars['val_player'] = null;
		$search_page_vars['the_stat'] = null;

		if(isset($_GET['player_name']) && ctype_alnum(trim($_GET['player_name'])))
		{

			$player_name = htmlspecialchars(trim($_GET['player_name']));

			$search_page_vars['val_player'] = $player_name;

			if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
				$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_STATS_ACCESS'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_NAME'], Main::db_escape_string($player_name, $GLOBALS['DB_GAME_SERVER']), $GLOBALS['DBSTRUCT_L2OFF_USERDAT_VIEWSET'], '1', '2', '3'), $GLOBALS['DB_GAME_SERVER']);
			else
				$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_STATS_ACCESS'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_NAME'], Main::db_escape_string($player_name, $GLOBALS['DB_GAME_SERVER']), $GLOBALS['DBSTRUCT_L2J_CHARS_VIEWSET'], '1', '2', '3'), $GLOBALS['DB_GAME_SERVER']);

			if(Main::db_rows($query) == 1 || $this->access_level == 5 || ($this->logged && strcasecmp(Main::db_result(Main::db_query(($GLOBALS['CONFIG_SERVER_TYPE'] == 1 ? sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_ACC'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_NAME'], Main::db_escape_string($player_name, $GLOBALS['DB_GAME_SERVER'])) : sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2J_CHARS_ACC'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_NAME'], Main::db_escape_string($player_name, $GLOBALS['DB_GAME_SERVER']))), $GLOBALS['DB_GAME_SERVER']), 0), $acc->account_username) == 0))
			{

				$stat_page_vars = array();

				@require_once(sep_path(CMS_DIR.'/sources/items_names.php'));

				$inv_items_vars = array();

				$cache_file = sep_path(CMS_DIR.'/cache/inventory_'.$player_name.'.txt');

				$inventory_items[0] = array();

				if($GLOBALS['CONFIG_STATS_INVENTORY_CACHE'] && file_exists($cache_file) && time() - filemtime($cache_file) < $GLOBALS['CONFIG_STATS_INVENTORY_CACHE'])
					$inventory_items = unserialize(file_get_contents($cache_file));
				else
				{

					if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
						$char_id = Main::db_result(Main::db_query(sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_NAME'], Main::db_escape_string($player_name, $GLOBALS['DB_GAGME_SERVER'])), $GLOBALS['DB_GAME_SERVER']), 0);
					else
						$char_id = Main::db_result(Main::db_query(sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2J_CHARS_ID'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_NAME'], Main::db_escape_string($player_name, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']), 0);

					if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
					{

						$stat_page_vars['inventory_items'] = '';

						$paperdoll_list = '';

						$query = Main::db_query(sprintf($GLOBALS['DBQUERY_L2OFF_PAPERDOLL'],
							$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_CHEST'],
							$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_LEGS'],
							$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_RHAND'],
							$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_LHAND'],
							$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_RING2'],
							$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_RING1'],
							$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_EARR2'],
							$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_EARR1'],
							$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_NECK'],
							$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_GLOVES'],
							$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_FEET'],
							$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_HEAD'],
							$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_HAIR'],
							$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_UNDERW'],
							$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_BACK'],
							$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_BHAND'],
							$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_HDECO'],
							$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_HALL'],
							sprintf($GLOBALS['DBQUERY_L2OFF_PAPERDOLL_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TYPE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_CID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_ID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_CHEST']),
							sprintf($GLOBALS['DBQUERY_L2OFF_PAPERDOLL_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TYPE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_CID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_ID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_LEGS']),
							sprintf($GLOBALS['DBQUERY_L2OFF_PAPERDOLL_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TYPE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_CID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_ID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_RHAND']),
							sprintf($GLOBALS['DBQUERY_L2OFF_PAPERDOLL_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TYPE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_CID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_ID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_LHAND']),
							sprintf($GLOBALS['DBQUERY_L2OFF_PAPERDOLL_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TYPE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_CID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_ID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_RING2']),
							sprintf($GLOBALS['DBQUERY_L2OFF_PAPERDOLL_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TYPE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_CID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_ID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_RING1']),
							sprintf($GLOBALS['DBQUERY_L2OFF_PAPERDOLL_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TYPE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_CID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_ID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_EARR2']),
							sprintf($GLOBALS['DBQUERY_L2OFF_PAPERDOLL_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TYPE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_CID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_ID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_EARR1']),
							sprintf($GLOBALS['DBQUERY_L2OFF_PAPERDOLL_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TYPE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_CID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_ID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_NECK']),
							sprintf($GLOBALS['DBQUERY_L2OFF_PAPERDOLL_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TYPE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_CID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_ID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_GLOVES']),
							sprintf($GLOBALS['DBQUERY_L2OFF_PAPERDOLL_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TYPE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_CID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_ID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_FEET']),
							sprintf($GLOBALS['DBQUERY_L2OFF_PAPERDOLL_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TYPE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_CID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_ID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_HEAD']),
							sprintf($GLOBALS['DBQUERY_L2OFF_PAPERDOLL_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TYPE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_CID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_ID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_HAIR']),
							sprintf($GLOBALS['DBQUERY_L2OFF_PAPERDOLL_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TYPE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_CID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_ID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_UNDERW']),
							sprintf($GLOBALS['DBQUERY_L2OFF_PAPERDOLL_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TYPE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_CID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_ID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_BACK']),
							sprintf($GLOBALS['DBQUERY_L2OFF_PAPERDOLL_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TYPE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_CID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_ID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_BHAND']),
							sprintf($GLOBALS['DBQUERY_L2OFF_PAPERDOLL_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TYPE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_CID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_ID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_HDECO']),
							sprintf($GLOBALS['DBQUERY_L2OFF_PAPERDOLL_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TYPE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_CID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_ID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ST_HALL']),
							$GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'],
							$GLOBALS['DBSTRUCT_L2OFF_USERDAT_NAME'],
							Main::db_escape_string($player_name, $GLOBALS['DB_GAME_SERVER'])
							), $GLOBALS['DB_GAME_SERVER']);

						$paperdoll_list .= '\''.Main::db_result($query, 0).'\',
						 \''.Main::db_result($query, 1).'\',
						 \''.Main::db_result($query, 2).'\',
						 \''.Main::db_result($query, 3).'\',
						 \''.Main::db_result($query, 4).'\',
						 \''.Main::db_result($query, 5).'\',
						 \''.Main::db_result($query, 6).'\',
						 \''.Main::db_result($query, 7).'\',
						 \''.Main::db_result($query, 8).'\',
						 \''.Main::db_result($query, 9).'\',
						 \''.Main::db_result($query, 10).'\',
						 \''.Main::db_result($query, 11).'\',
						 \''.Main::db_result($query, 12).'\',
						 \''.Main::db_result($query, 13).'\',
						 \''.Main::db_result($query, 14).'\',
						 \''.Main::db_result($query, 15).'\',
						 \''.Main::db_result($query, 16).'\',
						 \''.Main::db_result($query, 17).'\''
						;

						array_push($inventory_items[0],
							Main::db_result($query, 18), Main::db_result($query, 19) == Main::db_result($query, 18) ? Main::db_result($query, 19).'_2' : Main::db_result($query, 19),
							Main::db_result($query, 20), Main::db_result($query, 21),
							Main::db_result($query, 22), Main::db_result($query, 23),
							Main::db_result($query, 24), Main::db_result($query, 25),
							Main::db_result($query, 26), Main::db_result($query, 27),
							Main::db_result($query, 28), Main::db_result($query, 29),
							Main::db_result($query, 30), Main::db_result($query, 31),
							Main::db_result($query, 32), Main::db_result($query, 33),
							Main::db_result($query, 34), Main::db_result($query, 35)
						);

						$items = Main::db_query(sprintf($GLOBALS['DBQUERY_L2OFF_INVENTORY_DATA'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_ID'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_COUNT'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_ENCHANT'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_WAREHOUSE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TYPE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_ITEMS_CID'], Main::db_escape_string($char_id, $GLOBALS['DB_GAME_SERVER']), $GLOBALS['DBSTRUCT_L2OFF_ITEMS_ID'], $paperdoll_list), $GLOBALS['DB_GAME_SERVER']);

						while($row=Main::db_fetch_row($items))
							if(!$row[3] || ($row[3] && $GLOBALS['CONFIG_STATS_INVENTORY_WAREHOUSE']))
								$inventory_items[1][] = array($row[4], $row[1], $row[2], $row[3]);

					}
					else
					{

						$items = Main::db_query(sprintf($GLOBALS['DBQUERY_INVENTORY_DATA'], $GLOBALS['DBSTRUCT_L2J_ITEMS_ID'], $GLOBALS['DBSTRUCT_L2J_ITEMS_COUNT'], $GLOBALS['DBSTRUCT_L2J_ITEMS_ENCHANT'], $GLOBALS['DBSTRUCT_L2J_ITEMS_LOC'], $GLOBALS['DBSTRUCT_L2J_ITEMS_TYPE'], $GLOBALS['DBSTRUCT_L2J_ITEMS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ITEMS_CID'], Main::db_escape_string($char_id, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);

						while($row=Main::db_fetch_row($items))
						{

							if($row[3] == 'PAPERDOLL')
							{

								if($row[4] == '1')
									$inventory_items[0][6] = $row[0];
								elseif($row[4] == '2')
									$inventory_items[0][7] = $row[0];
								elseif($row[4] == '3')
									$inventory_items[0][8] = $row[0];
								elseif($row[4] == '4')
									$inventory_items[0][4] = $row[0];
								elseif($row[4] == '5')
									$inventory_items[0][5] = $row[0];
								elseif($row[4] == '6')
									$inventory_items[0][11] = $row[0];
								elseif($row[4] == '7' || $row[4] == '14')
									$inventory_items[0][2] = $row[0];
								elseif($row[4] == '8')
									$inventory_items[0][3] = $row[0];
								elseif($row[4] == '9')
									$inventory_items[0][9] = $row[0];
								elseif($row[4] == '10')
								{

									$inventory_items[0][0] = $row[0];

									if(!$have_gaiters && (@fopen($GLOBALS['CONFIG_STATS_INVENTORY_IMAGES'].$row[0].'_2.'.$GLOBALS['CONFIG_STATS_INVENTORY_IMAGES_EXT'], 'r') == true))
									{

										$inventory_items[0][1] = $row[0].'_2';

										$have_gaiters = true;

									}

								}
								elseif(!$have_gaiters && $row[4] == '11')
								{

									$inventory_items[0][1] = $row[0];

									$have_gaiters = true;

								}
								elseif($row[4] == '12')
									$inventory_items[0][10] = $row[0];
								elseif($row[4] == '16')
									$inventory_items[0][12] = $row[0];
								elseif($row[4] == '17')
									$inventory_items[0][13] = $row[0];
								elseif($row[4] == '15')
									$inventory_items[0][14] = $row[0];

							}
							elseif($row[3] == 'INVENTORY' || ($row[3] == 'WAREHOUSE' && $GLOBALS['CONFIG_STATS_INVENTORY_WAREHOUSE']))
									$inventory_items[1][] = array($row[0], $row[1], $row[2], $row[3] == 'INVENTORY' ? false : true);

						}

					}

					if($GLOBALS['CONFIG_STATS_INVENTORY_CACHE'])
						file_put_contents($cache_file, serialize($inventory_items));

				}

				$stat_page_vars['armorm_name'] = isset($items_names[current($inventory_items[0])]) ? $items_names[current($inventory_items[0])] : current($inventory_items[0]);

				next($inventory_items[0]);

				$stat_page_vars['armorg_name'] = isset($items_names[current($inventory_items[0])]) ? $items_names[current($inventory_items[0])] : null;

				next($inventory_items[0]);

				$stat_page_vars['weapon_name'] = isset($items_names[current($inventory_items[0])]) ? $items_names[current($inventory_items[0])] : current($inventory_items[0]);

				next($inventory_items[0]);

				$stat_page_vars['shield_name'] = isset($items_names[current($inventory_items[0])]) ? $items_names[current($inventory_items[0])] : current($inventory_items[0]);

				next($inventory_items[0]);

				$stat_page_vars['ring2_name'] = isset($items_names[current($inventory_items[0])]) ? $items_names[current($inventory_items[0])] : current($inventory_items[0]);

				next($inventory_items[0]);

				$stat_page_vars['ring1_name'] = isset($items_names[current($inventory_items[0])]) ? $items_names[current($inventory_items[0])] : current($inventory_items[0]);

				next($inventory_items[0]);

				$stat_page_vars['earring2_name'] = isset($items_names[current($inventory_items[0])]) ? $items_names[current($inventory_items[0])] : current($inventory_items[0]);

				next($inventory_items[0]);

				$stat_page_vars['earring1_name'] = isset($items_names[current($inventory_items[0])]) ? $items_names[current($inventory_items[0])] : current($inventory_items[0]);

				next($inventory_items[0]);

				$stat_page_vars['necklace_name'] = isset($items_names[current($inventory_items[0])]) ? $items_names[current($inventory_items[0])] : current($inventory_items[0]);

				next($inventory_items[0]);

				$stat_page_vars['gloves_name'] = isset($items_names[current($inventory_items[0])]) ? $items_names[current($inventory_items[0])] : current($inventory_items[0]);

				next($inventory_items[0]);

				$stat_page_vars['boots_name'] = isset($items_names[current($inventory_items[0])]) ? $items_names[current($inventory_items[0])] : current($inventory_items[0]);

				next($inventory_items[0]);

				$stat_page_vars['helment_name'] = isset($items_names[current($inventory_items[0])]) ? $items_names[current($inventory_items[0])] : current($inventory_items[0]);

				next($inventory_items[0]);

				$stat_page_vars['acc1_name'] = isset($items_names[current($inventory_items[0])]) ? $items_names[current($inventory_items[0])] : current($inventory_items[0]);

				next($inventory_items[0]);

				$stat_page_vars['acc2_name'] = isset($items_names[current($inventory_items[0])]) ? $items_names[current($inventory_items[0])] : current($inventory_items[0]);

				next($inventory_items[0]);

				$stat_page_vars['acc3_name'] = isset($items_names[current($inventory_items[0])]) ? $items_names[current($inventory_items[0])] : current($inventory_items[0]);

				reset($inventory_items[0]);

				$stat_page_vars['armorm_id'] = current($inventory_items[0]) ? current($inventory_items[0]) : '1';

				next($inventory_items[0]);

				$stat_page_vars['armorg_id'] = current($inventory_items[0]) ? current($inventory_items[0]) : '1';

				next($inventory_items[0]);

				$stat_page_vars['weapon_id'] = current($inventory_items[0]) ? current($inventory_items[0]) : '1';

				next($inventory_items[0]);

				$stat_page_vars['shield_id'] = current($inventory_items[0]) ? current($inventory_items[0]) : '1';

				next($inventory_items[0]);

				$stat_page_vars['ring2_id'] = current($inventory_items[0]) ? current($inventory_items[0]) : '1';

				next($inventory_items[0]);

				$stat_page_vars['ring1_id'] = current($inventory_items[0]) ? current($inventory_items[0]) : '1';

				next($inventory_items[0]);

				$stat_page_vars['earring2_id'] = current($inventory_items[0]) ? current($inventory_items[0]) : '1';

				next($inventory_items[0]);

				$stat_page_vars['earring1_id'] = current($inventory_items[0]) ? current($inventory_items[0]) : '1';

				next($inventory_items[0]);

				$stat_page_vars['necklace_id'] = current($inventory_items[0]) ? current($inventory_items[0]) : '1';

				next($inventory_items[0]);

				$stat_page_vars['gloves_id'] = current($inventory_items[0]) ? current($inventory_items[0]) : '1';

				next($inventory_items[0]);

				$stat_page_vars['boots_id'] = current($inventory_items[0]) ? current($inventory_items[0]) : '1';

				next($inventory_items[0]);

				$stat_page_vars['helment_id'] = current($inventory_items[0]) ? current($inventory_items[0]) : '1';

				next($inventory_items[0]);

				$stat_page_vars['acc1_id'] = current($inventory_items[0]) ? current($inventory_items[0]) : '1';

				next($inventory_items[0]);

				$stat_page_vars['acc2_id'] = current($inventory_items[0]) ? current($inventory_items[0]) : '1';

				next($inventory_items[0]);

				$stat_page_vars['acc3_id'] = current($inventory_items[0]) ? current($inventory_items[0]) : '1';

				foreach($inventory_items[1] as $item)
				{

					$inv_items_vars['item_name'] = isset($items_names[$item[0]]) ? $items_names[$item[0]] : $item[0];

					$inv_items_vars['item_id'] = $item[0];

					$inv_items_vars['item_count'] = $item[1];

					$inv_items_vars['item_enchant'] = $item[2];

					$inv_items_vars['item_location'] = $item[3] ? 'Warehouse' : 'Inventory';

					$stat_page_vars['inventory_items'] .= Template::load('styles/inventory_items.html', $inv_items_vars, 0);

				}

				$search_page_vars['the_stat'] = Template::load('inventory.html', $stat_page_vars, 0);

			}
			else
				$GLOBALS['the_status'] = $GLOBALS['LANG_ERROR_STAT_CHAR'];

		}

		$template_vars['stat_page'] = Template::load('statistics_search.html', $search_page_vars, 0);

	}

}
elseif(isset($_GET['skills']) && $GLOBALS['CONFIG_STATS_SKILLS_ENABLED'])
{

	if(!$GLOBALS['CONFIG_STATS_SKILLS_GUESTS'] && !$this->logged)
		$GLOBALS['the_status'] = $GLOBALS['LANG_PAGE_LOGGED'];
	else
	{

		$search_page_vars['get_stat'] = 'skills';

		$search_page_vars['val_player'] = null;
		$search_page_vars['the_stat'] = null;

		if(isset($_GET['player_name']) && ctype_alnum(trim($_GET['player_name'])))
		{

			$player_name = htmlspecialchars(trim($_GET['player_name']));

			$search_page_vars['val_player'] = $player_name;

			if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
				$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_STATS_ACCESS'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_NAME'], Main::db_escape_string($player_name, $GLOBALS['DB_GAME_SERVER']), $GLOBALS['DBSTRUCT_L2OFF_USERDAT_VIEWSET'], '2', '4', '5'), $GLOBALS['DB_GAME_SERVER']);
			else
				$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CHECK_STATS_ACCESS'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_NAME'], Main::db_escape_string($player_name, $GLOBALS['DB_GAME_SERVER']), $GLOBALS['DBSTRUCT_L2J_CHARS_VIEWSET'], '2', '4', '5'), $GLOBALS['DB_GAME_SERVER']);

			if(Main::db_rows($query) == 1 || $this->access_level == 5 || ($this->logged && strcasecmp(Main::db_result(Main::db_query(($GLOBALS['CONFIG_SERVER_TYPE'] == 1 ? sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_ACC'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_NAME'], Main::db_escape_string($player_name, $GLOBALS['DB_GAME_SERVER'])) : sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2J_CHARS_ACC'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_NAME'], Main::db_escape_string($player_name, $GLOBALS['DB_GAME_SERVER']))), $GLOBALS['DB_GAME_SERVER']), 0), $acc->account_username) == 0))
			{

				@require_once(sep_path(CMS_DIR.'/sources/passive_skills.php'));

				$stat_page_vars = array();

				$skills_list = array();

				$cache_file = sep_path(CMS_DIR.'/cache/skills_'.$player_name.'.txt');

				if($GLOBALS['CONFIG_STATS_SKILLS_CACHE'] && file_exists($cache_file) && time() - filemtime($cache_file) < $GLOBALS['CONFIG_STATS_SKILLS_CACHE'])
					$skills_list = unserialize(file_get_contents($cache_file));
				else
				{

					if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
						$cinfo = Main::db_query(sprintf($GLOBALS['DBQUERY_2_1'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_SUBJOB'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_NAME'], Main::db_escape_string($player_name, $GLOBALS['DB_GAGME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);
					else
						$cinfo = Main::db_query(sprintf($GLOBALS['DBQUERY_2_1'], $GLOBALS['DBSTRUCT_L2J_CHARS_ID'], $GLOBALS['DBSTRUCT_L2J_CHARS_CLASS'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_NAME'], Main::db_escape_string($player_name, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);

					$char_id = Main::db_result($cinfo, 0);

					$subjob = Main::db_result($cinfo, 1);

					if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
						$skills = Main::db_query(sprintf($GLOBALS['DBQUERY_SKILLS_DATA'], $GLOBALS['DBSTRUCT_L2OFF_SKILLS_ID'], $GLOBALS['DBSTRUCT_L2OFF_SKILLS_LEVEL'], $GLOBALS['DBSTRUCT_L2OFF_SKILLS_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_SKILLS_CID'], Main::db_escape_string($char_id, $GLOBALS['DB_GAME_SERVER']), $GLOBALS['DBSTRUCT_L2OFF_SKILLS_SUBJOB'], Main::db_escape_string($subjob, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);
					else
						$skills = Main::db_query(sprintf($GLOBALS['DBQUERY_SKILLS_DATA'], $GLOBALS['DBSTRUCT_L2J_SKILLS_ID'], $GLOBALS['DBSTRUCT_L2J_SKILLS_LEVEL'], $GLOBALS['DBSTRUCT_L2J_SKILLS_TABLE'], $GLOBALS['DBSTRUCT_L2J_SKILLS_CID'], Main::db_escape_string($char_id, $GLOBALS['DB_GAME_SERVER']), $GLOBALS['DBSTRUCT_L2J_SKILLS_CLASS'], Main::db_escape_string($subjob, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);

					$stat_page_vars['active_skills'] = '';

					$stat_page_vars['passive_skills'] = '';

					while($row=Main::db_fetch_row($skills))
					{

						if(!in_array($row[0], $passive_skills))
						{

							if($GLOBALS['CONFIG_STATS_SKILLS_ACTIVE'])
								$skills_list[0][$row[0]] = $row[1];

						}
						elseif($GLOBALS['CONFIG_STATS_SKILLS_PASSIVE'])
							$skills_list[1][($row[0] == '239' ? 238 + $row[1] : $row[0])] = $row[1];

					}

					if($GLOBALS['CONFIG_STATS_SKILLS_CACHE'])
						file_put_contents($cache_file, serialize($skills_list));

				}

				foreach($skills_list[0] as $id => $level)
					$stat_page_vars['active_skills'] .= Template::load('styles/skills_list.html', array('skill_id' => $id, 'skill_level' => $level), 0);

				foreach($skills_list[1] as $id => $level)
					$stat_page_vars['passive_skills'] .= Template::load('styles/skills_list.html', array('skill_id' => $id, 'skill_level' => $level), 0);

				$search_page_vars['the_stat'] = Template::load('skills.html', $stat_page_vars, 0);

			}
			else
				$GLOBALS['the_status'] = $GLOBALS['LANG_ERROR_STAT_CHAR'];

		}

		$template_vars['stat_page'] = Template::load('statistics_search.html', $search_page_vars, 0);

	}

}
elseif(isset($_GET['rb']) && $GLOBALS['CONFIG_STATS_RAID_ENABLED'])
{

	if(!$GLOBALS['CONFIG_STATS_RAID_GUESTS'] && !$this->logged)
		$GLOBALS['the_status'] = $GLOBALS['LANG_PAGE_LOGGED'];
	else
	{

		$stat_page_vars = array();
		$the_stat_vars = array();

		$stat_page_vars['min_level'] = $GLOBALS['CONFIG_STATS_RAID_MIN_LEVEL'];
		$stat_page_vars['max_level'] = $GLOBALS['CONFIG_STATS_RAID_MAX_LEVEL'];

		$the_results = array();

		$cache_file = sep_path(CMS_DIR.'/cache/raidbosses.txt');

		$raids_data = array(
						array('amber', 'Ember', '85'),
						array('archon_susceptor', 'Archon Suscepter', '45'),
						array('atraiban', 'Atraiban', '53'),
						array('chief_mate_tilion', 'Zaken\'s Chief Mate Tillion', '50'),
						array('discard_guardian', 'Discarded Guardian', '20'),
						array('eva_guardian_millenu', 'Eva\'s Guardian Millenu', '65'),
						array('guardian_3_of_garden', 'The 3rd Underwater Guardian', '60'),
						array('hallate_the_death_lord', 'Death Lord Hallate', '73'),
						array('heart_of_volcano', 'Heart of Volcano', '70'),
						array('kaysha_herald_of_ikaros', 'Kaysha Herald of Icarus', '21'),
						array('kernon', 'Kernon', '75'),
						array('kurikups', 'Demon Kurikups', '65'),
						array('magician_kenishee', 'Magus Kenishee', '53'),
						array('malex_herald_of_dagoniel', 'Malex Herald of Dagoniel', '21'),
						array('niniel_spirit_eva', 'Eva\'s Spirit Niniel', '55'),
						array('papurrion_pingolpin', 'Fafurion\'s Envoy Pingolpin', '52'),
						array('raid_boss_von_helman', 'Eilhalder von Hellmann', '71'),
						array('valakas', 'Valakas', '85'),
						array('zakens_butcher_krantz', 'Zaken\'s Butcher Krantz', '55'),
						array('zombie_lord_farakelsus', 'Zombie Lord Farakelsus', '20'),
						array('benom_triols_leader', 'Benom', '75'),
						array('serpent_demon_bifrons', 'Serpent Demon Bifrons', '21'),
						array('grandeur_soul_chertuba', 'Chertuba of Great Soul', '35'),
						array('turek_mercenary_boss', 'Turek Mercenary Captain', '30'),
						array('princess_molrang', 'Princess Molrang', '25'),
						array('soul_scavenger', 'Soul Scavenger', '25'),
						array('greyclaw_kutus', 'Greyclaw Kutus', '23'),
						array('ragraman', 'Ragraman', '30'),
						array('tracker_sharuk', 'Tracker Leader Sharuk', '23'),
						array('patriarch_kuroboros', 'Patriarch Kuroboros', '26'),
						array('priest_of_kuroboros', 'Kuroboros\' Priest', '23'),
						array('love_reverser_kael', 'Unrequited Kael', '24'),
						array('tirak', 'Tirak', '28'),
						array('elf_renoa', 'Elf Renoa', '29'),
						array('sukar_wererat_chief', 'Sukar Wererat Chief', '21'),
						array('zombie_lord_crowl', 'Zombie Lord Crowl', '25'),
						array('ikuntai', 'Ikuntai', '25'),
						array('giant_wasteland_basil', 'Giant Wasteland Basilisk', '30'),
						array('gargoyle_lord_sirocco', 'Gargoyle Lord Sirocco', '35'),
						array('madness_beast', 'Madness Beast', '20'),
						array('repiro_rot_tree', 'Rotten Tree Repiro', '44'),
						array('dread_avenger_kraven', 'Dread Avenger Kraven', '44'),
						array('handmaiden_of_orfen', 'Orfen\'s Handmaiden', '48'),
						array('carnage_lord_gato', 'Carnage Lord Gato', '50'),
						array('refuge_hoper_leo', 'Refugee Hopeful Leo', '56'),
						array('master_of_ledflag_shaka', 'Captain of Red Flag Shaka', '52'),
						array('crazy_mechanic_golem', 'Crazy Mechanic Golem', '43'),
						array('flamestone_golem', 'Flamestone Golem', '44'),
						array('iron_giant_totem', 'Iron Giant Totem', '45'),
						array('katu_van_atui', 'Katu Van Leader Atui', '49'),
						array('king_tarlk', 'King Tarlk', '48'),
						array('timak_orc_gosmos', 'Timak Orc Gosmos', '45'),
						array('timak_seer_ragoth', 'Timak Seer Ragoth', '57'),
						array('timak_orc_hunter_a', 'Timak Orc Chief Ranger', '44'),
						array('shaman_king_selu', 'Shaman King Selu', '40'),
						array('wizard_of_storm_teruk', 'Wizard of Storm Teruk', '40'),
						array('leto_chief_talkin', 'Leto Chief Talkin', '40'),
						array('roaring_seer_kastor', 'Roaring Lord Kastor', '62'),
						array('rahha', 'Rahha', '65'),
						array('manes_lidia', 'Ghost of the Well Lidia', '63'),
						array('betrayer_of_urutu_freki', 'Betrayer of Urutu Freki', '25'),
						array('mammons_collector_talos', 'Mammon Collector Talos', '25'),
						array('remmel', 'Remmel', '35'),
						array('lizardman_leader_hellion', 'Lizardmen Leader Hellion', '38'),
						array('jeruna_queen', 'Stakato Queen Zyrnna', '34'),
						array('queens_nobel_leader', 'Captain of Queen\'s Royal Guards', '32'),
						array('vuku_witchdr_gharmash', 'Vuku Grand Seer Gharmash', '33'),
						array('tiger_hornet', 'Tiger Hornet', '26'),
						array('pan_draid', 'Pan Dryad', '25'),
						array('eyes_of_bereth', 'Eye of Beleth', '35'),
						array('redeye_leader_trakia', 'Red Eye Captain Trakia', '35'),
						array('nurkas_messenger', 'Nurka\'s Messenger', '33'),
						array('partisan_leader_talakin', 'Partisan Leader Talakin', '28'),
						array('catseye', 'Cat\'s Eye Bandit', '30'),
						array('flamelord_shadar', 'Flame Lord Shadar', '35'),
						array('demon_tempest', 'Evil Spirit Tempest', '36'),
						array('warden_guillotine', 'Guilotine, Warden of the Execution Grounds', '35'),
						array('soul_collector_acheron', 'Soul Collector Acheron', '35'),
						array('corsair_captain_kylon', 'Corsair Captain Kylon', '33'),
						array('black_lily', 'Black Lily', '55'),
						array('necrosentinel_guard', 'Necrosentinel Royal Guard', '47'),
						array('cursed_clala', 'Cursed Clara', '50'),
						array('road_scavenger_leader', 'Road Scavenger Leader', '40'),
						array('breka_warlock_pastu', 'Breka Warlock Pastu', '34'),
						array('king_tiger_karuta', 'Tiger King Karuta', '45'),
						array('lost_cat_the_cat_a', 'Leader of Cat Gang', '39'),
						array('ancient_weird_drake', 'Ancient Weird Drake', '65'),
						array('lord_ishka', 'Lord Ishka', '60'),
						array('antaras_adherent_skyla', 'Skyla', '32'),
						array('bereths_seer_sephia', 'Beleth\'s Seer Sephia', '55'),
						array('meana_agent_of_beres', 'Agent of Beres, Meana', '30'),
						array('sejarr_s_summoner', 'Sejarr\'s Servitor', '35'),
						array('nakondas', 'Nakondas', '35'),
						array('bloody_priest_rudelto', 'Bloody Priest Rudelto', '69'),
						array('antaras_cloe', 'Antharas Priest Cloe', '74'),
						array('water_spirit_lian', 'Water Spirit Lian', '40'),
						array('sebek', 'Sebek', '36'),
						array('apepi', 'Apepi', '30'),
						array('tasaba_patriarch_hellena', 'Tasaba Patriarch Hellena', '35'),
						array('water_couatl_ateka', 'Water Couatle Ateka', '40'),
						array('cronoss_summons_mumu', 'Cronos\'s Servitor Mumu', '34'),
						array('gwindorr', 'Gwindorr', '40'),
						array('icarus_sample_21', 'Icarus Sample 1', '40'),
						array('faf_herald_lokness', 'Fafurion\'s Herald Lokness', '70'),
						array('wdragon_priest_sheshark', 'Water Dragon Seer Sheshark', '72'),
						array('krokian_padisha_sobekk', 'Krokian Padisha Sobekk', '74'),
						array('ocean_flame_ashakiel', 'Ocean Flame Ashakiel', '76'),
						array('istary_papurrion', 'Fafurion\'s Henchman Istary', '45'),
						array('biconne_of_blue_sky', 'Biconne of Blue Sky', '45'),
						array('earth_protecter_panathen', 'Earth Protector Panathen', '43'),
						array('fafurions_pagehood_sika', 'Fafurion\'s Page Sika', '40'),
						array('premo_prime_the_creature', 'Premo Prime', '38'),
						array('bandit_leader_barda', 'Bandit Leader Barda', '55'),
						array('enmity_ghost_ramdal', 'Enmity Ghost Ramdal', '65'),
						array('spirit_andras_betrayer', 'Spirit of Andras, the Betrayer', '69'),
						array('hallate_the_death_lord', 'Death Lord Hallate', '73'),
						array('hope_immortality_mardil', 'Immortal Savior Mardil', '71'),
						array('korim', 'Korim', '70'),
						array('kernon', 'Kernon', '75'),
						array('golkonda_longhorn', 'Longhorn Golkonda', '79'),
						array('shuriel_fire_of_wrath', 'Fire of Wrath Shuriel', '78'),
						array('cherub_garacsia', 'Cherub Galaxia', '79'),
						array('barion', 'Barion', '47'),
						array('karte', 'Karte', '49'),
						array('verfa', 'Verfa', '51'),
						array('fairy_queen_timiniel', 'Fairy Queen Timiniel', '56'),
						array('unicorn_paniel', 'Unicorn Paniel', '54'),
						array('obern_mgr_of_fairyqueen', 'Messenger of Fairy Queen Berun', '50'),
						array('fairys_watcher_ruell', 'Enchanted Forest Watcher Ruell', '55'),
						array('furious_thieles', 'Furious Thieles', '55'),
						array('roar_skylancer', 'Roaring Skylancer', '70'),
						array('retreat_spider_cletu', 'Retreat Spider Cletu', '42'),
						array('shacram', 'Shacram', '45'),
						array('monster_cyrion', 'Evil Spirit Cyrion', '45'),
						array('kelbar', 'Thief Kelbar', '44'),
						array('kernon_servant_kelone', 'Kernon\'s Faithful Servant Kelone', '67'),
						array('ipos_the_death_lord', 'Death Lord Ipos', '75'),
						array('storm_winged_naga', 'Storm Winged Naga', '75'),
						array('palatanos_power', 'Palatanos of Horrific Power', '75'),
						array('meanas_anor', 'Meanas Anor', '70'),
						array('sorcery_isirr', 'Sorcerer Isirr', '55'),
						array('oblivion_s_mirror', 'Mirror of Oblivion', '49'),
						array('ereve_deathman', 'Deadman Ereve', '51'),
						array('gargoyle_lord_tiphon', 'Gargoyle Lord Tiphon', '65'),
						array('doom_blade_tanatos', 'Doom Blade Tanatos', '72'),
						array('priest_hisilrome', 'Shilen\'s Priest Hisilrome', '65'),
						array('ghost_of_peasant_leader', 'Ghost of Peasant Leader', '50'),
						array('grave_rabber_khim', 'Grave Robber Kim', '52'),
						array('ghost_kabed', 'Ghost Knight Kabed', '55'),
						array('domb_death_cabrio', 'Shilen\'s Messenger Cabrio', '70'),
						array('palibati_queen_themis', 'Palibati Queen Themis', '70'),
						array('bloody_empress_decarbia', 'Bloody Empress Decarbia', '75'),
						array('guardian_of_karum', 'Guardian of the Statue of Giant Karum', '60'),
						array('giant_marpanak', 'Giant Marpanak', '60'),
						array('hekaton_prime', 'Hekaton Prime', '65'),
						array('taik_prefect_arak', 'Taik High Prefect Arak', '60'),
						array('last_lesser_olkuth', 'Last Lesser Giant Olkuth', '75'),
						array('last_lesser_glaki', 'Last Lesser Giant Glaki', '78'),
						array('last_lesser_utenus', 'Last Titan Utenus', '66'),
						array('gorgolos', 'Gorgolos', '64'),
						array('fiercetiger_king_angel', 'Fierce Tiger King Angel', '65'),
						array('soulless_wild_boar', 'Soulless Wild Boar', '59'),
						array('harit_hero_tamash', 'Harit Hero Tamash', '55'),
						array('harit_tutelar_garangky', 'Harit Guardian Garangky', '56'),
						array('varka_commnder_mos', 'Varka\'s Commander Mos', '84'),
						array('varka_chief_horuth', 'Varka\'s Chief Horus', '87'),
						array('varka_hero_shadith', 'Varka\'s Hero Shadith', '80'),
						array('vanor_chief_kandra', 'Vanor Chief Kandra', '72'),
						array('beastlord_behemoth', 'Beast Lord Behemoth', '70'),
						array('geyser_guardian_hestia', 'Hestia, Guardian Deity of the Hot Springs', '78'),
						array('ketra_commander_tayr', 'Ketra\'s Commander Tayr', '84'),
						array('ketra_chief_brakki', 'Ketra\'s Chief Brakki', '87'),
						array('ketra_hero_hekaton', 'Ketra\'s Hero Hekaton', '80'),
						array('abyss_brukunt', 'Abyss Brukunt', '67'),
						array('blinding_fire_barakiel', 'Flame of Splendor Barakiel', '70'),
						array('demonic_agent_falston', 'Demon\'s Agent Falston', '66'),
						array('langk_matriarch_rashkos', 'Langk Matriarch Rashkos', '24'),
						array('liliths_oracle_marilion', 'Lilith\'s Witch Marilion', '50'),
						array('revenant_of_sir_calibus', 'Revenant of Sir Calibus', '34'),
						array('pagan_warder_cerberon', 'Pagan Watcher Cerberon', '55'),
						array('anakims_nemesis_zakaron', 'Anakim\'s Nemesis Zakaron', '70'),
						array('anakim', 'Anakim', '80'),
						array('lilith', 'Lilith', '80'),
						array('shax_the_death_lord', 'Death Lord Shax', '75'),
						array('queen_ant', 'Queen Ant', '40'),
						array('orfen', 'Orfen', '50'),
						array('core', 'Core', '50'),
						array('antaras', 'Antharas', '79'),
						array('baium', 'Baium', '75'),
						array('zaken', 'Zaken', '60', '60', '20'));

		if($GLOBALS['CONFIG_STATS_RAID_CACHE'] && file_exists($cache_file) && time() - filemtime($cache_file) < $GLOBALS['CONFIG_STATS_RAID_CACHE'])
			$the_results = unserialize(file_get_contents($cache_file));
		else
		{

			if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
			{

				$raid_list = '';

				$i = 0;

				$count = count($raids_data) - 1;

				foreach($raids_data as $k)
					$raid_list .= '\''.$k[0].'\''.($count < ++$i ? '' : ', ');

				$query = Main::db_query(sprintf($GLOBALS['DBQUERY_L2OFF_RAIDS'], $GLOBALS['DBSTRUCT_L2OFF_NPCBOS_NAME'], $GLOBALS['DBSTRUCT_L2OFF_NPCBOS_TLOW'], $GLOBALS['DBSTRUCT_L2OFF_NPCBOS_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_NPCBOS_NAME'], $raid_list, $GLOBALS['DBSTRUCT_L2OFF_NPCBOS_NAME']), $GLOBALS['DB_GAME_SERVER']);

			}
			else
				$query = Main::db_query(sprintf($GLOBALS['DBQUERY_RAIDS'], $GLOBALS['DBSTRUCT_L2J_NPCS_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2J_NPCS_NAME'],
										$GLOBALS['DBSTRUCT_L2J_RAIDS_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2J_RAIDS_TIME'], $GLOBALS['DBSTRUCT_L2J_NPCS_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2J_NPCS_LEVEL'],
										$GLOBALS['DBSTRUCT_L2J_RAIDS_TABLE'], $GLOBALS['DBSTRUCT_L2J_NPCS_TABLE'], $GLOBALS['DBSTRUCT_L2J_RAIDS_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2J_RAIDS_ID'],
										$GLOBALS['DBSTRUCT_L2J_NPCS_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2J_NPCS_ID'], $GLOBALS['DBSTRUCT_L2J_NPCS_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2J_NPCS_LEVEL'],
										$GLOBALS['CONFIG_STATS_RAID_MIN_LEVEL'], $GLOBALS['DBSTRUCT_L2J_NPCS_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2J_NPCS_LEVEL'], $GLOBALS['CONFIG_STATS_RAID_MAX_LEVEL']), $GLOBALS['DB_GAME_SERVER']);

			$i = 1;

			while($raid_data=Main::db_fetch_row($query))
			{

				$raid_level = $GLOBALS['CONFIG_SERVER_TYPE'] == 1 ? $raids_data[$i-1][2] : $raid_data[2];

				++$i;

				if($raid_level >= $GLOBALS['CONFIG_STATS_RAID_MIN_LEVEL'] && $raid_level <= $GLOBALS['CONFIG_STATS_RAID_MAX_LEVEL'])			
					$the_results[] = $raid_data;

			}

			if($GLOBALS['CONFIG_STATS_RAID_CACHE'])
				file_put_contents($cache_file, serialize($the_results));

		}

		$stat_page_vars['the_stat'] = '';

		$i = 1;

		foreach($the_results as $raid_data)
		{

			$the_stat_vars['the_rank'] = $i;
			$the_stat_vars['raid_name'] = $GLOBALS['CONFIG_SERVER_TYPE'] == 1 ? $raids_data[$i-1][1] : $raid_data[0];
			$the_stat_vars['raid_level'] = $GLOBALS['CONFIG_STATS_RAID_LEVEL'] ? ($GLOBALS['CONFIG_SERVER_TYPE'] == 1 ? $raids_data[$i-1][2] : $raid_data[2]) : $GLOBALS['LANG_DISABLED'];
			$the_stat_vars['raid_status'] = $GLOBALS['CONFIG_STATS_RAID_ONLINE_STATUS'] ? ($raid_data[1] ? $GLOBALS['LANG_NO'] : $GLOBALS['LANG_YES']) : $GLOBALS['LANG_DISABLED'];
			$the_stat_vars['raid_died'] = $GLOBALS['CONFIG_STATS_RAID_TIME_DIED'] ? ($raid_data[1] ? date($GLOBALS['CONFIG_DATE_FORMAT'], ($GLOBALS['CONFIG_SERVER_TYPE'] == 1 ? $raid_data[1] : ($raid_data[1] / 1000))) : ' - ') : $GLOBALS['LANG_DISABLED'];

			++$i;

			$stat_page_vars['the_stat'] .= Template::load('styles/raids_list.html', $the_stat_vars, 0);

		}

		$template_vars['stat_page'] = Template::load('raids.html', $stat_page_vars, 0);

	}

}
elseif(isset($_GET['castle']) && $GLOBALS['CONFIG_STATS_CASTLE_ENABLED'])
{

	if(!$GLOBALS['CONFIG_STATS_CASTLE_GUESTS'] && !$this->logged)
		$GLOBALS['the_status'] = $GLOBALS['LANG_PAGE_LOGGED'];
	else
	{

		$stat_page_vars = array();
		$the_stat_vars = array();

		$the_results = array();

		$cache_file = sep_path(CMS_DIR.'/cache/castles.txt');

		if($GLOBALS['CONFIG_STATS_CASTLE_CACHE'] && file_exists($cache_file) && time() - filemtime($cache_file) < $GLOBALS['CONFIG_STATS_CASTLE_CACHE'])
			$the_results = unserialize(file_get_contents($cache_file));
		else
		{

			if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
				$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CASTLES'], $GLOBALS['DBSTRUCT_L2OFF_CASTLE_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_CASTLE_ID'], $GLOBALS['DBSTRUCT_L2OFF_CASTLE_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_CASTLE_DATE'],
										$GLOBALS['DBSTRUCT_L2OFF_PLEDGE_NAME'], $GLOBALS['DBSTRUCT_L2OFF_PLEDGE_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_PLEDGE_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_PLEDGE_CASTLE'], 
										$GLOBALS['DBSTRUCT_L2OFF_CASTLE_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_CASTLE_ID'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_NAME'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'],
										$GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_USERDAT_ID'], $GLOBALS['DBSTRUCT_L2OFF_PLEDGE_LEADER'], $GLOBALS['DBSTRUCT_L2OFF_PLEDGE_TABLE'],
										$GLOBALS['DBSTRUCT_L2OFF_PLEDGE_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_PLEDGE_CASTLE'], $GLOBALS['DBSTRUCT_L2OFF_CASTLE_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2OFF_CASTLE_ID'], $GLOBALS['DBSTRUCT_L2OFF_CASTLE_TABLE']), $GLOBALS['DB_GAME_SERVER']);
			else
				$query = Main::db_query(sprintf($GLOBALS['DBQUERY_CASTLES'], $GLOBALS['DBSTRUCT_L2J_CASTLE_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2J_CASTLE_ID'], $GLOBALS['DBSTRUCT_L2J_CASTLE_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2J_CASTLE_DATE'],
										$GLOBALS['DBSTRUCT_L2J_CLAN_NAME'], $GLOBALS['DBSTRUCT_L2J_CLAN_TABLE'], $GLOBALS['DBSTRUCT_L2J_CLAN_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2J_CLAN_CASTLE'], 
										$GLOBALS['DBSTRUCT_L2J_CASTLE_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2J_CASTLE_ID'], $GLOBALS['DBSTRUCT_L2J_CHARS_NAME'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'],
										$GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2J_CHARS_ID'], $GLOBALS['DBSTRUCT_L2J_CLAN_LEADER'], $GLOBALS['DBSTRUCT_L2J_CLAN_TABLE'],
										$GLOBALS['DBSTRUCT_L2J_CLAN_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2J_CLAN_CASTLE'], $GLOBALS['DBSTRUCT_L2J_CASTLE_TABLE'].'.'.$GLOBALS['DBSTRUCT_L2J_CASTLE_ID'], $GLOBALS['DBSTRUCT_L2J_CASTLE_TABLE']), $GLOBALS['DB_GAME_SERVER']);

			$i = 1;

			while($castle_data=Main::db_fetch_row($query))
					$the_results[] = $castle_data;

			if($GLOBALS['CONFIG_STATS_CASTLE_CACHE'])
				file_put_contents($cache_file, serialize($the_results));

		}

		$stat_page_vars['the_stat'] = '';

		$i = 1;

		foreach($the_results as $castle_data)
		{

			$the_stat_vars['the_rank'] = $i;
			$the_stat_vars['castle_name'] = Account::castle_name($castle_data[0]);
			$the_stat_vars['castle_date'] = $GLOBALS['CONFIG_SERVER_TYPE'] == 1 ? date($GLOBALS['CONFIG_DATE_FORMAT'], $castle_data[1]) : date($GLOBALS['CONFIG_DATE_FORMAT'], $castle_data[1] / 1000);
			$the_stat_vars['castle_owner'] = $castle_data[2] ? $castle_data[2] : $GLOBALS['LANG_NO_OWNER'];
			$the_stat_vars['castle_owner_name'] = $castle_data[3] ? $castle_data[3] : ' - ';

			++$i;

			$stat_page_vars['the_stat'] .= Template::load('styles/castles_list.html', $the_stat_vars, 0);

		}

		$template_vars['stat_page'] = Template::load('castles.html', $stat_page_vars, 0);

	}

}
elseif(isset($_GET['pvp']) && $GLOBALS['CONFIG_STATS_TOP_PVP_ENABLED'])
{

	if(!$GLOBALS['CONFIG_STATS_TOP_PVP_GUESTS'] && !$this->logged)
		$GLOBALS['the_status'] = $GLOBALS['LANG_PAGE_LOGGED'];
	else
	{

		$stat_page_vars = array();
		$the_top_vars = array();

		$stat_page_vars['total_results'] = $GLOBALS['CONFIG_STATS_TOP_PVP_RESULTS'];
		$stat_page_vars['the_top'] = '';

		$cache_file = sep_path(CMS_DIR.'/cache/total_pvp.txt');

		if($GLOBALS['CONFIG_STATS_TOTAL_PVP_CACHE'] && file_exists($cache_file) && time() - filemtime($cache_file) < $GLOBALS['CONFIG_STATS_TOTAL_PVP_CACHE'])
			$stat_page_vars['total_pvp'] = file_get_contents($cache_file);
		else
		{

			if($this->server_type == '1')
				$stat_page_vars['total_pvp'] = $GLOBALS['CONFIG_STATS_TOTAL_PVP_ENABLED'] ? Main::db_result(Main::db_query(sprintf($GLOBALS['DBQUERY_STAT_SUM'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_PVP'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE']), $GLOBALS['DB_GAME_SERVER']), 0) : $GLOBALS['LANG_DISABLED'];
			else
				$stat_page_vars['total_pvp'] = $GLOBALS['CONFIG_STATS_TOTAL_PVP_ENABLED'] ? Main::db_result(Main::db_query(sprintf($GLOBALS['DBQUERY_STAT_SUM'], $GLOBALS['DBSTRUCT_L2J_CHARS_PVP'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE']), $GLOBALS['DB_GAME_SREVER']), 0) : $GLOBALS['LANG_DISABLED'];

			if($GLOBALS['CONFIG_STATS_TOTAL_PVP_CACHE'])
				file_put_contents($cache_file, $stat_page_vars['total_pvp']);

		}

		@require_once(sep_path(CMS_DIR.'/libraries/paginate.class.inc'));

		if($this->server_type == '1')
			$top_pvp_query = sprintf($GLOBALS['DBQUERY_TOP_PVP'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_NAME'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_PVP'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_LEVEL'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_PVP'], '1');
		else
			$top_pvp_query = sprintf($GLOBALS['DBQUERY_TOP_PVP'], $GLOBALS['DBSTRUCT_L2J_CHARS_NAME'], $GLOBALS['DBSTRUCT_L2J_CHARS_PVP'], $GLOBALS['DBSTRUCT_L2J_CHARS_LEVEL'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_PVP'], '1');

		$paginate = new Paginate($top_pvp_query, $this->server_type == '1' ? $GLOBALS['DBSTRUCT_L2OFF_USERDAT_PVP'] : $GLOBALS['DBSTRUCT_L2J_CHARS_PVP'], $GLOBALS['CONFIG_STATS_TOP_PVP_ORDER'], $GLOBALS['DB_GAME_SERVER'], $GLOBALS['CONFIG_STATS_TOP_PVP_RESULTS'], $GLOBALS['CONFIG_STATS_TOP_PVP_RESULTS_PER_PAGE'], $GLOBALS['CONFIG_STATS_TOP_PVP_CACHE'], 'top_pvp');

		$paginate->load();

		$results = $paginate->results();

		$i = $paginate->offset;

		foreach($results as $k)
		{

			++$i;

			$the_top_vars['the_rank'] = $i;
			$the_top_vars['char_name'] = $k[0];
			$the_top_vars['char_pvp'] = $k[1];
			$the_top_vars['char_level'] = ($k[2] ? $k[2] : '1');

			$stat_page_vars['the_top'] .= Template::load('styles/toppvp_list.html', $the_top_vars, 0);

		}

		$prev_get = 'page=statistics&pvp';

		$stat_page_vars['first_page'] =  $paginate->first_page ? sprintf($GLOBALS['LANG_FIRSTP'], $prev_get, $paginate->first_page) : '';
		$stat_page_vars['prev_page'] = $paginate->prev_page ? sprintf($GLOBALS['LANG_PREVP'], $prev_get, $paginate->prev_page) : '';
		$stat_page_vars['next_page'] = $paginate->next_page ? sprintf($GLOBALS['LANG_NEXTP'], $prev_get, $paginate->next_page) : '';
		$stat_page_vars['last_page'] = $paginate->last_page ? sprintf($GLOBALS['LANG_LASTP'], $prev_get, $paginate->last_page) : '';

		$stat_page_vars['current_page'] = $paginate->current_page;
		$stat_page_vars['total_page'] = $paginate->total_pages ? $paginate->total_pages : '1';

		$template_vars['stat_page'] = Template::load('toppvp.html', $stat_page_vars, 0);

	}

}
elseif(isset($_GET['pk']) && $GLOBALS['CONFIG_STATS_TOP_PK_ENABLED'])
{

	if(!$GLOBALS['CONFIG_STATS_TOP_PK_GUESTS'] && !$this->logged)
		$GLOBALS['the_status'] = $GLOBALS['LANG_PAGE_LOGGED'];
	else
	{

		$stat_page_vars = array();
		$the_top_vars = array();

		$stat_page_vars['total_results'] = $GLOBALS['CONFIG_STATS_TOP_PK_RESULTS'];
		$stat_page_vars['the_top'] = '';

		$cache_file = sep_path(CMS_DIR.'/cache/total_pk.txt');

		if($GLOBALS['CONFIG_STATS_TOTAL_PK_CACHE'] && file_exists($cache_file) && time() - filemtime($cache_file) < $GLOBALS['CONFIG_STATS_TOTAL_PK_CACHE'])
			$stat_page_vars['total_pk'] = file_get_contents($cache_file);
		else
		{

			if($this->server_type == '1')
				$stat_page_vars['total_pk'] = $GLOBALS['CONFIG_STATS_TOTAL_PK_ENABLED'] ? Main::db_result(Main::db_query(sprintf($GLOBALS['DBQUERY_STAT_SUM'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_PK'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE']), $GLOBALS['DB_GAME_SERVER']), 0) : $GLOBALS['LANG_DISABLED'];
			else
				$stat_page_vars['total_pk'] = $GLOBALS['CONFIG_STATS_TOTAL_PK_ENABLED'] ? Main::db_result(Main::db_query(sprintf($GLOBALS['DBQUERY_STAT_SUM'], $GLOBALS['DBSTRUCT_L2J_CHARS_PK'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE']), $GLOBALS['DB_GAME_SREVER']), 0) : $GLOBALS['LANG_DISABLED'];

			if($GLOBALS['CONFIG_STATS_TOTAL_PK_CACHE'])
				file_put_contents($cache_file, $stat_page_vars['total_pk']);

		}

		@require_once(sep_path(CMS_DIR.'/libraries/paginate.class.inc'));

		if($this->server_type == '1')
			$top_pk_query = sprintf($GLOBALS['DBQUERY_TOP_PK'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_NAME'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_PK'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_LEVEL'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERDAT_PK'], '1');
		else
			$top_pk_query = sprintf($GLOBALS['DBQUERY_TOP_PK'], $GLOBALS['DBSTRUCT_L2J_CHARS_NAME'], $GLOBALS['DBSTRUCT_L2J_CHARS_PK'], $GLOBALS['DBSTRUCT_L2J_CHARS_LEVEL'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_PK'], '1');

		$paginate = new Paginate($top_pk_query, $this->server_type == '1' ? $GLOBALS['DBSTRUCT_L2OFF_USERDAT_PK'] : $GLOBALS['DBSTRUCT_L2J_CHARS_PK'], $GLOBALS['CONFIG_STATS_TOP_PK_ORDER'], $GLOBALS['DB_GAME_SERVER'], $GLOBALS['CONFIG_STATS_TOP_PK_RESULTS'], $GLOBALS['CONFIG_STATS_TOP_PK_RESULTS_PER_PAGE'], $GLOBALS['CONFIG_STATS_TOP_PK_CACHE'], 'top_pk');

		$paginate->load();

		$results = $paginate->results();

		$i = $paginate->offset;

		foreach($results as $k)
		{

			++$i;

			$the_top_vars['the_rank'] = $i;
			$the_top_vars['char_name'] = $k[0];
			$the_top_vars['char_pk'] = $k[1];
			$the_top_vars['char_level'] = ($k[2] ? $k[2] : '1');

			$stat_page_vars['the_top'] .= Template::load('styles/toppk_list.html', $the_top_vars, 0);

		}

		$prev_get = 'page=statistics&pk';

		$stat_page_vars['first_page'] =  $paginate->first_page ? sprintf($GLOBALS['LANG_FIRSTP'], $prev_get, $paginate->first_page) : '';
		$stat_page_vars['prev_page'] = $paginate->prev_page ? sprintf($GLOBALS['LANG_PREVP'], $prev_get, $paginate->prev_page) : '';
		$stat_page_vars['next_page'] = $paginate->next_page ? sprintf($GLOBALS['LANG_NEXTP'], $prev_get, $paginate->next_page) : '';
		$stat_page_vars['last_page'] = $paginate->last_page ? sprintf($GLOBALS['LANG_LASTP'], $prev_get, $paginate->last_page) : '';

		$stat_page_vars['current_page'] = $paginate->current_page;
		$stat_page_vars['total_page'] = $paginate->total_pages ? $paginate->total_pages : '1';

		$template_vars['stat_page'] = Template::load('toppk.html', $stat_page_vars, 0);

	}

}

$template_vars['status'] = $GLOBALS['the_status'];
