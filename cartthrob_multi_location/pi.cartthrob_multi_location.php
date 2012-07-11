<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
$plugin_info = array(
						'pi_name'			=> 'CartThrob Multi Location Utilities',
						'pi_version'		=> '1.1',
						'pi_author'			=> 'Chris Newton',
						'pi_author_url'		=> 'http://www.cartthrob.com',
						'pi_description'	=> 'Outputs information related to location changes',
						'pi_usage'			=> Cartthrob_multi_location::usage()
					);

class Cartthrob_multi_location
{
 
	function __construct()
	{
		$this->EE =& get_instance();
		$this->EE->load->add_package_path(PATH_THIRD.'cartthrob/');
		$this->EE->load->library('cartthrob_loader');
		$this->EE->load->library('number');
	}
	
 
	public function usage()
	{
		ob_start();
?>

Docs: 

{exp:cartthrob_multi_location:check_location message='Prices have been updated based on your new location'}
Outputs a message when the customer's location has been updated

{exp:cartthrob_multi_location:price entry_id="123"}
Outputs an item's price based on the configured price field. If you have three price fields this will always show the price from the field that is currently set.

{exp:cartthrob_multi_location:shipping entry_id="123"}
Outputs an item's shipping based on the configured shipping field. If you have three shipping fields this will always show the shipping amount from the field that is currently set.


<?php
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	} /* End of usage() function */
	
}