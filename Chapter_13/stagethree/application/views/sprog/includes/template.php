<?php

$data['title'] = $title;

$this->load->view('sprog/includes/header', $data); 
$this->load->view($content); 
$this->load->view('sprog/includes/footer'); 

?>