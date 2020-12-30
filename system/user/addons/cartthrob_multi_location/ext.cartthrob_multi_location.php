<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * Price field Changer for CartThrob Extension
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Extension
 * @author		Chris Newton (MIghtyBigRobot)
 * @link		http://cartthrob.com
 */

class Cartthrob_multi_location_ext {
	
	public $settings 		= [];
	public $description		= "Generic Description";
	public $docs_url		= 'http://cartthrob.com';
	public $name			= "Cartthrob Addon";
	public $settings_exist	= 'n';
	public $version; 
	public $required_by 	= array('module');
	public $testing 		= FALSE; // either FALSE, or 2 char country code.  
 	private $module_name; 
	private $remove_keys = array(
		'name',
		'submit',
		'x',
		'y',
		'templates',
		'XID',
	);
	
	private $EE;
	
	/**
	 * Constructor
	 *
	 * @param 	mixed	Settings array or empty string if none exist.
	 */
	public function __construct($settings = '')
	{
		$this->module_name = strtolower(str_replace(array('_ext', '_mcp', '_upd'), "", __CLASS__));
		
		ee()->lang->loadfile($this->module_name);
		
		include PATH_THIRD.$this->module_name.'/config'.EXT;
		$this->name= $config['name']; 
		$this->version = $config['version'];
		$this->description = lang($this->module_name. "_description"); 
		
		ee()->load->add_package_path(PATH_THIRD.'cartthrob/');
		ee()->load->add_package_path(PATH_THIRD.$this->module_name."/");

		$this->params = array(
			'module_name'	=> $this->module_name,
			);

 		ee()->load->library('mbr_addon_builder');
		ee()->mbr_addon_builder->initialize($this->params);
		
		ee()->load->library('get_settings');
		$this->settings = ee()->get_settings->settings($this->module_name);
	}
	
	/**
	 * Activate Extension
	 *
	 * This function enters the extension into the exp_extensions table
	 *
	 * @see http://codeigniter.com/user_guide/database/index.html for
	 * more information on the db class.
	 *
	 * @return void
	 */
	public function activate_extension()
	{
		return ee()->mbr_addon_builder->activate_extension();
	}

    /**
     * @param $row
     */
 	public function template_fetch_template($row)
	{
		// sometimes if an ajax request is run, this method will chew up a LOT of memory unecessarily
		if (AJAX_REQUEST)
		{
			return; 
		}
 		static $once;

		if ( ! is_null($once))
		{
			return;
		}
		$once = TRUE;
		
		$this->ct_price_field_update(); 
  	}

    /**
     *
     */
	public function cartthrob_update_cart_end()
	{
 		ee()->load->library('cartthrob_loader');
		$cart_hash = $this->cart_hash();
		ee()->cartthrob->cart->set_custom_data('update_hash', $cart_hash);
		ee()->cartthrob->cart->save();
		
		$this->ct_price_field_update(); 
	}

    /**
     * @param $ip
     * @return string
     */
    function inet_ntoa($ip)
    {
        $long = 4294967295 - ($ip - 1);
        return long2ip(-$long);
    }

    /**
     * @param $ip
     * @return int
     */
	function inet_aton($ip)
	{
		return ip2long($ip); 
	}

    /**
     * @return null|string
     */
	public function cart_hash()
	{
 		$array = ee()->cartthrob->cart_array();
		$cart_hash = NULL; 
		if (!empty($array['config']))
		{
			$cart_hash =  md5(serialize( $array['config'] ));
		}

		return $cart_hash; 
	}

    /**
     *
     */
	public function ct_price_field_update()
	{
 		ee()->load->library('cartthrob_loader');
		
		ee()->load->library('locales');
		ee()->load->model('cartthrob_field_model');
		$european_union_array = array('AUT','BEL','BGR','CYP','CZE','DNK','EST','FIN','FRA','DEU','GRC','HUN','IRL','ITA','LVA','LTU','LUX','MLT','NLD','POL','PRT','ROU', 'ROM','SVK','SVN','ESP','SWE','GBR'); 
	
		$europe_array = array_merge(array(
				'HRV',
				'MKD',
				'ISL',
				'MNE',
				'SRB',
				'TUR',
				'ALB',
				'AND',
				'ARM',
				'AZE',
				'BLR',
				'BIH',
				'GEO',
				'LIE',
				'MDA',
				'MCO',
				'NOR',
				'RUS',
				'SMR', 
				'CHE',
				'UKR',
				'VAT'
			
			), $european_union_array); 
		
		$us_offshore = array('HI', 'AK'); 
		
		$prefix= $this->settings['location_field']; 
		
		$country_code =	ee()->locales->alpha3_country_code(ee()->cartthrob->store->config('default_location', $prefix. "country_code"));
		$state = ee()->cartthrob->store->config('default_location', $prefix. "state");
		
		if (  ee()->db->table_exists('ip2nation'))
		{
			ee()->load->add_package_path(APPPATH.'modules/ip_to_nation/');
			ee()->load->model('ip_to_nation_data', 'ip_data');
			$country_code = ee()->ip_data->find( ee()->input->ip_address() );

			// Bypass for testing
			if ($this->testing)
			{
				$country_code = $this->testing;
			}

			if ($country_code !== FALSE)
			{   
				if ( ! isset(ee()->session->cache['ip_to_nation']['countries']))
				{
					if ( include(APPPATH.'config/countries.php'))
					{
						ee()->session->cache['ip_to_nation']['countries'] = $countries; // the countries.php file above contains the countries variable.
					}
				}
				$country_code =  strtoupper($country_code); 
				// damn you UK and your alpha3 exceptions
				if ($country_code == "UK") $country_code = "GB"; 
			}
			$country_code = ee()->locales->alpha3_country_code($country_code);
		} 
 		if (ee()->cartthrob->cart->customer_info($prefix."country_code"))
		{
			$country_code = ee()->cartthrob->cart->customer_info($prefix."country_code");
		}
		else
		{
			ee()->cartthrob->cart->set_customer_info($prefix."country_code", $country_code);
		}
		if (ee()->cartthrob->cart->customer_info($prefix."state"))
		{
			$state = ee()->cartthrob->cart->customer_info($prefix."state");
		}
		$country_code = ee()->locales->alpha3_country_code($country_code);
 		
  		if ( ee()->cartthrob->cart->custom_data('cartthrob_multi_location_country_code'))
		{
  			ee()->cartthrob->cart->set_custom_data('cartthrob_multi_location_last_country_code', ee()->cartthrob->cart->custom_data('cartthrob_multi_location_country_code'));
		}
 		
		if ($country_code)
		{
			ee()->cartthrob->cart->set_custom_data('cartthrob_multi_location_country_code', $country_code);
		}
		ee()->cartthrob->cart->save();
		
		$product_channel_fields = (ee()->cartthrob->store->config('product_channel_fields')) ? ee()->cartthrob->store->config('product_channel_fields') : [];
		
		$set_fields = [];
		$set_shipping_fields = [];
		if (isset($this->settings['fields']))
		{
			foreach ($this->settings['fields'] as $field_data)
			{
				if (!in_array($field_data['product_channel'], $set_fields) && 
								($country_code == $field_data['country'] 
								|| $field_data['country'] == 'global' 
								|| ($field_data['country'] == "non-continental_us" && in_array($state, $us_offshore)) 
								|| ($field_data['country'] == "europe" && in_array($country_code, $europe_array))
								|| ($field_data['country'] == "european_union" && in_array($country_code, $european_union_array))))
				{
	 				$channel_id = $field_data['product_channel']; 
					$field_id =  str_replace("field_id_",  "", $field_data['product_channel_fields']); 
					$product_channel_fields[$channel_id]['price'] = $field_id;
					ee()->cartthrob->cart->set_custom_data('cartthrob_price_field_updated', TRUE);
					
					// this tells the system that we've already set a field for this. if it comes up again: ignore it
					$set_fields[] = $field_data['product_channel']; 
				}
				
				if (!in_array($field_data['product_channel'], $set_shipping_fields) && 
								($country_code == $field_data['country'] 
								|| $field_data['country'] == 'global' 
								|| ($field_data['country'] == "non-continental_us" && in_array($state, $us_offshore)) 
								|| ($field_data['country'] == "europe" && in_array($country_code, $europe_array))
								|| ($field_data['country'] == "european_union" && in_array($country_code, $european_union_array))))
				{
	 				$channel_id = $field_data['product_channel']; 
					$field_id =  str_replace("field_id_",  "", $field_data['shipping_fields']); 
					
					$product_channel_fields[$channel_id]['shipping'] = $field_id;
					ee()->cartthrob->cart->set_custom_data('cartthrob_shipping_field_updated', TRUE);
					
					// this tells the system that we've already set a field for this. if it comes up again: ignore it
					$set_shipping_fields[] = $field_data['product_channel']; 
				}
			}
		}
		
		if (isset($this->settings['other']))
		{
			foreach($this->settings['other'] as $other)
			{
				if ( $country_code == $other['country'] 
					|| $other['country'] == 'global' 
					|| ($other['country'] == "non-continental_us" && in_array($state, $us_offshore)) 
					|| ($other['country'] == "europe" && in_array($country_code, $europe_array))
					|| ($other['country'] == "european_union" && in_array($country_code, $european_union_array)))
				{ 
					if (!empty($other['currency_code']))
					{
						ee()->cartthrob->cart->set_config('number_format_defaults_currency_code', $other['currency_code']);
						// people expect the customers info to be updated automatically. 
						ee()->cartthrob->cart->set_customer_info("currency_code", $other['currency_code']);
					}
					if (!empty($other['prefix']))
					{
 						ee()->cartthrob->cart->set_config('number_format_defaults_prefix', $other['prefix']);

					}
					if (!empty($other['dec_point']))
					{
						switch($other['dec_point'])
						{
							case "comma": 
								$dec_point = ","; 
								break;
							case "period": 
								$dec_point = "."; 
								break;
							default: 
								$dec_point = "."; 
						}
						ee()->cartthrob->cart->set_config('number_format_defaults_dec_point', $dec_point);

					}
					if (!empty($other['tax_plugin']))
					{
						ee()->cartthrob->cart->set_config('tax_plugin', $other['tax_plugin']);

					}
					if (!empty($other['shipping_plugin']))
					{
						ee()->cartthrob->cart->set_config('shipping_plugin', $other['shipping_plugin']);
					}
 					break ; 
				}
			}
		}

		if (isset($this->settings['configuration_settings']))
		{
			foreach($this->settings['configuration_settings'] as $conf_setting)
			{
 				if (!empty($conf_setting['custom_data_key']) && !empty($conf_setting['set_config']))
				{
					if (trim($conf_setting['custom_data']) == "GLOBAL" || trim($conf_setting['custom_data']) ==  ee()->cartthrob->cart->custom_data(strtolower(trim($conf_setting['custom_data_key']))))
					{
						// checking meta for the original setting before being configured by this.
						if (! ee()->cartthrob->cart->meta('original_'.$conf_setting['set_config']) !== FALSE)
						{
							// setting a default back
							ee()->cartthrob->cart->set_meta('original_'.$conf_setting['set_config'], ee()->cartthrob->store->config($conf_setting['set_config']));
						}
						ee()->cartthrob->cart->set_config($conf_setting['set_config'], $conf_setting['set_config_value']);
						ee()->cartthrob->cart->save();
						
					}
					else
					{
						// if there's a default, we'll set the value back to it, because the setting was changed. 
						if (ee()->cartthrob->cart->meta('original_'.$conf_setting['set_config']) !== FALSE)
						{
							ee()->cartthrob->cart->set_config($conf_setting['set_config'],  ee()->cartthrob->cart->meta('original_'.$conf_setting['set_config']) );
							ee()->cartthrob->cart->save();
						}
					}
				}
 			}
		}

		// overriding if manually set. 
		foreach (ee()->cartthrob->cart->custom_data() as $key => $data)
		{
			if (strpos($key,"pricefield_")!==FALSE)
			{
 				list(,$channel_id) = explode("_", $key); 
				$product_channel_fields[$channel_id]['price'] =  ee()->cartthrob_field_model->get_field_id( $data );
			}
			if (strpos($key,"shippingfield_")!==FALSE)
			{
 				list(,$channel_id) = explode("_", $key); 
				$product_channel_fields[$channel_id]['shipping'] =  ee()->cartthrob_field_model->get_field_id( $data );
			}
 		}

 		ee()->cartthrob->cart->set_config('product_channel_fields', $product_channel_fields);
		ee()->cartthrob->cart->save();
 	}

    /**
     * @param string $current
     * @return mixed
     */
	public function update_extension($current='')
	{
		return ee()->mbr_addon_builder->update_extension($current);
	}

    /**
     * @return mixed
     */
	public function disable_extension()
	{
		return ee()->mbr_addon_builder->disable_extension();
	}

    /**
     * @return array
     */
	public function settings()
	{
		return [];
	}
}