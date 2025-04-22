<?php 
$mysqli = new mysqli("192.168.100.172","sellin","cSN8aVjd39C7@","warehouse");

// Check connection
if ($mysqli -> connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
  exit();
}

ini_set('memory_limit','1024M'); phpinfo(); ?>
