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

  .list-group.list-group-root > .list-group > .list-group-item
  {
    padding-left: 30px;
  }

  .list-group.list-group-root > .list-group > .list-group > .list-group-item
  {
    padding-left: 45px;
  }

  .list-group-item .glyphicon
  {
    margin-right: 5px;
  }
</style>

<script>

  var g_aPropertiesWindows = [];
  var g_tTree = {};
  var g_sPropertiesButton = '<button class="btn btn-link btn-xs pull-right" onclick="openPropertiesWindow(event)" title="Properties" ><span class="glyphicon glyphicon-info-sign" style="width:40px;font-size:18px;" ></span></button>';

  $( document ).ready( initView );

  function initView()
  {
    $( window ).on( 'unload', closePropertiesWindows );
    getTreeNode( "" );
  }

  function getTreeNode( path )
  {
    // --> KLUDGE --> remove after paths are fixed in DB -->
    if ( path.includes( " " ) ) { console.log( "=> BAD PATH=" + path ); return; }
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
    // Insert node in tree
    var sPath = tRsp.path;
    g_tTree[sPath] = {};

    // Display tree node
    var sNode = "";
    sNode += '<a href="#' + sPath + '" class="list-group-item" data-toggle="collapse" path="' + sPath + '" >';
    sNode += '<i class="glyphicon glyphicon-chevron-right"></i>';
    sNode += sPath;
    sNode += g_sPropertiesButton;
    sNode += '</a>';
    $( "#circuitTree" ).append( sNode );

    // Open block of collapsed content
    var sCollapsed = "";
    sCollapsed += '<div class="list-group collapse" id="' + sPath + '">';

    // Load children into collapsed content
    for ( var iChild = 0; iChild < tRsp.children.length; iChild ++ )
    {
      var sChildPath = tRsp.children[iChild][1];
      if ( sChildPath == sPath ) continue;  // <-- KLUDGE. REMOVE AFTER ROOT PARENT FIELD IS CLEARED

      sCollapsed += '<a class="list-group-item" data-toggle="collapse" path="' + sChildPath + '" >';
      sCollapsed += '<i class="glyphicon glyphicon-chevron-right"></i>';
      sCollapsed += sChildPath;
      sCollapsed += g_sPropertiesButton;
      sCollapsed += '</a>';
    }

    // Load devices into collapsed content
    var aDevices = tRsp.devices;
    var aDeviceText = [];
    for ( var iDevice = 0; iDevice < aDevices.length; iDevice ++ )
    {
      var iDeviceId = aDevices[iDevice][0];
      var iDeviceLoc = aDevices[iDevice][1];
      var sDeviceDescr = aDevices[iDevice][2];
      sDevicePath = sPath + " " + iDeviceId + "," + iDeviceLoc + "," + sDeviceDescr;
      var sDeviceText = sDeviceDescr + ' at ' + iDeviceLoc
      aDeviceText.push( { path: sDevicePath, text: sDeviceText } );
      console.log( "" + iDevice + ": " + sDeviceText );
    }
    aDeviceText.sort( compareDevices );
    for ( var iDevice = 0; iDevice < aDeviceText.length; iDevice ++ )
    {
      sCollapsed += '<a href="#" class="list-group-item" path="' + aDeviceText[iDevice].path + '">';
      sCollapsed += aDeviceText[iDevice].text;
      sCollapsed += g_sPropertiesButton;
      sCollapsed += '</a>';
    }

    // Close collapsed content block
    sCollapsed += '</div>';

    // Load collapsed content
    $( "#circuitTree" ).append( sCollapsed );

    // Attach toggle handler
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

  function toggleFolder()
  {
    $( '.glyphicon-chevron-right,.glyphicon-chevron-down', this )
      .toggleClass('glyphicon-chevron-right')
      .toggleClass('glyphicon-chevron-down');
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


  <div class="just-padding">
    <div class="list-group list-group-root well">

              <a href="#MWSBmoo" class="list-group-item" data-toggle="collapse">
                <i class="glyphicon glyphicon-chevron-right"></i>
                MWSB
              </a>
              <div class="list-group collapse" id="MWSBmoo">
                <a href="#" class="list-group-item">1,UNKNOWN,Kitchen Mechanical</a>
                <a href="#" class="list-group-item">2,UNKNOWN,Transfer sw</a>
                <a href="#" class="list-group-item">3,UNKNOWN,Emergency distribution</a>
                <a href="#" class="list-group-item">4,UNKNOWN,Dunn Building</a>
                <a href="#" class="list-group-item">5,UNKNOWN,Collins Center</a>
                <a href="#" class="list-group-item">6,UNKNOWN,Distribution</a>
                <a class="list-group-item" data-toggle="collapse">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  MWSB.1
                </a>
                <a class="list-group-item" data-toggle="collapse">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  MWSB.2
                </a>
                <a class="list-group-item" data-toggle="collapse">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  MWSB.3
                </a>
                <a class="list-group-item" data-toggle="collapse">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  MWSB.4
                </a>
                <a class="list-group-item" data-toggle="collapse">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  MWSB.5
                </a>
                <a class="list-group-item" data-toggle="collapse">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  MWSB.6
                </a>
                <a class="list-group-item" data-toggle="collapse">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  MWSB.7
                </a>
                <a class="list-group-item" data-toggle="collapse">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  MWSB.8
                </a>
                <a class="list-group-item" data-toggle="collapse">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  MWSB.9
                </a>

              </div>

    </div>
  </div>


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

</div>
