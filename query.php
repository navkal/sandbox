<?php
  // Copyright 2017 Energize Apps.  All rights reserved.
  require_once $_SERVER["DOCUMENT_ROOT"]."/../common/util.php";
  error_log( "====> post=" . print_r( $_POST, true ) );

  $command = quote( getenv( "PYTHON" ) ) . " interface.py 2>&1 -t " . $_POST['object_type'] . " -i " . $_POST['object_id'];
  error_log( "===> command=" . $command );
  exec( $command, $output, $status );
  error_log( "===> output=" . print_r( $output, true ) );

  echo $output[ count( $output ) - 1 ];
?>
