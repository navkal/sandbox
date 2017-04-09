// DELETE THIS
var g_aStates =
['Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California',
  'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia', 'Hawaii',
  'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana',
  'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota',
  'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire',
  'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota',
  'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island',
  'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont',
  'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming'
];

// DELETE THIS
function substringMatcher(strs)
{
  return function findMatches(q, cb) {
    var matches, substringRegex;

    // an array that will be populated with substring matches
    matches = [];

    // regex used to determine if a string contains the substring `q`
    substrRegex = new RegExp(q, 'i');

    // iterate through the pool of strings and for any string that
    // contains the substring `q`, add it to the `matches` array
    $.each(strs, function(i, str) {
      if (substrRegex.test(str)) {
        matches.push(str);
      }
    });

    cb(matches);
    resizeTypeahead();
  };
};













$(document).ready( initSearch );

function initSearch()
{

  // DELETE THIS
  $( '#delete-this .typeahead' ).typeahead(
    {
      hint: false,
      highlight: false,
      minLength: 1
    },
    {
      name: 'states',
      source: substringMatcher(g_aStates)
    }
  );










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
    var aResults = tResults.searchResults;
    console.log( "===> searchResults=" + aResults );

    // Generate the HTML
    var sHtml = '';
    for ( var iResult in aResults )
    {
      var sResult = aResults[iResult];
      sHtml += '<div class="tt-suggestion">';
      sHtml += sResult;
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
