<!-- Copyright 2017 Energize Apps.  All rights reserved. -->

<!DOCTYPE html>
<html lang="en">

  <?php
    require_once $_SERVER["DOCUMENT_ROOT"]."/../common/util.php";
  ?>
  <head>
    <title>Properties:
      <?php
        echo( $_REQUEST["path"] );
      ?>
    </title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <!-- JS libraries -->
    <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

    <!-- CSS libraries -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:400,700" >

    <!-- Shared text style -->
    <style>
      .lato
      {
        font-family: 'Lato', Arial, sans-serif;
        line-height: 1.5;
      }

      .table > tbody > tr:first-child > td
      {
        border: none;
      }
    </style>
  </head>

	<body>
    <div class="container">

      <br/>
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title" id="propertiesTitle"></h3>
        </div>
        <div class="panel-body">
          <div id="objectArea">
            <table class="table table-hover table-condensed" >
              <tbody id="objectLayout" >
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
 	</body>
</html>

<script>
  var g_sPath = '<?=$_REQUEST["path"]?>';
  var g_sType = '<?=$_REQUEST["type"]?>';
  var g_sOid = '<?=$_REQUEST["oid"]?>';
  var g_tProperties = window.opener.g_tTreeMap[g_sPath];

  $( document ).ready( loadProperties );

  function loadProperties()
  {
    if ( g_tProperties )
    {
      showProperties();
    }
    else
    {
      getProperties();
    }
  }

  function getProperties()
  {
    // Post request to server
    var tPostData = new FormData();
    tPostData.append( "objectTable", ( ( g_sType == 'device' ) ? "device" : "cirobj" ) );
    tPostData.append( "objectSelector", ( ( g_sType == 'device' ) ? g_sOid : g_sPath ) );

    $.ajax(
      "getObject.php",
      {
        type: 'POST',
        processData: false,
        contentType: false,
        dataType : 'json',
        data: tPostData
      }
    )
    .done( saveProperties )
    .fail( handlePostError );
  }

  function saveProperties( tRsp, sStatus, tJqXhr )
  {
    g_tProperties = tRsp;
    showProperties();
  }

  function showProperties()
  {
    // Display title
    var sTitle = g_tProperties["description"] || g_tProperties["path"];
    $( "#propertiesTitle" ).html( sTitle );

    // Initialize map of property labels
    var tLabelMap =
    {
      error: "Error",
      path: "Path",
      children: "Children",
      //description: "Description",
      parent: "Parent",
      voltage: "Voltage",
      //room_id: "Room ID",
      devices: "Devices",
      //id: "ID",
      object_type: "Type",
      // panel_id: "Panel ID",
      closet_new: "New Closet",
      closet_old: "Old Closet",
      loc_new: "New Location",
      loc_old: "Old Location",
      loc_type: "Location Type",
      loc_descr: "Location Description"
    };

    // Build map of labels and values for display
    var tDisplayProps = {};
    var aKeys = Object.keys( g_tProperties );
    for ( var i = 0; i < aKeys.length; i++ )
    {
      // Get display label
      var sKey = aKeys[i];
      var sLabel = tLabelMap[sKey];

      if ( sLabel )
      {
        // Get display value
        var sVal = g_tProperties[sKey];
        if ( Array.isArray( sVal ) )
        {
          sVal = sVal.length;
        }

        // Save pair in map
        tDisplayProps[sLabel] = sVal;
      }
      else console.log( "=> Omitted field: " + sKey );
    }

    // Build layout of property display
    aKeys = Object.keys( tDisplayProps ).sort();
    var sTbody = "";
    for ( var i = 0; i < aKeys.length; i++ )
    {
      var sKey = aKeys[i];
      var sVal = tDisplayProps[sKey];
      if ( sVal != '' )
      {
        var sColor = ( sKey == "Error" ) ? "color:red;" : '';
        sTbody += '<tr><td style="text-align:right;' + sColor + '"><b>' + sKey + '</b></td><td style="' + sColor + '">' + sVal + '</td></tr>';
      }
    }

    // Display properties
    $( "#objectLayout" ).html( sTbody );
  }

  function handlePostError( tJqXhr, sStatus, sErrorThrown )
  {
    console.log( "=> ERROR=" + sStatus + " " + sErrorThrown );
    console.log( "=> HEADER=" + JSON.stringify( tJqXhr ) );
  }

</script>
