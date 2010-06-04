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
    $data['title'] = 'Log In, Please!';
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
  
  // Stage 2 Methods
  
  // Points to views/home.php
  function home()
  {
  	$this->is_logged_in();
  	$fullname = $this->encrypt->decode($this->session->userdata('fullname'));
  	$username = $this->encrypt->decode($this->session->userdata('username'));
	$config['base_url'] = site_url('/sprog/home');
	$config['total_rows'] = $this->db->get_where('update', array('username' => $username))->num_rows();
	$config['per_page'] = 15;
	$config['full_tag_open'] = '<div id="pagination">';
	$config['full_tag_close'] = '</div>';
	$this->pagination->initialize($config);
  	$data['updates'] = $this->sprog_model->updates($username, $config['per_page'], $this->uri->segment(3, 0));
  	$data['comments'] = $this->sprog_model->my_comments($username);
  	$data['pagination'] = $this->pagination->create_links();
  	$data['username'] = $username;
  	$data['content'] = 'sprog/home';
  	$data['title'] = 'Welcome, '.$fullname.'!';
  	$this->load->view('sprog/includes/template', $data);
  }
  
  function update()
  {
  	$this->load->library('form_validation');
  	$this->form_validation->set_rules('update', 'Update', 'trim|required');

	if($this->form_validation->run() == false) {
		$this->home();
	} else {
		$username = $this->encrypt->decode(
			$this->session->userdata('username')
		);
		$update = $this->input->post('update');
		$source = $this->encrypt->decode(
			$this->session->userdata('source')
		);
		$this->sprog_model->update($username, $update, $source);
		redirect('sprog/home');
	}
  }
  
  function delete()
  {
  	$update_id = $this->uri->segment(3);
  	$this->sprog_model->delete($update_id);
  	redirect('sprog/home');
  }
  
  // Points to views/profile.php
  function profile()
  {
    $this->is_logged_in();
    $username = $this->uri->segment(3);
	$config['base_url'] = site_url('/sprog/profile');
	$config['total_rows'] = $this->db->get_where('update', array('username' => $username))->num_rows();
	$config['per_page'] = 15;
	$config['full_tag_open'] = '<div id="pagination">';
	$config['full_tag_close'] = '</div>';
	$this->pagination->initialize($config);
  	$data['updates'] = $this->sprog_model->updates($username, $config['per_page'], $this->uri->segment(3, 0));
  	$data['pagination'] = $this->pagination->create_links();
  	$data['content'] = 'sprog/profile';
	$data['title'] = 'Profile for '.($username ? $username : 'Unknown');  	
    $this->load->view('sprog/includes/template', $data);	
  }
  
  // Points to views/latest.php
  function latest()
  {
  	$this->is_logged_in();
	$config['base_url'] = site_url('/sprog/latest');
	$config['total_rows'] = $this->db->get('update')->num_rows();
	$config['per_page'] = 15;
	$config['full_tag_open'] = '<div id="pagination">';
	$config['full_tag_close'] = '</div>';
	$this->pagination->initialize($config);
  	$data['latest_updates'] = $this->sprog_model->latest_updates($config['per_page'], $this->uri->segment(3, 0));
  	$data['pagination'] = $this->pagination->create_links();
  	$data['content'] = 'sprog/latest';
  	$data['title'] = 'Latest Updates';
  	$this->load->view('sprog/includes/template', $data);
  	
  }
  
  function like()
  {
  	$update_id = $this->uri->segment(3);
  	$this->sprog_model->like($update_id);
  	redirect('sprog/home');
  }
  
  // Points to views/comment.php
  function view_comment()
  {
    $this->is_logged_in();
  	$update_id = $this->uri->segment(3);
	$config['base_url'] = site_url('/sprog/view_comment/'.$update_id);
	$config['total_rows'] = $this->db->get_where('comment', array('update_id' => $update_id))->num_rows();
	$config['per_page'] = 15;
	$config['full_tag_open'] = '<div id="pagination">';
	$config['full_tag_close'] = '</div>';
	$this->pagination->initialize($config);
  	$data['latest_comments'] = $this->sprog_model->get_comments($update_id, $config['per_page'], $this->uri->segment(4, 0));
  	$data['pagination'] = $this->pagination->create_links();
  	$data['content'] = 'sprog/comment';
    $data['title'] = 'Comment, Please!';
    $data['original'] = $this->sprog_model->get_original($update_id);
    $this->load->view('sprog/includes/template', $data);
  }
  
  function comment()
  {
    $this->load->library('form_validation');
  	$this->form_validation->set_rules('comment', 'Comment', 'trim|required');
	$update_id = $this->input->post('update_id');
	if($this->form_validation->run() == false) {
		redirect('sprog/view_comment/'.$update_id);
	} else {
		$username = $this->encrypt->decode(
			$this->session->userdata('username')
		);
		$comment = $this->input->post('comment');
		$source = $this->encrypt->decode(
			$this->session->userdata('source')
		);
		$this->sprog_model->post_comment($update_id, $username, $comment, $source);
		redirect('sprog/home');
	}
  }

}

?>