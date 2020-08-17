/**
 * This script handles submission for the recipe filter
 */
(function( $ ) {

	const filter = $('#recipe-filter');
	const button = filter.find('button');
	const results = $('#filter_results');
	const loadingHint = $('.recipe-filter__loading-hint');

	let waitingForResponse = false;
	let currentPage = undefined;
	let maxPages = undefined;

	filter.submit( loadResults );
	$( window ).scroll( maybeLoadMore );
		
	// Load results via ajax.
	function loadResults(){
		if(waitingForResponse) 
			return false;

		waitingForResponse = true;
		currentPage = undefined;

		sendRequest({
			beforeSend: () => button.text('Suche läuft... Bitte warten.'), 
			success: ( {html} ) => {
				button.text('Rezepte filtern');
				results.html(html || '<p class="recipe-filter__no-results">Keine Rezepte gefunden...</p>');
			}
		});

		return false;
	}

	// Load more results when scrolled to bottom
	function maybeLoadMore() {
		if( waitingForResponse || undefined === currentPage || currentPage >= maxPages ) 
			return false;

		const resultsBottom = results.offset().top + results.outerHeight();
		const windowBottom = $(window).scrollTop() + $(window).outerHeight();
		if( windowBottom < resultsBottom ) 
			return false;

		waitingForResponse = true;

		sendRequest({
			beforeSend: () => loadingHint.show(),
			success: ( {html} ) => {
				loadingHint.hide();
				results.append(html);
			}
		});

		return false;
	}

	// Utility for sending ajax requests.
	function sendRequest({beforeSend, success}) {
		$.ajax({
			url: filter.attr('action'),
			data: filter.serialize() + (undefined !== currentPage ? '&page=' + (currentPage + 1) : ''),
			type: filter.attr('method'),
			beforeSend,
			success: ( {html, page, pages} ) => {
				currentPage = page;
				maxPages = pages;
				waitingForResponse = false;

				success({html, page, pages});
			}
		});
	}

})( jQuery );