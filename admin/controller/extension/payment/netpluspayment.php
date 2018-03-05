<?php

class ControllerExtensionPaymentNetpluspayment extends Controller
{
	private $error = array();

	public function index()
	{
		$this->load->language('extension/payment/netpluspayment');


		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('netpluspayment', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true));
		}


		//Load model
		$this->load->model('extension/payment/netpluspayment');
		$this->load->model('localisation/order_status');


		// - get states data from loaded model
		$data['states'] = $this->model_extension_payment_netpluspayment->getStatesData();


		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');

		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_total'] = $this->language->get('entry_total');

		$data['entry_merchant_id'] = $this->language->get('entry_merchant_id');
		$data['entry_client_id'] = $this->language->get('entry_client_id');
		$data['entry_client_secret'] = $this->language->get('entry_client_secret');
		$data['entry_test_mode'] = $this->language->get('entry_test_mode');
		$data['entry_merchant_name'] = $this->language->get('entry_merchant_name');
		$data['entry_merchant_email'] = $this->language->get('entry_merchant_email');
		$data['entry_merchant_phone'] = $this->language->get('entry_merchant_phone');
		$data['entry_merchant_street'] = $this->language->get('entry_merchant_street');
		$data['entry_merchant_city'] = $this->language->get('entry_merchant_city');
		$data['entry_merchant_state'] = $this->language->get('entry_merchant_state');
		$data['entry_merchant_country'] = $this->language->get('entry_merchant_country');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['help_total'] = $this->language->get('help_total');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/netpluspayment', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/netpluspayment', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true);

		if (isset($this->request->post['netpluspayment_total'])) {
			$data['netpluspayment_total'] = $this->request->post['netpluspayment_total'];
		} else {
			$data['netpluspayment_total'] = $this->config->get('netpluspayment_total');
		}

		if (isset($this->request->post['netpluspayment_order_status_id'])) {
			$data['netpluspayment_order_status_id'] = $this->request->post['netpluspayment_order_status_id'];
		} else {
			$data['netpluspayment_order_status_id'] = $this->config->get('netpluspayment_order_status_id');
		}


		if (isset($this->request->post['netpluspayment_payment_success_status_id'])) {

			$data['netpluspayment_payment_success_status_id'] = $this->request->post['netpluspayment_payment_success_status_id'];
		} else {
			$data['netpluspayment_payment_success_status_id'] = $this->config->get('netpluspayment_payment_success_status_id');
		}

		if (isset($this->request->post['netpluspayment_payment_failure_status_id'])) {

			$data['netpluspayment_payment_failure_status_id'] = $this->request->post['netpluspayment_payment_failure_status_id'];
		} else {
			$data['netpluspayment_payment_failure_status_id'] = $this->config->get('netpluspayment_payment_failure_status_id');
		}


		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();


		if (isset($this->request->post['netpluspayment_status'])) {
			$data['netpluspayment_status'] = $this->request->post['netpluspayment_status'];
		} else {
			$data['netpluspayment_status'] = $this->config->get('netpluspayment_status');
		}

		if (isset($this->request->post['netpluspayment_sort_order'])) {
			$data['netpluspayment_sort_order'] = $this->request->post['netpluspayment_sort_order'];
		} else {
			$data['netpluspayment_sort_order'] = $this->config->get('netpluspayment_sort_order');
		}


		if (isset($this->request->post['netpluspayment_merchant_id'])) {
			$data['netpluspayment_merchant_id'] = $this->request->post['netpluspayment_merchant_id'];
		} else {
			$data['netpluspayment_merchant_id'] = $this->config->get('netpluspayment_merchant_id');
		}


		if (isset($this->request->post['netpluspayment_test_mode'])) {
			$data['netpluspayment_test_mode'] = $this->request->post['netpluspayment_test_mode'];
		} else {
			$data['netpluspayment_test_mode'] = $this->config->get('netpluspayment_test_mode');
		}
		if (isset($this->request->post['netpluspayment_pre_auth'])) {
			$data['netpluspayment_pre_auth'] = $this->request->post['netpluspayment_pre_auth'];
		} else {
			$data['netpluspayment_pre_auth'] = $this->config->get('netpluspayment_pre_auth');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/netpluspayment', $data));
	}

	protected function validate()
	{
		if (!$this->user->hasPermission('modify', 'extension/payment/netpluspayment')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}