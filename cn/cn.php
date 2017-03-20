<!-- Copyright 2017 Energize Apps.  All rights reserved. -->

<style>
  .just-padding
  {
    padding: 15px;
  }

  .list-group.list-group-root
  {
    padding: 0;
    overflow: hidden;
  }

  .list-group.list-group-root .list-group
  {
    margin-bottom: 0;
  }

  .list-group.list-group-root .list-group-item
  {
    border-radius: 0;
    border-width: 1px 0 0 0;
  }

  .list-group.list-group-root > .list-group-item:first-child
  {
    border-top-width: 0;
  }

  .list-group.list-group-root > .list-group > .list-group-item
  {
    padding-left: 30px;
  }

  .list-group.list-group-root > .list-group > .list-group > .list-group-item
  {
    padding-left: 45px;
  }

  .list-group-item .glyphicon
  {
    margin-right: 5px;
  }
</style>

<script>

  var g_aChildWindows = [];

  $( document ).ready( initView );

  function initView()
  {
    $( window ).on( 'unload', closeChildWindows );
    $( '.list-group-item' ).on( 'click', toggleFolder );
  }

  function toggleFolder()
  {
    $('.glyphicon', this)
      .toggleClass('glyphicon-chevron-right')
      .toggleClass('glyphicon-chevron-down');
  }

  function openChildWindow()
  {
    var w = 300;
    var h = 400;
    var y = ( window.top.outerHeight / 2 ) + window.top.screenY - ( h / 2)
    var x = ( window.top.outerWidth / 2 ) + window.top.screenX - ( w / 2)

    var childWindow = window.open(
      '/cn/properties.php',
      'Properties',
      'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+y+', left='+x
    );

    g_aChildWindows.push( childWindow );
  }

  function closeChildWindows()
  {
    for ( var iWin = 0; iWin < g_aChildWindows.length; iWin ++ )
    {
      g_aChildWindows[iWin].close();
    }
  }
</script>

<div class="container">

  <button class="btn btn-default" onclick="openChildWindow()">Properties</button>

  <div class="just-padding">
    <div id="circuitTree" class="list-group list-group-root well">
    
              <a href="#MWSB" class="list-group-item" data-toggle="collapse">
                <i class="glyphicon glyphicon-chevron-right"></i>
                MWSB
              </a>
              <div class="list-group collapse" id="MWSB">
                <a href="#" class="list-group-item">1,UNKNOWN,Kitchen Mechanical</a>
                <a href="#" class="list-group-item">2,UNKNOWN,Transfer sw</a>
                <a href="#" class="list-group-item">3,UNKNOWN,Emergency distribution</a>
                <a href="#" class="list-group-item">4,UNKNOWN,Dunn Building</a>
                <a href="#" class="list-group-item">5,UNKNOWN,Collins Center</a>
                <a href="#" class="list-group-item">6,UNKNOWN,Distribution</a>
                <a class="list-group-item" data-toggle="collapse">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  MWSB.1
                </a>
                <a class="list-group-item" data-toggle="collapse">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  MWSB.2
                </a>
                <a class="list-group-item" data-toggle="collapse">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  MWSB.3
                </a>
                <a class="list-group-item" data-toggle="collapse">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  MWSB.4
                </a>
                <a class="list-group-item" data-toggle="collapse">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  MWSB.5
                </a>
                <a class="list-group-item" data-toggle="collapse">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  MWSB.6
                </a>
                <a class="list-group-item" data-toggle="collapse">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  MWSB.7
                </a>
                <a class="list-group-item" data-toggle="collapse">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  MWSB.8
                </a>
                <a class="list-group-item" data-toggle="collapse">
                  <i class="glyphicon glyphicon-chevron-right"></i>
                  MWSB.9
                </a>

              </div>
              
    </div>
  </div>


                              <div class="just-padding">

                                <div class="list-group list-group-root well">

                                  <a href="#item-1" class="list-group-item" data-toggle="collapse">
                                    <i class="glyphicon glyphicon-chevron-right"></i>Item 1
                                  </a>
                                  <div class="list-group collapse" id="item-1">

                                    <a href="#item-1-1" class="list-group-item" data-toggle="collapse">
                                      <i class="glyphicon glyphicon-chevron-right"></i>Item 1.1
                                    </a>
                                    <div class="list-group collapse" id="item-1-1">
                                      <a href="#" class="list-group-item">Item 1.1.1</a>
                                      <a href="#" class="list-group-item">Item 1.1.2</a>
                                      <a href="#" class="list-group-item">Item 1.1.3</a>
                                    </div>

                                    <a href="#item-1-2" class="list-group-item" data-toggle="collapse">
                                      <i class="glyphicon glyphicon-chevron-right"></i>Item 1.2
                                    </a>
                                    <div class="list-group collapse" id="item-1-2">
                                      <a href="#" class="list-group-item">Item 1.2.1</a>
                                      <a href="#" class="list-group-item">Item 1.2.2</a>
                                      <a href="#" class="list-group-item">Item 1.2.3</a>
                                    </div>

                                    <a href="#item-1-3" class="list-group-item" data-toggle="collapse">
                                      <i class="glyphicon glyphicon-chevron-right"></i>Item 1.3
                                    </a>
                                    <div class="list-group collapse" id="item-1-3">
                                      <a href="#" class="list-group-item">Item 1.3.1</a>
                                      <a href="#" class="list-group-item">Item 1.3.2</a>
                                      <a href="#" class="list-group-item">Item 1.3.3</a>
                                    </div>

                                  </div>

                                  <a href="#item-2" class="list-group-item" data-toggle="collapse">
                                    <i class="glyphicon glyphicon-chevron-right"></i>Item 2
                                  </a>
                                  <div class="list-group collapse" id="item-2">

                                    <a href="#item-2-1" class="list-group-item" data-toggle="collapse">
                                      <i class="glyphicon glyphicon-chevron-right"></i>Item 2.1
                                    </a>
                                    <div class="list-group collapse" id="item-2-1">
                                      <a href="#" class="list-group-item">Item 2.1.1</a>
                                      <a href="#" class="list-group-item">Item 2.1.2</a>
                                      <a href="#" class="list-group-item">Item 2.1.3</a>
                                    </div>

                                    <a href="#item-2-2" class="list-group-item" data-toggle="collapse">
                                      <i class="glyphicon glyphicon-chevron-right"></i>Item 2.2
                                    </a>
                                    <div class="list-group collapse" id="item-2-2">
                                      <a href="#" class="list-group-item">Item 2.2.1</a>
                                      <a href="#" class="list-group-item">Item 2.2.2</a>
                                      <a href="#" class="list-group-item">Item 2.2.3</a>
                                    </div>

                                    <a href="#item-2-3" class="list-group-item" data-toggle="collapse">
                                      <i class="glyphicon glyphicon-chevron-right"></i>Item 2.3
                                    </a>
                                    <div class="list-group collapse" id="item-2-3">
                                      <a href="#" class="list-group-item">Item 2.3.1</a>
                                      <a href="#" class="list-group-item">Item 2.3.2</a>
                                      <a href="#" class="list-group-item">Item 2.3.3</a>
                                    </div>

                                  </div>


                                  <a href="#item-3" class="list-group-item" data-toggle="collapse">
                                    <i class="glyphicon glyphicon-chevron-right"></i>Item 3
                                  </a>
                                  <div class="list-group collapse" id="item-3">

                                    <a href="#item-3-1" class="list-group-item" data-toggle="collapse">
                                      <i class="glyphicon glyphicon-chevron-right"></i>Item 3.1
                                    </a>
                                    <div class="list-group collapse" id="item-3-1">
                                      <a href="#" class="list-group-item">Item 3.1.1</a>
                                      <a href="#" class="list-group-item">Item 3.1.2</a>
                                      <a href="#" class="list-group-item">Item 3.1.3</a>
                                    </div>

                                    <a href="#item-3-2" class="list-group-item" data-toggle="collapse">
                                      <i class="glyphicon glyphicon-chevron-right"></i>Item 3.2
                                    </a>
                                    <div class="list-group collapse" id="item-3-2">
                                      <a href="#" class="list-group-item">Item 3.2.1</a>
                                      <a href="#" class="list-group-item">Item 3.2.2</a>
                                      <a href="#" class="list-group-item">Item 3.2.3</a>
                                    </div>

                                    <a href="#item-3-3" class="list-group-item" data-toggle="collapse">
                                      <i class="glyphicon glyphicon-chevron-right"></i>Item 3.3
                                    </a>
                                    <div class="list-group collapse" id="item-3-3">
                                      <a href="#" class="list-group-item">Item 3.3.1</a>
                                      <a href="#" class="list-group-item">Item 3.3.2</a>
                                      <a href="#" class="list-group-item">Item 3.3.3</a>
                                    </div>

                                  </div>

                                </div>

                              </div>

</div>
