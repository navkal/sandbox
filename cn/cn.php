<!-- Copyright 2017 Energize Apps.  All rights reserved. -->

<script>

  var TREE = {};
  var REQ = 0;
  var RSP = 0;
  var startTime = new Date();

  $( document ).ready( walkTree );

  function walkTree()
  {
    walkSubtree( "" );
  }

  function walkSubtree( path )
  {
    // --> KLUDGE --> remove after paths are fixed in DB -->
    if ( path.includes( " " ) ) { console.log( "=> BAD PATH=" + path ); return; }
    // <-- KLUDGE <-- remove after paths are fixed in DB <--

    // Post request to server
    var tPostData = new FormData();
    tPostData.append( "objectType", "circuit" );
    tPostData.append( "objectSelector", path );

    REQ ++;
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
    var nDepth = sPath.split( "." ).length;
    var sIndent = Array( nDepth ).join( "-");
    TREE[sPath] = sIndent + sPath + "<br/>";

    // Append devices to tree
    var aDevices = tRsp.devices;
    for ( var iDevice = 0; iDevice < aDevices.length; iDevice ++ )
    {
      var iDeviceId = aDevices[iDevice][0];
      var iDeviceLoc = aDevices[iDevice][1];
      var sDeviceDescr = aDevices[iDevice][2];
      var sDevName = iDeviceId + "," + iDeviceLoc + "," + sDeviceDescr;
      sDevicePath = sPath + " " + sDevName;
      sIndent = Array( nDepth + 1 ).join( "-");
      TREE[sDevicePath] = sIndent + sDevicePath + "<br/>";
    }

    // If this is a leaf, or there are device leaves, update display
    if ( ( ( RSP / REQ ) > 0.99 ) && ( ( aDevices.length > 0 ) || ( tRsp.children.length == 0 ) ) )
    {
      displayTree();
    }

    $( "#walkTime" ).html( timeSince( startTime ) );

    RSP ++;
    if ( ( RSP / REQ ) > 0.99 ) console.log( "===> " + REQ + " " + RSP + " " + ( RSP / REQ ) );

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

  function displayTree()
  {
    var sTree = "";
    var aPaths = Object.keys( TREE ).sort( compareKeys );
    for ( var iPath = 0; iPath < aPaths.length; iPath ++ )
    {
      sTree += TREE[aPaths[iPath]];
    }
    $( "#objectTree" ).html( sTree );
  }

  function compareKeys( s1, s2 )
  {
    // Extract path and device strings
    var aSplit1 = s1.split( " " );
    var aSplit2 = s2.split( " " );
    var sPath1 = aSplit1.shift();
    var sPath2 = aSplit2.shift();
    var sDev1 = aSplit1.join( " " );
    var sDev2 = aSplit2.join( " " );

    // Split path into fragments
    var aPath1 = sPath1.split( "." );
    var aPath2 = sPath2.split( "." );

    // Compare paths
    var iResult = 0;
    for ( var i = 0; ( iResult == 0 ) && ( i < aPath1.length ) && ( i < aPath2.length ); i++ )
    {
      var sFragment1 = aPath1[i];
      var sFragment2 = aPath2[i];

      var isNum1 = /^\d+$/.test( sFragment1 );
      var isNum2 = /^\d+$/.test( sFragment2 );

      if ( isNum1 && isNum2 )
      {
        // Compare fragments as numbers
        iResult = sFragment1 - sFragment2;
      }
      else
      {
        // Compare fragments as strings
        iResult = sFragment1.localeCompare( sFragment2 );
      }
    }

    // If leading parts of paths are the same, compare their lengths
    if ( iResult == 0 )
    {
      iResult = aPath1.length - aPath2.length;

      // Special treatment to order folders before devices:
      // If path lengths differ by 1, shorter path has a device, and longer path does not, flip the result
      if (
            ( ( iResult == -1 ) && ( sDev1 != "" ) && ( sDev2 == "" ) )
            ||
            ( ( iResult == 1 ) && ( sDev1 == "" ) && ( sDev2 != "" ) )
          )
      {
        iResult *= -1;
      }
    }

    // If paths are the same, compare devices
    if ( iResult == 0 )
    {
      if ( sDev1 == sDev2 )
      {
        iResult = 0;
      }
      else if ( sDev1 == "" )
      {
        iResult = -1;
      }
      else if ( sDev2 == "" )
      {
        iResult = 1;
      }
      else
      {
        // Compare device IDs, which are guaranteed to be numeric and unique
        iResult = sDev1.split( "," )[0] - sDev2.split( "," )[0];
      }
    }

    return iResult;
  }

  function handlePostError( tJqXhr, sStatus, sErrorThrown )
  {
    RSP ++;
    console.log( "==> ERROR=" + sStatus + " " + sErrorThrown );
    console.log( "==> HEADER=" + JSON.stringify( tJqXhr ) );
  }

  function timeSince( startTime )
  {
    var ms = new Date() - startTime + 886390000;

    var x = 1000 * 60 * 60 * 24;
    var day = Math.floor( ms / x );
    ms = ms - ( day * x );

    x = 1000 * 60 * 60;
    var hr = Math.floor( ms / x );
    ms = ms - ( hr * x );

    x = 1000 * 60;
    var min = Math.floor( ms / x );
    ms = ms - ( min * x );

    x = 1000;
    var sec = Math.floor( ms / x );
    ms = ms - ( sec * x );

    return day + "d " + pad( hr, 2 ) + ":" + pad( min, 2 ) + ":" + pad( sec, 2 ) + "." + pad( ms, 3 );
  }

  function pad( num, len )
  {
    var sNum = String( num );
    while( sNum.length < len )
    {
      sNum = "0" + sNum;
    }
    return sNum;
  }

  </script>

<div class="container">
  Elapsed time: <span id="walkTime"></span>
  <div id="objectTree" style="overflow:auto">
    <h1>Walking the Circuit Tree...</h1>
  </div>
</div>
