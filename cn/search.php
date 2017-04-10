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
        'path' => 'MWSB',
        'searchResult' => 'root+' . $sSearchText . ' MWSB'
      ],
      [
        'path' => 'MWSB.9',
        'searchResult' => $sSearchText . ' MWSB.9'
      ],
      [
        'path' => 'MWSB.1.PBA.1',
        'searchResult' => 'leaf+' . $sSearchText . ' MWSB.1.PBA.1'
      ],
      [
        'path' => 'MWSB.5.DHB.5',
        'searchResult' => $sSearchText . ' MWSB.5.DHB.5'
      ],
      [
        'path' => 'MWSB.7.DG',
        'searchResult' => 'MWSB.7.DG ' . $sSearchText .' MWSB.7.DG'
      ],
      [
        'path' => 'MWSB.8.DL.1',
        'searchResult' => 'MWSB.8.DL.1 ' . $sSearchText . ' MWSB.8.DL.1'
      ]
    ]
  ];

  echo json_encode( $tResult );
?>
