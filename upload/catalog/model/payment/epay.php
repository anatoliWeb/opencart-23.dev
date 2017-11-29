<?php 
class ModelPaymentepay extends Model {
    public function getMethod($address, $total) {
        $this->load->language('payment/epay');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('cod_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if ($this->config->get('epay_total') > 0 && $this->config->get('epay_total') > $total) {
            $status = false;
        } elseif (!$this->config->get('cod_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();
	
		if ($status) {  
      		$method_data = array( 
        		'code'         => 'epay',
        		'title'      => $this->config->get('epay_payment_name'),
				'sort_order' => $this->config->get('epay_sort_order'),
                'terms' => null
      		);
    	}
   
    	return $method_data;
  	}
}
?>