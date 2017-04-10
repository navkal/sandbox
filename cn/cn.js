// Copyright 2017 Energize Apps.  All rights reserved.

var g_aImageWindows = [];
var g_aPropertiesWindows = [];
var g_tTreeMap = {};
var g_sImageButton = '<button class="btn btn-link btn-xs" onclick="openImageWindow(event)" title="Image" ><span class="glyphicon glyphicon-picture" style="font-size:18px;" ></span></button>';
var g_sPropertiesButton = '<button class="btn btn-link btn-xs" onclick="openPropertiesWindow(event)" title="Properties" ><span class="glyphicon glyphicon-list" style="font-size:18px;" ></span></button>';
var g_sSearchTargetPath = '';

$( document ).ready( initView );

function initView()
{
  $( window ).on( 'unload', closeChildWindows );
  $( window ).resize( resizeTree );
  resizeTree();
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
  sNode += '<a href="#' + sEncode + '" class="list-group-item clearfix" data-toggle="collapse" onclick="toggleFolder(event);" ondblclick="toggleFolder(event);" path="' + sPath + '" type="' + sType + '" oid="' + sOid + '" title="' + sPath + '" style="padding-left:' + sPadNode + ';' + sErrorStyle + '" >';
  sNode += '<i class="glyphicon glyphicon-chevron-down toggle"></i>';
  sNode += sLabel;
  sNode += '<span class="pull-right">';
  sNode += tRsp.image ? g_sImageButton : '';
  sNode += g_sPropertiesButton;
  sNode += '</span>';
  sNode += '</a>';

  // Open block of collapsed content
  var sCollapse = "";
  sCollapse += '<div class="list-group collapse in" id="' + sEncode + '" >';

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
    sCollapse += '<a class="list-group-item clearfix collapsed" data-toggle="collapse" onclick="toggleFolder(event);"  ondblclick="toggleFolder(event);"path="' + aChildInfo[iChild].path + '" type="' + aChildInfo[iChild].type.toLowerCase() + '" oid="' + aChildInfo[iChild].oid + '" title="' + aChildInfo[iChild].path + '" style="padding-left:' + sPadCollapse + '" >';
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
    var sDeviceAt = ( sDeviceLoc == '' ) ? '' : ( ' <span class="glyphicon glyphicon-map-marker"></span>' + sDeviceLoc );
    var sDeviceLabel = sDeviceDescr + sDeviceAt;
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
    if ( $( '#' + sEncode ).length == 0 )
    {
      tReplace.replaceWith( sSubtree );
    }
  }

  var nCollapseElements = aChildren.length + aDevices.length;
  if ( ( aChildren.length + aDevices.length ) == 0 )
  {
    $( '#circuitTree a[path="' + sPath + '"] .toggle' ).addClass( "no-children" );
  }

  // Set toggle completion handlers
  $( '#' + sEncode ).on( 'shown.bs.collapse', collapseShown );
  $( '#' + sEncode ).on( 'hidden.bs.collapse', collapseHidden );

  // Set tooltips on tree toggles
  setToggleTooltips();

  // Insert node in tree map
  g_tTreeMap[sPath] = tRsp;

  // Handle continuation of navigation to search result
  if ( g_sSearchTargetPath )
  {
    navigateToSearchTarget();
  }
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

Element.prototype.documentOffsetTop = function()
{
  return this.offsetTop + ( this.offsetParent ? this.offsetParent.documentOffsetTop() : 0 );
};

function navigateToSearchTarget()
{
  console.log( '========> Navigate to path: ' + g_sSearchTargetPath );

  // Find first collapsed node hiding search target
  var aPath = g_sSearchTargetPath.split( '.' );
  var bExpanded = true;
  var sNavPath = '';
  var tNavNode = null;
  for ( var iLen = 0; ( iLen < aPath.length ) && bExpanded; iLen ++ )
  {
    sNavPath = aPath.slice( 0, iLen + 1 ).join( '.' );
    tNavNode = $( '#circuitTree a[path="' + sNavPath + '"]' );
    bExpanded = tNavNode.find( ".toggle.glyphicon-chevron-down" ).length > 0;
    console.log( '===> ' + sNavPath + ' EXPANDED? ' + bExpanded );
  }

  // Terminate or continue navigation to search target
  if ( sNavPath == g_sSearchTargetPath )
  {
    console.log( "===========> DONE! at path=" + sNavPath );
    // Navigation done

    // Clear search target path
    g_sSearchTargetPath = '';

    // Highlight search target in tree
    var tSearchTarget = $( '#circuitTree a[path="' + sNavPath + '"]' );
    $( '.searchTarget' ).removeClass( 'searchTarget' );
    tSearchTarget.addClass( 'searchTarget' );

    // Auto-scroll tree to search target
    var tTree = $( '#circuitTree' );
    tTree.scrollTop( tTree.scrollTop() + ( tSearchTarget.position().top - tTree.position().top ) - ( tTree.height() / 2 ) + ( tSearchTarget.height() / 2 ) );

    // Set tooltips on toggle buttons
    setToggleTooltips();
  }
  else
  {
    // Navigation not done

    // Expand node hiding search result
    console.log( "===> Need to expand: " + sNavPath );
    tNavNode.trigger( 'click' );
  }
}

function toggleFolder( tEvent )
{
  var tItem = $( tEvent.target ).closest( '.list-group-item' );

  // If we haven't already determined that it's a leaf, toggle it
  if ( ! tItem.find( '.toggle' ).hasClass( "no-children" ) )
  {
    var sPath = $( tItem ).attr( "path" );
    if ( ! g_tTreeMap[sPath] )
    {
      // Expand for the first time
      getTreeNode( sPath );
    }
    else
    {
      switch( tEvent.type )
      {
        case 'click':
          if ( g_sSearchTargetPath )
          {
            $( tItem.attr( 'href' ) ).collapse( 'show' );
            tItem.find( '.toggle' ).removeClass( 'glyphicon-chevron-right' ).addClass( 'glyphicon-chevron-down' );
            navigateToSearchTarget();
          }
          break;

        case 'dblclick':
          if ( tItem.find( ".toggle.glyphicon-chevron-down" ).length > 0 )
          {
            // Collapse all descendants of this target
            $( '.collapse', $( tItem.attr( 'href' ) ) ).collapse( 'hide' );
          }
          break;
      }
    }
  }
}

function collapseShown( tEvent )
{
  collapseComplete( tEvent, true );
}

function collapseHidden( tEvent )
{
  collapseComplete( tEvent, false );
}

function collapseComplete( tEvent, bShown )
{
  var sId = $( tEvent.target ).attr( "id" );
  var tItem = $( '.list-group-item[href="#' + sId + '"]' );
  var tToggle = tItem.find( '.toggle' );

  if ( ! tToggle.hasClass( 'no-children' ) )
  {
    if ( bShown )
    {
      tToggle.removeClass( 'glyphicon-chevron-right' ).addClass( 'glyphicon-chevron-down' );
    }
    else
    {
      tToggle.removeClass( 'glyphicon-chevron-down' ).addClass( 'glyphicon-chevron-right' );
    }

    setToggleTooltips();
  }
}

function setToggleTooltips()
{
  // Right-arrow shows tooltip of parent
  $( '.toggle.glyphicon-chevron-right' ).each(
    function()
    {
      $( this ).attr( 'title', $( this ).parent().attr( 'title' ) );
    }
  );

  // Down-arrow shows collapse-all instruction
  $( '.toggle.glyphicon-chevron-down:not(.no-children)' ).attr( 'title', 'Double click to collapse all' );
}

function resizeTree()
{
  var tFooter = $( '.navbar-fixed-bottom' );
  var nHeightMinus = tFooter.length ? tFooter.height() + 80 : 40;
  $( '#circuitTree' ).css( 'height', $( window ).height() - $( '#circuitTree' ).position().top - nHeightMinus );
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
