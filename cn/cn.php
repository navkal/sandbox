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

  .no-children
  {
    color: lightgray;
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
      "cn/getObject.php",
      {
        type: 'POST',
        processData: false,
        contentType: false,
        dataType : 'json',
        data: tPostData
      }
    )
    .done( insertTreeNode )
    .fail( handlePostError );
  }

  function insertTreeNode( tRsp, sStatus, tJqXhr )
  {
    // Get path and related values
    var sPath = tRsp.path;
    var sEncode = sPath.replace( /\./g, '-dot-' );
    var aPath = sPath.split( "." );
    var nDepth = aPath.length;
    var sLabel = aPath[nDepth-1] + ": " + tRsp.description;
    var sPadNode = "" + ( nDepth * 15 ) + "px";
    var sPadCollapse = "" + ( ( nDepth + 1 ) * 15 ) + "px";

    // Display tree node
    var sNode = "";
    sNode += '<a href="#' + sEncode + '" class="list-group-item" data-toggle="collapse" path="' + sPath + '" title="' + sPath + '" style="padding-left:' + sPadNode + '" >';
    sNode += '<i class="glyphicon glyphicon-chevron-down toggle"></i>';
    sNode += sLabel;
    sNode += g_sPropertiesButton;
    sNode += '</a>';

    // Open block of collapsed content
    var sCollapse = "";
    sCollapse += '<div class="list-group collapse in" id="' + sEncode + '">';

    // Sort children and load into collapsed content
    var aChildren = tRsp.children;
    var aChildInfo = [];
    for ( var iChild = 0; iChild < aChildren.length; iChild ++ )
    {
      var sChildPath = aChildren[iChild][1];
      if ( sChildPath == sPath ) continue;  // <-- KLUDGE. REMOVE AFTER ROOT PARENT FIELD IS CLEARED

      var sChildDescr = aChildren[iChild][2];
      var sChildLabel = sChildPath.split( "." )[nDepth] + ": " + sChildDescr;
      aChildInfo.push( { path: sChildPath, label: sChildLabel } );
    }
    aChildInfo.sort( compareNodes );
    for ( var iChild = 0; iChild < aChildInfo.length; iChild ++ )
    {
      sCollapse += '<a class="list-group-item collapsed" data-toggle="collapse" path="' + aChildInfo[iChild].path + '" title="' + aChildInfo[iChild].path + '" style="padding-left:' + sPadCollapse + '" >';
      sCollapse += '<i class="glyphicon glyphicon-chevron-right toggle"></i>';
      sCollapse += aChildInfo[iChild].label;
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
      var sDeviceLabel = sDeviceDescr + ' at ' + iDeviceLoc
      aDeviceInfo.push( { path: sDevicePath, label: sDeviceLabel } );
    }
    aDeviceInfo.sort( compareNodes );
    for ( var iDevice = 0; iDevice < aDeviceInfo.length; iDevice ++ )
    {
      sCollapse += '<a href="javascript:void(null)" class="list-group-item" path="' + aDeviceInfo[iDevice].path + '" title="Attached to ' + sPath + '" style="padding-left:' + sPadCollapse + '" >';
      sCollapse += aDeviceInfo[iDevice].label;
      sCollapse += g_sPropertiesButton;
      sCollapse += '</a>';
    }

    // Close collapsed content block
    sCollapse += '</div>';

    // Load collapsed content
    var sSubtree = sNode + sCollapse;

    if ( Object.keys( g_tTreeMap ).length == 0 )
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
      $( '#circuitTree a[path="' + sPath + '"] .toggle' ).addClass( "no-children" );
    }

    // Attach toggle handler
    $( '.list-group-item' ).off( 'click' );
    $( '.list-group-item' ).on( 'click', toggleFolder );

    // Insert node in tree map
    g_tTreeMap[sPath] = tRsp;
  }

  function compareNodes( d1, d2 )
  {
    // Extract labels
    var sLabel1 = d1.label;
    var sLabel2 = d2.label;

    // Extract prefixes
    var sPrefix1 = sLabel1.split( ":" )[0];
    var sPrefix2 = sLabel2.split( ":" )[0];

    // Determine whether prefixes are numeric
    var bNum1 = /^\d+$/.test( sPrefix1 );
    var bNum2 = /^\d+$/.test( sPrefix2 );

    var iResult = 0;
    if ( bNum1 && bNum2 )
    {
      // Compare numerically
      iResult = sPrefix1 - sPrefix2;
    }

    // If no difference found, compare full text
    if ( iResult == 0 )
    {
      // Compare alphabetically
      iResult = sLabel1.localeCompare( sLabel2 );
    }

    return iResult;
  }

  function handlePostError( tJqXhr, sStatus, sErrorThrown )
  {
    console.log( "=> ERROR=" + sStatus + " " + sErrorThrown );
    console.log( "=> HEADER=" + JSON.stringify( tJqXhr ) );
  }

  function toggleFolder( tEvent )
  {
    if ( ! $(tEvent.target).find(".toggle").hasClass( "no-children" ) )
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

</div>
