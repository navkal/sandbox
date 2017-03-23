<?php
  // Copyright 2016 Energize Apps.  All rights reserved.

  require_once $_SERVER["DOCUMENT_ROOT"]."/../common/util.php";

  error_log( "===> Starting tree dump" );
  
  $g_aTree = [];
  walkSubtree( "" );
  downloadTree();

  function walkSubtree( $sPath )
  {
    // Retrieve object
    $sSelector = ( $sPath == "" ) ? "" : ' -p ' . $sPath;
    $sCommand = quote( getenv( "PYTHON" ) ) . " interface.py 2>&1 -t circuit " . $sSelector;
    // error_log( "===> command=" . $sCommand );
    exec( $sCommand, $sOutput, $sStatus );
    // error_log( "===> output=" . print_r( $sOutput, true ) );

    // Decode object
    $sJson = $sOutput[ count( $sOutput ) - 1 ];
    $tObject = json_decode( $sJson );

    // Extract path from object
    $sPath = $tObject->path;

    // Set up indentation
    $nDepth = count( explode( ".", $sPath ) );
    $sIndent = str_repeat( '-', $nDepth - 1 );

    // Insert path in tree
    global $g_aTree;
    $g_aTree[$sPath] = $sIndent . $sPath . PHP_EOL;

    // Insert devices in tree




    // Traverse children
    $aChildren = $tObject->children;
    foreach( $aChildren as $aChild )
    {
      $sChildPath = $aChild[1];
      walkSubtree( $sChildPath );
    }

  }

  function downloadTree()
  {
    $sFilename = sys_get_temp_dir() . "/" . "tree_" . time() . ".txt";
    $file = fopen( $sFilename, "w" ) or die( "Unable to open file: " . $sFilename );
    writeTree( $file );
    fclose( $file );
    downloadFile( $sFilename, "", "text/plain" );
  }

  function writeTree( $file )
  {
    global $g_aTree;
    ksort( $g_aTree );

    foreach ( $g_aTree as $sKey => $sPath )
    {
      error_log( "==> key=" . $sKey . " path=" . $sPath );
      fwrite( $file, $g_aTree[$sKey] );
    }
  }

?>
