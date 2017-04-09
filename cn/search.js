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



  // Use hard-coded data
  $( '#the-basics .typeahead' ).typeahead(
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
  $( '#search .typeahead' ).on( 'keyup', getMatches );

  resizeTypeahead();
}

function getMatches( tEvent )
{
  var sText = $( tEvent.target ).text();

  $.ajax(
    encodeURI( "cn/search.php?text=" + sText ),
    {
      type: 'GET',
      dataType : 'json'
    }
  )
  .done( showSearchResults )
  .fail( handleAjaxError );

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
  var tSuggestions = $( '#search .tt-dataset' );
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










