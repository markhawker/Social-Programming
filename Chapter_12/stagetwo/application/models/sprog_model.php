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
  
  // Stage 2 Methods
  
  function updates($username, $limit, $offset)
  {
  	$this->db->select('*')->from('update')->join('like', 'like.update_id = update.id', 'left')->where('username', $username)->order_by('datetime', 'desc')->limit($limit, $offset);
  	$query = $this->db->get();
  	if($query->num_rows() > 0) {
  		$updates = array();
  		foreach($query->result() as $row) {
  			$comment_count = $this->comment_count($row->id);
  			$updates[] = array(
  				'id' => $row->id,
  				'text' => $row->text,
  				'source' => $row->source,
  				'time' => strtotime($row->datetime),
  				'like_count' => $row->count,
  				'comment_count' => $comment_count
  			);
  		}
  		return $updates;
  	} else {
  		return array(
  			array(
  				'id' => -1,
  				'text' => 'There are no updates, yet.',
  				'source' => 'n',
  				'time' => -1,
  				'like_count' => -1,
  				'comment_count' => -1
  			)
  		);
  	}
  }
  
  function my_comments($username)
  {
    $this->db->where('username', $username)->order_by('datetime', 'desc')->limit(10, 0);
  	$query = $this->db->get('comment');
  	if($query->num_rows() > 0) {
  		$my_comments = array();
  		foreach($query->result() as $row) {
  			$my_comments[] = array(
  				'id' => $row->update_id,
  				'text' => $row->text,
  				'source' => $row->source,
  				'time' => strtotime($row->datetime)
  			);
  		}
  		return $my_comments;
  	} else {
  		return array(
  			array(
  				'id' => -1,
  				'text' => 'There are no comments, yet.',
  				'source' => 'n',
  				'time' => -1
  			)
  		);
  	}	
  }
  
  function comment_count($update_id)
  {
  	$query = $this->db->get_where('comment', array('update_id' => $update_id));
  	return $query->num_rows();
  }
  
  function update($username, $update, $source)
  {
  	$data = array(
  		'id' => null,
  		'text' => $update,
  		'datetime' => date('Y-m-d H:i:s', time()),
  		'username' => $username,
  		'source' => $source
  	);
  	$this->db->insert('update', $data);
  	return $this->db->insert_id();
  }

  function delete($update_id)
  {
  	$this->db->where('id', $update_id);
	$this->db->delete('update');
	$tables = array('like', 'comment');
  	$this->db->where('update_id', $update_id);
	$this->db->delete($tables);
	return true;
  }
  
  function latest_updates($limit, $offset)
  {
    $this->db->select('*');
	$this->db->from('update');
	$this->db->join('like', 'like.update_id = update.id', 'left');
  	$this->db->order_by('datetime', 'desc');
  	$this->db->limit($limit, $offset);
  	$query = $this->db->get();
  	if($query->num_rows() > 0) {
  		$latest_updates = array();
  		foreach($query->result() as $row) {
  			$comment_count = $this->comment_count($row->id);
  			$latest_updates[] = array(
  				'id' => $row->id,
  				'text' => $row->text,
  				'source' => $row->source,
  				'time' => strtotime($row->datetime),
  				'username' => $row->username,
  				'like_count' => $row->count,
  				'comment_count' => $comment_count
  			);
  		}
  		return $latest_updates;
  	} else {
  		return array(
  			array(
  				'id' => -1,
  				'text' => 'There are no updates, yet.',
  				'source' => 'n',
  				'time' => -1,
  				'username' => -1,
  				'like_count' => -1,
  				'comment_count' => -1
  			)
  		);
  	}
  }
  
  function like($update_id)
  {
  	$query = $this->db->get_where('like', array('update_id' => $update_id));
  	if($query->num_rows() == 0) {
  		$data = array(
        	'update_id' => $update_id,
        	'count' => 1
        );
		$this->db->insert('like', $data);
	} else {
		$count = $query->row()->count;
		$this->db->set('count', $count + 1);
		$this->db->where('update_id', $update_id);
		$this->db->update('like'); 
	}
	return true;
  }
  
  function get_comments($update_id, $limit, $offset)
  {
  	$this->db->where('update_id', $update_id);
  	$this->db->order_by('datetime', 'desc');
  	$this->db->limit($limit, $offset);
  	$query = $this->db->get('comment');
  	if($query->num_rows() > 0) {
  		$comments = array();
  		foreach($query->result() as $row) {
  			$comments[] = array(
  				'update_id' => $row->update_id,
  				'text' => $row->text,
  				'source' => $row->source,
  				'time' => strtotime($row->datetime),
  				'username' => $row->username
  			);
  		}
  		return $comments;
  	} else {
  		return array(
  			array(
  				'update_id' => -1,
  				'text' => 'There are no comments, yet.',
  				'source' => 'n',
  				'time' => -1,
  				'username' => -1
  			)
  		);
  	}
  }
  
  function post_comment($update_id, $username, $comment, $source)
  {
    $data = array(
    	'id' => null,
  		'update_id' => $update_id,
  		'text' => $comment,
  		'datetime' => date('Y-m-d H:i:s', time()),
  		'username' => $username,
  		'source' => $source
  	);
  	$this->db->insert('comment', $data);
  	return $this->db->affected_rows();
  }
  
  function get_original($update_id)
  {
  	$query = $this->db->get_where('update', array('id' => $update_id));
  	return array(
  		'text' => $query->row()->text,
  		'username' => $query->row()->username,
  		'source' => $query->row()->source
  	);
  }

}

?>