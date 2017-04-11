// Copyright 2017 Energize Apps.  All rights reserved.

var g_iLastRequestTime = 0;

$(document).ready( initSearch );

function initSearch()
{
  $(window).resize( resizeTypeahead );
  $( '#search .typeahead' ).on( 'keyup', getSearchResults );
  $( '#search .typeahead' ).on( 'blur', hideSearchResults );
  $( '#search .typeahead' ).on( 'focus', showSearchResults );

  resizeTypeahead();
}

function resizeTypeahead()
{
  var sWidth = '' + $( '.typeahead' ).closest( '.container' ).width() + 'px';
  $( '.typeahead, .tt-menu' ).css( 'width', sWidth );
}

function getSearchResults( tEvent )
{
  var sText = $( tEvent.target ).val();

  if ( sText == '' )
  {
    $( '#search .tt-dataset' ).html( '' );
    hideSearchResults();
  }
  else
  {
    g_iLastRequestTime = Date.now();

    $.ajax(
      encodeURI( 'cn/search.php?requestTime=' + g_iLastRequestTime + '&searchText=' + sText ),
      {
        type: 'GET',
        dataType : 'json'
      }
    )
    .done( loadSearchResults )
    .fail( handleAjaxError );
  }
};

function loadSearchResults( tResults )
{
  console.log( '=====> loadSearchResults()' );
  // If handling response to latest request, update suggestions display
  if ( tResults.requestTime == g_iLastRequestTime )
  {
    var sSearchText = tResults.searchText;
    console.log( '=====> loadSearchResults() processing results for ' + sSearchText );
    var iSearchTextLen = sSearchText.length;
    var sSearchLower = sSearchText.toLowerCase();
    var aResults = tResults.searchResults;

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
        console.log( "==> BF sResult=" + sResult );
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
          console.log( "==> sLeading=" + sLeading );
          console.log( "==> sMatch=" + sMatch );
        }
        else
        {
          sResultFormat += sResult;
          sResult = '';
          sResultLower = '';
        }

        console.log( "==> sResultFormat=" + sResultFormat );
        console.log( "==> AF sResult=" + sResult );
      }

      sHtml += '<div class="tt-suggestion" path="' + sPath + '" >';
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
}

function selectSearchResult( tEvent )
{
  // Set selection in search field
  var tTarget = $( tEvent.target ).closest( '.tt-suggestion' );
  var sSearchResult = tTarget.text();
  var sPath = tTarget.attr( 'path' );
  $( '#search .typeahead' ).val( sSearchResult );
  $( '#search .typeahead' ).attr( 'path', sPath );

  // Clear search results
  hideSearchResults();
  clearSearchResults();

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

function hideSearchResults()
{
  $( '#search .tt-menu' ).hide();
}

function clearSearchResults()
{
  $( '#search .tt-dataset' ).html( '' );
}
