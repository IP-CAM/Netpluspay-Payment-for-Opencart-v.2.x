<?php

class ModelExtensionPaymentNetpluspayment extends Model {
	
	//Sample DB access - Get all customers
	function getCustomerData() {
		$query = "SELECT * FROM " . DB_PREFIX . "customer";
		$result = $this->db->query($query);
		return $result->rows;
	}


	public function getMethod($address, $total) {
		$this->load->language('extension/payment/netpluspayment');

                    $status = true;
		if (!$this->cart->hasShipping()) {
			$status = false;
		}
               if(!$this->config->get('netpluspayment_status'))
                $status = false;
		$method_data = array();

		if ($status) {
			$method_data = array(
				'code'       => 'netpluspayment',
				'title'      => $this->language->get('heading_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('netpluspayment_sort_order')
			);
		}

		return $method_data;
	}
	
}

?>