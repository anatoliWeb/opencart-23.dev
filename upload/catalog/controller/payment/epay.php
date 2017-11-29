<?php
class ControllerPaymentEPay extends Controller {
	public function index() {
		$this->language->load('payment/epay');
		
		$data['text_instruction'] = $this->language->get('text_instruction');
		$data['text_payment'] = $this->language->get('text_payment');
		
		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['button_back'] = $this->language->get('button_back');

		$data['continue'] = $this->url->link('checkout/epay');	
		
		if($this->request->get['route'] == 'checkout/confirm') {
			$data['back'] = $this->url->link('checkout/payment');
		} elseif ($this->request->get['route'] != 'checkout/guest_step_3') {
			$data['back'] = $this->url->link('checkout/confirm');
		} else {
			$data['back'] = $this->url->link('checkout/guest_step_2');
		}
		
		$this->load->model('checkout/order');
        
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        
		$amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) * 100;
		 
		$params = array
		(
			'merchantnumber' => $this->config->get('epay_merchant_number'),
			'amount' => $amount,
			'currency' => $this->session->data['currency'],
			'orderid' => $this->session->data['order_id'],
			'group' => $this->config->get('epay_group'),
			'mailreceipt' => $this->config->get('epay_authemail'),
			'windowstate' => intval($this->config->get('epay_payment_window')),
			'instantcapture' => intval($this->config->get('epay_instantcapture')),
			'ownreceipt' => intval($this->config->get('epay_ownreceipt')),
			'accepturl' => $this->url->link('payment/epay/accept', '', 'SSL'),
			'callbackurl' => $this->url->link('payment/epay/confirm', '', 'SSL'),
			'cancelurl' => $this->url->link('checkout/epay', '', 'SSL')
		);
		
		$params["hash"] = md5(implode("", array_values($params)) . $this->config->get('epay_md5key'));
		
        $data["params"] = $params;
        
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/epay.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/epay.tpl', $data);
		} else {
			return $this->load->view('default/template/payment/epay.tpl', $data);
		}
	}
	
	static function getCardnameById($cardid) {
		switch($cardid)
		{
			case 1:
				return 'Dankort / VISA/Dankort';
			case 2:
				return 'eDankort';
			case 3:
				return 'VISA / VISA Electron';
			case 4:
				return 'MasterCard';
			case 6:
				return 'JCB';
			case 7:
				return 'Maestro';
			case 8:
				return 'Diners Club';
			case 9:
				return 'American Express';
			case 10:
				return 'ewire';
			case 12:
				return 'Nordea e-betaling';
			case 13:
				return 'Danske Netbetalinger';
			case 14:
				return 'PayPal';
			case 16:
				return 'MobilPenge';
		}
		return 'unknown';
	}
	
	public function confirm() {
		$this->language->load('payment/epay');
		
		$this->load->model('checkout/order');
		
		$amount = $this->currency->format($_GET['amount']/100, $_GET['currency'], FALSE, TRUE);
		
		$comment = $this->language->get('payment_process') . $amount;
		$comment .= $this->language->get('payment_with_transactionid') . $_GET['txnid'];
		$comment .= $this->language->get('payment_card') . $this->getCardnameById($_GET['paymenttype']) . ' ' . $_GET['cardno'];
			
		if(strlen($this->config->get('epay_md5key')) > 0) {
			$md5 = 0;
				
			$params = $_GET;
			$var = "";

			foreach ($params as $key => $value)
			{
				if($key != "hash")
				{
					$var .= $value;
				}
			}

			$genstamp = md5($var . $this->config->get('epay_md5key'));

			if($genstamp != $_GET["hash"]) {
				$md5 = 0;
			} else {
				$md5 = 1; 
			}
		
		} else {
			$md5 = 1;
		}
		
		if($md5 == 1) {
            $this->model_checkout_order->addOrderHistory($_GET["orderid"], $this->config->get('epay_order_status_id'), $comment, true);
			echo "OK";
		} else {
			header('HTTP/1.1 500 Internal Server Error');
			header("Status: 500 Internal Server Error");
		}
	}
	
	public function accept() {
		if(strlen($this->config->get('epay_md5key')) > 0) {
			$md5 = 0;
				
			$params = $_GET;
			$var = "";

			foreach ($params as $key => $value)
			{
				if($key != "hash")
				{
					$var .= $value;
				}
			}

			$genstamp = md5($var . $this->config->get('epay_md5key'));

			if($genstamp != $_GET["hash"])
			{
				$md5 = 0;
			}
			else
			{
				$md5 = 1; 
			}
		
		} else {
			$md5 = 1;
		}
			
		if($md5 == 1) {
            $this->response->redirect($this->url->link('checkout/success', 'token=' . $this->session->data['token'], 'SSL'));
		} else {
            $this->response->redirect($this->url->link('checkout/epay', 'md5error=1', 'SSL'));
		}
	}	
}
?>