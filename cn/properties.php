<!-- Copyright 2017 Energize Apps.  All rights reserved. -->

<!DOCTYPE html>
<html lang="en">

  <!-- Head -->
  <?php
    require_once $_SERVER["DOCUMENT_ROOT"]."/../common/headStart.php";
  ?>
  <title>Properties:
    <?php
      echo( $_REQUEST["path"] );
    ?>
  </title>
  <style>
    #objectArea .table > tbody > tr:first-child > td
    {
      border: none;
    }
  </style>

  <script>
    var g_sPath = '<?=$_REQUEST["path"]?>';
    var g_sType = '<?=$_REQUEST["type"]?>';
    var g_sOid = '<?=$_REQUEST["oid"]?>';
    var g_sRole = '<?=$_SESSION["user"]["role"]?>';
  </script>
  <script src="properties.js?version=<?=$iVersion?>"></script>

  <?php
    require_once $_SERVER["DOCUMENT_ROOT"]."/../common/headEnd.php";
  ?>

  <!-- Body -->
	<body>
    <div class="container">

      <br/>
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title" id="propertiesTitle"></h3>
        </div>
        <div class="panel-body">
          <div id="objectArea" style="overflow:auto;">
            <table class="table table-hover table-condensed" >
              <tbody id="objectLayout" >
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div id="historyArea" >
      </div>

      <div id="notesEditor">
        <div class="form-group">
          <label for="notes">Notes</label>
          <textarea id="notes" class="form-control" maxlength="1000"></textarea>
        </div>
        <div style="text-align:center;" >
          <button class="btn btn-primary btn-xs" onclick="saveNotes(event)" >Save</button>
          <button class="btn btn-default btn-xs" onclick="clearNotes(event)" >Clear</button>
        </div>
      </div>
    </div>
 	</body>
</html>
