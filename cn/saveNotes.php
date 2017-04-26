<?php
  // Copyright 2017 Energize Apps.  All rights reserved.
  require_once $_SERVER["DOCUMENT_ROOT"]."/../common/util.php";
  error_log( "====> post=" . print_r( $_POST, true ) );

  // Get posted values
  $postTable = $_POST['objectTable'];
  $postSelector = $_POST['objectSelector'];

  // Determine query selector argument
  $sSelector = ' -' . ( ( $postTable == "cirobj" ) ? 'p' : 'i' ) . ' ' . $postSelector;


  $command = quote( getenv( "PYTHON" ) ) . " saveNotes.py 2>&1 -t " . $postTable . $sSelector;
  error_log( "===> command=" . $command );
  exec( $command, $output, $status );
  error_log( "===> output=" . print_r( $output, true ) );

  $sResult = $output[ count( $output ) - 1 ];
  echo $sResult;


  /////////echo json_encode( [ $postTable, $postSelector, $sSelector ] );
?>
