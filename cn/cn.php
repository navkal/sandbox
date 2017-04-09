<!-- Copyright 2017 Energize Apps.  All rights reserved. -->

<?php
  $_SESSION["pathDotReplacement"] = '-_-_-';
?>

<!-- Circuit Navigator scripts -->
<link rel="stylesheet" href="cn/cn.css">
<script src="cn/cn.js"></script>

<!-- Search scripts -->
<script src="cn/typeahead.js/typeahead.jquery.js"></script>
<link rel="stylesheet" href="cn/search.css">
<script src="cn/search.js"></script>

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
      <div id="the-basics" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
        <div class="form-group has-feedback">
          <input class="typeahead" type="text" placeholder="Regex..." >
          <i class="glyphicon glyphicon-search form-control-feedback"></i>
        </div>
      </div>
    </div>

    <div class="row" >
      <div id="search" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
        <div class="form-group has-feedback">
          <input class="typeahead" type="text" placeholder="Search..." >
          <i class="glyphicon glyphicon-search form-control-feedback"></i>
          <pre aria-hidden="true" style="position: absolute; visibility: hidden; white-space: pre; font-family: HelveticaNeue, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 18px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400; word-spacing: 0px; letter-spacing: 0px; text-indent: 0px; text-rendering: auto; text-transform: none;">a</pre>
          <div class="tt-menu" style="position: absolute; top: 100%; left: 0px; z-index: 100; display: none; overflow: auto">
            <div class="tt-dataset tt-dataset-search">
            </div>
          </div>
        </div>
      </div>
    </div>


<!-------------------------------------------------------------------------- >
<label for="something">
    <input type="text" class="typeahead" id="something" placeholder="Search..." list="somethingelse">
    <datalist id="somethingelse">
        <option value="Something"></option>
        <option value="Something Else"></option>
        <option value="Another One"></option>
        <option value="Alpha"></option>
        <option value="Bravo"></option>
        <option value="Charlie"></option>
        <option value="Delta"></option>
        <option value="Echo"></option>
        <option value="Foxtrot"></option>
        <option value="Gamma"></option>
    </datalist>
</label>

<section class="main">
	 <form class="search" method="post" action="index.html" >
		 <input type="text" name="q" placeholder="Search..." />
		 <ul class="results" >
			 <li><a href="index.html">Search Result #1<br /><span>Description...</span></a></li>
			 <li><a href="index.html">Search Result #2<br /><span>Description...</span></a></li>
	 		<li><a href="index.html">Search Result #3<br /><span>Description...</span></a></li>
         	<li><a href="index.html">Search Result #4</a></li>
		 </ul>
	 </form>
</section>
<!-------------------------------------------------------------------------->





    <div class="row" >
      <div class="just-padding">
        <div id="circuitTree" class="list-group list-group-root well">
        </div>
      </div>
    </div>
  </div>
</div>
