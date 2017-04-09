<?php
  // Copyright 2017 Energize Apps.  All rights reserved.
  require_once $_SERVER["DOCUMENT_ROOT"]."/../common/util.php";
  error_log( "====> get=" . print_r( $_GET, true ) );

  $sSearchText = $_GET['searchText'];
  $iRequestTime = $_GET['requestTime'];

  $tResult =
  [
    "requestTime" => $iRequestTime,
    "searchText" => $sSearchText,
    "searchResults" => [ $sSearchText."abc", "abcc".$sSearchText.'xx', "abcWWWWWWWWWWWWWWWWWWWWWWWWWWWWW".$sSearchText."WWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWcc" ]
  ];

  echo json_encode( $tResult );
?>
