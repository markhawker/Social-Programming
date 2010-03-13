<?php

class Sprog extends Controller {

  function Sprog()
  {
    parent::Controller();
  }
  
  // Stage 1 Methods
  
  function is_logged_in()
  {
    $is_logged_in = $this->session->userdata('is_logged_in');
    $uri_segment = ($this->uri->segment(2) ? $this->uri->segment(2) : 'index');
    if((isset($is_logged_in) && $is_logged_in == true) && ($uri_segment == 'index' || $uri_segment == 'register')) {
      redirect('sprog/home');
     }
     elseif((!isset($is_logged_in) || $is_logged_in != true) && $uri_segment != 'index' && $uri_segment != 'register') {
       redirect('sprog/index');
     }
  }
  
  // Stage 2 Methods

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

  // Stage 3 Methods

  function twitter()
  {
    $token = $this->uri->segment(3);
    $oauth_token = $this->encrypt->decode(
      $this->session->userdata('oauth_token')
    );
    $oauth_token_secret = $this->encrypt->decode(
      $this->session->userdata('oauth_token_secret')
    );
    if (!empty($token)) {
      $session = $this->twitter_model->set_tokens($token);
      $this->check_link($this->encrypt->decode($session['oauth_token']),$this->encrypt->decode($session['oauth_token_secret']));
    } else if(empty($oauth_token) && empty($oauth_token_secret)) {
      $this->session->set_userdata('oauth_token', '');
      $this->session->set_userdata('oauth_token_secret', '');
      redirect('sprog/index'); 
    } else {
      $this->check_link($oauth_token, $oauth_token_secret);
    }
  }

  function check_link($oauth_token, $oauth_token_secret) 
  {
    $twitter = $this->twitter->init($oauth_token, $oauth_token_secret);
    $twitter_user = $this->twitter_model->get_user($twitter);
    $check_user = $this->twitter_model->check_user($twitter_user['id']);
    if(!$twitter_user) {
      redirect('sprog/index/twitterexception');
    } else {
        $this->session->set_userdata('twitter_id', $this->encrypt->encode($twitter_user['id']));
        if($check_user) {
	  $data = array(
            'username' => $this->encrypt->encode($check_user['user_username']),
  	    'fullname' => $this->encrypt->encode($twitter_user['fullname']),
  	    'is_logged_in' => true,
  	    'source' => $this->encrypt->encode('t')
           );
           $this->session->set_userdata($data);
           redirect('sprog/home');
        } else {
          redirect('sprog/index');
        }
     }
  }

  // Stage 4 Methods

  function facebook()
  {
    $function = $this->uri->segment(3);
    switch($function) {
      case 'authorize':
        break;
      case 'remove':
	$this->facebook_model->remove();
	break;
      case 'logout':
	$this->session->set_userdata('facebook_logout', true);
        redirect('sprog/index');
	break;
    }
  }
  
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
    $data['has_twitter'] = $this->twitter_model->has_twitter($username);
    $data['has_facebook'] = $this->facebook_model->has_facebook($username);
    $data['has_facebook_permissions'] = $this->facebook_model->has_permissions($username);
    $data['updates'] = $this->sprog_model->updates($username, $config['per_page'], $this->uri->segment(3, 0));
    $data['comments'] = $this->sprog_model->my_comments($username);
    $data['pagination'] = $this->pagination->create_links();
    $data['username'] = $username;
    $data['content'] = 'sprog/home';
    $data['title'] = 'Welcome, '.$fullname.'!';
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
      $has_facebook_permissions = $this->facebook_model->has_permissions($username);
      $facebook_id = $this->facebook_model->get_facebook_id($update_id);
      if($has_facebook_permissions && $facebook_id) {
	    $comment_id = $this->facebook_model->comment($facebook_id, $comment);
	  }
      $this->sprog_model->post_comment($update_id, $username, $comment, $source, $comment_id);
      redirect('sprog/home');
    }
  }
  
  function logout()
  {
    $oauth_token = $this->encrypt->decode(
      $this->session->userdata('oauth_token')
    );
    $oauth_token_secret = $this->encrypt->decode(
      $this->session->userdata('oauth_token_secret')
    );
    if(!empty($oauth_token) && !empty($oauth_token_secret)) {
      $this->twitter_model->logout($oauth_token, $oauth_token_secret);
    }
    $this->session->sess_destroy();
    if($this->facebook_model->get_user()) {
      $this->facebook_library->get_facebook()->logout(base_url().'sprog/facebook/logout');
    } else {
      redirect('sprog/index');
    }
  }
  
  function like()
  {
    $update_id = $this->uri->segment(3);
    $this->sprog_model->like($update_id);
    $username = $this->encrypt->decode($this->session->userdata('username'));
    $has_facebook_permissions = $this->facebook_model->has_permissions($username);
    $facebook_id = $this->facebook_model->get_facebook_id($update_id);
    if($has_facebook_permissions && $facebook_id) {
	  $this->facebook_model->like($facebook_id);
	}
    redirect('sprog/home');
  }
  
  // Stage 5 Methods
  
  // Points to views/login.php
  function index($error = false)
  {
  	$this->google_model->is_google_logged_in();
    $this->facebook_model->is_facebook_logged_in();
    $this->is_logged_in();
    $data['twitter_url'] = $this->twitter->get_url();
    $data['error'] = ($error =='error' ? 'The username or password you supplied was incorrect, please try again.' : false);
    if($this->session->userdata('twitter_id')) {
      $data['has_twitter'] = 'You are signed in with Twitter, but you must login or register with us to link accounts. You will only have to do this once.';
    }
    if($this->facebook_model->get_user()) {
      $data['has_facebook'] = 'You are signed in with Facebook, but you must login or register with us to link accounts. You will only have to do this once.';
    }
    if($this->google_model->get_viewer()) {
      $data['has_google'] = 'You are signed in with Google Friend Connect, but you must login or register with us to link accounts. You will only have to do this once.';
    }
    $data['content'] = 'sprog/login';
    $data['title'] = 'Login, Please!';
    $this->load->view('sprog/includes/template', $data);
  }
  
  // Points to views/register.php
  function register($error = false)
  {
    $this->google_model->is_google_logged_in();
    $this->facebook_model->is_facebook_logged_in();
    $this->is_logged_in();
    $data['error'] = ($error == 'error' ? 'The username you supplied already exists, please choose another.' : false);
    if($this->session->userdata('twitter_id')) {
      $data['has_twitter'] = 'You are signed in with Twitter, but must login or register a new account to link. You will only have to do this once.';
    }
    if($this->facebook_model->get_user()) {
      $data['has_facebook'] = 'You are signed in with Facebook, but you must login or register with us to link accounts. You will only have to do this once.';
    }
    if($this->google_model->get_viewer()) {
      $data['has_google'] = 'You are signed in with Google Friend Connect, but you must login or register with us to link accounts. You will only have to do this once.';
    }
    $data['content'] = 'sprog/register';
    $data['title'] = 'Register, Please!';
    $this->load->view('sprog/includes/template', $data);
  }
  
  function login()
  {
    $username = $this->input->post('username');
    $password = md5($this->input->post('password'));
    $user = $this->sprog_model->validate($username, $password);
    if(!empty($user)) {
      $source = 's';
      $data = array();
      // Create Twitter Link
      if($this->session->userdata('twitter_id')) {
	    $this->twitter_model->link($this->session->userdata('twitter_id'), $username);
	    $source = 't';
      } else {
	    $data['oauth_token'] = $this->encrypt->encode($user['oauth_token']);
	    $data['oauth_token_secret'] = $this->encrypt->encode($user['oauth_token_secret']);
      }
      // Create Facebook Link
      if($this->facebook_model->get_user()) {
	    $this->facebook_model->link($this->facebook_model->get_user(), $username);
	    $source = 'f';
      }
      // Create Google Link
      if($this->google_model->get_viewer()) {
	    $this->google_model->link($this->google_model->get_viewer(), $username);
	    $source = 'g';
      }
      $data['username'] = $this->encrypt->encode($user['username']);
      $data['fullname'] = $this->encrypt->encode($user['fullname']);
      $data['is_logged_in'] = true;
      $data['source'] = $this->encrypt->encode($source);
      $this->session->set_userdata($data);
      redirect('sprog/home');
    } else {
      $this->index('error');
    }	
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
	$source = 's';
	// Create Twitter Link
	if($this->session->userdata('twitter_id')) {
	  $this->twitter_model->link($this->session->userdata('twitter_id'), $username);
	  $source = 't';
	}
	// Create Facebook Link
	if($this->facebook_model->get_user()) {
	  $this->facebook_model->link($this->facebook_model->get_user(), $username);
	  $source = 'f';
	}
	// Create Google Link
    if($this->google_model->get_viewer()) {
	  $this->google_model->link($this->google_model->get_viewer(), $username);
	  $source = 'g';
    }
	$data = array(
	  'username' => $this->encrypt->encode($user['username']),
  	  'fullname' => $this->encrypt->encode($user['fullname']),
  	  'is_logged_in' => true,
  	  'source' => $this->encrypt->encode($source)
  	);
  	$this->session->set_userdata($data);
  	redirect('sprog/home');	
      } else {
        $this->register('error');
      }
    }
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
    $data['is_google'] = ($this->encrypt->decode($this->session->userdata('source')) == 'g' ? true : false);
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
      $id = $this->sprog_model->update($username, $update, $source);
      if($this->input->post('twitter') == 1) {
	    $this->twitter_model->update($update, $id);
      }
      if($this->input->post('facebook') == 1) {
	    $this->facebook_model->update($username, $update, $id);
      }
      $has_google_id = $this->google_model->get_google_id($username);
      if($has_google_id) {
      	$this->google_model->update($username, $update, $id);
      }
      redirect('sprog/home');
    }
  }

}

?>