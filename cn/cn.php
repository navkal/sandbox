<!-- Copyright 2017 Energize Apps.  All rights reserved. -->

<script>

  var TREE = {};

  $( document ).ready( walkTree );

  function walkTree()
  {
    walkSubtree( "" );
  }

  function walkSubtree( path )
  {
    // --> KLUDGE --> remove after paths are fixed in DB -->
    if ( path.includes( " " ) ) { console.log( "====> BAD PATH=" + path ); return; }
    // <-- KLUDGE <-- remove after paths are fixed in DB <--

    // Post request to server
    var tPostData = new FormData();
    tPostData.append( "objectType", "circuit" );
    tPostData.append( "objectSelector", path );

    $.ajax(
      "cn/query.php",
      {
        type: 'POST',
        processData: false,
        contentType: false,
        dataType : 'json',
        data: tPostData
      }
    )
    .done( handlePostResponse )
    .fail( handlePostError );
  }

  function handlePostResponse( tRsp, sStatus, tJqXhr )
  {
    var sPath = tRsp.path;

    // Append path to tree
    var nDepth = sPath.split( "." ).length
    var sIndent = Array( nDepth ).join( "-")
    TREE[sPath] = sIndent + sPath + "<br/>";

    // Append devices to tree
    var aDevices = tRsp.devices;
    if ( aDevices.length > 0 ){ console.log( "====> " + sPath + " has " + aDevices.length + " devices" ); }
    for ( var iDevice = 0; iDevice < aDevices.length; iDevice ++ )
    {
      var iDeviceId = aDevices[iDevice][0];
      var sDeviceDescr = aDevices[iDevice][1];
      console.log( iDeviceId + " " + sDeviceDescr );
      sDevicePath = sPath + ".0" + iDeviceId + "-" + sDeviceDescr;
      TREE[sDevicePath] = sIndent + "-" + sDevicePath + "<br/>";
    }

    // Update display
    $( "#objectTree" ).html( "" );
    var aPaths = Object.keys( TREE ).sort();
    for ( var iPath = 0; iPath < aPaths.length; iPath ++ )
    {
      $( "#objectTree" ).append( TREE[aPaths[iPath]] );
    }

    // Traverse children
    for ( var iChild = 0; iChild < tRsp.children.length; iChild ++ )
    {
      var sChildPath = tRsp.children[iChild][1];
      if ( sChildPath != sPath )  // <-- KLUDGE. REMOVE AFTER ROOT PARENT FIELD IS CLEARED
      {
        walkSubtree( sChildPath );
      }
    }
  }

  function handlePostError( tJqXhr, sStatus, sErrorThrown )
  {
    console.log( "==> ERROR=" + sStatus + " " + sErrorThrown );
    console.log( "==> HEADER=" + JSON.stringify( tJqXhr ) );
  }

  </script>

<div class="container">
  <div id="objectTree" style="overflow:auto">
  </div>
</div>
