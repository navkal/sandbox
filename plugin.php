<!-- Copyright 2016 Energize Apps.  All rights reserved. -->

<div class="container">
  <?php
    echo( "<h2>Plugin stub</h2>" );
    
    // Execute Python script
    $command = quote( getenv( "PYTHON" ) ) . " test.py";
    error_log( "===> command=" . $command );
    exec( $command, $output, $status );
    echo( "===> output=" . print_r( $output, true ) );
  ?>
</div>
