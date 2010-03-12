<?php

class Sprog_Model extends Model {

  function Sprog_Model()
  {
    parent::Model();
  }
  
  // Stage 1 Methods
  
  function validate($username, $password)
  {
  	$this->db->where('username', $username);
  	$this->db->where('password', $password);
  	$query = $this->db->get('user');
  	  	
  	if($query->num_rows() == 1) {
  		$data = array(
  			'username' => $query->row()->username,
  			'fullname' => $query->row()->fullname
  		);
  		return $data;
  	} else {
  		return false;
  	}
  	
  }
  
  function create($username, $fullname, $password)
  {
  	$query = $this->db->get_where('user', array('username' => $username));
  	if($query->num_rows() == 0) {
  		$user = array(
  			'username' => $username,
  			'fullname' => $fullname,
  			'password' => $password
  		);
  		$query = $this->db->insert('user', $user);
  		
  		if($query) {
  			$data = array(
  				'username' => $username,
  				'fullname' => $fullname
  			);
  			return $data;
  		} else {
  			return false;
  		}
  	} else {
  		return false;
  	}
  }

}

?>