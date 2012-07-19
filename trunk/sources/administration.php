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

if(!$this->logged)
{

	$template_location[] = 'header.html';
	$template_location[] = 'errors.html';
	$template_location[] = 'footer.html';

	$template_vars['the_error'] = 'You must be logged in to access this page!';

}
else
{

	$template_location[] = 'administration/header.html';

	$template_vars['page_title'] = 'Admin Panel - ';

	if(isset($_GET['settings']))
	{

		if($this->access_level < 5)
			// Coming soon, privileges etc
			;
		else
		{

			$template_location[] = 'administration/settings.html';

			$template_vars['page_title'] .= 'Server Settings';
			$template_vars['the_status'] = '';

			if(isset($_POST['hostname_gs']) && isset($_POST['hostname_ls']) && isset($_POST['hostname_forum']) && isset($_POST['user_gs']) && isset($_POST['user_ls']) && isset($_POST['user_forum']) && isset($_POST['pass_gs']) && isset($_POST['pass_ls']) && isset($_POST['pass_forum']) && isset($_POST['data_gs']) && isset($_POST['data_ls']) && isset($_POST['data_forum']) && isset($_POST['website_name']) && isset($_POST['website_url']) && isset($_POST['date_format']) && isset($_POST['select_timezone']))
			{

				$host_gs = $step_vars['val_h_gs'] = htmlspecialchars($_POST['hostname_gs']);
				$host_ls = $step_vars['val_h_ls'] = htmlspecialchars($_POST['hostname_ls']);
				$host_forum = htmlspecialchars($_POST['hostname_forum']);

				$user_gs = $step_vars['val_u_gs'] = htmlspecialchars($_POST['user_gs']);
				$user_ls = $step_vars['val_u_ls'] = htmlspecialchars($_POST['user_ls']);
				$user_forum = htmlspecialchars($_POST['user_forum']);

				$pass_gs = htmlspecialchars($_POST['pass_gs']);
				$pass_ls = htmlspecialchars($_POST['pass_ls']);
				$pass_forum = htmlspecialchars($_POST['pass_forum']);

				$data_gs = $step_vars['val_d_gs'] = htmlspecialchars($_POST['data_gs']);
				$data_ls = $step_vars['val_d_ls'] = htmlspecialchars($_POST['data_ls']);
				$data_forum = htmlspecialchars($_POST['data_forum']);

				$config_pconnect = intval($_POST['config_pconnect']);

				$dbt = $server_type == 1 ? 'MSSQL' : 'MYSQL';

				Configs::update_configs(array('CONFIG_'.$dbt.'_PCONNECT' => '\''.$config_pconnect.'\'', 'CONFIG_'.$dbt.'_HOST_GS' => '\''.$host_gs.'\'', 'CONFIG_'.$dbt.'_HOST_LS' => '\''.$host_ls.'\'', 'CONFIG_'.$dbt.'_USER_GS' => '\''.$user_gs.'\'', 'CONFIG_'.$dbt.'_USER_LS' => '\''.$user_ls.'\'', 'CONFIG_'.$dbt.'_PASS_GS' => '\''.$pass_gs.'\'', 'CONFIG_'.$dbt.'_PASS_LS' => '\''.$pass_ls.'\'', 'CONFIG_'.$dbt.'_NAME_GS' => '\''.$data_gs.'\'', 'CONFIG_'.$dbt.'_NAME_LS' => '\''.$data_ls.'\''), 'connection');

				Configs::update_configs(array('CONFIG_WEBSITE_NAME' => '\''.$_POST['website_name'].'\'', 'CONFIG_WEBSITE_URL' => '\''.$_POST['website_url'].'\'', 'CONFIG_DATE_TIMEZONE' => '\''.$_POST['select_timezone'].'\'', 'CONFIG_DATE_FORMAT' => '\''.$_POST['date_format'].'\''), 'main');

				$GLOBALS['CONFIG_WEBSITE_NAME'] = $_POST['website_name'];

				$GLOBALS['CONFIG_WEBSITE_URL'] = $_POST['website_url'];

				$GLOBALS['CONFIG_DATE_FORMAT'] = $_POST['date_format'];

				$template_vars['the_status'] = 'Server settings updated!';

			}

			if($this->server_type == 1)
			{

				$pconn_val = $GLOBALS['CONFIG_MSSQL_PCONNECT'];
				$template_vars['val_h_gs'] = $GLOBALS['CONFIG_MSSQL_HOST_GS'];
				$template_vars['val_h_ls'] = $GLOBALS['CONFIG_MSSQL_HOST_LS'];
				$template_vars['val_u_gs'] = $GLOBALS['CONFIG_MSSQL_USER_GS'];
				$template_vars['val_u_ls'] = $GLOBALS['CONFIG_MSSQL_USER_LS'];
				$template_vars['val_p_gs'] = $GLOBALS['CONFIG_MSSQL_PASS_GS'];
				$template_vars['val_p_ls'] = $GLOBALS['CONFIG_MSSQL_PASS_LS'];
				$template_vars['val_d_gs'] = $GLOBALS['CONFIG_MSSQL_NAME_GS'];
				$template_vars['val_d_ls'] = $GLOBALS['CONFIG_MSSQL_NAME_LS'];

			}
			else
			{

				$pconn_val = $GLOBALS['CONFIG_MYSQL_PCONNECT'];
				$template_vars['val_h_gs'] = $GLOBALS['CONFIG_MYSQL_HOST_GS'];
				$template_vars['val_h_ls'] = $GLOBALS['CONFIG_MYSQL_HOST_LS'];
				$template_vars['val_u_gs'] = $GLOBALS['CONFIG_MYSQL_USER_GS'];
				$template_vars['val_u_ls'] = $GLOBALS['CONFIG_MYSQL_USER_LS'];
				$template_vars['val_p_gs'] = $GLOBALS['CONFIG_MYSQL_PASS_GS'];
				$template_vars['val_p_ls'] = $GLOBALS['CONFIG_MYSQL_PASS_LS'];
				$template_vars['val_d_gs'] = $GLOBALS['CONFIG_MYSQL_NAME_GS'];
				$template_vars['val_d_ls'] = $GLOBALS['CONFIG_MYSQL_NAME_LS'];

			}

			$template_vars['db_pconne'] = $pconn_val ? 'checked="checked" ' : null;

		}

	}
	elseif(isset($_GET['content']))
	{

		$template_location[] = 'administration/content.html';

		$template_vars['page_title'] .= 'Content Management';

		$cpages = CData::content_pages();

		if(!isset($_GET['cpage']) || (isset($_GET['cpage']) && !in_array(strtoupper($_GET['cpage']), $cpages)))
		{

			$cpagel_vars['cpage_list'] = null;

			foreach($cpages as $cp)
			{

				$cpagel_vars['cpage_name'] = ucfirst(strtolower($cp));

				$cpage_vars['cpage_list'] .= Template::load('administration/styles/content_pages_list.html', $cpagel_vars, 0);

			}

			$template_vars['content_management'] = Template::load('administration/styles/content_pages.html', $cpage_vars, 0);

		}
		else
		{

			$cpage = new CData($_GET['cpage']);

			$cpage_vars = array();

			$cpage_vars = $cpage->used_vars();
			$cpage_vars['cpage_name'] = ucfirst(strtolower($_GET['cpage']));
			$cpage_vars['cpage_pp'] = $cpage->results_pp;
			$cpage_vars['cpage_data_list'] = '';
			$cpage_vars['the_status'] = '';

			$i = 1;

			if(isset($_GET['action']) && $_GET['action'] == 'add')
				$template_vars['content_management'] = Template::load('administration/styles/content_page_data_add.html', $cpage_vars, 0);
			elseif(isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id']))
			{

				foreach($cpage->used_content() as $used_content)
					if($used_content['cdata_id'] == (int) $_GET['id'])
					{

						$the_title = $used_content['cdata_subject'];
						$the_content = D64Code::decode($used_content['cdata_content']);

						$cpage_vars['cpage_id'] = $used_content['cdata_id'];

						break;

					}

				$cpage_vars['val_cdata_title'] = trim($the_title);
				$cpage_vars['val_cdata_content'] = trim($the_content);

				$template_vars['content_management'] = Template::load('administration/styles/content_page_data_edit.html', $cpage_vars, 0);

			}
			else
			{

				if(isset($_POST['cdata_title']) && isset($_POST['cdata_content']) && isset($_POST['cdata_add']))
				{

					if(!isset($_POST['cdata_title'][3]) || !isset($_POST['cdata_content'][3]))
						$cpage_vars['the_status'] = 'Subject and content must have at least 4 characters!';
					else
					{

						$cdata_add_title = htmlspecialchars($_POST['cdata_title']);
						$cdata_add_content = D64Code::parse(htmlspecialchars($_POST['cdata_content']));

						$cpage_vars['the_status'] = 'Content data added!';

						$cpage->add_content($cdata_add_title, $acc->account_username, $cdata_add_content);

					}

				}
				elseif(isset($_POST['cdata_title']) && isset($_POST['cdata_content']) && isset($_POST['cdata_edit']))
				{

					if(!isset($_POST['cdata_title'][3]) || !isset($_POST['cdata_content'][3]))
						$cpage_vars['the_status'] = 'Subject and content must have at least 4 characters!';
					else
					{

						$cdata_edit_title = D64Code::parse(htmlspecialchars($_POST['cdata_title']));
						$cdata_edit_content = D64Code::parse(htmlspecialchars($_POST['cdata_content']));

						$cpage_vars['the_status'] = 'Content data edited!';
	
						$cpage->edit_content(intval($_GET['id']), $cdata_edit_title, $cdata_edit_content);

					}

				}
				elseif(isset($_GET['action']) && ($_GET['action'] == 'del' && isset($_GET['id'])))
				{

					$cpage_vars['the_status'] = 'Content data deleted!';

					$cpage->del_content(intval($_GET['id']));

				}

				$cdata_vars = $cpage->used_content();

				foreach($cdata_vars as $used_content)
				{

					$used_content['cdata_rnk'] = $i;
					$used_content['cdata_cpage'] = ucfirst(strtolower($_GET['cpage']));
					$used_content['cdata_res'] = intval($_GET['res']);
					$used_content['cdata_content'] = nl2br(CData::convert_whitespaces($used_content['cdata_content']));

					$cpage_vars['cpage_data_list'] .= Template::load('administration/styles/content_page_data_list.html', $used_content, 0);

					$i++;

				}

				$cpage_vars['cpage_data_list'] = $cpage_vars['cpage_data_list'];

				$template_vars['content_management'] = Template::load('administration/styles/content_page_data.html', $cpage_vars, 0);

			}

		}

	}
	elseif(isset($_GET['template']))
	{

		$template_location[] = 'administration/template.html';

		$template_vars['page_title'] .= 'Template Management';

		$template_vars['templates_list'] = '';

		$template_vars['template_data'] = null;

		$template_vars['status'] = null;

		$current_templates = Template::current_templates();

		foreach($current_templates as $id => $name)
		{

			$template_page_vars['template_name'] = $name;

			$template_page_vars['template_id'] = $id;

			$template_vars['templates_list'] .= Template::load('administration/styles/templates_list.html', $template_page_vars, 0);

		}

		if(isset($_GET['id']) && isset($current_templates[$_GET['id']]))
		{

			$template_name = $current_templates[$_GET['id']];

			Configs::update_configs(array('CONFIG_TEMPLATE_SELECTED' => '\''.$template_name.'\''), 'template');

			$GLOBALS['CONFIG_TEMPLATE_SELECTED'] = $template_name;

			$template_vars['status'] = 'Template selected!';

		}

		if(isset($_POST['images_folder']) && isset($_POST['description']) && isset($_POST['keywords']) && isset($_POST['author']) && isset($_POST['save']))
		{

			Configs::update_configs(array(
			'CONFIG_TEMPLATE_IMAGES' => '\''.$_POST['images_folder'].'\'',
			'CONFIG_TEMPLATE_DESCRIPTION' => '\''.$_POST['description'].'\'',
			'CONFIG_TEMPLATE_KEYWORDS' => '\''.$_POST['keywords'].'\'',
			'CONFIG_TEMPLATE_AUTHOR' => '\''.$_POST['author'].'\'',
			), 'template');

			$GLOBALS['CONFIG_TEMPLATE_IMAGES'] = $_POST['images_folder'];

			$GLOBALS['CONFIG_TEMPLATE_DESCRIPTION'] = $_POST['description'];

			$GLOBALS['CONFIG_TEMPLATE_KEYWORDS'] = $_POST['keywords'];

			$GLOBALS['CONFIG_TEMPLATE_AUTHOR'] = $_POST['author'];

			$template_vars['status'] = 'Template settings saved!';

		}

	}
	elseif(isset($_GET['underattack']))
	{

		$template_location[] = 'administration/under_attack.html';

		$template_vars['page_title'] .= 'Under Attack';

		$template_vars['status'] = null;

		if(isset($_POST['attackmsg']) && isset($_POST['save']))
		{

			Configs::update_configs(array('CONFIG_ATTACK_MODE' => '\'1\'', 'CONFIG_ATTACK_MESSAGE' => '\''.$_POST['attackmsg'].'\''), 'main');

			$GLOBALS['CONFIG_ATTACK_MODE'] = '1';

			$GLOBALS['CONFIG_ATTACK_MESSAGE'] = $_POST['attackmsg'];

			$template_vars['status'] = '<b><font color="#ffff00">Under Attack mode enabled! Now you will be logged out and don\'t have access anymore to admin panel untill you disable under attack mode!</font></b>';

		}

	}
	elseif(!isset($_GET['logs']) && !isset($_GET['shop']) && isset($_GET['donate']))
	{

		$template_location[] = 'administration/donate.html';

		$template_vars['page_title'] .= 'Donate System';

		$template_vars['the_status'] = null;

		if(isset($_POST['system_status']) && isset($_POST['currency']) && isset($_POST['donate_multiplier']) && isset($_POST['paypal_status']) && isset($_POST['paypal_multiplier']) && isset($_POST['paypal_mode']) && isset($_POST['paypal_mail']) && isset($_POST['moneybookers_status']) && isset($_POST['moneybookers_multiplier']) && isset($_POST['moneybookers_mail']) && isset($_POST['moneybookers_secret_word']) && isset($_POST['save']))
		{

			Configs::update_configs(array(
			'CONFIG_DONATE_ENABLED' => '\''.$_POST['system_status'].'\'',
			'CONFIG_DONATE_CURRENCY' => '\''.$_POST['currency'].'\'',
			'CONFIG_DONATE_MULTIPLIER' => '\''.$_POST['donate_multiplier'].'\'',
			'CONFIG_DONATE_PAYPAL_ENABLE' => '\''.$_POST['paypal_status'].'\'',
			'CONFIG_DONATE_PAYPAL_MULTIPLIER' => '\''.$_POST['paypal_multiplier'].'\'',
			'CONFIG_DONATE_PAYPAL_TEST' => '\''.$_POST['paypal_mode'].'\'',
			'CONFIG_DONATE_PAYPAL_MAIL' => '\''.$_POST['paypal_mail'].'\'',
			'CONFIG_DONATE_MONEYBOOKERS_ENABLE' => '\''.$_POST['moneybookers_status'].'\'',
			'CONFIG_DONATE_MONEYBOOKERS_MULTIPLIER' => '\''.$_POST['moneybookers_multiplier'].'\'',
			'CONFIG_DONATE_MONEYBOOKERS_MAIL' => '\''.$_POST['moneybookers_mail'].'\'',
			'CONFIG_DONATE_MONEYBOOKERS_SECRET_WORD' => '\''.$_POST['moneybookers_secret_word'].'\'',
			), 'donate_system');

			$GLOBALS['CONFIG_DONATE_ENABLED'] = $_POST['system_status'];

			$GLOBALS['CONFIG_DONATE_CURRENCY'] = $_POST['currency'];

			$GLOBALS['CONFIG_DONATE_MULTIPLIER'] = $_POST['donate_multiplier'];

			$GLOBALS['CONFIG_DONATE_PAYPAL_ENABLE'] = $_POST['paypal_status'];

			$GLOBALS['CONFIG_DONATE_PAYPAL_MULTIPLIER'] = $_POST['paypal_multiplier'];

			$GLOBALS['CONFIG_DONATE_PAYPAL_TEST'] = $_POST['paypal_mode'];

			$GLOBALS['CONFIG_DONATE_PAYPAL_MAIL'] = $_POST['paypal_mail'];

			$GLOBALS['CONFIG_DONATE_MONEYBOOKERS_ENABLE'] = $_POST['moneybookers_status'];

			$GLOBALS['CONFIG_DONATE_MONEYBOOKERS_MULTIPLIER'] = $_POST['moneybookers_multiplier'];

			$GLOBALS['CONFIG_DONATE_MONEYBOOKERS_MAIL'] = $_POST['moneybookers_mail'];

			$GLOBALS['CONFIG_DONATE_MONEYBOOKERS_SECRET_WORD'] = $_POST['moneybookers_secret_word'];

			$template_vars['the_status'] = 'Donate System settings saved!';

		}

		$template_vars['system_status'] = $GLOBALS['CONFIG_DONATE_ENABLED'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['paypal_status'] = $GLOBALS['CONFIG_DONATE_PAYPAL_ENABLE'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['moneybookers_status'] = $GLOBALS['CONFIG_DONATE_MONEYBOOKERS_ENABLE'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['currency'] = $GLOBALS['CONFIG_DONATE_CURRENCY'] == '1' ? '<option value="1">EUR</option><option value="2">USD</option>' : '<option value="2">USD</option><option value="1">EUR</option>';

		$template_vars['paypal_mode'] = $GLOBALS['CONFIG_DONATE_PAYPAL_TEST'] ? '<option value="1">TEST</option><option value="0">LIVE</option>' : '<option value="0">LIVE</option><option value="1">TEST</option>';

	}
	elseif(!isset($_GET['logs']) && !isset($_GET['shop']) && isset($_GET['vote']))
	{

		$template_location[] = 'administration/vote.html';

		$template_vars['page_title'] .= 'Vote System';

		$template_vars['the_status'] = null;

		if(isset($_POST['system_status']) && isset($_POST['method']) && isset($_POST['l2ranking_server_link']) && isset($_POST['l2ranking_server_id']) && isset($_POST['l2ranking_points_pv']) && isset($_POST['l2ranking_banner_link']) && isset($_POST['save']))
		{

			if($_POST['method'] == '1' && !extension_loaded('curl'))
				$template_vars['the_status'] = 'You must enable \'php_curl.dll\' extension to use this check method!';
			else
			{

				$GLOBALS['CONFIG_VOTE_WEBSITES'][0][2] = $_POST['l2ranking_server_link'];

				$GLOBALS['CONFIG_VOTE_WEBSITES'][0][3] = $_POST['l2ranking_server_id'];

				$GLOBALS['CONFIG_VOTE_WEBSITES'][0][5] = $_POST['l2ranking_points_pv'];

				$GLOBALS['CONFIG_VOTE_WEBSITES'][0][1] = $_POST['l2ranking_banner_link'];

				Configs::update_configs(array('CONFIG_VOTE_ENABLED' => '\''.$_POST['system_status'].'\'', 'CONFIG_VOTE_CHECK_METHOD' => '\''.$_POST['method'].'\'', 'CONFIG_VOTE_WEBSITES' => Configs::remake_array($GLOBALS['CONFIG_VOTE_WEBSITES'])), 'vote_system');

				$GLOBALS['CONFIG_VOTE_ENABLED'] = $_POST['system_status'];

				$GLOBALS['CONFIG_VOTE_CHECK_METHOD'] = $_POST['method'];

				$template_vars['the_status'] = 'Vote System settings saved!';

			}

		}

		$template_vars['system_status'] = $GLOBALS['CONFIG_VOTE_ENABLED'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['method'] = $GLOBALS['CONFIG_VOTE_CHECK_METHOD'] == '1' ? '<option value="1">CURL (Recommended)</option><option value="2">PHP Default</option>' : '<option value="2">PHP Default</option><option value="1">CURL (Recommended)</option>';

		$template_vars['l2ranking_server_link'] = $GLOBALS['CONFIG_VOTE_WEBSITES'][0][2];

		$template_vars['l2ranking_server_id'] = $GLOBALS['CONFIG_VOTE_WEBSITES'][0][3];

		$template_vars['l2ranking_points_per_vote'] = $GLOBALS['CONFIG_VOTE_WEBSITES'][0][5];

		$template_vars['l2ranking_banner_link'] = $GLOBALS['CONFIG_VOTE_WEBSITES'][0][1];

	}
	elseif(!isset($_GET['logs']) && isset($_GET['shop']))
	{

		$template_location[] = 'administration/shop.html';

		$template_vars['page_title'] .= 'Shop System';

		$template_vars['the_status'] = null;

		$shop_page_vars = array();

		$template_vars['refer_shop_items'] = '';

		$template_vars['vote_shop_items'] = '';

		$template_vars['donate_shop_items'] = '';

		$template_vars['val_refer_item_name'] = null;

		$template_vars['val_refer_item_id'] = null;

		$template_vars['val_refer_item_count'] = null;

		$template_vars['val_refer_item_enchant'] = null;

		$template_vars['val_refer_item_price'] = null;

		$template_vars['val_vote_item_name'] = null;

		$template_vars['val_vote_item_id'] = null;

		$template_vars['val_vote_item_count'] = null;

		$template_vars['val_vote_item_enchant'] = null;

		$template_vars['val_vote_item_price'] = null;

		$template_vars['val_donate_item_name'] = null;

		$template_vars['val_donate_item_id'] = null;

		$template_vars['val_donate_item_count'] = null;

		$template_vars['val_donate_item_enchant'] = null;

		$template_vars['val_donate_item_price'] = null;

		if(isset($_POST['save']) && isset($_POST['system_status']) && isset($_POST['refer_shop_status']) && isset($_POST['vote_shop_status']) && isset($_POST['donate_shop_status']))
		{

			if($GLOBALS['CONFIG_SERVER_TYPE'] == 1 && isset($_POST['cached_ip']) && isset($_POST['cached_port']) && isset($_POST['cached_admin']))
			{

				Configs::update_configs(array('CONFIG_SHOP_CACHED_IP' => '\''.$_POST['cached_ip'].'\'', 'CONFIG_SHOP_CACHED_PORT' => '\''.$_POST['cached_port'].'\'', 'CONFIG_SHOP_CACHED_ADMIN' => '\''.$_POST['cached_admin'].'\''), 'shop');

				$GLOBALS['CONFIG_SHOP_CACHED_IP'] = $_POST['cached_ip'];

				$GLOBALS['CONFIG_SHOP_CACHED_PORT'] = $_POST['cached_port'];

				$GLOBALS['CONFIG_SHOP_CACHED_ADMIN'] = $_POST['cached_admin'];

			}
			elseif(isset($_POST['telnet_ip']) && isset($_POST['telnet_port']) && isset($_POST['telnet_pass']))
			{

				Configs::update_configs(array('CONFIG_SHOP_TELNET_IP' => '\''.$_POST['telnet_ip'].'\'', 'CONFIG_SHOP_TELNET_PORT' => '\''.$_POST['telnet_port'].'\'', 'CONFIG_SHOP_TELNET_PASSWORD' => '\''.$_POST['telnet_pass'].'\''), 'shop');

				$GLOBALS['CONFIG_SHOP_TELNET_IP'] = $_POST['telnet_ip'];

				$GLOBALS['CONFIG_SHOP_TELNET_PORT'] = $_POST['telnet_port'];

				$GLOBALS['CONFIG_SHOP_TELNET_PASSWORD'] = $_POST['telnet_pass'];

			}

			Configs::update_configs(array('CONFIG_SHOP_ENABLED' => '\''.$_POST['system_status'].'\'', 'CONFIG_SHOP_REFER_ENABLED' => '\''.$_POST['refer_shop_status'].'\'', 'CONFIG_SHOP_VOTE_ENABLED' => '\''.$_POST['vote_shop_status'].'\'', 'CONFIG_SHOP_DONATE_ENABLED' => '\''.$_POST['donate_shop_status'].'\''), 'shop');

			$GLOBALS['CONFIG_SHOP_ENABLED'] = $_POST['system_status'];

			$GLOBALS['CONFIG_SHOP_REFER_ENABLED'] = $_POST['refer_shop_status'];

			$GLOBALS['CONFIG_SHOP_VOTE_ENABLED'] = $_POST['vote_shop_status'];

			$GLOBALS['CONFIG_SHOP_DONATE_ENABLED'] = $_POST['donate_shop_status'];

			$template_vars['the_status'] = 'Shop System settings saved!';

		}
		elseif(isset($_GET['delete']) && isset($_GET['refer']) && isset($GLOBALS['CONFIG_SHOP_REFER'][$_GET['delete']]))
		{

			unset($GLOBALS['CONFIG_SHOP_REFER'][$_GET['delete']]);

			Configs::update_configs(array('CONFIG_SHOP_REFER' => Configs::remake_array($GLOBALS['CONFIG_SHOP_REFER'])), 'shop');

		}
		elseif(isset($_GET['delete']) && isset($_GET['vote']) && isset($GLOBALS['CONFIG_SHOP_VOTE'][$_GET['delete']]))
		{

			unset($GLOBALS['CONFIG_SHOP_VOTE'][$_GET['delete']]);

			Configs::update_configs(array('CONFIG_SHOP_VOTE' => Configs::remake_array($GLOBALS['CONFIG_SHOP_VOTE'])), 'shop');

		}
		elseif(isset($_GET['delete']) && isset($_GET['donate']) && isset($GLOBALS['CONFIG_SHOP_DONATE'][$_GET['delete']]))
		{

			unset($GLOBALS['CONFIG_SHOP_DONATE'][$_GET['delete']]);

			Configs::update_configs(array('CONFIG_SHOP_DONATE' => Configs::remake_array($GLOBALS['CONFIG_SHOP_DONATE'])), 'shop');

		}	
		elseif(isset($_POST['refer_item_name']) && isset($_POST['refer_item_id']) && isset($_POST['refer_item_count']) && isset($_POST['refer_item_price']) && isset($_POST['add_refer']))
			if($_POST['refer_item_name'] != '' && trim($_POST['refer_item_id']) != '' && trim($_POST['refer_item_count']) != '' && trim($_POST['refer_item_enchant']) != '' && trim($_POST['refer_item_price']) != '')
			{	

				$items_ids = preg_split('/[\s,]+/', trim($_POST['refer_item_id']));

				$GLOBALS['CONFIG_SHOP_REFER'][] = array($_POST['refer_item_name'], $items_ids, trim($_POST['refer_item_count']), trim($_POST['refer_item_price']), trim($_POST['refer_item_enchant']));

				Configs::update_configs(array('CONFIG_SHOP_REFER' => Configs::remake_array($GLOBALS['CONFIG_SHOP_REFER'])), 'shop');

			}
			else
			{

				$template_vars['val_refer_item_name'] = $_POST['refer_item_name'];

				$template_vars['val_refer_item_id'] = trim($_POST['refer_item_id']);

				$template_vars['val_refer_item_count'] = trim($_POST['refer_item_count']);

				$template_vars['val_refer_item_enchant'] = trim($_POST['refer_item_enchant']);

				$template_vars['val_refer_item_price'] = trim($_POST['refer_item_price']);

			}
		elseif(isset($_POST['vote_item_name']) && isset($_POST['vote_item_id']) && isset($_POST['vote_item_count']) && isset($_POST['vote_item_price']) && isset($_POST['add_vote']))
			if($_POST['vote_item_name'] != '' && trim($_POST['vote_item_id']) != '' && trim($_POST['vote_item_count']) != '' && trim($_POST['vote_item_enchant']) != '' && trim($_POST['vote_item_price']) != '')
			{	

				$items_ids = preg_split('/[\s,]+/', trim($_POST['vote_item_id']));

				$GLOBALS['CONFIG_SHOP_VOTE'][] = array($_POST['vote_item_name'], $items_ids, trim($_POST['vote_item_count']), trim($_POST['vote_item_price']), trim($_POST['vote_item_enchant']));

				Configs::update_configs(array('CONFIG_SHOP_VOTE' => Configs::remake_array($GLOBALS['CONFIG_SHOP_VOTE'])), 'shop');

			}
			else
			{

				$template_vars['val_vote_item_name'] = $_POST['vote_item_name'];

				$template_vars['val_vote_item_id'] = trim($_POST['vote_item_id']);

				$template_vars['val_vote_item_count'] = trim($_POST['vote_item_count']);

				$template_vars['val_vote_item_enchant'] = trim($_POST['vote_item_enchant']);

				$template_vars['val_vote_item_price'] = trim($_POST['vote_item_price']);

			}
		elseif(isset($_POST['donate_item_name']) && isset($_POST['donate_item_id']) && isset($_POST['donate_item_count']) && isset($_POST['donate_item_price']) && isset($_POST['add_donate']))
			if($_POST['donate_item_name'] != '' && trim($_POST['donate_item_id']) != '' && trim($_POST['donate_item_count']) != '' && trim($_POST['donate_item_enchant']) != '' && trim($_POST['donate_item_price']) != '')
			{	

				$items_ids = preg_split('/[\s,]+/', trim($_POST['donate_item_id']));

				$GLOBALS['CONFIG_SHOP_DONATE'][] = array($_POST['donate_item_name'], $items_ids, trim($_POST['donate_item_count']), trim($_POST['donate_item_price']), trim($_POST['donate_item_enchant']));

				Configs::update_configs(array('CONFIG_SHOP_DONATE' => Configs::remake_array($GLOBALS['CONFIG_SHOP_DONATE'])), 'shop');

			}
			else
			{

				$template_vars['val_donate_item_name'] = $_POST['donate_item_name'];

				$template_vars['val_donate_item_id'] = trim($_POST['donate_item_id']);

				$template_vars['val_donate_item_count'] = trim($_POST['donate_item_count']);

				$template_vars['val_donate_item_enchant'] = trim($_POST['donate_item_enchant']);

				$template_vars['val_donate_item_price'] = trim($_POST['donate_item_price']);

			}

		$template_vars['system_status'] = $GLOBALS['CONFIG_SHOP_ENABLED'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['refer_shop_status'] = $GLOBALS['CONFIG_SHOP_REFER_ENABLED'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['vote_shop_status'] = $GLOBALS['CONFIG_SHOP_VOTE_ENABLED'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['donate_shop_status'] = $GLOBALS['CONFIG_SHOP_DONATE_ENABLED'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['reward_settings'] = $GLOBALS['CONFIG_SERVER_TYPE'] == 1 ? 
			<<<SETTINGS
			<tr><th>Cached IP:</th><td><input type="text" name="cached_ip" tabindex="23" value="[config_shop_cached_ip]" /></td></tr>
			<tr><th>Cached Port:</th><td><input type="text" name="cached_port" tabindex="24" value="[config_shop_cached_port]" /></td></tr>
			<tr><th>Cached Admin:</th><td><input type="text" name="cached_admin" tabindex="25" value="[config_shop_cached_admin]" /></td></tr>
SETTINGS
:
			<<<SETTINGS
			<tr><th>Telnet IP:</th><td><input type="text" name="telnet_ip" tabindex="23" value="[config_shop_telnet_ip]" /></td></tr>
			<tr><th>Telnet Port:</th><td><input type="text" name="telnet_port" tabindex="24" value="[config_shop_telnet_port]" /></td></tr>
			<tr><th>Telnet Pass:</th><td><input type="text" name="telnet_pass" tabindex="25" value="[config_shop_telnet_password]" /></td></tr>
SETTINGS;

		$i = 1;

		foreach($GLOBALS['CONFIG_SHOP_REFER'] as $k => $v)
		{

			$shop_page_vars['rank'] = $i;

			$shop_page_vars['item_name'] = $v[0];

			$shop_page_vars['item_id'] = '';

			$z = 0;

			$count = count($v[1]);

			foreach($v[1] as $id)
			{

				$shop_page_vars['item_id'] .= $id.($z == $count - 1 ? '' : ',');

				++$z;

			}

			$shop_page_vars['item_count'] = $v[2];

			$shop_page_vars['item_enchant'] = $v[4];

			$shop_page_vars['item_price'] = $v[3];

			$shop_page_vars['item_listid'] = $k;

			$shop_page_vars['sname'] = 'refer';

			$template_vars['refer_shop_items'] .= Template::load('administration/styles/shop_list.html', $shop_page_vars, 0);

			++$i;

		}

		$i = 1;

		foreach($GLOBALS['CONFIG_SHOP_VOTE'] as $k => $v)
		{

			$shop_page_vars['rank'] = $i;

			$shop_page_vars['item_name'] = $v[0];

			$shop_page_vars['item_id'] = '';

			$z = 0;

			$count = count($v[1]);

			foreach($v[1] as $id)
			{

				$shop_page_vars['item_id'] .= $id.($z == $count - 1 ? '' : ',');

				++$z;

			}

			$shop_page_vars['item_count'] = $v[2];

			$shop_page_vars['item_enchant'] = $v[4];

			$shop_page_vars['item_price'] = $v[3];

			$shop_page_vars['item_listid'] = $k;

			$shop_page_vars['sname'] = 'vote';

			$template_vars['vote_shop_items'] .= Template::load('administration/styles/shop_list.html', $shop_page_vars, 0);

			++$i;

		}

		$i = 1;

		foreach($GLOBALS['CONFIG_SHOP_DONATE'] as $k => $v)
		{

			$shop_page_vars['rank'] = $i;

			$shop_page_vars['item_name'] = $v[0];

			$shop_page_vars['item_id'] = '';

			$z = 0;

			$count = count($v[1]);

			foreach($v[1] as $id)
			{

				$shop_page_vars['item_id'] .= $id.($z == $count - 1 ? '' : ',');

				++$z;

			}

			$shop_page_vars['item_count'] = $v[2];

			$shop_page_vars['item_enchant'] = $v[4];

			$shop_page_vars['item_price'] = $v[3];

			$shop_page_vars['item_listid'] = $k;

			$shop_page_vars['sname'] = 'donate';

			$template_vars['donate_shop_items'] .= Template::load('administration/styles/shop_list.html', $shop_page_vars, 0);

			++$i;

		}

	}
	elseif(isset($_GET['stats']))
	{

		$template_location[] = 'administration/stats.html';

		$template_vars['page_title'] .= 'Stats System';

		$template_vars['the_status'] = null;

		if(isset($_POST['pvp_system']) && isset($_POST['pvp_guests']) && isset($_POST['pvp_results']) && isset($_POST['pvp_respp']) && isset($_POST['pvp_cache']) && isset($_POST['pk_system']) && isset($_POST['pk_guests']) && isset($_POST['pk_results']) && isset($_POST['pk_respp']) && isset($_POST['pk_cache']) && isset($_POST['raids_system']) && isset($_POST['raids_guests']) && isset($_POST['raids_minl']) && isset($_POST['raids_maxl']) && isset($_POST['raids_cache']) && isset($_POST['castle_system']) && isset($_POST['castle_guests']) && isset($_POST['castle_cache']) && isset($_POST['items_system']) && isset($_POST['items_guests']) && isset($_POST['items_warehouse']) && isset($_POST['items_cache']) && isset($_POST['skills_system']) && isset($_POST['skills_guests']) && isset($_POST['skills_active']) && isset($_POST['skills_passive']) && isset($_POST['skills_cache']) && isset($_POST['tpvp_system']) && isset($_POST['tpvp_cache']) && isset($_POST['tpk_system']) && isset($_POST['tpk_cache']))
		{

			Configs::update_configs(
			array(
			'CONFIG_STATS_TOP_PVP_ENABLED' => '\''.$_POST['pvp_system'].'\'',
			'CONFIG_STATS_TOP_PVP_GUESTS' => '\''.$_POST['pvp_guests'].'\'',
			'CONFIG_STATS_TOP_PVP_RESULTS' => '\''.$_POST['pvp_results'].'\'',
			'CONFIG_STATS_TOP_PVP_RESULTS_PER_PAGE' => '\''.$_POST['pvp_respp'].'\'',
			'CONFIG_STATS_TOP_PVP_CACHE' => '\''.$_POST['pvp_cache'].'\'',
			'CONFIG_STATS_TOP_PK_ENABLED' => '\''.$_POST['pk_system'].'\'',
			'CONFIG_STATS_TOP_PK_GUESTS' => '\''.$_POST['pk_guests'].'\'',
			'CONFIG_STATS_TOP_PK_RESULTS' => '\''.$_POST['pk_results'].'\'',
			'CONFIG_STATS_TOP_PK_RESULTS_PER_PAGE' => '\''.$_POST['pk_respp'].'\'',
			'CONFIG_STATS_TOP_PK_CACHE' => '\''.$_POST['pk_cache'].'\'',
			'CONFIG_STATS_RAID_ENABLED' => '\''.$_POST['raids_system'].'\'',
			'CONFIG_STATS_RAID_GUESTS' => '\''.$_POST['raids_guests'].'\'',
			'CONFIG_STATS_RAID_MIN_LEVEL' => '\''.$_POST['raids_minl'].'\'',
			'CONFIG_STATS_RAID_MAX_LEVEL' => '\''.$_POST['raids_maxl'].'\'',
			'CONFIG_STATS_RAID_CACHE' => '\''.$_POST['raids_cache'].'\'',
			'CONFIG_STATS_CASTLE_ENABLED' => '\''.$_POST['castle_system'].'\'',
			'CONFIG_STATS_CASTLE_GUESTS' => '\''.$_POST['castle_guests'].'\'',
			'CONFIG_STATS_CASTLE_CACHE' => '\''.$_POST['castle_cache'].'\'',
			'CONFIG_STATS_INVENTORY_ENABLED' => '\''.$_POST['items_system'].'\'',
			'CONFIG_STATS_INVENTORY_GUESTS' => '\''.$_POST['items_guests'].'\'',
			'CONFIG_STATS_INVENTORY_WAREHOUSE' => '\''.$_POST['items_warehouse'].'\'',
			'CONFIG_STATS_INVENTORY_CACHE' => '\''.$_POST['items_cache'].'\'',
			'CONFIG_STATS_SKILLS_ENABLED' => '\''.$_POST['skills_system'].'\'',
			'CONFIG_STATS_SKILLS_GUESTS' => '\''.$_POST['skills_guests'].'\'',
			'CONFIG_STATS_SKILLS_ACTIVE' => '\''.$_POST['skills_active'].'\'',
			'CONFIG_STATS_SKILLS_PASSIVE' => '\''.$_POST['skills_passive'].'\'',
			'CONFIG_STATS_SKILLS_CACHE' => '\''.$_POST['skills_cache'].'\'',
			'CONFIG_STATS_TOTAL_PVP_ENABLED' => '\''.$_POST['tpvp_system'].'\'',
			'CONFIG_STATS_TOTAL_PVP_CACHE' => '\''.$_POST['tpvp_cache'].'\'',
			'CONFIG_STATS_TOTAL_PK_ENABLED' => '\''.$_POST['tpk_system'].'\'',
			'CONFIG_STATS_TOTAL_PK_CACHE' => '\''.$_POST['tpk_cache'].'\'',
			), 'statistics');

			$GLOBALS['CONFIG_STATS_TOP_PVP_ENABLED'] = $_POST['pvp_system'];

			$GLOBALS['CONFIG_STATS_TOP_PVP_GUESTS'] = $_POST['pvp_guests'];

			$GLOBALS['CONFIG_STATS_TOP_PVP_RESULTS'] = $_POST['pvp_results'];

			$GLOBALS['CONFIG_STATS_TOP_PVP_RESULTS_PER_PAGE'] = $_POST['pvp_respp'];

			$GLOBALS['CONFIG_STATS_TOP_PVP_CACHE'] = $_POST['pvp_cache'];

			$GLOBALS['CONFIG_STATS_TOP_PK_ENABLED'] = $_POST['pk_system'];

			$GLOBALS['CONFIG_STATS_TOP_PK_GUESTS'] = $_POST['pk_guests'];

			$GLOBALS['CONFIG_STATS_TOP_PK_RESULTS'] = $_POST['pk_results'];

			$GLOBALS['CONFIG_STATS_TOP_PK_RESULTS_PER_PAGE'] = $_POST['pk_respp'];

			$GLOBALS['CONFIG_STATS_TOP_PK_CACHE'] = $_POST['pk_cache'];

			$GLOBALS['CONFIG_STATS_RAID_ENABLED'] = $_POST['raids_system'];

			$GLOBALS['CONFIG_STATS_RAID_GUESTS'] = $_POST['raids_guests'];

			$GLOBALS['CONFIG_STATS_RAID_MIN_LEVEL'] = $_POST['raids_minl'];

			$GLOBALS['CONFIG_STATS_RAID_MAX_LEVEL'] = $_POST['raids_maxl'];

			$GLOBALS['CONFIG_STATS_RAID_CACHE'] = $_POST['raids_cache'];

			$GLOBALS['CONFIG_STATS_CASTLE_ENABLED'] = $_POST['castle_system'];

			$GLOBALS['CONFIG_STATS_CASTLE_GUESTS'] = $_POST['castle_guests'];

			$GLOBALS['CONFIG_STATS_CASTLE_CACHE'] = $_POST['castle_cache'];

			$GLOBALS['CONFIG_STATS_INVENTORY_ENABLED'] = $_POST['items_system'];

			$GLOBALS['CONFIG_STATS_INVENTORY_GUESTS'] = $_POST['items_guests'];

			$GLOBALS['CONFIG_STATS_INVENTORY_WAREHOUSE'] = $_POST['items_warehouse'];

			$GLOBALS['CONFIG_STATS_INVENTORY_CACHE'] = $_POST['items_cache'];

			$GLOBALS['CONFIG_STATS_SKILLS_ENABLED'] = $_POST['skills_system'];

			$GLOBALS['CONFIG_STATS_SKILLS_GUESTS'] = $_POST['skills_guests'];

			$GLOBALS['CONFIG_STATS_SKILLS_ACTIVE'] = $_POST['skills_active'];

			$GLOBALS['CONFIG_STATS_SKILLS_PASSIVE'] = $_POST['skills_passive'];

			$GLOBALS['CONFIG_STATS_SKILLS_CACHE'] = $_POST['skills_cache'];

			$GLOBALS['CONFIG_STATS_TOTAL_PVP_ENABLED'] = $_POST['tpvp_system'];

			$GLOBALS['CONFIG_STATS_TOTAL_PVP_CACHE'] = $_POST['tpvp_cache'];

			$GLOBALS['CONFIG_STATS_TOTAL_PK_ENABLED'] = $_POST['tpk_system'];

			$GLOBALS['CONFIG_STATS_TOTAL_PK_CACHE'] = $_POST['tpk_cache'];

			$template_vars['the_status'] = 'Stats system settings saved!';

		}

		$template_vars['pvp_status'] = $GLOBALS['CONFIG_STATS_TOP_PVP_ENABLED'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['pk_status'] = $GLOBALS['CONFIG_STATS_TOP_PK_ENABLED'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['raids_status'] = $GLOBALS['CONFIG_STATS_RAID_ENABLED'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['castle_status'] = $GLOBALS['CONFIG_STATS_CASTLE_ENABLED'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['items_status'] = $GLOBALS['CONFIG_STATS_INVENTORY_ENABLED'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['skills_status'] = $GLOBALS['CONFIG_STATS_SKILLS_ENABLED'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['tpvp_status'] = $GLOBALS['CONFIG_STATS_TOTAL_PVP_ENABLED'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['tpk_status'] = $GLOBALS['CONFIG_STATS_TOTAL_PK_ENABLED'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['pvp_guests'] = $GLOBALS['CONFIG_STATS_TOP_PVP_GUESTS'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['pk_guests'] = $GLOBALS['CONFIG_STATS_TOP_PK_GUESTS'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['raids_guests'] = $GLOBALS['CONFIG_STATS_RAID_GUESTS'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['castle_guests'] = $GLOBALS['CONFIG_STATS_CASTLE_GUESTS'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['items_guests'] = $GLOBALS['CONFIG_STATS_INVENTORY_GUESTS'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['skills_guests'] = $GLOBALS['CONFIG_STATS_SKILLS_GUESTS'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['items_warehouse'] = $GLOBALS['CONFIG_STATS_INVENTORY_WAREHOUSE'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['skills_active'] = $GLOBALS['CONFIG_STATS_SKILLS_ACTIVE'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['skills_passive'] = $GLOBALS['CONFIG_STATS_SKILLS_PASSIVE'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

	}
	elseif(isset($_GET['flood']))
	{

		$template_location[] = 'administration/flood.html';

		$template_vars['page_title'] .= 'Flood System';

		$template_vars['the_status'] = null;

		if(isset($_POST['main_bypass']) && isset($_POST['main_time']) && isset($_POST['register_bypass']) && isset($_POST['register_time']) && isset($_POST['recover_bypass']) && isset($_POST['recover_time']) && isset($_POST['cpass_bypass']) && isset($_POST['cpass_time']))
		{

			Configs::update_configs(array(
			'CONFIG_MAIN_FLOOD_BYPASS' => '\''.$_POST['main_bypass'].'\'',
			'CONFIG_MAIN_FLOOD_TIME' => '\''.$_POST['main_time'].'\'',
			'CONFIG_REGISTER_FLOOD_BYPASS' => '\''.$_POST['register_bypass'].'\'',
			'CONFIG_REGISTER_FLOOD_TIME' => '\''.$_POST['register_time'].'\'',
			'CONFIG_RECOVER_FLOOD_BYPASS' => '\''.$_POST['recover_bypass'].'\'',
			'CONFIG_RECOVER_FLOOD_TIME' => '\''.$_POST['recover_time'].'\'',
			'CONFIG_CPASS_FLOOD_BYPASS' => '\''.$_POST['cpass_bypass'].'\'',
			'CONFIG_CPASS_FLOOD_TIME' => '\''.$_POST['cpass_time'].'\'',
			), 'flood');

			$GLOBALS['CONFIG_MAIN_FLOOD_BYPASS'] = $_POST['main_bypass'];

			$GLOBALS['CONFIG_MAIN_FLOOD_TIME'] = $_POST['main_time'];

			$GLOBALS['CONFIG_REGISTER_FLOOD_BYPASS'] = $_POST['register_bypass'];

			$GLOBALS['CONFIG_REGISTER_FLOOD_TIME'] = $_POST['register_time'];

			$GLOBALS['CONFIG_RECOVER_FLOOD_BYPASS'] = $_POST['recover_bypass'];

			$GLOBALS['CONFIG_RECOVER_FLOOD_TIME'] = $_POST['recover_time'];

			$GLOBALS['CONFIG_CPASS_FLOOD_BYPASS'] = $_POST['cpass_bypass'];

			$GLOBALS['CONFIG_CPASS_FLOOD_TIME'] = $_POST['cpass_time'];

			$template_vars['the_status'] = 'Flood system settings saved!';

		}

	}
	elseif(isset($_GET['others']))
	{

		$template_location[] = 'administration/others.html';

		$template_vars['page_title'] .= 'Others';

		$template_vars['the_status'] = null;

		if(isset($_POST['logged_cache']) && isset($_POST['server_status']) && isset($_POST['ls_ip']) && isset($_POST['ls_port']) && isset($_POST['gs_ip']) && isset($_POST['gs_port']) && isset($_POST['status_cache']) && isset($_POST['players_online']) && isset($_POST['register_activation']) && isset($_POST['recover_pass']) && isset($_POST['change_pass']) && isset($_POST['act_session']) && isset($_POST['rec_session']) && isset($_POST['refer_system']) && isset($_POST['exec_time']) && isset($_POST['total_queries']) && isset($_POST['debug']) && isset($_POST['online_cache']) && isset($_POST['save']))
		{

			Configs::update_configs(array(
			'CONFIG_CHECK_LOGGED' => '\''.$_POST['logged_cache'].'\'',
			'CONFIG_ENABLE_STATUS' => '\''.$_POST['server_status'].'\'',
			'CONFIG_STATUS_LOGIN_IP' => '\''.$_POST['ls_ip'].'\'',
			'CONFIG_STATUS_LOGIN_PORT' => '\''.$_POST['ls_port'].'\'',
			'CONFIG_STATUS_SERVER_IP' => '\''.$_POST['gs_ip'].'\'',
			'CONFIG_STATUS_SERVER_PORT' => '\''.$_POST['gs_port'].'\'',
			'CONFIG_STATUS_CACHE' =>'\''.$_POST['status_cache'].'\'',
			'CONFIG_ENABLE_ONLINE' => '\''.$_POST['players_online'].'\'',
			'CONFIG_ONLINE_CACHE' => '\''.$_POST['online_cache'].'\'',
			'CONFIG_REGISTER_ACTIVATION' => '\''.$_POST['register_activation'].'\'',
			'CONFIG_RECOVER_PASS' => '\''.$_POST['recover_pass'].'\'',
			'CONFIG_CHANGE_PASS' => '\''.$_POST['change_pass'].'\'',
			'CONFIG_REGISTER_ACTIVATION_SESS_EXPIRE' => '\''.$_POST['act_session'].'\'',
			'CONFIG_RECOVER_SESS_EXPIRE' => '\''.$_POST['rec_session'].'\'',
			'CONFIG_REFER_SYSTEM' => '\''.$_POST['refer_system'].'\'',
			'CONFIG_EXEC_TIME' => '\''.$_POST['exec_time'].'\'',
			'CONFIG_QUERY_COUNT' => '\''.$_POST['total_queries'].'\'',
			'CONFIG_DEV_DEBUG' => '\''.$_POST['debug'].'\'',
			), 'main');

			$GLOBALS['CONFIG_CHECK_LOGGED'] = $_POST['logged_cache'];
			$GLOBALS['CONFIG_ENABLE_STATUS'] = $_POST['server_status'];
			$GLOBALS['CONFIG_STATUS_LOGIN_IP'] = $_POST['ls_ip'];
			$GLOBALS['CONFIG_STATUS_LOGIN_PORT'] = $_POST['ls_port'];
			$GLOBALS['CONFIG_STATUS_SERVER_IP'] = $_POST['gs_ip'];
			$GLOBALS['CONFIG_STATUS_SERVER_PORT'] = $_POST['gs_port'];
			$GLOBALS['CONFIG_STATUS_CACHE'] = $_POST['status_cache'];
			$GLOBALS['CONFIG_ENABLE_ONLINE'] = $_POST['players_online'];
			$GLOBALS['CONFIG_ONLINE_CACHE'] = $_POST['online_cache'];
			$GLOBALS['CONFIG_REGISTER_ACTIVATION'] = $_POST['register_activation'];
			$GLOBALS['CONFIG_RECOVER_PASS'] = $_POST['recover_pass'];
			$GLOBALS['CONFIG_CHANGE_PASS'] = $_POST['change_pass'];
			$GLOBALS['CONFIG_REGISTER_ACTIVATION_SESS_EXPIRE'] = $_POST['act_session'];
			$GLOBALS['CONFIG_RECOVER_SESS_EXPIRE'] = $_POST['rec_session'];
			$GLOBALS['CONFIG_REFER_SYSTEM'] = $_POST['refer_system'];
			$GLOBALS['CONFIG_QUERY_COUNT'] = $_POST['total_queries'];
			$GLOBALS['CONFIG_DEV_DEBUG'] = $_POST['debug'];

			$GLOBALS['DB_QUERY_COUNT'] = 0;

			$template_vars['the_status'] = 'Settings Saved!';

		}

		$template_vars['status_playerson'] = $GLOBALS['CONFIG_ENABLE_ONLINE'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['status_registeract'] = $GLOBALS['CONFIG_REGISTER_ACTIVATION'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['status_recoverpass'] = $GLOBALS['CONFIG_RECOVER_PASS'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['status_changepass'] = $GLOBALS['CONFIG_CHANGE_PASS'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['status_refer'] = $GLOBALS['CONFIG_REFER_SYSTEM'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['status_pagetime'] = $GLOBALS['CONFIG_EXEC_TIME'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['status_tqueries'] = $GLOBALS['CONFIG_QUERY_COUNT'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['status_debug'] = $GLOBALS['CONFIG_DEV_DEBUG'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

		$template_vars['status_server']= $GLOBALS['CONFIG_ENABLE_STATUS'] ? '<option value="1">Enabled</option><option value="0">Disabled</option>' : '<option value="0">Disabled</option><option value="1">Enabled</option>';

	}
	elseif(isset($_GET['logs']))
	{

		$template_vars['page_title'] .= 'Logs';

		if(isset($_GET['donate']))
		{

			$template_location[] = 'administration/donate_logs.html';

			$logs_page_vars = array();

			$template_vars['ok_logs_list'] .= '';

			$log_file = sep_path(CMS_DIR.'/logs/'.md5('donate_paypal_ok'.$GLOBALS['CONFIG_DONATE_LOGS_EXTRA']).'.txt');

			$log_data = array();

			$log_data = unserialize(file_get_contents($log_file));

			$i = 1;

			foreach($log_data as $k)
			{

				$logs_page_vars['rank'] = $i;

				$logs_page_vars['transaction_id'] = $k['txn_id'];

				$logs_page_vars['value'] = $k['mc_gross'];

				$logs_page_vars['currency'] = $k['mc_currency'];

				$logs_page_vars['payer_mail'] = $k['payer_email'];

				$logs_page_vars['account'] = $k['dragon_eye_receiver'];

				$logs_page_vars['pay_method'] = 'PayPal';

				$template_vars['ok_logs_list'] .= Template::load('administration/styles/logs_list.html', $logs_page_vars, 0);

				++$i;

			}

			$template_vars['inv_logs_list'] .= '';

			$log_file = sep_path(CMS_DIR.'/logs/'.md5('donate_paypal_inv'.$GLOBALS['CONFIG_DONATE_LOGS_EXTRA']).'.txt');

			$log_data = array();

			$log_data = unserialize(file_get_contents($log_file));

			$i = 1;

			foreach($log_data as $k)
			{

				$logs_page_vars['rank'] = $i;

				$logs_page_vars['transaction_id'] = $k['txn_id'];

				$logs_page_vars['value'] = $k['mc_gross'];

				$logs_page_vars['currency'] = $k['mc_currency'];

				$logs_page_vars['payer_mail'] = $k['payer_email'];

				$logs_page_vars['account'] = $k['dragon_eye_receiver'];

				$logs_page_vars['pay_method'] = 'PayPal';

				$template_vars['inv_logs_list'] .= Template::load('administration/styles/logs_list.html', $logs_page_vars, 0);

				++$i;

			}

		}
		//elseif(isset($_GET['vote']))
		//{

			// Vote Logs Coming soon

		//}
		else
			$template_location[] = 'administration/logs.html';

	}
	else
	{

		$template_vars['page_title'] .= 'Home';
		$template_location[] = 'administration/dashboard.html';

	}

	$template_location[] = 'administration/footer.html';

}
