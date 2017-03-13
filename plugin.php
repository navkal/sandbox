<!-- Copyright 2016 Energize Apps.  All rights reserved. -->
<?php
  // Execute Python script
  $command = quote( getenv( "PYTHON" ) ) . " interface.py 2>&1 -t device -i 164";
  $command = quote( getenv( "PYTHON" ) ) . " interface.py 2>&1 -t room -i 10";
  $command = quote( getenv( "PYTHON" ) ) . " interface.py 2>&1 -t circuit -i 3";
  error_log( "===> command=" . $command );
  exec( $command, $output, $status );
  error_log( "===> output=" . print_r( $output, true ) );

  // Decode JSON output
  $obj = (array) json_decode( $output[0] );
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
