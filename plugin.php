<!-- Copyright 2016 Energize Apps.  All rights reserved. -->
<?php
  // Execute Python script
  $command = quote( getenv( "PYTHON" ) ) . " interface.py 2>&1 -t room -i 10";
  $command = quote( getenv( "PYTHON" ) ) . " interface.py 2>&1 -t circuit -i 3";
  $command = quote( getenv( "PYTHON" ) ) . " interface.py 2>&1 -t BAD_TYPE -i 3";
  $command = quote( getenv( "PYTHON" ) ) . " interface.py 2>&1 -t device -i 164";
  error_log( "===> command=" . $command );

  exec( $command, $output, $status );
  error_log( "===> output=" . print_r( $output, true ) );

  // Extract and decode output
  error_log( "===> output length=" . count( $output ) );
  error_log( "===> type of output[0]=" . gettype( $output[0] ) );
  $result = $output[ count( $output ) - 1 ];
  $obj = (array) json_decode( $result );
  error_log( "===> length of obj=" . count( $obj ) );
  error_log( "===> type of obj=" . gettype( $obj ) );
  ksort( $obj );
?>

<div class="container">
  <table class="table" >
    <thead>
      <tr>
        <th>Key</th>
        <th>Value</th>
      </tr>
    </thead>
    <tbody>
      <?php
        foreach ( $obj as $key => $val )
        {
          echo( '<tr>' );
          echo( '<td>' . $key . '</td>' );
          echo( '<td>' . $val . '</td>' );
          echo( '</tr>' );
        }
      ?>
    </tbody>
  </table>
</div>
