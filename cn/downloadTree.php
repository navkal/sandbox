<?php
  // Copyright 2016 Energize Apps.  All rights reserved.

  require_once $_SERVER["DOCUMENT_ROOT"]."/../common/util.php";

  $tempFilename = sys_get_temp_dir() . "/" . "tree_" . time() . ".txt";

  $file = fopen( $tempFilename, "w" ) or die( "Unable to open file: " . $tempFilename );
  fwrite( $file,  "moo " . time() );
  fclose( $file );

  downloadFile( $tempFilename, "", "text/plain" );
?>
