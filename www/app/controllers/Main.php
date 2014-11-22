<?php

class Main extends CI_Controller {

	private $session_id;

	public function __construct(){
		parent::__construct();
		$this->load->driver('session');
		$this->session_id = $this->session->userdata('id');
	}

	public function index()	{
		if($this->session_id){
			$data['title'] = 'Foyer';
			$data['bodyClass'] = 'foyer';
			$this->load->view('global/head', $data);
			$this->load->view('global/nav', $data);
			$this->load->view('foyer', $data);
			$this->load->view('global/footer', $data);
		}else{
			header("Location:/login/");
		}
	}
}

?>