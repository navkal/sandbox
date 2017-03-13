<!-- Copyright 2016 Energize Apps.  All rights reserved. -->
<?php
  // Execute Python script
  $command = quote( getenv( "PYTHON" ) ) . " interface.py 2>&1";
  error_log( "===> command=" . $command );
  exec( $command, $output, $status );
  error_log( "===> output=" . print_r( $output, true ) );

  // Decode JSON output
  $dev = (array) json_decode( $output[0] );
  ksort( $dev );
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
        foreach ( $dev as $key => $val )
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
