<!-- Copyright 2017 Energize Apps.  All rights reserved. -->
<?php
  $_SESSION["pathDotReplacement"] = '-_-_-';
?>

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
    padding-top: 7px;
    padding-bottom: 5px;
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
  var g_aImageWindows = [];
  var g_aPropertiesWindows = [];
  var g_tTreeMap = {};
  var g_sImageButton = '<button class="btn btn-link btn-xs" onclick="openImageWindow(event)" title="Image" ><span class="glyphicon glyphicon glyphicon-picture" style="font-size:18px;" ></span></button>';
  var g_sPropertiesButton = '<button class="btn btn-link btn-xs" onclick="openPropertiesWindow(event)" title="Properties" ><span class="glyphicon glyphicon-list" style="font-size:18px;" ></span></button>';

  $( document ).ready( initView );

  function initView()
  {
    $( window ).on( 'unload', closeChildWindows );
    getTreeNode( "" );
  }

  function getTreeNode( sPath )
  {
    // Post request to server
    var tPostData = new FormData();
    tPostData.append( "objectTable", "cirobj" );
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
    .fail( handleAjaxError );
  }

  function insertTreeNode( tRsp, sStatus, tJqXhr )
  {
    // Get path and related values
    var sPath = tRsp.path;
    var sEncode = sPath.replace( /\./g, '-_-_-' );
    var aPath = sPath.split( "." );
    var nDepth = aPath.length;
    var sLabel = aPath[nDepth-1] + ": " + ( tRsp.error ? tRsp.error : tRsp.description );
    var sPadNode = "" + ( nDepth * 15 ) + "px";
    var sPadCollapse = "" + ( ( nDepth + 1 ) * 15 ) + "px";
    var sType = tRsp.object_type.toLowerCase();
    var sOid = tRsp.id;
    var sErrorStyle =  tRsp.error ? 'color:red;' : '';

    // Display tree node
    var sNode = "";
    sNode += '<a href="#' + sEncode + '" class="list-group-item clearfix" data-toggle="collapse" path="' + sPath + '" type="' + sType + '" oid="' + sOid + '" title="' + sPath + '" style="padding-left:' + sPadNode + ';' + sErrorStyle + '" >';
    sNode += '<i class="glyphicon glyphicon-chevron-down toggle"></i>';
    sNode += sLabel;
    sNode += '<span class="pull-right">';
    sNode += tRsp.image ? g_sImageButton : '';
    sNode += g_sPropertiesButton;
    sNode += '</span>';
    sNode += '</a>';

    // Open block of collapsed content
    var sCollapse = "";
    sCollapse += '<div class="list-group collapse in" id="' + sEncode + '">';

    // Sort children and load into collapsed content
    var aChildren = tRsp.children;
    var aChildInfo = [];
    for ( var iChild = 0; iChild < aChildren.length; iChild ++ )
    {
      var sChildOid = aChildren[iChild][0];
      var sChildPath = aChildren[iChild][1];
      var sChildDescr = aChildren[iChild][2];
      var sChildLabel = sChildPath.split( "." )[nDepth] + ": " + sChildDescr;
      var sChildType = aChildren[iChild][3];
      var sChildImage = aChildren[iChild][4];
      aChildInfo.push( { oid: sChildOid, path: sChildPath, label: sChildLabel, type: sChildType, image: sChildImage } );
    }
    aChildInfo.sort( compareNodes );
    for ( var iChild = 0; iChild < aChildInfo.length; iChild ++ )
    {
      sCollapse += '<a class="list-group-item clearfix collapsed" data-toggle="collapse" path="' + aChildInfo[iChild].path + '" type="' + aChildInfo[iChild].type.toLowerCase() + '" oid="' + aChildInfo[iChild].oid + '" title="' + aChildInfo[iChild].path + '" style="padding-left:' + sPadCollapse + '" >';
      sCollapse += '<i class="glyphicon glyphicon-chevron-right toggle"></i>';
      sCollapse += aChildInfo[iChild].label;
      sCollapse += '<span class="pull-right">';
      sCollapse += aChildInfo[iChild].image ? g_sImageButton : '';
      sCollapse += g_sPropertiesButton;
      sCollapse += '</span>';
      sCollapse += '</a>';
    }

    // Sort devices and load into collapsed content
    var aDevices = tRsp.devices;
    var aDeviceInfo = [];
    for ( var iDevice = 0; iDevice < aDevices.length; iDevice ++ )
    {
      var sDeviceOid = aDevices[iDevice][0];
      var sDeviceLoc = aDevices[iDevice][1] || aDevices[iDevice][2];
      var sDeviceDescr = aDevices[iDevice][3];
      sDevicePath = sPath + " " + sDeviceOid + "," + sDeviceLoc + "," + sDeviceDescr;
      var sDeviceLabel = sDeviceDescr + ' <span class="text-info">(at ' + sDeviceLoc + ')</span>';
      aDeviceInfo.push( { oid: sDeviceOid, path: sDevicePath, label: sDeviceLabel } );
    }

    aDeviceInfo.sort( compareNodes );
    for ( var iDevice = 0; iDevice < aDeviceInfo.length; iDevice ++ )
    {
      sCollapse += '<a href="javascript:void(null)" class="list-group-item clearfix" path="' + aDeviceInfo[iDevice].path + '" type="device" oid="' + aDeviceInfo[iDevice].oid + '" title="Attached to ' + sPath + '" style="padding-left:' + sPadCollapse + '" >';
      sCollapse += aDeviceInfo[iDevice].label;
      sCollapse += '<span class="pull-right">';
      sCollapse += g_sPropertiesButton;
      sCollapse += '</span>';
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

  function handleAjaxError( tJqXhr, sStatus, sErrorThrown )
  {
    console.log( "=> ERROR=" + sStatus + " " + sErrorThrown );
    console.log( "=> HEADER=" + JSON.stringify( tJqXhr ) );
  }

  function toggleFolder( tEvent )
  {
    if ( ! $(tEvent.target).find(".toggle").hasClass( "no-children" ) )
    {
      if (tEvent.ctrlKey)
        alert('Ctrl down');
      if (tEvent.altKey)
        alert('Alt down');
      if (tEvent.shiftKey)
        alert('Shift down');
      if (tEvent.metaKey)
        alert('Meta down');

  
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

  function collapseTree()
  {
    // Toggle the icons
    var tOpenFolders = $( '#circuitTree .toggle.glyphicon-chevron-down:not(.no-children)' ).closest( '.list-group-item' );
    tOpenFolders.each( toggleIcon );

    // Collapse the content
    $( '.collapse' ).collapse( 'hide' );
  }

  function toggleIcon()
  {
    $( '.toggle', this )
      .toggleClass( 'glyphicon-chevron-right' )
      .toggleClass( 'glyphicon-chevron-down' );
  }

  function openImageWindow( tEvent )
  {
    tEvent.preventDefault();
    tEvent.stopPropagation();

    var sPath = $( tEvent.target ).closest( "a" ).attr( "path" );
    var sUrl = 'cn/image.php?path=' + sPath;

    var nDefaultWidth = 800;
    var nDefaultAspect = 2550 / 3300;
    var nDefaultHeight = nDefaultWidth / nDefaultAspect;

    childWindowOpen( tEvent, g_aImageWindows, sUrl, "Image", sPath, nDefaultWidth, nDefaultHeight );
  }

  function openPropertiesWindow( tEvent )
  {
    tEvent.preventDefault();
    tEvent.stopPropagation();

    var sPath = $( tEvent.target ).closest( "a" ).attr( "path" );
    var sType = $( tEvent.target ).closest( "a" ).attr( "type" );
    var sOid = $( tEvent.target ).closest( "a" ).attr( "oid" );
    var sUrl = 'cn/properties.php?path=' + sPath + '&type=' + sType + '&oid=' + sOid;

    childWindowOpen( tEvent, g_aPropertiesWindows, sUrl, "Properties", sPath, 350, 550 );
  }

  function closeChildWindows()
  {
    childWindowsClose( g_aImageWindows );
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

  // -> -> -> Tree dump -> -> ->

  var g_iStartTime = null;
  var g_iInterval = null;
  function startTreeDump( tEvent )
  {
    closeChildWindows();
    $( '#dumpStart,#circuitNavigator' ).addClass( "hidden" );
    $( "#dumpTime" ).html( "00:00:00" );
    $( '#dumpStatus' ).removeClass( "hidden" );
    g_iInterval = setInterval( waitTreeDump, 1000 );
    g_iStartTime = new Date();
    return true;
  }

  function waitTreeDump()
  {
    $( "#dumpTime" ).html( timeSince( g_iStartTime ) );

    $.ajax(
      "cn/downloadWait.php",
      {
        type: 'GET'
      }
    )
    .done( endTreeDump )
    .fail( handleAjaxError );
  }

  function endTreeDump( tRsp, sStatus, tJqXhr )
  {
    if ( tRsp && g_iInterval )
    {
      console.log( "=> Tree dump completed.  Elapsed time: " + timeSince( g_iStartTime ) );
      clearInterval( g_iInterval );
      g_iInterval = null;
      $( '#dumpStart,#circuitNavigator' ).removeClass( "hidden" );
      $( '#dumpStatus' ).addClass( "hidden" );
    }
  }

  // <- <- <- Tree dump <- <- <-

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
    sElapsed = sDay + pad( hr, 2 ) + ":" + pad( min, 2 ) + ":" + pad( sec, 2 ) /* + "." + pad( ms, 3 ) */;

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

</script>

<br/>
<div class="container">
  <div id="dumpStart" >
    <a class="btn btn-default" href="cn/downloadTree.php" onclick="return startTreeDump(event);" title="Generate and download dump of Circuit Tree" >Download Tree Dump</a>
    <span class="well-sm text-info" >
      <sup><i class="glyphicon glyphicon-asterisk"></i></sup>
      Takes several minutes
    </span>
  </div>
  <div id="dumpStatus" class="well well-sm hidden" >
    Generating tree. <span id="dumpTime"></span>
  </div>

  <div id="circuitNavigator">
    <hr/>
    <a class="btn btn-default btn-xs" href="javascript:void(null)" onclick="collapseTree();" title="Collapse Circuit Tree" >Collapse All</a>

    <div class="row" >
      <div class="just-padding">
        <div id="circuitTree" class="list-group list-group-root well">
        </div>
      </div>
    </div>
  </div>
</div>
