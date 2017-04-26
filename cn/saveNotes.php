<?php
  // Copyright 2017 Energize Apps.  All rights reserved.
  require_once $_SERVER["DOCUMENT_ROOT"]."/../common/util.php";
  error_log( "====> post=" . print_r( $_POST, true ) );

  // Get posted values
  $postTable = $_POST['objectTable'];
  $postSelector = $_POST['objectSelector'];




  // Use getObject.php as example
  // Use getObject.php as example
  // Use getObject.php as example
  // Use getObject.php as example
  // Use getObject.php as example
  // Use getObject.php as example
  // Use getObject.php as example




  echo json_encode( [ $postTable, $postSelector ] );;
?>
