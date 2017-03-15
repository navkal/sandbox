<script>
  function clearObject( rsp )
  {
    $( "#objectArea" ).css( "display", "none" );
    $( "#objectLayout" ).html( "" );
  }

  function showObject( rsp )
  {
    $( "#objectArea" ).css( "display", "initial" );

    var tbody = "";

    var keys = Object.keys( rsp ).sort();
    for ( var i = 0; i < keys.length; i++ )
    {
      key = keys[i];
      val = rsp[key];
      tbody += "<tr><td>" + key + "</td><td>" + val + "</td></tr>"
    }

    $( "#objectLayout" ).html( tbody );
  }
</script>

<div id="objectArea">
  <table class="table" >
    <thead>
      <tr>
        <th>Key</th>
        <th>Value</th>
      </tr>
    </thead>
    <tbody id="objectLayout" >
    </tbody>
  </table>
</div>
