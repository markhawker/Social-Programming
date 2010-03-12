<?php

class Facebook_Model extends Model {

  function Facebook_Model()
  {
    parent::Model();
  }
  
  // Stage 4 Methods

  function remove()
  {
    $facebook = $this->facebook_library->get_facebook();
    $facebook_parameters = $facebook->get_valid_fb_params($_POST, null, 'fb_sig');
    if (!empty($facebook_parameters) && $facebook->fb_params['uninstall'] == 1) {
      $this->db->delete('facebook', array('id' => $facebook->fb_params['user']));
    }
  }

  function get_user()
  {
    $facebook = $this->facebook_library->get_facebook();
    return $facebook->get_loggedin_user();
  }

  function check_user($facebook_id)
  {
    $this->db->select('*')->from('user')->join('facebook', 'facebook.user_username = user.username', 'left');
    $this->db->where('id', $facebook_id);
    $query = $this->db->get();
    if($query->num_rows() == 0) {
      return false;
    } else {
      $user = $query->row();
      $data = array(
        'username' => $this->encrypt->encode($user->username),
        'fullname' => $this->encrypt->encode($user->fullname),
        'is_logged_in' => true,
        'source' => $this->encrypt->encode('f')
      );
      $this->session->set_userdata($data);
      return true;
    }
  }

  function is_facebook_logged_in()
  {
    $is_logged_in = $this->session->userdata('is_logged_in');
    $facebook_logout = $this->session->userdata('facebook_logout');
    $facebook_user = $this->get_user();
    if($facebook_user && !$is_logged_in && !$facebook_logout) {
      $data = $this->check_user($facebook_user);
    }
    $this->session->set_userdata('facebook_logout', false);
  }

  function link($facebook_id, $username)
  {
    $query = $this->db->get_where('facebook', array('id' => $facebook_id));
    if($query->num_rows() == 0) {
      $user = array(
        'id' => $facebook_id,
        'session_key' => '',
        'user_username' => $username
      );
      $query = $this->db->insert('facebook', $user);
      return true;
    } else {
      return false;
    }
  }
  
  function has_facebook($username)
  {
    $query = $this->db->get_where('facebook', array('user_username' => $username));
    return ($query->num_rows() > 0 ? true : false);
  }
  
  function has_permissions($username)
  {
  	$facebook = $this->facebook_library->get_facebook();
  	$user = $facebook->get_loggedin_user();
  	if($user) {
  	  try {
        $data = $facebook->api_client->fql_query('SELECT uid, publish_stream, read_stream, offline_access FROM permissions WHERE uid = "'.$user.'"');
        if(is_array($data)) {
          $permissions = array(
            'publish_stream' => $data[0]['publish_stream'],
            'read_stream' => $data[0]['read_stream'],
            'offline_access' => $data[0]['offline_access']
          );
        }
        if($permissions['publish_stream'] && $permissions['read_stream'] && $permissions['offline_access']) {
      	  $session_key = (isset($_COOKIE[API_KEY.'_session_key']) ? $_COOKIE[API_KEY.'_session_key'] : false);
      	  $expires = (isset($_COOKIE[API_KEY.'_expires']) ? $_COOKIE[API_KEY.'_expires'] : -1);
      	  if($expires == 0 && $session_key) {
      	  	$this->db->set('session_key', $session_key);
      	  	$this->db->where('id', $user);
			$this->db->update('facebook');
      	  }
      	  return true;
        } else {
          return false;
        }
      }
      catch (Exception $e) {
        return false;
      }
    } else {
      $query = $this->db->get_where('facebook', array('user_username' => $username, 'session_key !=' => ''));
      return ($query->num_rows() == 1 ? true : false);
    }
  }
  
  function update($username, $update, $id)
  {
    $query = $this->db->get_where('facebook', array('user_username' => $username));
	$user = $query->row();
  	$facebook = $this->facebook_library->get_facebook();
  	$facebook->set_user($user->id, $user->session_key);
  	try {
  	  $post_id = $facebook->api_client->stream_publish($update);
      $this->db->set('facebook_id', $post_id);
      $this->db->where('id', $id);
      $this->db->update('update');
      return true;
  	}
  	catch (Exception $e) {
  	  return false;
  	}
  }
  
  function get_facebook_id($update_id)
  {
  	$query = $this->db->get_where('update', array('id' => $update_id, 'facebook_id !=' => ''));	
  	return ($query->num_rows() == 1 ? $query->row()->facebook_id : false);
  }
  
  function like($facebook_id)
  {
    $facebook = $this->facebook_library->get_facebook();
  	$user = $facebook->get_loggedin_user();
  	if($user) {
  	  try {
  	  	$like = $facebook->api_client->stream_addLike($facebook_id);
  	  	return $like;
  	  }
  	  catch (Exception $e) {
        return false;
      }
    } else {
      return false;
    }
  }
  
  function comment($facebook_id, $comment)
  {
    $facebook = $this->facebook_library->get_facebook();
  	$user = $facebook->get_loggedin_user();
  	if($user) {
  	  try {
  	  	$comment_id = $facebook->api_client->stream_addComment($facebook_id, $comment);
  	  	return $comment_id;
  	  }
  	  catch (Exception $e) {
        return false;
      }
    } else {
      return false;
    }
  }

}

?>