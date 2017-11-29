<?php 
class ControllerPaymentEPay extends Controller {
	private $error = array(); 

	public function index() {
		$this->load->language('payment/epay');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		        
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			unset($this->request->post['epay_module']);

			$this->model_setting_setting->editSetting('epay', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_merchantnumber'] = $this->language->get('text_merchantnumber');
		$data['text_paymentwindow'] = $this->language->get('text_paymentwindow');
		$data['text_ownreceipt'] = $this->language->get('text_ownreceipt');
		$data['text_paymentwindow_overlay'] = $this->language->get('text_paymentwindow_overlay');
		$data['text_paymentwindow_fullscreen'] = $this->language->get('text_paymentwindow_fullscreen');
		$data['text_payment'] = $this->language->get('text_payment');
        $data['text_edit'] = $this->language->get('text_edit');
		
		$data['text_fee'] = $this->language->get('text_fee');
		$data['text_group'] = $this->language->get('text_group');
		
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
				
		$data['text_paymentmethods'] = $this->language->get('text_paymentmethods');
		
		$data['text_service_not_free'] = $this->language->get('text_service_not_free');
		
		$data['text_help'] = $this->language->get('text_help');
		$data['text_logos'] = $this->language->get('text_logos');		
		
        $data['entry_payment_name'] = $this->language->get('entry_payment_name');
		$data['entry_order_status'] = $this->language->get('entry_order_status');		
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_total'] = $this->language->get('entry_total');
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_search'] = $this->language->get('button_search');
        
        $data['help_total'] = $this->language->get('help_total');

		$data['tab_general'] = $this->language->get('tab_general');

        if (isset($this->session->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		$this->load->model('localisation/language');
		
		$languages = $this->model_localisation_language->getLanguages();
		
		foreach ($languages as $language) {
			if (isset($this->error['bank_' . $language['language_id']])) {
				$data['error_bank_' . $language['language_id']] = $this->error['bank_' . $language['language_id']];
			} else {
				$data['error_bank_' . $language['language_id']] = '';
			}
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('payment/epay', 'token=' . $this->session->data['token'], 'SSL'),
        );
				
		$data['action'] = $this->url->link('payment/epay', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
        $data['search'] = $this->url->link('payment/epay/search', 'token=' . $this->session->data['token'], 'SSL');
        
        if (isset($this->request->post['cod_total'])) {
			$data['epay_total'] = $this->request->post['epay_total'];
		} else {
			$data['epay_total'] = $this->config->get('epay_total');
		}
		
		if (isset($this->request->post['epay_payment_name'])) {
			$data['epay_payment_name'] = $this->request->post['epay_payment_name'];
		} else {
			if(strlen($this->config->get('epay_payment_name')) == 0) {
				$data['epay_payment_name'] = 'ePay Payment Solutions';
			} else {
				$data['epay_payment_name'] = $this->config->get('epay_payment_name');
			}
		}
		
		if (isset($this->request->post['epay_merchant_number'])) {
			$data['epay_merchant_number'] = $this->request->post['epay_merchant_number'];
		} else {
			$data['epay_merchant_number'] = $this->config->get('epay_merchant_number');
		}
		
		if (isset($this->request->post['epay_order_status_id'])) {
			$data['epay_order_status_id'] = $this->request->post['epay_order_status_id'];
		} else {
			$data['epay_order_status_id'] = $this->config->get('epay_order_status_id'); 
		} 
		
		$this->load->model('localisation/order_status');
		
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['epay_geo_zone_id'])) {
			$data['epay_geo_zone_id'] = $this->request->post['epay_geo_zone_id'];
		} else {
			$data['epay_geo_zone_id'] = $this->config->get('epay_geo_zone_id'); 
		} 
		
		$this->load->model('localisation/geo_zone');
										
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['epay_status'])) {
			$data['epay_status'] = $this->request->post['epay_status'];
		} else {
			$data['epay_status'] = $this->config->get('epay_status');
		}
		
		if (isset($this->request->post['epay_sort_order'])) {
			if(strlen($this->request->post['epay_sort_order']) == 0){
				$data['epay_sort_order'] = 1;	
			} else {
				$data['epay_sort_order'] = $this->request->post['epay_sort_order'];	
			}
		} else {
			if(strlen($this->config->get('epay_sort_order')) == 0){
				$data['epay_sort_order'] = 1;	
			} else {
				$data['epay_sort_order'] = $this->config->get('epay_sort_order');
			}
		}
		
		if (isset($this->request->post['epay_payment_window'])) {
			$data['epay_payment_window'] = $this->request->post['epay_payment_window'];
		} else {
			$data['epay_payment_window'] = $this->config->get('epay_payment_window');
		}
		
		if (isset($this->request->post['epay_md5key'])) {
			$data['epay_md5key'] = $this->request->post['epay_md5key'];
		} else {
			$data['epay_md5key'] = $this->config->get('epay_md5key');
		}
		
		if (isset($this->request->post['epay_group'])) {
			$data['epay_group'] = $this->request->post['epay_group'];
		} else {
			$data['epay_group'] = $this->config->get('epay_group');
		}
		
		if (isset($this->request->post['epay_authemail'])) {
			$data['epay_authemail'] = $this->request->post['epay_authemail'];
		} else {
			$data['epay_authemail'] = $this->config->get('epay_authemail');
		}
		
		if (isset($this->request->post['epay_instantcapture'])) {
			$data['epay_instantcapture'] = $this->request->post['epay_instantcapture'];
		} else {
			$data['epay_instantcapture'] = $this->config->get('epay_instantcapture');
		}
		
		if (isset($this->request->post['epay_ownreceipt'])) {
			$data['epay_ownreceipt'] = $this->request->post['epay_ownreceipt'];
		} else {
			$data['epay_ownreceipt'] = $this->config->get('epay_ownreceipt');
		}		
		
		$data['token'] = $this->session->data['token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/epay.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/epay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
        
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}

    public function install() {

//    $this->load->model('extension/event');
//
//    $this->model_extension_event->addEvent('mymodule', 'pre.admin.store.delete', 'module/mymodule/on_store_delete');
//
//    $this->model_extension_event->addEvent('mymodule', 'post.customer.add', 'module/mymodule/on_customer_add');

        die('asfsdg');

    }


    public function uninstall() {

//    $this->load->model('extension/event');
//
//    $this->model_extension_event->deleteEvent('mymodule');
        die('asdgsdga');
    }

}
?>