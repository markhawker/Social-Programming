<?php

function hash_email($email_address) {
  $normalized_email_address = strtolower(trim($email_address));
  return sprintf("%u", crc32($normalized_email_address)).'_'.md5($normalized_email_address);
}

?>