<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cartthrob_multi_location
{
 	public $return_data;

    /**
     * Cartthrob_multi_location constructor.
     */
	function Cartthrob_multi_location()
	{
		ee()->load->add_package_path(PATH_THIRD.'cartthrob/');
		ee()->load->library('cartthrob_loader');
		ee()->load->library('number');
	}

    /**
     * @return null|string
     */
	private function cart_hash()
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
     * @return mixed
     */
 	public function check_location()
	{
		$cart_hash = $this->cart_hash(); 
		
 		if (ee()->cartthrob->cart->custom_data('update_hash') != $cart_hash)
		{
			ee()->cartthrob->cart->set_custom_data('update_hash', $cart_hash);
			ee()->cartthrob->cart->save();
  			return ee()->TMPL->fetch_param('message');
		}
	}

    /**
     * @return int
     */
	public function price()
	{
		ee()->load->library('number');
		ee()->load->model('product_model');
		ee()->load->library('api/api_cartthrob_tax_plugins');
		
		$price = 0;
		$price_plus_tax = 0; 
		if (ee()->TMPL->fetch_param('entry_id'))
		{
			$product = ee()->product_model->get_product(ee()->TMPL->fetch_param('entry_id'));
	 		$price =  ee()->number->format( $product['price'] );
			$price_plus_tax = $product['price'] + ee()->api_cartthrob_tax_plugins->get_tax($product['price']);
	
			if (isset(ee()->TMPL->tagparts[2]) && ee()->TMPL->tagparts[2] === 'plus_tax')
			{
				return ee()->number->format( $price_plus_tax );
			}
			if (isset(ee()->TMPL->tagparts[2]) && ee()->TMPL->tagparts[2] === 'plus_tax_numeric')
			{
				return $price_plus_tax; 
			}
 		}

		if ($item = ee()->cartthrob->cart->item(ee()->TMPL->fetch_param('row_id')))
		{
			$price =$item->price();
			$price_plus_tax = $price + ee()->api_cartthrob_tax_plugins->get_tax($price, $item);

			if (isset(ee()->TMPL->tagparts[2]) && ee()->TMPL->tagparts[2] === 'plus_tax')
			{
				return ee()->number->format( $price_plus_tax );
			}
			if (isset(ee()->TMPL->tagparts[2]) && ee()->TMPL->tagparts[2] === 'plus_tax_numeric')
			{
				return $price_plus_tax; 
			}
		}
		
		if (isset(ee()->TMPL->tagparts[2]) && ee()->TMPL->tagparts[2] === 'numeric')
		{
			return $price;
		}
		
		return ee()->number->format( $price);
	}

    /**
     * @return mixed
     */
 	public function shipping()
	{
		ee()->load->model('product_model');
		
		if (ee()->TMPL->fetch_param('entry_id'))
		{
			$product = ee()->product_model->get_product(ee()->TMPL->fetch_param('entry_id'));
			ee()->load->library('number');
	 		return ee()->number->format( $product['shipping'] );
 		}
	}

    /**
     *
     */
 	public function save_codes()
	{
		if (ee()->TMPL->fetch_param("currency_code"))
		{
			$_POST['currency_code'] =  ee()->TMPL->fetch_param("currency_code");

			ee()->cartthrob->cart->set_config('number_format_defaults_currency_code',  ee()->TMPL->fetch_param("currency_code") );
			ee()->cartthrob->cart->set_customer_info("currency_code", ee()->TMPL->fetch_param("currency_code"));
		}
		
		if (ee()->TMPL->fetch_param("country_code"))
		{
			$_POST['country_code'] =  ee()->TMPL->fetch_param("country_code");
			ee()->cartthrob->cart->set_customer_info("country_code", ee()->TMPL->fetch_param("country_code"));
		}
		if (ee()->TMPL->fetch_param("shipping_country_code"))
		{
			// apparently ct pretty much ignores if we're manually setting shipping and standard country code unless we inject them into post. 
			$_POST['shipping_country_code'] =  ee()->TMPL->fetch_param("shipping_country_code");
			ee()->cartthrob->cart->set_customer_info("shipping_country_code", ee()->TMPL->fetch_param("shipping_country_code"));
		}
		$cart_hash = $this->cart_hash();
		ee()->cartthrob->cart->set_custom_data('update_hash', $cart_hash);

		ee()->cartthrob->save_customer_info();
 		ee()->cartthrob->cart->save();
 		
		if (ee()->TMPL->fetch_param("return"))
		{
			ee()->load->library('paths');
			ee()->load->library('javascript');

			ee()->functions->redirect(ee()->paths->parse_url_path(ee()->TMPL->fetch_param('return')));
		}
	}
}