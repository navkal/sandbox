<?php
  // Copyright 2017 Energize Apps.  All rights reserved.
  require_once $_SERVER['DOCUMENT_ROOT'].'/../common/util.php';
  error_log( '====> get=' . print_r( $_GET, true ) );

  $sSearchText = $_GET['searchText'];
  $iRequestTime = $_GET['requestTime'];

  $tResult =
  [
    'requestTime' => $iRequestTime,
    'searchText' => $sSearchText,
    'searchResults' =>
    [
      [
        'path' => 'MWSB.5.DBH.5',
        'searchResult' => $sSearchText.'abc'
      ],
      [
        'path' => 'MWSB.7.DG',
        'searchResult' => 'abcc'.$sSearchText.'xx'
      ],
      [
        'path' => 'MWSB.8.DL.1',
        'searchResult' => 'abcc'.$sSearchText.'xx', 'XYZ'.$sSearchText.'WWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWcc'
      ]
    ]
  ];

  echo json_encode( $tResult );
?>
