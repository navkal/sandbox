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
    // Determine current cursor index
    var iCursor = $( '#search .search-result.search-cursor' ).index();

    switch( tEvent.keyCode )
    {
      case 13:
        // Enter: Select result highlighted by cursor, if any
        var tCursor = $( '#search .search-cursor' );
        if ( tCursor.length )
        {
          selectSearchResult( { target: tCursor[0] } );
        }
        break;

      case 38:
        // Up-arrow: Cycle cursor upward
        if ( iCursor < 1 )
        {
          iCursor = nResults;
        }
        moveCursor( -- iCursor, nResults );
        break;

      case 40:
        // Down-arrow: Cycle cursor downward
        if ( iCursor >= ( nResults - 1 ) )
        {
          iCursor = -1;
        }
        moveCursor( ++ iCursor, nResults );
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
      $( '#search .tt-dataset' ).html( sHtml );

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
  if ( $( '#search .search-result' ).length )
  {
    $( '#search-menu' ).show();

    // If displaying new results, restore the scroll position
    if ( ! tEvent )
    {
      $( '#search-menu' ).scrollTop( 0 );
    }
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
  $( '#search .tt-dataset' ).html( '' );
  $( '#search-input' ).focus();
}

function hideSearchResults()
{
  $( '#search-menu' ).hide();
}
