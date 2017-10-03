<?php  
class ControllerNetpluspaymentResponse extends Controller {
  public function index() {
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

$status_code=$this->request->post['code'];
$status_from_netplus=$this->request->post['description'];
$order_id=$this->request->post['order_id'];
$order = $this->model_checkout_order->getOrder($order_id);




 

    
if($status_code=="00")
{
//update order status


  $this->db->query("UPDATE " . DB_PREFIX . "order SET order_status_id = ".$this->config->get('netpluspayment_payment_success_status_id')." WHERE order_id=".$order_id);
 $this->db->query("INSERT INTO " . DB_PREFIX . "order_history(order_id,order_status_id,comment) VALUES(".$order_id.",".$this->config->get('netpluspayment_payment_success_status_id').",'".$status_from_netplus."')");

$state_query = $this->db->query("SELECT name FROM " . DB_PREFIX . "zone WHERE zone_id = ".$this->config->get('netpluspayment_merchant_state'));
$state_res=$state_query->rows;
			
$origin_state=$state_res[0]['name'];
$origin_city=$this->config->get('netpluspayment_merchant_city');
$client_id=$this->config->get('netpluspayment_client_id');


$origin_name=$this->config->get('netpluspayment_merchant_name');
$origin_phone=$this->config->get('netpluspayment_merchant_phone');
$origin_street=$this->config->get('netpluspayment_merchant_street');
$origin_email=$this->config->get('netpluspayment_merchant_email');
$client_secret=$this->config->get('netpluspayment_client_secret');

//get order weight
$order_weight=0;


$products = $this->model_account_order->getOrderProducts($order_id);
foreach($products as $item)
{
$weight_query = $this->db->query("SELECT weight FROM " . DB_PREFIX . "product WHERE product_id =".$item['product_id']);
$weight_res=$weight_query->rows;

$order_weight+=$weight_res[0]['weight'];
}
$post='{ "delivery_state": "'.$order['shipping_zone'].'",
"delivery_lga": "'.$order['shipping_city'].'",
"pickup_state": "'.$origin_state.'",
"pickup_lga": "'.$origin_city.'",
"weight":'.$order_weight.',
"courier_id":"ksixga9",
"client_id":"'. $client_id.'" }';
$url= 'http://saddleng.com/v2/shipping_price';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch,CURLOPT_POSTFIELDS, $post);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Content-Type: application/json')
);
$response= json_decode(curl_exec($ch));

$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
// close the connection, release resources used
curl_close($ch);
   if(!isset($response->error) && !isset($response->fail) && !isset($response->Warning))
   {
$shipping_price=$response->{'Shipping Price'};
//post to delivery
$pickup_date= $order['date_added'];   
//shipping street
 $shipping_street=$order['shipping_address_1'];
 
 $output="";
 $httpcode="";
 $tracking_code="";
 $pod=0;


 $payment_method=$order['payment_method'];

 $pod=0;
 if($payment_method=="cod")
    $pod=1;
	




foreach($products as $item)
{
$model_product = $this->model_catalog_product->getProduct($item['product_id']);
$fullname=$order['shipping_firstname'].' '.$order['shipping_lastname'];
$delivery_post='{"transaction_id":"'.$order_id.'",    
"client_id":"'.$client_id.'",  
"item_cost":"'.$item['price'].'",
"delivery_cost":'.$shipping_price.',       
"courier_id":"ksixga9", 
"pickup_address":"'.$origin_street.'",  
"pickup_location":"'.$origin_city.'",   
"pickup_contactname":"'.$origin_name.'",    
"pickup_contactnumber":"'.$origin_phone.'", 
"pickup_contactemail":"'.$origin_email.'",  
"delivery_address":"'.$shipping_street.'",  
"delivery_location":"'.$order['shipping_city'].'",  
"delivery_contactname":"'.$fullname.'",   
"delivery_contactnumber":"'.$order['telephone'].'",    
"delivery_contactemail":"'.$order['email'].'", 
"item_name":"'.$item['name'].'", 
"item_size":"", 
"item_weight":"'.$model_product['weight'].'", 
"item_color":"-",   
"item_quantity":"'.$item['quantity'].'",   
"image_location":"'.$this->config->get('config_url').'image/'.$model_product['image'].'",  
"fragile":"1",  
"perishable":"1",
"pre_auth":"1",
"status":"0",
"POD": "'.$pod.'"
}';


   if($this->config->get('netpluspayment_test_mode'))
  $url= "http://test.saddleng.com/v2/delivery";
   else
    $url= "http://saddleng.com/v2/delivery";


$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch,CURLOPT_POSTFIELDS, $delivery_post);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Content-Type: application/json',
'client-id: '.$client_id,
'client-secret: '.$client_secret
));
 
$resp= curl_exec($ch);
$delivery_response= json_decode($resp);


$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
// close the connection, release resources used
curl_close($ch);
if(!isset($delivery_response->error) && !isset($delivery_response->fail))
{
    $tracking_code=$delivery_response->delivery_id;
    $output.=$delivery_response->success;
    $output.="; Delivery ID ".$tracking_code;
}else
{
    if(isset($delivery_response->error)){
        $output.=$delivery_response->error;
    }
    if(isset($delivery_response->fail)){
        $output.=$delivery_response->fail;
    }
}
  
}//end for each

if(!isset($delivery_response->error) && !isset($delivery_response->fail))
{

  $this->db->query("UPDATE " . DB_PREFIX . "order SET order_status_id = ".$this->config->get('netpluspayment_shipment_status_id')." WHERE order_id=".$order_id);
 $this->db->query("INSERT INTO " . DB_PREFIX . "order_history(order_id,order_status_id,comment) VALUES(".$order_id.",".$this->config->get('netpluspayment_shipment_status_id').",'".$output."')");

 
}
}//end outer if response is successful
}//if succesful
else{

 
 $this->db->query("INSERT INTO " . DB_PREFIX . "order_history(order_id,order_status_id,comment) VALUES(".$order_id.",".$this->config->get('netpluspayment_payment_failure_status_id').",'".$status_from_netplus."')");




}

$data['amount_paid']=$this->request->post['amount_paid'];
$data['bank']=$this->request->post['bank'];
$data['transaction_id']=$this->request->post['transaction_id'];
$data['description']=$this->request->post['description'];
$data['order_id']=$this->request->post['order_id'];
//end debit on delivery
 
    // call the "View" to render the output
    $this->response->setOutput($this->load->view('netpluspayment/response', $data));
  }
}
?>