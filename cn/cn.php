<!-- Copyright 2017 Energize Apps.  All rights reserved. -->

<style>
  .just-padding
  {
    padding: 15px;
  }

  .list-group.list-group-root
  {
    padding: 0;
    overflow: hidden;
  }

  .list-group.list-group-root .list-group
  {
    margin-bottom: 0;
  }

  .list-group.list-group-root .list-group-item
  {
    border-radius: 0;
    border-width: 1px 0 0 0;
  }

  .list-group.list-group-root > .list-group-item:first-child
  {
    border-top-width: 0;
  }

  .toggle
  {
    margin-right: 5px;
  }
</style>

<script>

  var g_aPropertiesWindows = [];
  var g_tTreeMap = {};
  var g_sPropertiesButton = '<button class="btn btn-link btn-xs pull-right" onclick="openPropertiesWindow(event)" title="Properties" ><span class="glyphicon glyphicon-info-sign" style="width:40px;font-size:16px;" ></span></button>';

  $( document ).ready( initView );

  function initView()
  {
    $( window ).on( 'unload', closePropertiesWindows );
    getTreeNode( "" );
  }

  function getTreeNode( sPath )
  {
    // --> KLUDGE --> remove after paths are fixed in DB -->
    if ( sPath.includes( " " ) ) { console.log( "=> BAD PATH=" + sPath ); return; }
    // <-- KLUDGE <-- remove after paths are fixed in DB <--

    // Post request to server
    var tPostData = new FormData();
    tPostData.append( "objectType", "circuit" );
    tPostData.append( "objectSelector", sPath );

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
    // Get path and related values
    var sPath = tRsp.path;
    var sEncode = sPath.replace( /\./g, '-dot-' );
    var nDepth = sPath.split( "." ).length;
    var sPadNode = "" + ( nDepth * 15 ) + "px";
    var sPadCollapse = "" + ( ( nDepth + 1 ) * 15 ) + "px";

    // Insert node in tree
    g_tTreeMap[sPath] = { children:[] };

    // Display tree node
    var sNode = "";
    sNode += '<a href="#' + sEncode + '" class="list-group-item" data-toggle="collapse" path="' + sPath + '" style="padding-left:' + sPadNode + '" >';
    sNode += '<i class="glyphicon glyphicon-chevron-down toggle"></i>';
    sNode += sPath;
    sNode += g_sPropertiesButton;
    sNode += '</a>';

    // Open block of collapsed content
    var sCollapse = "";
    sCollapse += '<div class="list-group collapse in" id="' + sEncode + '">';

    // Sort children and load into collapsed content
    var aChildren = tRsp.children;
    for ( var iChild = 0; iChild < aChildren.length; iChild ++ )
    {
      var sChildPath = aChildren[iChild][1];
      if ( sChildPath == sPath ) continue;  // <-- KLUDGE. REMOVE AFTER ROOT PARENT FIELD IS CLEARED
      g_tTreeMap[sPath].children.push( sChildPath );
    }
    g_tTreeMap[sPath].children.sort();
    for ( var iChild = 0; iChild < g_tTreeMap[sPath].children.length; iChild ++ )
    {
      var sChildPath = g_tTreeMap[sPath].children[iChild];
      sCollapse += '<a class="list-group-item collapsed" data-toggle="collapse" path="' + sChildPath + '" style="padding-left:' + sPadCollapse + '" >';
      sCollapse += '<i class="glyphicon glyphicon-chevron-right toggle"></i>';
      sCollapse += sChildPath;
      sCollapse += g_sPropertiesButton;
      sCollapse += '</a>';
    }

    // Sort devices and load into collapsed content
    var aDevices = tRsp.devices;
    var aDeviceInfo = [];
    for ( var iDevice = 0; iDevice < aDevices.length; iDevice ++ )
    {
      var iDeviceId = aDevices[iDevice][0];
      var iDeviceLoc = aDevices[iDevice][1];
      var sDeviceDescr = aDevices[iDevice][2];
      sDevicePath = sPath + " " + iDeviceId + "," + iDeviceLoc + "," + sDeviceDescr;
      var sDeviceText = sDeviceDescr + ' at ' + iDeviceLoc
      aDeviceInfo.push( { path: sDevicePath, text: sDeviceText } );
    }
    aDeviceInfo.sort( compareDevices );
    for ( var iDevice = 0; iDevice < aDeviceInfo.length; iDevice ++ )
    {
      sCollapse += '<a href="#" class="list-group-item" path="' + aDeviceInfo[iDevice].path + '" style="padding-left:' + sPadCollapse + '" >';
      sCollapse += aDeviceInfo[iDevice].text;
      sCollapse += g_sPropertiesButton;
      sCollapse += '</a>';
    }

    // Close collapsed content block
    sCollapse += '</div>';

    // Load collapsed content
    console.log( "=========> num elements=" + nCollapseElements );
    var sSubtree = sNode + sCollapse;

    if ( Object.keys( g_tTreeMap ).length == 1 )
    {
      $( "#circuitTree" ).append( sSubtree );
    }
    else
    {
      var tReplace = $( '#circuitTree a[path="' + sPath + '"]' );
      tReplace.replaceWith( sSubtree );
    }

    var nCollapseElements = aChildren.length + aDevices.length;
    if ( ( aChildren.length + aDevices.length ) == 0 )
    {
      $( '#circuitTree a[path="' + sPath + '"] .toggle' ).css( "visibility", "hidden" );
    }

    // Attach toggle handler
    $( '.list-group-item' ).off( 'click' );
    $( '.list-group-item' ).on( 'click', toggleFolder );
  }

  function compareDevices( d1, d2 )
  {
    return d1.text.localeCompare( d2.text );
  }

  function handlePostError( tJqXhr, sStatus, sErrorThrown )
  {
    console.log( "=> ERROR=" + sStatus + " " + sErrorThrown );
    console.log( "=> HEADER=" + JSON.stringify( tJqXhr ) );
  }

  function toggleFolder( tEvent )
  {
    $( '.toggle', this )
      .toggleClass( 'glyphicon-chevron-right' )
      .toggleClass( 'glyphicon-chevron-down' );

    var sPath = $( this ).attr( "path" );
    if ( ! g_tTreeMap[sPath] )
    {
      getTreeNode( sPath );
    }

  }

  function openPropertiesWindow( tEvent )
  {
    tEvent.stopPropagation();
    var sPath = $(event.target).closest( "a" ).attr("path");
    var sUrl = '/cn/properties.php?path=' + sPath;
    childWindowOpen( tEvent, g_aPropertiesWindows, sUrl, "Properties", sPath, 400, 500 );
  }

  function closePropertiesWindows()
  {
    childWindowsClose( g_aPropertiesWindows );
  }


// -> -> -> Manage child windows -> -> ->

// Open child window and save reference in array.
// - If opened with Click, save in element [0].
// - If opened with <key>+Click, save in new element.
function childWindowOpen( tEvent, aChildWindows, sUrl, sName, sNameSuffix, iWidth, iHeight )
{
  var iIndex, sWindowFeatures, bFocus;

  if ( tEvent.altKey || tEvent.shiftKey || tEvent.ctrlKey )
  {
    // User pressed a special key while clicking.  Allow browser default behavior.
    iIndex = aChildWindows.length;
    sName += "_" + sNameSuffix;
    sWindowFeatures = "";
    bFocus = false;
  }
  else
  {
    // User pressed no special key while clicking.  Override browser default behavior.
    iIndex = 0;
    var nLeft = parseInt( ( screen.availWidth / 2 ) - ( iWidth / 2 ) );
    var nTop = parseInt( ( screen.availHeight / 2 ) - ( iHeight / 2 ) );
    sWindowFeatures = "width=" + iWidth + ",height=" + iHeight + ",status,resizable,left=" + nLeft + ",top=" + nTop + ",screenX=" + nLeft + ",screenY=" + nTop + ",scrollbars=yes";
    bFocus = true;
  }

  // Open the new child window
  aChildWindows[iIndex] = window.open( sUrl, sName, sWindowFeatures );

  // Optionally focus on the new child window
  if ( bFocus )
  {
    aChildWindows[iIndex].focus();
  }
}

// Close all child windows in given array
function childWindowsClose( aWindows )
{
  for ( var iIndex = 0; iIndex < aWindows.length; iIndex ++ )
  {
    aWindows[iIndex].close();
  }
}

// <- <- <- Manage child windows <- <- <-



</script>

<div class="container">

  <div class="just-padding">
    <div id="circuitTree" class="list-group list-group-root well">
    </div>
  </div>

<!------------------------------------------------------------------------->

                              <div class="just-padding">

                                <div class="list-group list-group-root well">

                                  <a href="#item-1" class="list-group-item" data-toggle="collapse">
                                    <i class="glyphicon glyphicon-chevron-right"></i>Item 1
                                  </a>
                                  <div class="list-group collapse" id="item-1">

                                    <a href="#item-1-1" class="list-group-item" data-toggle="collapse">
                                      <i class="glyphicon glyphicon-chevron-right"></i>Item 1.1
                                    </a>
                                    <div class="list-group collapse" id="item-1-1">
                                      <a href="#" class="list-group-item">Item 1.1.1</a>
                                      <a href="#" class="list-group-item">Item 1.1.2</a>
                                      <a href="#" class="list-group-item">Item 1.1.3</a>
                                      <a href="#item-1-1-4" class="list-group-item" data-toggle="collapse">
                                        <i class="glyphicon glyphicon-chevron-right"></i>Item 1.1.4
                                      </a>
                                      <div class="list-group collapse" id="item-1-1-4">
                                        <a href="#" class="list-group-item">Item 1.1.4.1</a>
                                        <a href="#" class="list-group-item">Item 1.1.4.2</a>
                                        <a href="#" class="list-group-item">Item 1.1.4.3</a>
                                      </div>

                                    </div>

                                    <a href="#item-1-2" class="list-group-item" data-toggle="collapse">
                                      <i class="glyphicon glyphicon-chevron-right"></i>Item 1.2
                                    </a>
                                    <div class="list-group collapse" id="item-1-2">
                                      <a href="#" class="list-group-item">Item 1.2.1</a>
                                      <a href="#" class="list-group-item">Item 1.2.2</a>
                                      <a href="#" class="list-group-item">Item 1.2.3</a>
                                    </div>

                                    <a href="#item-1-3" class="list-group-item" data-toggle="collapse">
                                      <i class="glyphicon glyphicon-chevron-right"></i>Item 1.3
                                    </a>
                                    <div class="list-group collapse" id="item-1-3">
                                      <a href="#" class="list-group-item">Item 1.3.1</a>
                                      <a href="#" class="list-group-item">Item 1.3.2</a>
                                      <a href="#" class="list-group-item">Item 1.3.3</a>
                                    </div>

                                  </div>

                                  <a href="#item-2" class="list-group-item" data-toggle="collapse">
                                    <i class="glyphicon glyphicon-chevron-right"></i>Item 2
                                  </a>
                                  <div class="list-group collapse" id="item-2">

                                    <a href="#item-2-1" class="list-group-item" data-toggle="collapse">
                                      <i class="glyphicon glyphicon-chevron-right"></i>Item 2.1
                                    </a>
                                    <div class="list-group collapse" id="item-2-1">
                                      <a href="#" class="list-group-item">Item 2.1.1</a>
                                      <a href="#" class="list-group-item">Item 2.1.2</a>
                                      <a href="#" class="list-group-item">Item 2.1.3</a>
                                    </div>

                                    <a href="#item-2-2" class="list-group-item" data-toggle="collapse">
                                      <i class="glyphicon glyphicon-chevron-right"></i>Item 2.2
                                    </a>
                                    <div class="list-group collapse" id="item-2-2">
                                      <a href="#" class="list-group-item">Item 2.2.1</a>
                                      <a href="#" class="list-group-item">Item 2.2.2</a>
                                      <a href="#" class="list-group-item">Item 2.2.3</a>
                                    </div>

                                    <a href="#item-2-3" class="list-group-item" data-toggle="collapse">
                                      <i class="glyphicon glyphicon-chevron-right"></i>Item 2.3
                                    </a>
                                    <div class="list-group collapse" id="item-2-3">
                                      <a href="#" class="list-group-item">Item 2.3.1</a>
                                      <a href="#" class="list-group-item">Item 2.3.2</a>
                                      <a href="#" class="list-group-item">Item 2.3.3</a>
                                    </div>

                                  </div>


                                  <a href="#item-3" class="list-group-item" data-toggle="collapse">
                                    <i class="glyphicon glyphicon-chevron-right"></i>Item 3
                                  </a>
                                  <div class="list-group collapse" id="item-3">

                                    <a href="#item-3-1" class="list-group-item" data-toggle="collapse">
                                      <i class="glyphicon glyphicon-chevron-right"></i>Item 3.1
                                    </a>
                                    <div class="list-group collapse" id="item-3-1">
                                      <a href="#" class="list-group-item">Item 3.1.1</a>
                                      <a href="#" class="list-group-item">Item 3.1.2</a>
                                      <a href="#" class="list-group-item">Item 3.1.3</a>
                                    </div>

                                    <a href="#item-3-2" class="list-group-item" data-toggle="collapse">
                                      <i class="glyphicon glyphicon-chevron-right"></i>Item 3.2
                                    </a>
                                    <div class="list-group collapse" id="item-3-2">
                                      <a href="#" class="list-group-item">Item 3.2.1</a>
                                      <a href="#" class="list-group-item">Item 3.2.2</a>
                                      <a href="#" class="list-group-item">Item 3.2.3</a>
                                    </div>

                                    <a href="#item-3-3" class="list-group-item" data-toggle="collapse">
                                      <i class="glyphicon glyphicon-chevron-right"></i>Item 3.3
                                    </a>
                                    <div class="list-group collapse" id="item-3-3">
                                      <a href="#" class="list-group-item">Item 3.3.1</a>
                                      <a href="#" class="list-group-item">Item 3.3.2</a>
                                      <a href="#" class="list-group-item">Item 3.3.3</a>
                                    </div>

                                  </div>

                                </div>

                              </div>
<!------------------------------------------------------------------------->

</div>
