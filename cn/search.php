<?php
  // Copyright 2017 Energize Apps.  All rights reserved.
  require_once $_SERVER["DOCUMENT_ROOT"]."/../common/util.php";
  error_log( "====> get=" . print_r( $_GET, true ) );

  $aResult = [ "abc", "abcc", "abccc" ];
  echo json_encode( $aResult );
?>
