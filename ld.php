<!-- Copyright 2017 Energize Apps.  All rights reserved. -->

<script>
  function onGo()
  {
    // Disable inputs
    $( "#go_button" ).prop( "disabled", true );
    $( "#object_type" ).prop( "disabled", true );
    $( "#object_id a" ).prop( "disabled", true );

    // Set wait cursor
    $( "body" ).css( "cursor", "progress" );

    // Post input to server
    var postData = new FormData();
    postData.append( "object_type", $( "#object_type" ).val() );
    postData.append( "object_id", $( "#object_id" ).val() );

    $.ajax(
      "query.php",
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
    $( "#go_button" ).prop( "disabled", false );
    $( "#object_type" ).prop( "disabled", false );
    $( "#object_id a" ).prop( "disabled", false );

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

  <div class="form-group">
    <div class="row" >
      <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
        <select id="object_type" class="form-control" >
          <option>
            circuit
          </option>
          <option>
            room
          </option>
          <option>
            device
          </option>
        </select>
      </div>

      <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
        <input type="text" id="object_id" />
      </div>

      <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
        <button id="go_button" class="btn btn-primary" onclick="onGo()" >Go</button>
      </div>
    </div>
  </div>

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
