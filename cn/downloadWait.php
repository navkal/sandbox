<?php
  // Copyright 2016 Energize Apps.  All rights reserved.

  require_once $_SERVER["DOCUMENT_ROOT"]."/../common/util.php";

  echo( json_encode( $_SESSION["downloadDone"] ) );
?>
