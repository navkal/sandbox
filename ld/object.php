<script>
  function clearObject( rsp )
  {
    $( "#objectLayout" ).html( "" );
  }

  function showObject( rsp )
  {
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
