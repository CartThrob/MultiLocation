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