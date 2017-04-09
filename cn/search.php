<?php
  // Copyright 2017 Energize Apps.  All rights reserved.
  require_once $_SERVER["DOCUMENT_ROOT"]."/../common/util.php";
  error_log( "====> get=" . print_r( $_GET, true ) );
  
  $sText = $_GET['text'];

  $aResult = [ $sText."abc", "abcc".$sText.'xx', "abcWWWWWWWWWWWWWWWWWWWWWWWWWWWWW".$sText."WWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWcc" ];
  echo json_encode( $aResult );
?>
