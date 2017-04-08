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
  resizeTypeahead();
  $(window).resize( resizeTypeahead );

  // Use hard-coded data
  $( '#the-basics .typeahead' ).typeahead(
    {
      hint: true,
      highlight: true,
      minLength: 1
    },
    {
      name: 'states',
      source: substringMatcher(g_aStates)
    }
  );

  // Use Bloodhound with hard-coded data
  var fnFindStates = new Bloodhound(
    {
      datumTokenizer: Bloodhound.tokenizers.whitespace,
      queryTokenizer: Bloodhound.tokenizers.whitespace,
      local: g_aStates
    }
  );

  $( '#bloodhound .typeahead' ).typeahead(
    {
      hint: true,
      highlight: true,
      minLength: 1
    },
    {
      name: 'states',
      source: fnFindStates
    }
  );

  // Use Bloodhound with prefetched data
  var countries = new Bloodhound(
    {
      datumTokenizer: Bloodhound.tokenizers.whitespace,
      queryTokenizer: Bloodhound.tokenizers.whitespace,
      prefetch: 'cn/prefetch.json'
    }
  );

  $( '#prefetch .typeahead' ).typeahead(
    null,
    {
      name: 'countries',
      source: countries
    }
  );

  // Get dynamic results from backend
  $( '#search .typeahead' ).typeahead(
    {
      hint: true,
      highlight: true,
      minLength: 1
    },
    {
      name: 'search',
      source: makeMatchfinder(g_aStates)
    }
  );

}

function makeMatchfinder( aStrings )
{
  // Create and return a function that finds matches
  return function( sFragment, fnShowDropdown )
  {
    console.log( "===> in anon function, sFragment=" + sFragment );

    $.ajax(
      encodeURI( "cn/search.php?query=" + sFragment ),
      {
        type: 'GET',
        dataType : 'json'
      }
    )
    .done( showSearchResults )
    .fail( handleAjaxError );
  };
};

function showSearchResults( aResults )
{
  console.log( "===> showSearchResults: " + aResults );

  // Generate the HTML
  var sHtml = '';
  for ( var i in aResults )
  {
    var sResult = aResults[i];
    sHtml += '<div class="tt-suggestion tt-selectable">';
    sHtml += sResult;
    sHtml += '</div>';
  }

  // Replace HTML in suggestions div
  var tSuggestions = $( '.tt-dataset-search' );
  tSuggestions.html( sHtml );

  // Show the suggestions menu
  var tMenu = tSuggestions.closest( '.tt-menu' );
  tMenu.removeClass( 'tt-empty' );
  tMenu.show();
}

function resizeTypeahead()
{
  var sWidth = '' + $( '.typeahead' ).closest( '.container' ).width() + 'px';
  $( '.typeahead, .tt-menu' ).css( 'width', sWidth );
}










