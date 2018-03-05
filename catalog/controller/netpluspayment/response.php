<?php

class ControllerNetpluspaymentResponse extends Controller
{
    public function index()
    {
        // set title of the page
        $this->document->setTitle("Payment Details");

        // define template file
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/netpluspayment/response.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/netpluspayment/response.tpl';
        } else {
            $this->template = 'default/template/netpluspayment/response.tpl';
        }

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        // set data to the variable
        $data['my_custom_text'] = "This is my custom page.";

        //process debit on delivery
        $this->load->model('checkout/order');

        $this->load->model('account/order');
        $this->load->model('catalog/product');

        $status_code = $this->request->post['code'];
        $status_from_netplus = $this->request->post['description'];
        $order_id = $this->request->post['order_id'];
        $order = $this->model_checkout_order->getOrder($order_id);

        if ($status_code == "00") {
            if ($this->config->get('saddleshipping_status')) {
                $this->load->model("extension/shipping/saddleshipping");
                $response = $this->model_extension_shipping_saddleshipping->postDelivery($order_id, $order);

                if (isset($response->status) && $response->status == 0) {
                    $this->db->query("UPDATE " . DB_PREFIX . "order SET order_status_id = " . $this->config->get('saddleshipping_shipment_status_id') . " WHERE order_id=" . $order_id);
                    $this->db->query("INSERT INTO " . DB_PREFIX . "order_history(order_id,order_status_id,comment) VALUES(" . $order_id . "," . $this->config->get('saddleshipping_shipment_status_id') . ",'" . $response->message . "')");
                }
            }
        }//if succesful
        else {
            $this->db->query("INSERT INTO " . DB_PREFIX . "order_history(order_id,order_status_id,comment) VALUES(" . $order_id . "," . $this->config->get('netpluspayment_payment_failure_status_id') . ",'" . $status_from_netplus . "')");
        }

        $data['amount_paid'] = $this->request->post['amount_paid'];
        $data['bank'] = $this->request->post['bank'];
        $data['transaction_id'] = $this->request->post['transaction_id'];
        $data['description'] = $this->request->post['description'];
        $data['order_id'] = $this->request->post['order_id'];
        //end debit on delivery

        // call the "View" to render the output
        $this->response->setOutput($this->load->view('netpluspayment/response', $data));
    }
}

?>