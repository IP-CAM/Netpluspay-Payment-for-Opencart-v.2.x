<?php

class ControllerExtensionPaymentNetpluspayment extends Controller
{

	public function index()
	{

		//Load language file
		$this->language->load('extension/payment/netpluspayment');

		//Set title from language file
		$data['heading_title'] = $this->language->get('heading_title');

		//Load model
		$this->load->model('extension/payment/netpluspayment');
		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['continue'] = $this->url->link('checkout/success');

		$data['text_loading'] = $this->language->get('text_loading');


		//Sample - get data from loaded model
		$data['customers'] = $this->model_extension_payment_netpluspayment->getCustomerData();

		//Select template
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/payment/netpluspayment.tpl')) {
			$this->response->setOutput($this->load->view('extension/payment/netpluspayment.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('extension/payment/netpluspayment.tpl', $data));
		}


		return $this->load->view('extension/payment/netpluspayment', $data);

	}

	public function confirm()
	{
		if ($this->session->data['payment_method']['code'] == 'netpluspayment') {
			$this->load->model('checkout/order');
			$merchant_id = $this->config->get('netpluspayment_merchant_id');
			$websiteUrl = $this->config->get('config_url');
			if ($this->config->get('netpluspayment_test_mode'))
				$gateway_url = 'https://netpluspay.com/testpayment/paysrc/';
			else
				$gateway_url = 'https://netpluspay.com/payment/';
			$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
			$response = [];
			$fullname = $order_info['firstname'] . '  ' . $order_info['lastname'];

			$response['fullname'] = $fullname;
			$response['order_id'] = $this->session->data['order_id'];
			$response['email'] = $order_info['email'];
			$response['total'] = $order_info['total'];
			$response['merchant_id'] = $merchant_id;
			$response['website_url'] = $websiteUrl;
			$response['gateway_url'] = $gateway_url;
			echo json_encode($response, JSON_UNESCAPED_SLASHES);

			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('netpluspayment_order_status_id'), "Redirected to Netpluspay gateway");

			$this->load->language('checkout/success');

			$this->cart->clear();

			// Add to activity log
			if ($this->config->get('config_customer_activity')) {
				$this->load->model('account/activity');

				if ($this->customer->isLogged()) {
					$activity_data = array(
						'customer_id' => $this->customer->getId(),
						'name' => $this->customer->getFirstName() . ' ' . $this->customer->getLastName(),
						'order_id' => $this->session->data['order_id']
					);

					$this->model_account_activity->addActivity('order_account', $activity_data);
				} else {
					$activity_data = array(
						'name' => $this->session->data['guest']['firstname'] . ' ' . $this->session->data['guest']['lastname'],
						'order_id' => $this->session->data['order_id']
					);

					$this->model_account_activity->addActivity('order_guest', $activity_data);
				}
			}


			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
			unset($this->session->data['totals']);
		}
	}


}

?>