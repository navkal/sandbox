<!-- Copyright 2017 Energize Apps.  All rights reserved. -->

<?php
  $_SESSION["pathDotReplacement"] = '-_-_-';
?>

<link rel="stylesheet" href="cn/cn.css">
<script src="cn/cn.js"></script>

<br/>
<div class="container">
  <div id="dumpStart" >
    <a class="btn btn-default btn-sm" href="cn/downloadTree.php" onclick="return startTreeDump(event);" title="Generate and download dump of Circuit Tree" ><span class="glyphicon glyphicon-download-alt"></span> Download Tree Dump</a>
    <span class="well-sm text-info" >
      <small>
        <sup>
          <span class="glyphicon glyphicon-asterisk"></span>
        </sup>
        Takes several minutes
      </small>
    </span>
  </div>
  <div id="dumpStatus" class="well well-sm hidden" >
    <span class="glyphicon glyphicon-hourglass" style="font-size:18px;" ></span> Generating tree. <span id="dumpTime"></span>
  </div>

  <div id="circuitNavigator">
    <hr/>
    <div class="row" >
      <div class="just-padding">
        <div id="circuitTree" class="list-group list-group-root well">
        </div>
      </div>
    </div>
  </div>
</div>
