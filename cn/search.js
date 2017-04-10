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
  // If handling response to latest request, update suggestions display
  if ( tResults.requestTime == g_iLastRequestTime )
  {
    var sSearchText = tResults.searchText;
    var aResults = tResults.searchResults;

    // Generate the HTML
    var sHtml = '';
    for ( var iResult in aResults )
    {
      var tResult = aResults[iResult];
      var sPath = tResult.path;
      var sResult = tResult.searchResult;

      sHtml += '<div class="tt-suggestion" path="' + sPath + '" >';
      sHtml += sResult.split( sSearchText ).join( '<span class="searchTextHighlight">' + sSearchText + '</span>' );
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
  g_sSearchResultPath = sPath;
  navigateToSearchResult();
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

function navigateToSearchResult()
{
  console.log( '========> Navigate to path: ' + g_sSearchResultPath );

  var aPath = g_sSearchResultPath.split( '.' );
  var bExpanded = true;
  var sNavPath = '';
  var tNavNode = null;
  for ( var iLen = 0; ( iLen < aPath.length ) && bExpanded; iLen ++ )
  {
    sNavPath = aPath.slice( 0, iLen + 1 ).join( '.' );
    tNavNode = $( '#circuitTree a[path="' + sNavPath + '"]' );
    bExpanded = tNavNode.find( ".toggle.glyphicon-chevron-down" ).length > 0;
    console.log( '===> ' + sNavPath + ' EXPANDED? ' + bExpanded );
  }

  if ( sNavPath == g_sSearchResultPath )
  {
    // Navigation done
    setToggleTooltips();
    console.log( "===========> DONE! at path=" + g_sSearchResultPath );
    $( '#circuitTree a[path="' + g_sSearchResultPath + '"]' ).css( 'color', 'red' );
    g_sSearchResultPath = '';
  }
  else
  {
    // Expand node hiding search result
    console.log( "===> Need to expand: " + sNavPath );
    tNavNode.trigger( 'click' );
  }
}
