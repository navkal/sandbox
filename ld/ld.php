<!-- Copyright 2017 Energize Apps.  All rights reserved. -->

<script>
  function onGo()
  {
    // Prepare UI to wait for post response
    $( "#btnGo" ).prop( "disabled", true );
    $( "#objectType" ).prop( "disabled", true );
    $( "#objectSelector a" ).prop( "disabled", true );
    clearObject();

    // Clean up input
    $( "#objectSelector" ).val( $( "#objectSelector" ).val().trim() );
    if ( $( "#objectSelector" ).val() == "" )
    {
      $( "#objectSelector" ).val( 0 );
    }

    // Set wait cursor
    $( "body" ).css( "cursor", "progress" );

    // Post input to server
    var postData = new FormData();
    postData.append( "objectType", $( "#objectType" ).val() );
    postData.append( "objectSelector", $( "#objectSelector" ).val() );

    $.ajax(
      "ld/query.php",
      {
        type: 'POST',
        processData: false,
        contentType: false,
        dataType : 'json',
        data: postData
      }
    )
    .done( handlePostResponse )
    .fail( handlePostError );
  }

  function handlePostResponse( rsp, sStatus, tJqXhr )
  {
    // Restore cursor and button states
    $( "body" ).css( "cursor", "default" );
    $( "#btnGo" ).prop( "disabled", false );
    $( "#objectType" ).prop( "disabled", false );
    $( "#objectSelector a" ).prop( "disabled", false );

    // Show the object
    showObject( rsp );
  }

  function handlePostError( tJqXhr, sStatus, sErrorThrown )
  {
    // Restore cursor and button states
    $( "body" ).css( "cursor", "default" );
    $( "#submitFileButton" ).prop( "disabled", false );
    $( "#inputFileFields" ).prop( "disabled", false );
    $( "#inputFileTabs a" ).prop( "disabled", false );

    // Show error information
    showObject( { sStatus : sErrorThrown } )
  }
</script>

<div class="container">

  <form>
    <div class="form-group">
      <div class="row" >

        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
          <label for="objectType">Type</label>
          <select id="objectType" class="form-control" >
            <option value="circuit">
              Circuit
            </option>
            <option value="room">
              Room
            </option>
            <option value="device">
              Device
            </option>
          </select>
        </div>

        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
          <label for="objectSelector">Selector</label>
          <input type="text" class="form-control" id="objectSelector" maxlength=256 >
        </div>

        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
          <label>&nbsp;</label>
          <button id="btnGo" class="btn btn-primary form-control" onclick="onGo()" >Go</button>
        </div>

      </div>
    </div>
  </form>

  <br/>

  <?php
    require_once "object.php";
  ?>
</div>
