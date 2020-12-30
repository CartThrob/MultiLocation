<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cartthrob_multi_location_mcp {

	private $module_name;
	public $required_settings = array();
	public $template_errors = array();
	public $templates_installed = array();
	public $extension_enabled = 0;
	public $module_enabled = 0;
	public $required_by 	= array('extension');
	
	public $version;
	
	private $initialized = FALSE;
	
	public $nav = array(
	);
	
	public $no_nav = array(
	); 
 	private $remove_keys = array(
		'name',
		'submit',
		'x',
		'y',
		'templates',
		'XID',
	);
	public $params; 
	
	public $cartthrob, $store, $cart;
	
	
    function Cartthrob_multi_location_mcp()
    {
		$this->module_name = strtolower(str_replace(array('_ext', '_mcp', '_upd'), "", __CLASS__));
	
        $this->EE =& get_instance();
		$this->EE->load->add_package_path(PATH_THIRD.$this->module_name.'/');
		include PATH_THIRD.$this->module_name.'/config'.EXT;
		
		$this->EE->load->add_package_path(PATH_THIRD.'cartthrob/'); 
		$this->EE->load->library('cartthrob_loader'); 
    }

	private function initialize()
	{
		$this->params['module_name']	= $this->module_name; 
		$this->params['nav'] = array(
 			'index' => array(
				'system_settings' => $this->EE->lang->line('nav_general_settings'."_".$this->module_name),
			)
		); 
 
 		$this->params['no_form'] = array(
			'edit',
			'delete',
			'add',
			'view',
			'view_subscriptions',
  		);
		$this->params['no_nav'] = array(
			'edit',
			'delete'
		);
		
 		$this->EE->load->library('mbr_addon_builder');
		$this->EE->mbr_addon_builder->initialize($this->params);
	}
	public function quick_save()
	{
		return $this->EE->mbr_addon_builder->quick_save();
	 	
	}
	public function index()
	{	
		$this->initialize();
		
		$structure['class']	= 'price_fields'; 
		$structure['description']	=''; 
		$structure['caption']	=''; 
		$structure['title']	= "price_fields"; 
	 	$structure['settings'] = array(
			array(
				'name' => 'location_field',
				'short_name' => 'location_field',
				'type' => 'select',
				'default'	=> '',
				'options' => array(
					'' => 'billing_address',
					'shipping_' => 'shipping_address'
	 			)
			),
			array(
				'name' => 'fields',
				'short_name' => 'fields',
				'type' => 'matrix',
				'settings' => array(
					array(
						'name'=>'country',
						'short_name'=>'country',
						'type'=>'select',
						'attributes' => array(
							'class' 	=> 'countries',
							),
					),
					array(
						'name'=>'channel',
						'short_name'=>'product_channel',
						'type'=>'select',
						'attributes' => array(
							'class' 	=> 'product_channels',
							),
					),
					array(
						'name'=>'price_fields',
						'short_name'=>'product_channel_fields',
						'type'=>'select',
						'attributes' => array(
							'class' 	=> 'product_channel_fields',
							),
					),
					array(
						'name'=>'shipping_fields',
						'short_name'=>'shipping_fields',
						'type'=>'select',
						'attributes' => array(
							'class' 	=> 'product_channel_fields',
							),
					),
				)
			),
			array(
				'name' => 'other',
				'short_name' => 'other',
				'type' => 'matrix',
				'settings' => array(
					array(
						'name'=>'country',
						'short_name'=>'country',
						'type'=>'select',
						'attributes' => array(
							'class' 	=> 'countries',
							),
					),
					array(
						'name'=>'number_format_defaults_currency_code',
						'short_name'=>'currency_code',
						'type'=>'text',
					),
					array(
						'name'=>'number_format_defaults_prefix',
						'short_name'=>'prefix',
						'type'=>'text',
					),
					array(
						'name'=>'number_format_defaults_dec_point',
						'short_name'=>'dec_point',
						'type'=>'select',
						'options'	=> array('period' => 'period' , 'comma' => 'comma')
					),
					array(
						'name'=>'shipping_plugin',
						'short_name'=>'shipping_plugin',
						'type'=>'select',
						'options'	=> array_merge(array(""=> "---"), $this->EE->mbr_addon_builder->get_shipping_plugins()),
						
					),
					array(
						'name'=>'tax_plugin',
						'short_name'=>'tax_plugin',
						'type'=>'select',
						'options'	=> array_merge(array(""=> "---"), $this->EE->mbr_addon_builder->get_tax_plugins()),
						
					),
				)
			),
			array(
				'name' => 'configuration_settings',
				'short_name' => 'configuration_settings',
				'type' => 'matrix',
				'settings' => array(
					array(
						'name'=>'custom_data_key',
						'short_name'=>'custom_data_key',
						'type'=>'text',
					),
					array(
						'name'=>'matches',
						'short_name'=>'custom_data',
						'type'=>'text',
						'default'	=> 'GLOBAL',
					),
					array(
						'name'=>'change_configuration_option',
						'short_name'=>'set_config',
						'type'=>'select',
						'options'=> array(   
							// added a space after the name so that the config name isn't all langged up
							'',                                      
							'admin_email'								   =>		'admin_email '							   ,
							'allow_empty_cart_checkout'					   =>		'allow_empty_cart_checkout '				   ,
							'allow_gateway_selection'					   =>		'allow_gateway_selection '				   ,
							'allow_products_more_than_once'				   =>		'allow_products_more_than_once '			   ,
							'approve_orders'							   =>		'approve_orders '						   ,
							'checkout_form_captcha'						   =>		'checkout_form_captcha '					   ,
							'checkout_registration_options'				   =>		'checkout_registration_options '			   ,
							'clear_cart_on_logout'						   =>		'clear_cart_on_logout '					   ,
							'clear_session_on_logout'					   =>		'clear_session_on_logout '				   ,
							'coupon_code_channel'						   =>		'coupon_code_channel '					   ,
							'coupon_code_field'							   =>		'coupon_code_field '						   ,
							'coupon_code_type'							   =>		'coupon_code_type '						   ,
							'currency_code'								   =>		'currency_code '							   ,
							'default_member_id'							   =>		'default_member_id '						   ,
							'description'								   =>		'description '							   ,
							'discount_channel'							   =>		'discount_channel '						   ,
							'discount_type'								   =>		'discount_type '							   ,
							'enable_logging'							   =>		'enable_logging '						   ,
							'encode_gateway_selection'					   =>		'encode_gateway_selection '				   ,
							'encrypted_sessions'						   =>		'encrypted_sessions '					   ,
							'global_coupon_limit'						   =>		'global_coupon_limit '					   ,
							'global_item_limit'							   =>		'global_item_limit '						   ,
							'last_order_number'							   =>		'last_order_number '						   ,
							'license_number'							   =>		'license_number '						   ,
							'logged_in'									   =>		'logged_in '								   ,
							'member_address2_field'						   =>		'member_address2_field '					   ,
							'member_address_field'						   =>		'member_address_field '					   ,
							'member_city_field'							   =>		'member_city_field '						   ,
							'member_company_field'						   =>		'member_company_field '					   ,
							'member_country_code_field'					   =>		'member_country_code_field '				   ,
							'member_country_field'						   =>		'member_country_field '					   ,
							'member_email_address_field'				   =>		'member_email_address_field '			   ,
							'member_first_name_field'					   =>		'member_first_name_field '				   ,
							'member_language_field'						   =>		'member_language_field '					   ,
							'member_last_name_field'					   =>		'member_last_name_field '				   ,
							'member_phone_field'						   =>		'member_phone_field '					   ,
							'member_region_field'						   =>		'member_region_field '					   ,
							'member_shipping_address2_field'			   =>		'member_shipping_address2_field '		   ,
							'member_shipping_address_field'				   =>		'member_shipping_address_field '			   ,
							'member_shipping_city_field'				   =>		'member_shipping_city_field '			   ,
							'member_shipping_company_field'				   =>		'member_shipping_company_field '			   ,
							'member_shipping_country_code_field'		   =>		'member_shipping_country_code_field '	   ,
							'member_shipping_country_field'				   =>		'member_shipping_country_field '			   ,
							'member_shipping_first_name_field'			   =>		'member_shipping_first_name_field '		   ,
							'member_shipping_last_name_field'			   =>		'member_shipping_last_name_field '		   ,
							'member_shipping_option_field'				   =>		'member_shipping_option_field '			   ,
							'member_shipping_state_field'				   =>		'member_shipping_state_field '			   ,
							'member_shipping_zip_field'					   =>		'member_shipping_zip_field '				   ,
							'member_state_field'						   =>		'member_state_field '					   ,
							'member_use_billing_info_field'				   =>		'member_use_billing_info_field '			   ,
							'member_zip_field'							   =>		'member_zip_field '						   ,
							'modulus_10_checking'						   =>		'modulus_10_checking '					   ,
							'number_format_defaults_currency_code'		   =>		'number_format_defaults_currency_code '	   ,
							'number_format_defaults_decimals'			   =>		'number_format_defaults_decimals '		   ,
							'number_format_defaults_dec_point'			   =>		'number_format_defaults_dec_point '		   ,
							'number_format_defaults_prefix'				   =>		'number_format_defaults_prefix '			   ,
							'number_format_defaults_prefix_position'	   =>		'number_format_defaults_prefix_position'   ,
							'number_format_defaults_space_after_prefix'	   =>		'number_format_defaults_space_after_prefix ',
							'number_format_defaults_thousands_sep'		   =>		'number_format_defaults_thousands_sep '	   ,
							'orders_billing_address'					   =>		'orders_billing_address '				   ,
							'orders_billing_address2'					   =>		'orders_billing_address2 '				   ,
							'orders_billing_city'						   =>		'orders_billing_city '					   ,
							'orders_billing_company'					   =>		'orders_billing_company '				   ,
							'orders_billing_country'					   =>		'orders_billing_country '				   ,
							'orders_billing_first_name'					   =>		'orders_billing_first_name '				   ,
							'orders_billing_last_name'					   =>		'orders_billing_last_name '				   ,
							'orders_billing_state'						   =>		'orders_billing_state '					   ,
							'orders_billing_zip'						   =>		'orders_billing_zip '					   ,
							'orders_channel'							   =>		'orders_channel '						   ,
							'orders_convert_country_code'				   =>		'orders_convert_country_code '			   ,
							'orders_country_code'						   =>		'orders_country_code '					   ,
							'orders_coupon_codes'						   =>		'orders_coupon_codes '					   ,
							'orders_customer_email'						   =>		'orders_customer_email '					   ,
							'orders_customer_ip_address'				   =>		'orders_customer_ip_address '			   ,
							'orders_customer_name'						   =>		'orders_customer_name '					   ,
							'orders_customer_phone'						   =>		'orders_customer_phone '					   ,
							'orders_declined_status'					   =>		'orders_declined_status '				   ,
							'orders_default_status'						   =>		'orders_default_status '					   ,
							'orders_discount_field'						   =>		'orders_discount_field '					   ,
							'orders_error_message_field'				   =>		'orders_error_message_field '			   ,
							'orders_failed_status'						   =>		'orders_failed_status '					   ,
							'orders_full_billing_address'				   =>		'orders_full_billing_address '			   ,
							'orders_full_shipping_address'				   =>		'orders_full_shipping_address '			   ,
							'orders_items_field'						   =>		'orders_items_field '					   ,
							'orders_language_field'						   =>		'orders_language_field '					   ,
							'orders_last_four_digits'					   =>		'orders_last_four_digits '				   ,
							'orders_license_number_field'				   =>		'orders_license_number_field '			   ,
							'orders_license_number_type'				   =>		'orders_license_number_type '			   ,
							'orders_payment_gateway'					   =>		'orders_payment_gateway '				   ,
							'orders_processing_status'					   =>		'orders_processing_status '				   ,
							'orders_sequential_order_numbers'			   =>		'orders_sequential_order_numbers '		   ,
							'orders_shipping_address'					   =>		'orders_shipping_address '				   ,
							'orders_shipping_address2'					   =>		'orders_shipping_address2 '				   ,
							'orders_shipping_city'						   =>		'orders_shipping_city '					   ,
							'orders_shipping_company'					   =>		'orders_shipping_company '				   ,
							'orders_shipping_country'					   =>		'orders_shipping_country '				   ,
							'orders_shipping_country_code'				   =>		'orders_shipping_country_code '			   ,
							'orders_shipping_field'						   =>		'orders_shipping_field '					   ,
							'orders_shipping_first_name'				   =>		'orders_shipping_first_name '			   ,
							'orders_shipping_last_name'					   =>		'orders_shipping_last_name '				   ,
							'orders_shipping_option'					   =>		'orders_shipping_option '				   ,
							'orders_shipping_plus_tax_field'			   =>		'orders_shipping_plus_tax_field '		   ,
							'orders_shipping_state'						   =>		'orders_shipping_state '					   ,
							'orders_shipping_zip'						   =>		'orders_shipping_zip '					   ,
							'orders_status_canceled'					   =>		'orders_status_canceled '				   ,
							'orders_status_expired'						   =>		'orders_status_expired '					   ,
							'orders_status_field'						   =>		'orders_status_field '					   ,
							'orders_status_offsite'						   =>		'orders_status_offsite '					   ,
							'orders_status_pending'						   =>		'orders_status_pending '					   ,
							'orders_status_refunded'					   =>		'orders_status_refunded '				   ,
							'orders_status_reversed'					   =>		'orders_status_reversed '				   ,
							'orders_status_voided'						   =>		'orders_status_voided '					   ,
							'orders_subtotal_field'						   =>		'orders_subtotal_field '					   ,
							'orders_subtotal_plus_tax_field'			   =>		'orders_subtotal_plus_tax_field '		   ,
							'orders_tax_field'							   =>		'orders_tax_field '						   ,
							'orders_title_prefix'						   =>		'orders_title_prefix '					   ,
							'orders_title_suffix'						   =>		'orders_title_suffix '					   ,
							'orders_total_field'						   =>		'orders_total_field '					   ,
							'orders_transaction_id'						   =>		'orders_transaction_id '					   ,
							'orders_url_title_prefix'					   =>		'orders_url_title_prefix '				   ,
							'orders_url_title_suffix'					   =>		'orders_url_title_suffix '				   ,
							'payment_gateway'							   =>		'payment_gateway '						   ,
							'product_split_items_by_quantity'			   =>		'product_split_items_by_quantity '		   ,
							'purchased_items_channel'					   =>		'purchased_items_channel '				   ,
							'purchased_items_declined_status'			   =>		'purchased_items_declined_status '		   ,
							'purchased_items_default_status'			   =>		'purchased_items_default_status '		   ,
							'purchased_items_failed_status'				   =>		'purchased_items_failed_status '			   ,
							'purchased_items_id_field'					   =>		'purchased_items_id_field '				   ,
							'purchased_items_license_number_field'		   =>		'purchased_items_license_number_field '	   ,
							'purchased_items_license_number_type'		   =>		'purchased_items_license_number_type '	   ,
							'purchased_items_order_id_field'			   =>		'purchased_items_order_id_field '		   ,
							'purchased_items_price_field'				   =>		'purchased_items_price_field '			   ,
							'purchased_items_processing_status'			   =>		'purchased_items_processing_status '		   ,
							'purchased_items_quantity_field'			   =>		'purchased_items_quantity_field '		   ,
							'purchased_items_status_canceled'			   =>		'purchased_items_status_canceled '		   ,
							'purchased_items_status_expired'			   =>		'purchased_items_status_expired '		   ,
							'purchased_items_status_offsite'			   =>		'purchased_items_status_offsite '		   ,
							'purchased_items_status_pending'			   =>		'purchased_items_status_pending '		   ,
							'purchased_items_status_refunded'			   =>		'purchased_items_status_refunded '		   ,
							'purchased_items_status_reversed'			   =>		'purchased_items_status_reversed '		   ,
							'purchased_items_status_voided'				   =>		'purchased_items_status_voided '			   ,
							'purchased_items_title_prefix'				   =>		'purchased_items_title_prefix '			   ,
							'save_member_data'							   =>		'save_member_data '						   ,
							'save_orders'								   =>		'save_orders '							   ,
							'save_purchased_items'						   =>		'save_purchased_items '					   ,
							'session_expire'							   =>		'session_expire '						   ,
							'session_fingerprint_method'				   =>		'session_fingerprint_method '			   ,
							'session_use_fingerprint'					   =>		'session_use_fingerprint '				   ,
							'shipping_plugin'							   =>		'shipping_plugin '						   ,
							'tax_inclusive_price'						   =>		'tax_inclusive_price '					   ,
							'tax_plugin'								   =>		'tax_plugin '							   ,
							'tax_settings'								   =>		'tax_settings '							   ,
							'tax_use_shipping_address'					   =>		'tax_use_shipping_address '				   ,
							'use_profile_edit'							   =>		'use_profile_edit '						   ,
						),
					),
					array(
						'name'=>'option_value',
						'short_name'=>'set_config_value',
						'type'=>'text',
					),
				)
			),
	 	);
	
		$jquery = "
			<script type=\"text/javascript\">
			jQuery(document).ready(function($){
				 /*
				*/
			});
			</script>";
		
		
		// use this to add custom jquery to head that's not found in common JS
		$this->EE->cp->add_to_head($jquery); 
	 	return $this->EE->mbr_addon_builder->load_view(__FUNCTION__, array(), $structure);
	}
}