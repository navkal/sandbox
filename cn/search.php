<?php
  // Copyright 2017 Energize Apps.  All rights reserved.
  require_once $_SERVER['DOCUMENT_ROOT'].'/../common/util.php';
  error_log( '====> get=' . print_r( $_GET, true ) );

  $sSearchText = $_GET['searchText'];
  $iRequestTime = $_GET['requestTime'];

  $command = quote( getenv( "PYTHON" ) ) . " search.py 2>&1 -s " . quote( $sSearchText );
  error_log( "===> command=" . $command );
  exec( $command, $output, $status );
  error_log( "===> output=" . print_r( $output, true ) );

  $sSearchResults = $output[ count( $output ) - 1 ];
  $tSearchResults = json_decode( $sSearchResults );
  $aSearchResults = $tSearchResults->searchResults;

  $tResult =
  [
    'requestTime' => $iRequestTime,
    'searchText' => $sSearchText,
    'searchResults' => $aSearchResults
  ];

  echo json_encode( $tResult );
?>
