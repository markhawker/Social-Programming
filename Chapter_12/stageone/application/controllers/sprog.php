<?php

class Sprog extends Controller {

  function Sprog()
  {
    parent::Controller();
  }
  
  // Stage 1 Methods

  // Points to views/login.php
  function index($error = false)
  {
  	$this->is_logged_in();
    $data['error'] = ($error ? 'The username or password you supplied was incorrect, please try again.' : false);
    $data['content'] = 'sprog/login';
    $data['title'] = 'Login, Please!';
    $this->load->view('sprog/includes/template', $data);
  }
  
  function login()
  {
  	$username = $this->input->post('username');
  	$password = md5($this->input->post('password'));
  	$user = $this->sprog_model->validate($username, $password);
  	
  	if(!empty($user)) {
  	  $data = array(
  	  	'username' => $this->encrypt->encode($user['username']),
  	  	'fullname' => $this->encrypt->encode($user['fullname']),
  	  	'is_logged_in' => true,
  	  	'source' => $this->encrypt->encode('s')
  	  );
  	  $this->session->set_userdata($data);
  	  redirect('sprog/home');
  	} else {
  	  $this->index(true);
  	}
  	
  }
  
  function logout()
  {
  	$this->session->sess_destroy();  
    redirect('sprog/index');
  }
  
  // Points to views/register.php
  function register($error = false)
  {
  	$this->is_logged_in();
    $data['error'] = ($error ? 'The username you supplied already exists, please choose another.' : false);
  	$data['content'] = 'sprog/register';
    $data['title'] = 'Register, Please!';
    $this->load->view('sprog/includes/template', $data);
  }
  
  function create()
  {
  	$this->load->library('form_validation');
  	$this->form_validation->set_rules('username', 'User Name', 'trim|required|min_length[4]|max_length[24]');
  	$this->form_validation->set_rules('fullname', 'Full Name', 'trim|required|max_length[64]');
  	$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
  	$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|matches[password]');
  	
  	if($this->form_validation->run() == false) {
  		$this->register();
  	} else {
  		$username = $this->input->post('username');
  		$fullname = $this->input->post('fullname');
  		$password = md5($this->input->post('password'));
  		$user = $this->sprog_model->create($username, $fullname, $password);
  		if(!empty($user)) {
  			$data = array(
  	  			'username' => $this->encrypt->encode($user['username']),
  	  			'fullname' => $this->encrypt->encode($user['fullname']),
  	  			'is_logged_in' => true,
  	  			'source' => $this->encrypt->encode('s')
  	  		);
  	  		$this->session->set_userdata($data);
  	  		redirect('sprog/home');	
  		} else {
  			$this->register(true);
  		}
  	}
  }
  
  function is_logged_in()
  {
  	$is_logged_in = $this->session->userdata('is_logged_in');
  	$uri_segment = $this->uri->segment(2);
  	if((isset($is_logged_in) && $is_logged_in == true) && ($uri_segment == 'index' || $uri_segment == 'register')) {
  		redirect('sprog/home');
  	}
  	elseif((!isset($is_logged_in) || $is_logged_in != true) && $uri_segment != 'index' && $uri_segment != 'register') {
  		redirect('sprog/index');
  	}
  }
  
  // Points to views/home.php
  function home()
  {
  	$this->is_logged_in();
  	$fullname = $this->encrypt->decode($this->session->userdata('fullname'));
  	$data['content'] = 'sprog/home';
  	$data['title'] = 'Welcome, '.$fullname.'!';
  	$this->load->view('sprog/includes/template', $data);
  }

}

?>