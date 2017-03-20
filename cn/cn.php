<!-- Copyright 2017 Energize Apps.  All rights reserved. -->

<script>
  var TEST_DEPTH = 0;

  var TREE = {};
  var REQ = 0;
  var RSP = 0;
  var START = new Date();

  $( document ).ready( walkTree );

  function walkTree()
  {

    // Set handlers to show plus and minus icons on folders
    $( ".collapse" ).on( "shown.bs.collapse", collapseShow );
    $( ".collapse" ).on( "hidden.bs.collapse", collapseHide );

    return;
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
    RSP ++;

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

    // Update timer
    $( "#walkTime" ).html( timeSince( START ) );
    $( "#walkReq" ).html( REQ );
    $( "#walkRsp" ).html( RSP );

    // Optionally truncate tree for faster testing
    if ( TEST_DEPTH && ( nDepth >= TEST_DEPTH ) )
    {
      tRsp.children=[];
    }

    // If all requests have been satisfied and there are no more children to traverse, display the tree
    if ( ( REQ == RSP ) && ( tRsp.children.length == 0 ) )
    {
      $( "#walkStatus" ).html( "Circuit Tree" );
      displayTree();
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

      // Special handling to order folders before devices:
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
    console.log( "=> ERROR=" + sStatus + " " + sErrorThrown );
    console.log( "=> HEADER=" + JSON.stringify( tJqXhr ) );
  }

  function timeSince( startTime )
  {
    var ms = new Date() - startTime;

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

    sDay = day ? ( day + "d " ) : "";
    sElapsed = sDay + pad( hr, 2 ) + ":" + pad( min, 2 ) + ":" + pad( sec, 2 ) + "." + pad( ms, 3 );

    return sElapsed;
  }

  function pad( iNum, iLen )
  {
    var sNum = String( iNum );
    while( sNum.length < iLen )
    {
      sNum = "0" + sNum;
    }
    return sNum;
  }

  function openPropertiesWindow()
  {
    return popupWindow("/cn/properties.php", "Properties", window, 300, 500 );
  }

  function popupWindow( url, title, win, w, h )
  {
    var y = ( win.top.outerHeight / 2 ) + win.top.screenY - ( h / 2)
    var x = ( win.top.outerWidth / 2 ) + win.top.screenX - ( w / 2)
    return win.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+y+', left='+x);
  }

  function collapseShow( tEvent )
  {
    $(this).parent().find( ".glyphicon-plus" ).removeClass( "glyphicon-plus" ).addClass( "glyphicon-minus" );
  }

  function collapseHide( tEvent )
  {
    $(this).parent().find( ".glyphicon-minus" ).removeClass( "glyphicon-minus" ).addClass( "glyphicon-plus" );
  }

</script>

<div class="container">
<a class="btn btn-primary" role="button" data-toggle="collapse" href="#collapseExample" >
  Link with href
</a>
<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" >
  Button with data-target
</button>
<div class="collapse" id="collapseExample">
  <div class="well">
    stuff
  </div>
</div>

<!-------------------------------------->
        <div class="panel panel-default panel-info">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" href="#subtree_MWSB" >
                <span class="glyphicon glyphicon-plus"></span>
              </a>
              <a data-toggle="collapse" href="#subtree_MWSB" >
                <span class="glyphicon glyphicon-flash"></span>
              </a>
              <a data-toggle="collapse" href="#subtree_MWSB" >
                MWSB
              </a>
              <a href="javascript:void(null)" onclick="openPropertiesWindow()">
                Properties
              </a>
            </h4>
          </div>
          <div id="subtree_MWSB" class="panel-collapse collapse" >
            <dl class="dl-horizontal list-group-item list-group-item-info" >
              <dt>moo</dt>
              <dd>a</dd>
              <dd>b</dd>
            </dl>
          </div>
        </div>
<!-------------------------------------->



<!-- == DELETE THIS BELOW ------------->
                                                <div style="display:none" >
                                                  <h3 id="walkStatus" >Retrieving Circuit Tree...</h3>
                                                  <h5>Retrieval time: <span id="walkTime"></span></h5>
                                                  <h5>Requests: <span id="walkReq"></span></h5>
                                                  <h5>Responses: <span id="walkRsp"></span></h5>
                                                  <hr/>
                                                  <div id="objectTree" style="font-family:courier">
                                                  </div>
                                                </div>
<!-- == DELETE THIS ABOVE ------------->

</div>
