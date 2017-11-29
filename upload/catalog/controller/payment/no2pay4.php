<?php

/**
 * use version 2.3.*
 *
 * Class ControllerPaymentNo2pay4
 */
class ControllerPaymentNo2pay4 extends Controller {
    /**
     * @var HelperNo2pay4
     */
    protected $_helper;

    public $testmode = true;
    /**
     * data to template
     * @var array
     */
    protected $_data = array();

    /**
     * @param $registry
     */
    public function __construct($registry){

        parent::__construct($registry);
        $this->load->helper('no2pay4');
        $this->_helper = new HelperNo2pay4($this);
    }

    /**
     * @return mixed
     */
    public function index(){

        // add _data language field
        $this->_helper->translate($this->_data);

        $link = new stdClass();
        $link->continue = $this->url->link('checkout/no2pay4');

        if($this->request->get['route'] == 'checkout/confirm') {

            $link->back = $this->url->link('checkout/payment');

        } elseif ($this->request->get['route'] != 'checkout/guest_step_3') {

            $link->back = $this->url->link('checkout/confirm');

        } else {
            $link->back = $this->url->link('checkout/guest_step_2');
        }

        $this->_data['link'] = $link;
        $this->_data['payment_name'] = $this->_helper->getConfig('payment_name',' 2PAY4 ');

        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $args = $this->_helper->get_pay_args($order_info);

        $args_array = array();
        foreach($args as $key=>$value){
            $args_array[] = "'" . $key . "':'" . $value ."'";
        }

        $this->_data['typedateinput'] = $this->_helper->getConfig('typedateinput', '2');
        $this->_data['args']  = $args;
        $this->_data['args_array']  = $args_array;
        $this->_data['action']  = $this->_helper->get_request_url($order_info, true);

        return $this->_helper->view('payment/No2pay4', $this->_data);
    }
	
	/**
     *  callback
     */
    public function callback(){

        //add check method post

        $returnData = array(
            'status' => '200',
            'success'=> true
        );

//        header('Content-Type: application/json');

        if(array_key_exists('typedateinput', $this->request->get) && $this->request->get['typedateinput'] == 3){
            $formRows =  json_decode(file_get_contents('php://input'),true);

			$this->_helper->logInfo(__METHOD__ . ':' . __LINE__ . '(formRows): '.json_encode($formRows) );
			
            try{
                if(!array_key_exists('orderid', $formRows)){
                    throw new Exception('Not order id');
                }

                $this->load->model('checkout/order');

                $order = $this->model_checkout_order->getOrder($formRows['orderid']);

                if (!$order) {
                    throw new Exception('error load order');
                }

                $rows = array(
                    'form' => $formRows,
                    'args' => $this->_helper->get_pay_args($order, $this->testmode)
                );

				$this->_helper->logInfo(__METHOD__ . ':' . __LINE__ . '(rows): '.json_encode($rows) );
				
                $url = $this->_helper->getUrl($this->testmode) .http_build_query( array('data'=>json_encode($rows),'typedateinput'=>'3',  'check_form'=>'true'), '', '&' );

                $curl = $this->_helper->successfulRequest($url, $rows);
				
				$this->_helper->logInfo(__METHOD__ . ':' . __LINE__ . '(curl): '.json_encode($curl) );
				
                if($curl['info']['http_code']!= 200){
                    throw new Exception('error ');
                }

                $this->response->setOutput($curl['result']);
            }catch (Exception $e){

				$this->_helper->logError(__METHOD__ . ':' . __LINE__ . ', message: '.$e->getMessage().', Code: '.$e->getCode());
			
                $this->response->setOutput(json_encode(array(
                    'success'=>false,
                    'message'=>$e->getMessage()
                )));
            }
        }else{
            $posted = array_keys($this->request->get);

			$this->_helper->logInfo(__METHOD__ . ':' . __LINE__ . '(posted): '.json_encode($posted) );
			
            try{
                // check key
                if(array_key_exists('1', $posted)){
                    $order_id = $posted[1];
                }else{
                    throw new Exception('No GET(order id) was supplied to the system!');
                }

                if(array_key_exists('2', $posted)){
                    $order_key = $posted[2];
                }else{
                    throw new Exception('No GET(order key) was supplied to the system!');
                }

                if(array_key_exists('3', $posted)){
                    $key = $posted[3];
                }else{
                    throw new Exception('No GET(key) was supplied to the system!');
                }

                reset($_POST);
                $postData = json_decode($this->_helper->fixJsonString(key($_POST)));

				$this->_helper->logInfo(__METHOD__ . ':' . __LINE__ . '(postData): '.json_encode($postData) );
				
                // check key
                if($postData->key != md5($key) && $postData->key != $this->_helper->getConfig('key', '12345')){
                    throw new Exception('MD5 check failed for 2Pay4 callback with order_id:'.$order_id);
                }

                //check privateKey
                if($postData->privatekey != md5($this->_helper->getConfig('private_key', '12345'))){
                    throw new Exception('key check failed for 2Pay4 callback with order_id:'.$order_id);
                }

                $this->load->model('checkout/order');

                $order = $this->model_checkout_order->getOrder($order_id);
                // add coment
                $comment = array(
                    $this->_helper->getConfig('payment_name'),
                    $this->language->get('payment_process') .': '. $this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false),
                    $this->language->get('payment_with_transactionid') . ': ' . $postData->txnid
                ) ;

                // check order
                if (!$order) {
                    throw new Exception('error load order');
                }

                // status sucsefull
                $order_status_id = $this->_helper->getConfig('order_status_id');

                $this->model_checkout_order->addOrderHistory($order_id, $order_status_id, implode("|", $comment));

                
            }catch (Exception $e){
				
				$this->_helper->logError(__METHOD__ . ':' . __LINE__ . ', message: '.$e->getMessage().', Code: '.$e->getCode());

                $returnData['status'] = '500';
                $returnData['success'] = false;
                $returnData['error'] =  array(
                    'message'   =>  $e->getMessage(),
                    'code'      =>  $e->getCode()
                );

            }

			$this->_helper->logInfo(__METHOD__ . ':' . __LINE__ . '(return): '.json_encode($returnData));
			
            $this->response->setOutput(json_encode($returnData));
        }

    }

	/**
     * redirect to confirm order
     */
    public function confirm(){
		
		$this->_helper->logInfo(__METHOD__ . ':' . __LINE__ . '(return): redirect to'.$this->url->link('checkout/success'));
		
        $this->response->redirect($this->url->link('checkout/success'));
    }

    public function on_customer_add(){
        $arg_list = func_get_args();
        $this->_helper->logInfo($arg_list);
        $this->_helper->logInfo('on_customer_add on_customer_add on_customer_add on_customer_add pre');
//        dd($arg_list, 'fack pre');
    }
    public function on_customer_add_after(){
        $arg_list = func_get_args();
        $this->_helper->logInfo($arg_list);
        $this->_helper->logInfo('on_customer_add on_customer_add on_customer_add  after');

//        dd($arg_list, 'fack after');
    }

    // event
    public function on_checkout_order_addorderhistory_after(){
        $this->_helper->sendDataWhenUpdateDataOrder();
    }
}