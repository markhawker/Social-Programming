<?php

class Twitter_Model extends Model {

  function Twitter_Model()
  {
    parent::Model();
  }
  
  // Stage 3 Methods

  function set_tokens($oauth_token) 
  {			
    $twitter = $this->twitter->init();
    try {
      $twitter->setToken($oauth_token);
      $token = $twitter->getAccessToken();
      $twitter->setToken($token->oauth_token, $token->oauth_token_secret);
      $data = array(
	'oauth_token' => $this->encrypt->encode($token->oauth_token),
	'oauth_token_secret' => $this->encrypt->encode($token->oauth_token_secret)
      );
      $this->session->set_userdata($data);
      return $data;	
    }
    catch(EpiOAuthException $e) { redirect('sprog/index/oauthexception'); }
    catch(EpiTwitterException $e) { redirect('sprog/index/twitterexception'); }
  }

  function get_user($twitter)
  {
     $twitter_user = $this->twitter->verify($twitter);
     if (!empty($twitter_user)) {
       $user = array(
         'id' => $twitter_user->id,
         'fullname' => $twitter_user->name
       );
       return $user;
     } else {
       return false;
     }
  }

  function check_user($id)
  {
    $query = $this->db->get_where('twitter', array('id' => $id, 'user_username !=' => ''));
    if($query->num_rows() == 0) {
      return false;
    } else {
      return array('id' => $id, 'user_username' => $query->row()->user_username);
    }
  }

  function link($id, $username)
  {
    $id = $this->encrypt->decode($id);
    $oauth_token = $this->encrypt->decode(
      $this->session->userdata('oauth_token')
    );
    $oauth_token_secret = $this->encrypt->decode(
      $this->session->userdata('oauth_token_secret')
    );
    $user = array(
      'id' => $id,
      'access_token' => $oauth_token,
      'token_secret' => $oauth_token_secret,
      'user_username' => $username
     );
     $query = $this->db->insert('twitter', $user);
     return true;
  }

  function has_twitter($username)
  {
    $query = $this->db->get_where('twitter', array('user_username' => $username));
    return ($query->num_rows() > 0 ? true : false);
  }

  function update($text, $id)
  {
    $oauth_token = $this->encrypt->decode(
      $this->session->userdata('oauth_token')
    );
    $oauth_token_secret = $this->encrypt->decode(
      $this->session->userdata('oauth_token_secret')
    );
    $twitter = $this->twitter->init($oauth_token, $oauth_token_secret);
    $response = $twitter->post_statusesUpdate(array('status' => $text));
    if($this->twitter->check($response)) {
      $this->db->set('twitter_id', $response->id);
      $this->db->where('id', $id);
      $this->db->update('update');
    }
  }

  function logout($oauth_token, $oauth_token_secret)
  {
    $twitter = $this->twitter->init($oauth_token, $oauth_token_secret);
    $twitter->post_accountEnd_session();
  }

}

?>