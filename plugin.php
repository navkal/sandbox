<!-- Copyright 2016 Energize Apps.  All rights reserved. -->

<div class="container">
  <?php   
    // Execute Python script
    $command = quote( getenv( "PYTHON" ) ) . " interface.py";
    error_log( "===> command=" . $command );
    exec( $command, $output, $status );
    
    var_dump( $output );
  ?>
</div>
