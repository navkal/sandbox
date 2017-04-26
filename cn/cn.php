<!-- Copyright 2017 Energize Apps.  All rights reserved. -->

<?php
  $_SESSION["pathDotReplacement"] = '-_-_-';
  $_SESSION['user'] = [];
  $_SESSION['user']['role'] = ( strpos( $_SERVER['TMP'], 'xampp' ) !== false ) ? 'admin' : 'readonly';

  $iVersion = time();
  $sGoto = isset( $_REQUEST['goto'] ) ? $_REQUEST['goto'] : '';
?>


<!-- Circuit Navigator scripts -->
<link rel="stylesheet" href="cn/cn.css?version=<?=$iVersion?>">
<script src="cn/cn.js?version=<?=$iVersion?>"></script>

<!-- Search scripts -->
<link rel="stylesheet" href="cn/search.css?version=<?=$iVersion?>">
<script src="cn/search.js?version=<?=$iVersion?>"></script>

<!-- Goto parameter -->
<input id="goto" type="hidden" value="<?=$sGoto?>"/>

<br/>
<div class="container">
  <div id="headerContent" >
    <div class="row" >
      <div class="col-xs-12 col-sm-4 col-md-3 col-lg-2 headerButton" >
        <a class="btn btn-default btn-sm" href="cn/circuitTopology.pdf" target="_blank" title="View diagram of Circuit Topology" >
          <span class="glyphicon glyphicon-blackboard"></span> View Circuit Topology
        </a>
      </div>
      <div class="col-xs-12 col-sm-8 col-md-9 col-lg-10 headerButton" >
        <a class="btn btn-default btn-sm" href="cn/downloadTree.php" onclick="return startTreeDump(event);" title="Generate and download dump of Circuit Tree" ><span class="glyphicon glyphicon-download-alt"></span> Download Tree Dump <sup><span class="glyphicon glyphicon-asterisk"></span></sup></a>
        <span class="well-sm text-info" style="white-space:nowrap;" >
          <small>
            <sup>
              <span class="glyphicon glyphicon-asterisk"></span>
            </sup>
            Takes several minutes
          </small>
        </span>
      </div>
    </div>
  </div>
  <div id="dumpStatus" class="well well-sm hidden" >
    <span class="glyphicon glyphicon-hourglass" style="font-size:18px;" ></span> Generating tree. <span id="dumpTime"></span>
  </div>

  <div id="circuitNavigator">
    <hr/>

    <!-- Search -->
    <div class="row" >
      <div id="search" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
        <div class="form-group">

          <!-- Search input control -->
          <div id="search-control" class="input-group" >
          <div class="input-group-addon">
            <span class="glyphicon glyphicon-search">
            </span>
          </div>
            <input class="form-control search-input" id="search-input" type="text" placeholder="Search..." >
            <div class="input-group-btn">
              <button type="button" id="search-input-clear" class="btn btn-default" title="Clear search input" onclick="clearSearchInput()">
                <span class="glyphicon glyphicon-remove">
                </span>
              </button>
            </div>
          </div>

          <!-- Menu showing search results -->
          <div id="search-menu" class="search-menu" style="position:absolute; top:100%; left:0px; z-index:100; display:none; overflow:auto; resize:both; ">
            <div id="search-results">
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Tree -->
    <div class="row" >
      <div class="just-padding">
        <div id="circuitTree" class="list-group list-group-root well" style="overflow:auto; min-height:36px" >
        </div>
      </div>
    </div>
  </div>
</div>
