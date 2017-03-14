<!-- Copyright 2017 Energize Apps.  All rights reserved. -->

<script>
  function onGo()
  {
    // Disable inputs
    $( "#btnGo" ).prop( "disabled", true );
    $( "#objectType" ).prop( "disabled", true );
    $( "#objectId a" ).prop( "disabled", true );

    // Clean up input
    $( "#objectId" ).val( $( "#objectId" ).val().trim() );
    if ( $( "#objectId" ).val() == "" )
    {
      $( "#objectId" ).val( 0 );
    }

    // Set wait cursor
    $( "body" ).css( "cursor", "progress" );

    // Post input to server
    var postData = new FormData();
    postData.append( "objectType", $( "#objectType" ).val() );
    postData.append( "objectId", $( "#objectId" ).val() );

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
    $( "#objectId a" ).prop( "disabled", false );

    var tbody = "";

    var keys = Object.keys( rsp ).sort();
    for ( var i = 0; i < keys.length; i++ )
    {
      key = keys[i];
      val = rsp[key];
      tbody += "<tr><td>" + key + "</td><td>" + val + "</td></tr>"
    }

    $( "tbody" ).html( tbody );
  }

  function handlePostError( tJqXhr, sStatus, sErrorThrown )
  {
    // Restore cursor and button states
    $( "body" ).css( "cursor", "default" );
    $( "#submitFileButton" ).prop( "disabled", false );
    $( "#inputFileFields" ).prop( "disabled", false );
    $( "#inputFileTabs a" ).prop( "disabled", false );

    $( "tbody" ).html( "<tr><td>" + sStatus + "</td><td>" + sErrorThrown + "</td></tr>" );
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
          <label for="objectId">ID</label>
          <input type="text" class="form-control" id="objectId" maxlength=8 >
        </div>

        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
          <label>&nbsp;</label>
          <button id="btnGo" class="btn btn-primary form-control" onclick="onGo()" >Go</button>
        </div>

      </div>
    </div>
  </form>

  <table class="table" >
    <thead>
      <tr>
        <th>Key</th>
        <th>Value</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>

</div>
