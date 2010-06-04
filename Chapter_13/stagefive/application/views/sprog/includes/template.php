<?php

$source = $this->encrypt->decode($this->session->userdata('source'));

$data['title'] = $title;

switch($source) {
 case 's':
   $data['via'] = '<b>Sprog</b>';
   break;
 case 't':
   $data['via'] = '<b>Twitter</b>';
   break;  
 case 'f':
   $data['via'] = '<b>Facebook</b>';
   break;
 case 'g':
   $data['via'] = '<b>Google Friend Connect</b>';
   break;
 default:
   $data['via'] = false;
}

$this->load->view('sprog/includes/header', $data); 
$this->load->view($content); 
$this->load->view('sprog/includes/footer'); 

?>