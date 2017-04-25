<!-- Copyright 2017 Energize Apps.  All rights reserved. -->

<!DOCTYPE html>
<html lang="en">

  <?php
    require_once $_SERVER["DOCUMENT_ROOT"]."/../common/util.php";
    $sPath = $_REQUEST['path'];
    $sImg = 'images/' . $sPath . '.jpg';
  ?>
  <head>
    <title>
      Image: <?=$sPath?>
    </title>
    <?php
      require_once "head.php";
    ?>
  </head>

	<body>
    <div class="container-fluid">
      <img class="img-responsive" src="<?=$sImg?>" alt="<?=$sPath?>">
    </div>
 	</body>
</html>
<script>

  $( document ).ready( resizeWindow );

  function resizeWindow()
  {
    var tImg = new Image();
    tImg.src = $("img").attr("src");

    // Get window aspect
    var nWinWidth = $( window ).width();
    var nWinHeight = $( window ).height();
    var nWinAspect = nWinWidth / nWinHeight;

    // Get image aspect
    var nImgWidth = tImg.naturalWidth;
    var nImgHeight = tImg.naturalHeight;
    var nImgAspect = nImgWidth / nImgHeight;

    // Measure discrepancy between the two aspects
    var nDiscrepancy = Math.abs( ( nWinAspect / nImgAspect ) - 1 );
    console.log( nDiscrepancy );

    // If default aspect does not fit image, resize the window
    if ( nDiscrepancy > 0.1 )
    {
      var nHeight = nWinWidth * tImg.naturalHeight / tImg.naturalWidth;
      window.resizeTo( nWinWidth, nHeight );
    }
   }
</script>