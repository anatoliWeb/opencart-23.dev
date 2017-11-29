<?php

class HelperNo2pay4 {
	
	const VERSION = '1.0.0.0';

    /**
     * supported currency
     *
     * @var array
     */
    public $supportedCurrencies = array( 'NOK' );

    protected $_alias = 'No2pay4';

    protected $_this;
	
	protected $_log = array();
	
	protected $_testmode = true;
	
	protected $_debug = true;

    protected $_db = false;

    /**
     * @param null $_this
     */
    public function __construct( $_this = null){
        $this->_this = $_this;

        if(!is_null($this->_this)){

            if($this->getConfig('testmode') == '1'){
                $this->supportedCurrencies = array('AUD', 'BRL', 'CAD', 'MXN', 'NZD', 'HKD', 'SGD', 'USD', 'EUR', 'JPY', 'TRY', 'NOK', 'CZK', 'DKK', 'HUF', 'ILS', 'MYR', 'PHP', 'PLN', 'SEK', 'CHF', 'TWD', 'THB', 'GBP', 'RMB', 'RUB');
            }else{
                $this->supportedCurrencies = array('NOK');
            }
        }
    }

    /**
     * @param $key
     * @return null
     */
    public function getConfig($key, $default = null){

        if(is_null($this->_this)){
            return $default;
        }

        return $this->_this->config->get(strtolower($this->_alias) . '_' . $key) ?: $this->_this->config->get($key) ?: $default;
    }

    /**
     * add to array data translate
     *
     * @param $data
     */
    public function translate(&$data){
        if(!is_array($data)){
            $data = array();
        }

        $this->_this->language->load('payment/'.$this->_alias);

        if($this->isVersionShop('2.1')){
            $languages = $this->_this->language->all();
        }else{
            $languages = $this->_this->language->load('no2pay4');
        }

        $words = array();
        foreach($languages as $key=>$language){
            $words[$key] = $language;
        }

        $data['_'] = new HelperNo2pay4Translate($words);
    }

    /**
     * @param $template
     * @param $data
     * @return mixed
     */
    public function view($template, $data){

        if (file_exists(DIR_TEMPLATE . $this->_this->config->get('config_template') . '/template/'.$template.'.tpl')) {
            $path = $this->_this->config->get('config_template') . '/template/'.$template.'.tpl';
        } else {
            // use version 2.3
            $path = $template;
        }



        return $this->_this->load->view($path, $data);
    }

    /**
     * @param $isoCurrency
     * @return bool
     */
    public function checkCurrency($isoCurrency){
        return in_array($isoCurrency, $this->supportedCurrencies);
    }

    /**
     * check total
     *      minimum 1000 nok
     *      maximum 20000 nok
     *
     * @param $total
     * @return bool
     */
    public function checkTotalMinMax($total){
        return (1000 <= $total) && (20000 >= $total);
    }

    /**
     * @param $locale
     * @return string
     */
    public function get_language_code($locale)
    {
        $locale = strtolower($locale);
        $langCode = array(
            'nb_no' =>  '1',
            'nn_no' =>  '1',
            'en_au' =>  '2',
            'en_bz' =>  '2',
            'en_ca' =>  '2',
            'en_cb' =>  '2',
            'en_gb' =>  '2',
            'en_ie' =>  '2',
            'en_jm' =>  '2',
            'en_nz' =>  '2',
            'en_ph' =>  '2',
            'en_tt' =>  '2',
            'en_us' =>  '2',
            'en_za' =>  '2',
            'en_zw' =>  '2',
            'se_fi' =>  '3',
            'se_no' =>  '3',
            'se_se' =>  '3',
        );

        if(array_key_exists($locale, $langCode)){
            return $langCode[$locale];
        }

        return "0";
    }

    /**
     * @param $value
     * @return mixed
     */
    public function removeSpecialCharacters($value)
    {
        return preg_replace('/[^\p{Latin}\d ]/u', '', $value);
    }


    /**
     * check version shop
     *
     * @param int $version
     * @param string $operator - <, lt, <=, le, >, gt, >=, ge, ==, =, eq, !=, <>, ne
     * @return bool
     */
    public function isVersionShop($version = 0, $operator = '>='){
        return version_compare(VERSION, $version, $operator);
    }

    /**
     * @return string
     */
    public function getModuleHeaderInfo(){
       
        return implode(' ', array(
            'Opencart/'.VERSION,
            'Module/'.self::VERSION
        ));
    }

    /**
     * return api url
     *
     * @param $sandbox
     * @return string
     */
    public function getUrl($sandbox){

        if ($sandbox) {
            return 'https://compareking.dev/2Pay4/api/?test_ipn=1&';
        } else {
            return 'https://compareking.dev/2Pay4/api/?';
        }
    }

    /**
     * Get the 2Pay4 request URL for an order.
     *
     * @param $context
     * @param bool $sandbox
     * @return string
     */
    public function get_request_url( $context, $sandbox = false ) {
        $pay_args = http_build_query( $this->get_pay_args( $context ), '', '&' );
        dd($this->get_pay_args( $context ));
        $url = $this->getUrl($sandbox);

        return $url. $pay_args;
    }

    /**
     * @param $order
     * @return array
     */
    public function get_pay_args($order){
		
		$listProduct = array();

        $products = $this->_this->cart->getProducts();

        $this->_this->load->model('payment/no2pay4');
        $productsByProductId = $this->_this->model_payment_no2pay4->getOrderProductsByProductId($order['order_id']);

        foreach($products as $product){
            $listProduct[] = array(
                'item_id' => array_key_exists($product['product_id'], $productsByProductId) ? $productsByProductId[$product['product_id']]['order_product_id'] : null,
                'product_id' => $product['product_id'],
                'name' => $product['name'],
                'count' => $product['quantity'],
                'total' => $product['total'],
                'price' => $product['price'],
            );
        }
		
        $args = array(
            'encoding'      =>  "UTF-8",
            'cmd'           => '_cart',
            'cms'           =>  $this->getModuleHeaderInfo(),
            'login'         =>  $this->getConfig('login'),
            'password'      =>  password_hash($this->getConfig( 'password' ), PASSWORD_DEFAULT),
            'merchantnumber'=>  $this->getConfig( 'merchantnumber' ),
            'checkkey'      =>  $this->getConfig( 'key' ),
            'business'      =>  $order['email'],
            'language'      =>  $this->get_language_code($order['language_code']),
            'windowstate'   =>  $this->getConfig( 'windowstate' ),
            'typedateinput' =>  $this->getConfig( 'typedateinput' ),
            'no_note'       =>  $order['comment'],
            'currency_code' =>  $order['currency_code'],
            'charset'       =>  'utf-8',
//            'rm'            => is_ssl() ? 2 : 1,
            'upload'        =>  1,
            'return'        =>  $this->getFixUrl('payment/no2pay4/confirm'),
//            'cancel_return' =>  $this->_this->url->link('checkout/no2pay4', '', 'SSL'),
            'cancel_return' =>  $this->getFixUrl('checkout/no2pay4'),
            'notify_url'    =>  $this->getFixUrl('payment/no2pay4/accept'),
            'page_style'    =>  $this->getConfig( 'page_style' ),
            'paymentaction' =>  $this->getConfig( 'paymentaction' ),
            'orderid'       =>  $order['order_id'],
            'storeid'       =>  $order['store_id'],
            'custom'        =>  json_encode( array( 'order_id' => $order['order_id'], 'order_key' => md5($order['order_id']) ) ),
            'amount'        =>  $this->_this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false) * 100,
            'first_name'    => $order['payment_firstname'],
            'last_name'     => $order['payment_lastname'],
            'company'       => $order['payment_company'],
            'address1'      => $order['payment_address_1'],
            'address2'      => $order['payment_address_2'],
            'city'          => $order['payment_city'],
            'zone'          => $order['payment_zone'],
            'zip'           => $order['payment_postcode'],
            'country'       => $order['payment_country'],
            'email'         => $order['email'],
			'order_detail'  =>  json_encode(array('products'=>$listProduct)),
        );
		
		$this->logInfo(__METHOD__.'(return): '.json_encode($args));
		
        return $args;
    }

    /**
     * @param $url string
     * @param $fields array
     * @return array
     */
    public function successfulRequest($url, $fields){

		$this->logInfo(__METHOD__ .'(input url): '.$url);
		$this->logInfo(__METHOD__ .'(input fields): '.json_encode($fields));
	
        $result = array();

        //open connection
        $Session = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($Session, CURLOPT_URL, $url);
        curl_setopt($Session, CURLOPT_POST, count($fields));
        curl_setopt($Session, CURLOPT_POSTFIELDS, json_encode($fields));

        curl_setopt($Session, CURLOPT_HEADER, false);
        curl_setopt($Session, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($Session, CURLOPT_RETURNTRANSFER, TRUE);

        // ssl false
        curl_setopt($Session, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($Session, CURLOPT_SSL_VERIFYHOST, false);

        $result['result'] = curl_exec($Session);
        $result['info'] = curl_getinfo($Session);
        $result['error'] = curl_error($Session);

        //close connection
        curl_close($Session);

		$this->logInfo(__METHOD__ .'(return): '.json_encode($result));
		
        return $result;
    }

    public function getFixUrl( $url, $admin = false){

        $links = array();
		
        if(array_key_exists('token', $this->_this->session->data)){
            $token = $this->_this->session->data['token'];
        }else{
            $token = null;
        }

        // admin link
        $links['common/dashboard'] = $this->_this->url->link('common/dashboard', 'token=' . $token, 'SSL');
        $links['payment/no2pay4'] = $this->_this->url->link('payment/no2pay4', 'token=' . $token, 'SSL');
        $links['payment/no2pay4/search'] = $this->_this->url->link('payment/no2pay4/search', 'token=' . $token, 'SSL');
        // site link
        $links['checkout/no2pay4'] = $this->_this->url->link('checkout/checkout', '', 'true');



        if($this->isVersionShop('2.3')){
            // admin link
            $links['extension/payment'] = $this->_this->url->link('extension/extension', 'token=' . $token . '&type=payment', true);
            // site link
            $links['payment/no2pay4/confirm'] = $this->_this->url->link('extension/payment/no2pay4/confirm', '', true);
            $links['payment/no2pay4/accept'] = $this->_this->url->link('extension/payment/no2pay4/callback', '', true);

        }else{
            // admin link
            $links['extension/payment'] = $this->_this->url->link('extension/payment', 'token=' . $token, 'SSL');
            // site link
            $links['payment/no2pay4/confirm'] = $this->_this->url->link('payment/no2pay4/confirm', '', 'SSL');
            $links['payment/no2pay4/accept'] = $this->_this->url->link('payment/no2pay4/callback', '', 'SSL');
        }

        return array_key_exists($url, $links) ? $links[$url]: '';
    }
	
	/**
     * @param $message
     * @return HelperNo2pay4
     */
    public function logError($message){
        return $this->log($message, 'error');
    }

    /**
     * @param $message
     * @return HelperNo2pay4
     */
    public function logInfo($message){
        return $this->log($message, 'info');
    }

    /**
     * @param $message
     * @param string $type
     * @return $this
     */
    public function log($message, $type = 'info'){
//		if(!$this->_debug){
//			return $this;
//		}
		
        if(!array_key_exists($type, $this->_log)){
            $this->_log[$type] = new Log($this->_alias.'-'.$type.'.log');
        }

        $this->_log[$type]->write($message);
        return $this;
    }

    /**
     * @param $string
     * @return mixed|string
     */
    public function fixJsonString($string){

        $js_str = utf8_encode(trim($string));
        $js_str = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $js_str);
        $js_str = preg_replace('/\s+/', '',$js_str);
        $js_str = str_replace('&quot;', '"', $js_str);

        return $js_str;
    }

    /**
     * @return string
     */
    public function getAlias(){
        return $this->_alias;
    }

    /**
     * @return bool|DB
     */
    public function getDb(){
        if(!$this->_db){
            $this->_db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        }

        return $this->_db;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getPaymentStatusById($id = 0){
        $query = $this->getDb()->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id ='" . (int)$id . "'");
        return $query;
    }

    public function sendDataWhenUpdateDataOrder(){

        if(array_key_exists('order_id', $_GET) && array_key_exists('payment_method', $_POST) && $_POST['payment_method'] === 'no2pay4' && array_key_exists('order_status_id', $_POST)){

            try{

                $order_id = array_key_exists('order_id', $_GET) ?$_GET['order_id'] : 0;
                $title = array_key_exists('payment_method', $_POST) ? $_POST['payment_method'] : '';
                $status_id = array_key_exists('order_status_id', $_POST) ? $_POST['order_status_id'] : 0;

                $fields = array(
                    'order_id' => $order_id,
                    'order_key' => md5($order_id),
                    'title' => $title,
                    'status_id' => $status_id,
                    'status_label' => '',
                );

                $paymentStatus = $this->getPaymentStatusById($status_id);
                if($paymentStatus['num_rows']){
                    $fields['status_label'] = $paymentStatus['row']['name'];
                }

                $url = $this->getUrl(false).http_build_query(array('status_order'=>true));

                $this->successfulRequest($url, $fields);

            }catch(Exception $e){
                $this->logError($e->getMessage());
            }

        }
    }
}

class HelperNo2pay4Translate{

    protected $_words = array();

    /**
     * @param array $words
     */
    public function __construct(array $words){
        $this->_words = $words;
    }

    /**
     * @param $word
     * @return mixed
     */
    public function _($word){
        return $this->$word;
    }

    /**
     * @param $word
     * @return mixed
     */
    public function __get($word){
        return array_key_exists($word, $this->_words) ? $this->_words[$word] : '_'.$word;
    }
}

// add utils
if ( ! function_exists('dd'))
{
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed
     * @return void
     */
    function dd()
    {
        print"<pre>";
        array_map(function($x) { var_dump($x); }, func_get_args()); die;
    }
}