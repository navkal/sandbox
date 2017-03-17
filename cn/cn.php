<!-- Copyright 2017 Energize Apps.  All rights reserved. -->

<script>

  var TREE = {};

  $( document ).ready( walkTree );

  function walkTree()
  {
    walkSubtree( "" );
  }

  function walkSubtree( path )
  {
    // --> KLUDGE --> remove after paths are fixed in DB -->
    if ( path.includes( " " ) ) { console.log( "====> BAD PATH=" + path ); return; }
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
    // Append to tree
    var nDepth = tRsp.path.split( "." ).length
    var sIndent = Array( nDepth ).join( "-")
    TREE[tRsp.path] = sIndent + tRsp.path + "<br/>";

    // Optionally update display
    if ( tRsp.children.length == 0 )
    {
      $( "#objectTree" ).html( "" );
      var aPaths = Object.keys( TREE ).sort();
      for ( var iPath = 0; iPath < aPaths.length; iPath ++ )
      {
        $( "#objectTree" ).append( TREE[aPaths[iPath]] );
      }
    }

    // Traverse children
    for ( var iChild = 0; iChild < tRsp.children.length; iChild ++ )
    {
      var sChildPath = tRsp.children[iChild][0];
      if ( sChildPath != tRsp.path )
      {
        walkSubtree( sChildPath );
      }
    }
  }

  function handlePostError( tJqXhr, sStatus, sErrorThrown )
  {
    console.log( "==> ERROR=" + sStatus + " " + sErrorThrown );
    console.log( "==> HEADER=" + JSON.stringify( tJqXhr ) );
  }

  </script>

<div class="container">
  <div id="objectTree" style="overflow:auto">
  </div>
</div>
