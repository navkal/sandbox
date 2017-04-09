// Copyright 2017 Energize Apps.  All rights reserved.

$(document).ready( initSearch );

function initSearch()
{
  $(window).resize( resizeTypeahead );
  $( '#search .typeahead' ).on( 'keyup', getSearchResults );
  $( '#search .typeahead' ).on( 'blur', hideSearchResults );
  $( '#search .typeahead' ).on( 'focus', showSearchResults );

  resizeTypeahead();
}

var g_iLastRequestTime = 0;

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
  console.log( "===> Last requestTime=" + g_iLastRequestTime );
  console.log( "===> requestTime=" + tResults.requestTime );

  // If handling response to latest request, update suggestions display
  if ( tResults.requestTime == g_iLastRequestTime )
  {
    var sSearchText = tResults.searchText;
    console.log( "===> searchText=" + sSearchText );
    var aResults = tResults.searchResults;
    console.log( "===> searchResults=" + aResults );

    // Generate the HTML
    var sHtml = '';
    for ( var iResult in aResults )
    {
      var sResult = aResults[iResult];

      sHtml += '<div class="tt-suggestion">';
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
  $( '#search .typeahead' ).val( $( tEvent.target ).text() );
  hideSearchResults();
  clearSearchResults();
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

function resizeTypeahead()
{
  var sWidth = '' + $( '.typeahead' ).closest( '.container' ).width() + 'px';
  $( '.typeahead, .tt-menu' ).css( 'width', sWidth );
}
