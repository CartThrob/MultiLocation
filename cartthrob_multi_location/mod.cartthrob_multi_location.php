<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cartthrob_multi_location
{
 	public $return_data; 

	function Cartthrob_multi_location()
	{
		$this->EE =& get_instance();
		$this->EE->load->add_package_path(PATH_THIRD.'cartthrob/');
		$this->EE->load->library('cartthrob_loader');
		$this->EE->load->library('number');
	}
	
	private function cart_hash()
	{
 		$array = $this->EE->cartthrob->cart_array(); 
		$cart_hash = NULL; 
		if (!empty($array['config']))
		{
			$cart_hash =  md5(serialize( $array['config'] ));
		}
		return $cart_hash; 
	}
	
 	public function check_location()
	{
		$cart_hash = $this->cart_hash(); 
		
 		if ($this->EE->cartthrob->cart->custom_data('update_hash') != $cart_hash)
		{
			$this->EE->cartthrob->cart->set_custom_data('update_hash', $cart_hash); 
			$this->EE->cartthrob->cart->save();
  			return $this->EE->TMPL->fetch_param('message');
		}
	}
	
	public function price()
	{
		$this->EE->load->library('number');
		$this->EE->load->model('product_model'); 
		$this->EE->load->library('api/api_cartthrob_tax_plugins');
		
		$price = 0;
		$price_plus_tax = 0; 
		if ($this->EE->TMPL->fetch_param('entry_id'))
		{
			$product = $this->EE->product_model->get_product($this->EE->TMPL->fetch_param('entry_id')); 
	 		$price =  $this->EE->number->format( $product['price'] );
			$price_plus_tax = $product['price'] + $this->EE->api_cartthrob_tax_plugins->get_tax($product['price']);
	
			if (isset($this->EE->TMPL->tagparts[2]) && $this->EE->TMPL->tagparts[2] === 'plus_tax')
			{
				return $this->EE->number->format( $price_plus_tax ); 
			}
			if (isset($this->EE->TMPL->tagparts[2]) && $this->EE->TMPL->tagparts[2] === 'plus_tax_numeric')
			{
				return $price_plus_tax; 
			}
 		}

		if ($item = $this->EE->cartthrob->cart->item($this->EE->TMPL->fetch_param('row_id')))
		{
			$price =$item->price();
			$price_plus_tax = $price + $this->EE->api_cartthrob_tax_plugins->get_tax($price, $item);

			if (isset($this->EE->TMPL->tagparts[2]) && $this->EE->TMPL->tagparts[2] === 'plus_tax')
			{
				return $this->EE->number->format( $price_plus_tax ); 
			}
			if (isset($this->EE->TMPL->tagparts[2]) && $this->EE->TMPL->tagparts[2] === 'plus_tax_numeric')
			{
				return $price_plus_tax; 
			}
		}
		
		if (isset($this->EE->TMPL->tagparts[2]) && $this->EE->TMPL->tagparts[2] === 'numeric')
		{
			return $price;
		}
		
		return $this->EE->number->format( $price); 
	}
	
 	public function shipping()
	{
		$this->EE->load->model('product_model'); 
		
		if ($this->EE->TMPL->fetch_param('entry_id'))
		{
			$product = $this->EE->product_model->get_product($this->EE->TMPL->fetch_param('entry_id')); 
			$this->EE->load->library('number');
	 		return $this->EE->number->format( $product['shipping'] );
 		}
	}
 
 	public function save_codes()
	{
		if ($this->EE->TMPL->fetch_param("currency_code"))
		{
			$_POST['currency_code'] =  $this->EE->TMPL->fetch_param("currency_code"); 

			$this->EE->cartthrob->cart->set_config('number_format_defaults_currency_code',  $this->EE->TMPL->fetch_param("currency_code") );
			$this->EE->cartthrob->cart->set_customer_info("currency_code", $this->EE->TMPL->fetch_param("currency_code")); 
		}
		
		if ($this->EE->TMPL->fetch_param("country_code"))
		{
			$_POST['country_code'] =  $this->EE->TMPL->fetch_param("country_code"); 
			$this->EE->cartthrob->cart->set_customer_info("country_code", $this->EE->TMPL->fetch_param("country_code")); 
		}
		if ($this->EE->TMPL->fetch_param("shipping_country_code"))
		{
			// apparently ct pretty much ignores if we're manually setting shipping and standard country code unless we inject them into post. 
			$_POST['shipping_country_code'] =  $this->EE->TMPL->fetch_param("shipping_country_code"); 
			$this->EE->cartthrob->cart->set_customer_info("shipping_country_code", $this->EE->TMPL->fetch_param("shipping_country_code")); 
		}
		$cart_hash = $this->cart_hash();
		$this->EE->cartthrob->cart->set_custom_data('update_hash', $cart_hash); 

		$this->EE->cartthrob->save_customer_info(); 
 		$this->EE->cartthrob->cart->save();
 		
		if ($this->EE->TMPL->fetch_param("return"))
		{
			$this->EE->load->library('paths');
			$this->EE->load->library('javascript');

			$this->EE->functions->redirect($this->EE->paths->parse_url_path($this->EE->TMPL->fetch_param('return')));
		}
	}
}