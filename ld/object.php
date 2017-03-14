<script>
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

    $( "tbody" ).html( tbody );
  }
</script>

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
