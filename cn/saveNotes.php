<?php
  // Copyright 2017 Energize Apps.  All rights reserved.
  require_once $_SERVER["DOCUMENT_ROOT"]."/../common/util.php";
  error_log( "====> post=" . print_r( $_POST, true ) );

  // Get posted values
  $postTable = $_POST['objectTable'];
  $postSelector = $_POST['objectSelector'];

  // Determine query selector argument
  $sTag = ( $postTable == "cirobj" ) ? 'p' : 'i';
  $sSelector = ' -' . ( ( $postTable == "cirobj" ) ? 'p' : 'i' ) . ' ' . $postSelector;

  echo json_encode( [ $postTable, $postSelector, $sSelector ] );;
?>
