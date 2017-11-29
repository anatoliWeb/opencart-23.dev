<?php 
class ControllerPaymentNo2pay4 extends Controller {

	private $error = array();

    /**
     * @var HelperNo2pay4
     */
    protected $_helper;

    protected $_data = array();

    public function __construct($registry){

        parent::__construct($registry);
        $this->load->helper('no2pay4');
        $this->_helper = new HelperNo2pay4($this);
    }

	public function index() {

        if(array_key_exists('extension', $this->request->get) && $this->request->get['extension'] === 'no2pay4' && array_key_exists('route', $this->request->get)){

            if($this->request->get['route'] === 'extension/extension/payment/install'){

                $this->install();

            }elseif($this->request->get['route'] === 'extension/extension/payment/uninstall'){

                $this->uninstall();

            }
        }


		$this->load->language('payment/no2pay4');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');

        $data = array();

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			unset($this->request->post['no2pay4_module']);

            // fix field password
            if(empty($this->request->post['no2pay4_password'])){
                $this->request->post['no2pay4_password'] = $this->request->post['no2pay4_password_hash'];
            }else{
                $this->request->post['no2pay4_password'] = md5($this->request->post['no2pay4_password']);
            }
            unset($this->request->post['no2pay4_password_hash']);

            // geo zone id
            if(is_array($this->request->post['no2pay4_geo_zone_id'])){
                $this->request->post['no2pay4_geo_zone_id'] = implode(',',$this->request->post['no2pay4_geo_zone_id']);
            }

            // update data to setting
			$this->model_setting_setting->editSetting('no2pay4', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->_helper->getFixUrl('extension/payment'));

		}

        // load models
        $this->load->model('localisation/order_status');
        $this->load->model('localisation/geo_zone');
        $this->load->model('localisation/language');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->session->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$languages = $this->model_localisation_language->getLanguages();

		foreach ($languages as $language) {
			if (isset($this->error['bank_' . $language['language_id']])) {
				$data['error_bank_' . $language['language_id']] = $this->error['bank_' . $language['language_id']];
			} else {
				$data['error_bank_' . $language['language_id']] = '';
			}
		}

        // breadcrumb
		$data['breadcrumbs'] = array(
            array(
                'text' => $this->language->get('text_home'),
                'href' => $this->_helper->getFixUrl('common/dashboard'),
            ),
            array(
                'text' => $this->language->get('text_payment'),
                'href' => $this->_helper->getFixUrl('extension/payment'),
            ),
            array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->_helper->getFixUrl('payment/no2pay4'),
            )
        );

        // link button
        $obj = new stdClass();
        $obj->action = $this->_helper->getFixUrl('payment/no2pay4');
        $obj->cancel = $this->_helper->getFixUrl('extension/payment');
        $obj->search = $this->_helper->getFixUrl('payment/no2pay4/search');
        $data['link'] = $obj;

        // data
        foreach(array(
                    'no2pay4_login', 'no2pay4_password', 'no2pay4_key', 'no2pay4_private_key', 'no2pay4_merchant_number','no2pay4_typedateinput',
                    'no2pay4_paymentwindow','no2pay4_order_status_id', 'no2pay4_geo_zone_id', 'no2pay4_status', 'no2pay4_md5key','no2pay4_group',
                    'no2pay4_authemail', 'no2pay4_instantcapture', 'no2pay4_ownreceipt', 'no2pay4_total', 'no2pay4_order_status_pre_payment_id',
                    'no2pay4_order_status_after_payment_id','no2pay4_order_status_cancel_payment_id','no2pay4_order_status_cancel_payment_id',
					'no2pay4_testmode','no2pay4_debug',
                ) as $key){

            $data[$key] = array_key_exists($key, $this->request->post) ? $this->request->post[$key] : $this->config->get($key);
        }

        $data['no2pay4_geo_zone_id'] = explode(',',$data['no2pay4_geo_zone_id']);

		if (isset($this->request->post['no2pay4_payment_name'])) {
			$data['no2pay4_payment_name'] = $this->request->post['no2pay4_payment_name'];
		} else {
			if(strlen($this->config->get('no2pay4_payment_name')) == 0) {
				$data['no2pay4_payment_name'] = 'no2pay4 Payment Solutions';
			} else {
				$data['no2pay4_payment_name'] = $this->config->get('no2pay4_payment_name');
			}
		}

		if (isset($this->request->post['no2pay4_sort_order'])) {
			if(strlen($this->request->post['no2pay4_sort_order']) == 0){
				$data['no2pay4_sort_order'] = 1;
			} else {
				$data['no2pay4_sort_order'] = $this->request->post['no2pay4_sort_order'];
			}
		} else {
			if(strlen($this->config->get('no2pay4_sort_order')) == 0){
				$data['no2pay4_sort_order'] = 1;
			} else {
				$data['no2pay4_sort_order'] = $this->config->get('no2pay4_sort_order');
			}
		}

		$data['token'] = $this->session->data['token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

        $this->_data = $data;

        // load array and class translate
        $this->_helper->translate($this->_data);

        // load class build form
        $this->load->helper('buildFormAdminNo2Pay4');
        $this->_data['buildForm'] = new HelperBuildFormAdminNo2Pay4();

        // show template
		$this->response->setOutput($this->load->view('payment/no2pay4.tpl', $this->_data));
	}

    /**
     * @return bool
     */
	protected function validate() {

        if (!$this->user->hasPermission('modify', 'payment/no2pay4')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        // add request rows
        return !$this->error;
	}

	
	public function install() {

        $this->load->model('extension/event');

        if($this->_helper->isVersionShop('2.2')){
            $this->model_extension_event->addEvent($this->_helper->getAlias(), 'catalog/model/checkout/order/addOrderHistory/after', 'payment/no2pay4/on_checkout_order_addorderhistory_after');
        }else{
            $this->model_extension_event->addEvent($this->_helper->getAlias(), 'post.order.edit', 'payment/no2pay4/on_post_order_edit');
        }

    }

    public function uninstall() {
//        $this->load->model('extension/extension');
        $this->load->model('extension/event');
//        dd(get_class_methods($this->model_extension_event));
        $this->model_extension_event->deleteEvent($this->_helper->getAlias());
    }
}
