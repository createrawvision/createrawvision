/**
 * This script handles submission for the recipe filter
 */
(function( $ ) {

  const filter = $('#recipe-filter');
  const button = filter.find('button');
  const results = $('#filter_results');

	// send ajax request on submit
	filter.submit( () => {
		$.ajax({
			url: filter.attr('action'),
			data: filter.serialize(),
			type: filter.attr('method'),
			beforeSend: () => button.text('Suche läuft... Bitte warten.'),
			success: data => {
				button.text('Rezepte filtern');
				results.html(data);
			}
		});
		return false;
	});

})( jQuery );