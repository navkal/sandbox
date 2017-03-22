<!-- Copyright 2017 Energize Apps.  All rights reserved. -->

<script>
  $( document ).ready( showRoot );

  function showRoot()
  {
    $( "#objectTable" ).val( "circuit" );
    onGo();
  }

  function onGo()
  {
    // Prepare UI to wait for post response
    setWaitState( true );
    clearObject();

    // Trim input
    $( "#objectSelector" ).val( $( "#objectSelector" ).val().trim() );

    // Post input to server
    var postData = new FormData();
    postData.append( "objectTable", $( "#objectTable" ).val() );
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
    // Restore cursor and control states
    setWaitState( false );

    if ( $( "#objectSelector" ).val() == "" )
    {
      $( "#objectSelector" ).val( rsp.Path || rsp.ID );
    }

    // Show the object
    showObject( rsp );
  }

  function handlePostError( tJqXhr, sStatus, sErrorThrown )
  {
    // Restore cursor and control states
    setWaitState( false );

    // Show error information
    showObject( { sStatus : sErrorThrown } )
  }

  function setWaitState( bWait )
  {
    $( "body" ).css( "cursor", bWait ? "progress" : "default" );
    $( "#btnGo" ).prop( "disabled", bWait );
    $( "#objectTable" ).prop( "disabled", bWait );
    $( "#objectSelector" ).prop( "disabled", bWait );
  }
</script>

<div class="container">

  <form>
    <div class="form-group">
      <div class="row" >

        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
          <label for="objectTable">Table</label>
          <select id="objectTable" class="form-control" >
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
