<?php

if ( ! defined('CT_MULTI_LOCATION_VERSION')) {
	define('CT_MULTI_LOCATION_VERSION', '1.675');
}

if (defined('PATH_THEMES')) {
	if ( ! defined('PATH_THIRD_THEMES')) {
		define('PATH_THIRD_THEMES', PATH_THEMES.'third_party/');
	}
	
	if ( ! defined('URL_THIRD_THEMES')) {
		define('URL_THIRD_THEMES', get_instance()->config->slash_item('theme_folder_url').'third_party/');
	}
}


$config['name'] = 'CartThrob Multi Location';
$config['version'] =  CT_MULTI_LOCATION_VERSION; 
$config['cartthrob_multi_location_description'] = 'Settings manager for CartThrob.';