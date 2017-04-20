// Copyright 2017 Energize Apps.  All rights reserved.

var g_iLastRequestTime = 0;
var g_sLastText = '';

$(document).ready( initSearch );

function initSearch()
{
  // Set focus on the search input control
  $( '#search-input' ).focus();

  // Set size of search input control
  resizeSearchInput();

  // Set handlers
  $( window ).resize( resizeSearchInput );
  $( '#search-input' ).on( 'keydown', cycleCursor );
  $( '#search-input' ).on( 'keyup', getSearchResults );
  $( '#search-input' ).on( 'blur', hideSearchResults );
  $( '#search-input' ).on( 'click', showSearchResults );
}

function resizeSearchInput()
{
  var sWidth = '' + $( '#search-input' ).closest( '.container' ).width() + 'px';
  $( '#search-control, #search-menu' ).css( 'width', sWidth );
}

function getSearchResults()
{
  var sText = $( '#search-input' ).val();

  if ( sText == '' )
  {
    closeSearchResults();
  }
  else
  {
    if ( sText != g_sLastText )
    {
      g_iLastRequestTime = Date.now();

      // Post request to server
      var tPostData = new FormData();
      tPostData.append( "requestTime", g_iLastRequestTime );
      tPostData.append( "searchText", sText );

      $.ajax(
        "cn/search.php",
        {
          type: 'POST',
          processData: false,
          contentType: false,
          dataType : 'json',
          data: tPostData
        }
      )
      .done( loadSearchResults )
      .fail( handleAjaxError );
    }
  }

  g_sLastText = sText;
};

function cycleCursor( tEvent )
{
  var nResults = $( '#search .search-result' ).length;

  if ( nResults )
  {
    // Determine whether menu is visible
    var bVisible = $( '#search-menu' ).is( ':visible' );

    // Determine current cursor index
    var iCursor = $( '#search .search-result.search-cursor' ).index();

    switch( tEvent.keyCode )
    {
      case 13:
        // Enter: Select highlighted result or show hidden menu
        if ( bVisible )
        {
          var tCursor = $( '#search .search-cursor' );
          if ( tCursor.length )
          {
            selectSearchResult( { target: tCursor[0] } );
          }
        }
        else
        {
          showSearchResults( tEvent );
        }
        break;

      case 38:
        // Up-arrow: Cycle cursor upward
        if ( bVisible )
        {
          tEvent.preventDefault();

          if ( iCursor < 1 )
          {
            iCursor = nResults;
          }

          moveCursor( -- iCursor, nResults );
        }
        break;

      case 40:
        // Down-arrow: Cycle cursor downward
        if ( bVisible )
        {
          tEvent.preventDefault();

          if ( iCursor >= ( nResults - 1 ) )
          {
            iCursor = -1;
          }

          moveCursor( ++ iCursor, nResults );
        }
        break;
    }
  }
}

function moveCursor( iCursor, nResults )
{
  // Clear existing cursor
  $( '#search .search-cursor' ).removeClass( 'search-cursor' );

  // If new cursor index is within range, update display
  if ( ( iCursor >= 0 ) && ( iCursor < nResults ) )
  {
    var tResult = $( $( '#search .search-result' )[iCursor] );
    tResult.addClass( 'search-cursor' );
    scrollToVisible( $( '#search-menu' ), tResult );
  }
}

function scrollToVisible( tContainer, tItem )
{
  tContainer.scrollTop( tContainer.scrollTop() + ( tItem.position().top - tContainer.position().top ) - ( tContainer.height() / 2 ) + ( tItem.height() / 2 ) );
}

function loadSearchResults( tResults )
{
  // If handling response to latest request, update results display
  if ( tResults.requestTime == g_iLastRequestTime )
  {
    var sSearchText = tResults.searchText;
    var iSearchTextLen = sSearchText.length;
    var sSearchLower = sSearchText.toLowerCase();
    var aResults = tResults.searchResults;

    if ( aResults.length )
    {
      // Generate the HTML
      var sHtml = '';
      for ( var iResult in aResults )
      {
        var aResult = aResults[iResult];
        var sPath = aResult[0];
        var sResult = aResult[1];
        var sResultLower = sResult.toLowerCase();

        var sResultFormat = '';
        while( sResult != '' )
        {
          var iPos = sResultLower.indexOf( sSearchLower );
          if ( iPos >= 0 )
          {
            var sLeading = sResult.substr( 0, iPos );
            var sMatch = sResult.substr( iPos, iSearchTextLen );
            sResultFormat += sLeading;
            sResultFormat += '<span class="search-result-highlight">';
            sResultFormat += sMatch;
            sResultFormat += '</span>';
            sResult = sResult.substr( iPos + iSearchTextLen );
            sResultLower = sResultLower.substr( iPos + iSearchTextLen );
          }
          else
          {
            sResultFormat += sResult;
            sResult = '';
            sResultLower = '';
          }
        }

        sHtml += '<div class="search-result" path="' + sPath + '" title="' + sPath + '" >';
        sHtml += sResultFormat;
        sHtml += '</div>';
      }

      // Replace HTML in results div
      $( '#search-results' ).html( sHtml );

      // Set handlers
      $( '#search .search-result' ).on( 'mousedown', selectSearchResult );

      // Show the results menu
      showSearchResults();
    }
    else
    {
      closeSearchResults();
    }
  }
}

function selectSearchResult( tEvent )
{
  // Get event target
  var tTarget = $( tEvent.target ).closest( '.search-result' );

  // Set cursor on search result
  $( '#search .search-cursor' ).removeClass( 'search-cursor' );
  tTarget.addClass( 'search-cursor' );

  // Hide search results
  hideSearchResults();

  // Navigate to selected search result in tree
  g_sSearchTargetPath = tTarget.attr( 'path' );
  navigateToSearchTarget();
}

function showSearchResults( tEvent )
{
  var aResults = $( '#search .search-result' )
  var nResults = aResults.length;

  if ( nResults > 0 )
  {
    var tMenu = $( '#search-menu' );

    // If displaying new results, resize and clear scroll position
    if ( ! tEvent )
    {
      // Set menu width
      var nWidth = tMenu.closest( '.container' ).width();
      tMenu.width( nWidth );

      // Calculate per-result height
      var tResult = $( aResults[0] );
      var nLineHeight = parseInt( tResult.css( 'line-height' ) );
      var nPadTop = parseInt( tResult.css( 'padding-top' ) );
      var nPadBottom = parseInt( tResult.css( 'padding-bottom' ) );
      var nMarginTop = parseInt( tResult.css( 'margin-top' ) );
      var nMarginBottom = parseInt( tResult.css( 'margin-bottom' ) );
      var nResultHeight = nLineHeight + nPadTop + nPadBottom + nMarginTop + nMarginBottom;
      var nResultsHeight = nResultHeight * Math.min( nResults, 10 );

      // Calculate additional menu height
      var nMenuPadBottom = parseInt( tMenu.css( 'padding-bottom' ) );
      var nMenuPadTop = parseInt( tMenu.css( 'padding-top' ) );
      var nMenuMarginBottom = parseInt( tMenu.css( 'margin-bottom' ) );
      var nMenuMarginTop = parseInt( tMenu.css( 'margin-top' ) );
      var nMenuExtraHeight = nMenuPadTop + nMenuPadBottom + nMenuMarginTop + nMenuMarginBottom;

      // Set menu height
      var nHeight = nResultsHeight + nMenuExtraHeight;
      tMenu.height( nHeight );

      // Clear scroll top
      tMenu.scrollTop( 0 );
    }

    tMenu.show();
  }
}

function clearSearchInput()
{
  $( '#search-input' ).val( '' );
  g_sLastText = '';
  closeSearchResults();
}

function closeSearchResults()
{
  hideSearchResults();
  $( '#search-results' ).html( '' );
  $( '#search-input' ).focus();
}

function hideSearchResults()
{
  $( '#search-menu' ).hide();
}
