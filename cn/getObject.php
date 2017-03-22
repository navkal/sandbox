<?php
  // Copyright 2017 Energize Apps.  All rights reserved.
  require_once $_SERVER["DOCUMENT_ROOT"]."/../common/util.php";
  error_log( "====> post=" . print_r( $_POST, true ) );

  // Get posted values
  $postTable = $_POST['objectTable'];
  $postSelector = $_POST['objectSelector'];

  // Determine query selector argument
  $selector = '';
  if ( $postTable == "circuit" )
  {
    // Object is a circuit

    if ( $postSelector != '' )
    {
      if ( ctype_digit( $postSelector ) )
      {
        // All digits: argument is an id
        $selector = ' -i ';
      }
      else
      {
        // Not all digits: argument is a path
        $selector = ' -p ';
      }

      $selector .= $postSelector;
    }
  }
  else
  {
    // Object is not a circuit
    $selector = ' -i ' . ( ( $postSelector == '' ) ? '1' : $postSelector );
  }


  $command = quote( getenv( "PYTHON" ) ) . " interface.py 2>&1 -t " . $postTable . $selector;
  error_log( "===> command=" . $command );
  exec( $command, $output, $status );
  error_log( "===> output=" . print_r( $output, true ) );

  echo $output[ count( $output ) - 1 ];
?>
