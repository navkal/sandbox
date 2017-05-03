// Copyright 2017 Energize Apps.  All rights reserved.

var g_tProperties = window.opener.g_tTreeMap[g_sPath];

$( document ).ready( loadProperties );

function loadProperties()
{
  // Set handler to toggle plus and minus icons on collapse panel head
  $( ".collapse" ).on( "shown.bs.collapse hidden.bs.collapse", collapseToggle );

  // Show/hide Notes Editor
  var bEdit = ( g_sRole == 'technician' );
  $( '#notesEditor,#historyArea' ).css( 'display', bEdit ? 'initial' : 'none' );
  if ( bEdit )
  {
    $( '#notes' ).on( 'keyup paste drop', setOnBeforeUnload );
  }

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

  // If main window already has this element, update it
  if ( window.opener.g_tTreeMap[g_tProperties["path"]] )
  {
    window.opener.g_tTreeMap[g_tProperties["path"]] = tRsp;
  }

  showProperties();
}

function showProperties()
{
  // Display title
  var sTitle = g_tProperties["description"] || g_tProperties["path"];
  $( "#propertiesTitle" ).html( sTitle );

  // Enable/disable buttons
  $( '#btnUp' ).prop( 'disabled', ! g_tProperties.parent_path );
  $( '#btnDown' ).prop( 'disabled', ( window.opener.g_iPropertiesTrailIndex >= ( window.opener.g_aPropertiesTrail.length - 1 ) ) );

  // Initialize map of property labels
  var tLabelMap =
  {
    error: "Error",
    path: "Path",
    children: "Children",
    //description: "Description",
    attached_to: "Attached To",
    //parent_path: "Parent",
    voltage: "Voltage",
    //room_id: "Room ID",
    devices: "Devices",
    //id: "ID",
    object_type: "Type",
    // panel_id: "Panel ID",
    loc_new: "Location",
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

  // Display history
  showHistory();
}

function goUp()
{
  var tButton = window.opener.$( '#circuitTree a[path="' +  g_tProperties.parent_path + '"] .propertiesButton' );
  tButton.addClass( 'btnUp' );
  tButton.click();
}

function goDown()
{
  var aPath = window.opener.g_aPropertiesTrail.slice( 0, window.opener.g_iPropertiesTrailIndex + 2 );
  var sPath = aPath.join( '.' );
  var tButton = window.opener.$( '#circuitTree a[path="' +  sPath + '"] .propertiesButton' );
  tButton.addClass( 'btnDown' );
  tButton.click();
}

function collapseToggle()
{
  $( this ).parent().find( ".glyphicon-plus,.glyphicon-minus" ).toggleClass( "glyphicon-plus" ).toggleClass( "glyphicon-minus" );
}

function showHistory()
{
  var aEvents = g_tProperties.events;

  if ( aEvents.length == 0 )
  {
    $( '#historyTable' ).hide();
    $( '#historyNone' ).show();
  }
  else
  {
    aEvents.sort( compareEventTimestamps );

    var sTbody = '';
    for ( var iEvent in aEvents )
    {
      var aCells = aEvents[iEvent];

      sTbody += '<tr>';
      for( var iCell in aCells )
      {
        var sCell = aCells[iCell];
        if ( iCell == 0 )
        {
          sCell = new Date( Math.floor( sCell * 1000 ) ).toLocaleString();
        }
        sTbody += '<td>' + sCell + '</td>';
      }
      sTbody += '</tr>';
    }
    $( '#historyTableBody' ).html( sTbody );

    $( '#historyTable' ).show();
    $( '#historyNone' ).hide();
  }
}

function compareEventTimestamps( aEvent1, aEvent2 )
{
  return aEvent2[0] - aEvent1[0];
}

function handlePostError( tJqXhr, sStatus, sErrorThrown )
{
  console.log( "=> ERROR=" + sStatus + " " + sErrorThrown );
  console.log( "=> HEADER=" + JSON.stringify( tJqXhr ) );
}

function saveNotes( tEvent )
{
  // Post request to server
  var tPostData = new FormData();
  tPostData.append( "targetTable", ( ( g_sType == 'device' ) ? "Device" : "CircuitObject" ) );
  tPostData.append( "targetColumn", ( ( g_sType == 'device' ) ? "id" : "path" ) );
  tPostData.append( "targetValue", ( ( g_sType == 'device' ) ? g_sOid : g_sPath ) );
  tPostData.append( "notes", $( '#notes' ).val() );

  $.ajax(
    "saveNotes.php",
    {
      type: 'POST',
      processData: false,
      contentType: false,
      dataType : 'json',
      data: tPostData
    }
  )
  .done( saveNotesCompletion )
  .fail( handlePostError );
}

function saveNotesCompletion( tRsp, sStatus, tJqXhr )
{
  clearNotes( { type: 'click' } );
  getProperties();
}

function clearNotes( tEvent )
{
  $( '#notes' ).val( '' );
  setOnBeforeUnload( tEvent );
}

// Set or clear handler for onbeforeunload event
function setOnBeforeUnload( tEvent )
{
  switch( tEvent.type )
  {
    case 'paste':
    case 'drop':
      // Don't know what was pasted or dropped; set artificial value
      sVal = 'unknown';
      break;

    default:
      // Get current value of text area
      sVal = $( '#notes' ).val().trim();
      break;
  }

  // Set/clear handler based on value
  window.onbeforeunload = ( sVal == '' ) ? null : onBeforeUnload;
}

// Handle onbeforeunload event
function onBeforeUnload( tEvent )
{
  var sMsg = 'Changes you made will not be saved.';
  tEvent.returnValue = sMsg;
  return sMsg;
}
