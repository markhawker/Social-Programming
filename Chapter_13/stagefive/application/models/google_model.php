<?php

class Google_Model extends Model {

  function Google_Model()
  {
    parent::Model();
  }
  
  // Stage 5 Methods
  
  function get_viewer()
  {
  	$cookieIdentifier = 'fcauth'.GFC_SITE_ID;
	$cookie = isset($_COOKIE[$cookieIdentifier]) ? $_COOKIE[$cookieIdentifier] : null;
	if ($cookie) {
	  $opensocial = $this->google->get_google_cookie($cookie);
	  $batch = $opensocial->newBatch();
	  $viewerParameters = array(
        'userId' => '@me',
        'groupId' => '@self',
        'fields' => '@all'
      );
      $getViewer = $opensocial->people->get($viewerParameters);
      $batch->add($getViewer, 'viewer');
      $response = $batch->execute();
      $data = $response['viewer'];
      if ($data instanceof osapiError) {
        return false;
      } else {
      	$data = array(
      	  'id' => $data->getId(),
      	  'name' => htmlentities($data->getName()),
      	  'thumbnailUrl' => htmlentities($data->getThumbnailUrl())
      	);
      	return $data;
      }
	} else {
	  return false;
	}
  }
  
  function is_google_logged_in()
  {
    $is_logged_in = $this->session->userdata('is_logged_in');
    $google_user = $this->get_viewer();
    if($google_user && !$is_logged_in) {
      return $this->check_user($google_user);
    } else {
      return false;
    }
  }
  
  function check_user($google_user)
  {
    $this->db->select('*')->from('user')->join('google', 'google.user_username = user.username', 'left');
    $this->db->where('id', $google_user['id']);
    $query = $this->db->get();
    if($query->num_rows() == 0) {
      return false;
    } else {
      $user = $query->row();
      $data = array(
        'username' => $this->encrypt->encode($user->username),
        'fullname' => $this->encrypt->encode($user->fullname),
        'is_logged_in' => true,
        'source' => $this->encrypt->encode('g')
      );
      $this->session->set_userdata($data);
      return true;
    }
  }
  
  function link($google_user, $username)
  {
    $query = $this->db->get_where('google', array('id' => $google_user['id']));
    if($query->num_rows() == 0) {
      $user = array(
        'id' => $google_user['id'],
        'user_username' => $username
      );
      $query = $this->db->insert('google', $user);
      return true;
    } else {
      return false;
    }
  }
  
  function get_google_id($username) 
  {
  	$query = $this->db->get_where('google', array('user_username' => $username));	
  	return ($query->num_rows() == 1 ? $query->row()->id : false);
  }
  
  function update($username, $update, $id)
  {
  	$google_id = $this->get_google_id($username);
  	$opensocial = $this->google->get_google_oauth($google_id);
  	$batch = $opensocial->newBatch();
  	$activity = new osapiActivity($id, $google_id);
  	$activity->setTitle($username);
  	$activity->setBody($update);
    $parameters = array(
        'userId' => '@me',
        'groupId' => '@self',
        'activity' => $activity
    );
    $addActivity = $opensocial->activities->create($parameters);
    $batch->add($addActivity, 'activity');
    $response = $batch->execute();
    $data = $response['activity'];
    return ($data instanceof osapiError ? false : true);
  }

}

?>