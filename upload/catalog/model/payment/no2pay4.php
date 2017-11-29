<?php 
class ModelPaymentno2pay4 extends Model {

    protected $_helper;

    public function __construct($registry){
        parent::__construct($registry);

        $this->load->helper('no2pay4');
        $this->_helper = new HelperNo2pay4($this);

    }

    public function getMethod($address, $total) {

        $this->load->language('payment/no2pay4');

        // check support
        if((!$this->_helper->checkCurrency($this->session->data['currency'])) || (!$this->_helper->checkTotalMinMax($total))){
            return false;
        }

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id IN(" . $this->config->get('cod_geo_zone_id') . ") AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        // check total
        if ($this->config->get('no2pay4_total') > 0 && $this->config->get('no2pay4_total') > $total) {
            $status = false;
        } elseif (!$this->config->get('cod_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        // add check currency
        $status = true;

        $method_data = array();

		if ($status) {
            $method_data['code'] = 'no2pay4';
            $method_data['title'] = $this->config->get('no2pay4_payment_name');
            $method_data['sort_order'] = $this->config->get('no2pay4_sort_order');
            $method_data['terms'] = '';
    	}
   
    	return $method_data;
  	}
	
    public function getOrderProducts($order_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

        return $query->rows;
    }

    public function getOrderProductsByProductId($order_id){
        $productsByProductId = array();
        $products = $this->getOrderProducts($order_id);

        foreach($products as $product){
            $productsByProductId[$product['product_id']] = $product;
        }

        return $productsByProductId;
    }


}
