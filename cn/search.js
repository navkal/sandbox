// Copyright 2017 Energize Apps.  All rights reserved.

var g_iLastRequestTime = 0;
var g_sLastText = '';

$(document).ready( initSearch );

function initSearch()
{
  $(window).resize( resizeSearchInput );
  $( '#search .searchinput' ).on( 'keydown', cycleCursor );
  $( '#search .searchinput' ).on( 'keyup', getSearchResults );
  $( '#search .searchinput' ).on( 'blur', hideSearchResults );
  $( '#search .searchinput' ).on( 'focus', showSearchResults );

  resizeSearchInput();
}

function resizeSearchInput()
{
  var sWidth = '' + $( '.searchinput' ).closest( '.container' ).width() + 'px';
  $( '.searchinput, .tt-menu' ).css( 'width', sWidth );
}

function getSearchResults( tEvent )
{
  var sText = $( tEvent.target ).val();

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
  var nSuggestions = $( '#search .tt-suggestion' ).length;

  if ( nSuggestions )
  {
    // Determine current cursor index
    var iCursor = $( '#search .tt-suggestion.tt-cursor' ).index();

    switch( tEvent.keyCode )
    {
      case 13:
        // Enter: Select result highlighted by cursor, if any
        var tCursor = $( '#search .tt-cursor' );
        if ( tCursor.length )
        {
          selectSearchResult( { target: tCursor[0] } );
        }
        break;

      case 38:
        // Up-arrow: Cycle cursor upward
        console.log( "==> up from " + iCursor );
        if ( iCursor < 1 )
        {
          iCursor = nSuggestions;
        }
        moveCursor( -- iCursor, nSuggestions );
        break;

      case 40:
        // Down-arrow: Cycle cursor downward
        console.log( "==> down from " + iCursor );
        if ( iCursor >= ( nSuggestions - 1 ) )
        {
          iCursor = -1;
        }
        moveCursor( ++ iCursor, nSuggestions );
        break;
    }
  }
}

function moveCursor( iCursor, nSuggestions )
{
  // Clear existing cursor
  $( '#search .tt-cursor' ).removeClass( 'tt-cursor' );

  // If new cursor index is within range, update display
  if ( ( iCursor >= 0 ) && ( iCursor < nSuggestions ) )
  {
    var tSuggestion = $( $( '#search .tt-suggestion' )[iCursor] );
    tSuggestion.addClass( 'tt-cursor' );
    scrollToCenter( $( '#search .tt-menu' ), tSuggestion );
  }
}

function loadSearchResults( tResults )
{
  // If handling response to latest request, update suggestions display
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
            sResultFormat += '<span class="searchTextHighlight">';
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

        sHtml += '<div class="tt-suggestion" path="' + sPath + '" title="' + sPath + '" >';
        sHtml += sResultFormat;
        sHtml += '</div>';
      }

      // Replace HTML in suggestions div
      $( '#search .tt-dataset' ).html( sHtml );

      // Set handlers
      $( '#search .tt-suggestion' ).on( 'mousedown', selectSearchResult );

      // Show the suggestions menu
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
  // Set selection in search field
  var tTarget = $( tEvent.target ).closest( '.tt-suggestion' );
  var sSearchResult = tTarget.text();
  var sPath = tTarget.attr( 'path' );
  $( '#search .searchinput' ).val( sSearchResult );
  $( '#search .searchinput' ).attr( 'path', sPath );

  // Update copy of last text
  g_sLastText = sSearchResult;

  // Close search results
  closeSearchResults();

  // Navigate to selected search result in tree
  g_sSearchTargetPath = sPath;
  navigateToSearchTarget();
}

function showSearchResults( tEvent )
{
  if ( $( '#search .tt-suggestion' ).length )
  {
    $( '#search .tt-menu' ).show();
  }
}

function closeSearchResults()
{
  hideSearchResults();
  clearSearchResults();
}

function hideSearchResults()
{
  $( '#search .tt-menu' ).hide();
}

function clearSearchResults()
{
  $( '#search .tt-dataset' ).html( '' );
}
