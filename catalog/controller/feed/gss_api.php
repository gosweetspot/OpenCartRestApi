<?php

class ControllerFeedGssApi extends Controller {

	private $debugIt = false;

	/*
	* Get orders
	*/
	public function orders() {

		$this->checkPlugin();

		$orderData['orders'] = array();

		/*check offset parameter*/
		if (isset($this->request->get['offset']) && $this->request->get['offset'] != "" && ctype_digit($this->request->get['offset'])) {
			$offset = $this->request->get['offset'];
		} else {
			$offset 	= 0;
		}

		/*check limit parameter*/
		if (isset($this->request->get['limit']) && $this->request->get['limit'] != "" && ctype_digit($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit 	= 10000;
		}

		if (isset($this->request->get['status']) && $this->request->get['status'] != "" && ctype_digit($this->request->get['status'])) {
			$status = $this->request->get['status'];
		} else {
			$status 	= 0;
		}

		// This does not work, and i want this to work.
		$this->load->model('account/gssorder');

		// This works, but the downside is that i have to change the core/opencart dist order.php file - bad practice
		//$this->load->model('account/order');
		/*get all orders of user*/
		//$results = $this->model_account_order->getOrderByStatusId($status);

		// Again this is what i need to use
		$results = $this->model_account_gssorder->getOrderByStatusId($status);

		if ($this->debugIt) {
			echo '<pre>';
			print_r($results);
			echo '</pre>';

		} else {
			$this->response->setOutput(json_encode($results));
		}


	}


	private function checkPlugin() {

		$json = array("success"=>false);

		/*check rest api is enabled*/
		if (!$this->config->get('gss_api_status')) {
			$json["error"] = 'API is disabled. Enable it!';
		}

		/*validate api security key*/
		if ($this->config->get('gss_api_key') && (!isset($this->request->get['key']) || $this->request->get['key'] != $this->config->get('gss_api_key'))) {
			$json["error"] = 'Invalid secret key';
		}

		if(isset($json["error"])){
			$this->response->addHeader('Content-Type: application/json');
			echo(json_encode($json));
			exit;
		}else {
			$this->response->setOutput(json_encode($json));
		}
	}

}
