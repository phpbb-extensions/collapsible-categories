(function($) { // Avoid conflicts with other libraries

	'use strict';

	$('a.category[data-id] + .forabg').each(function() {
		var $this = $(this),
			id = $this.prev().attr('data-id'),
			hidden = $this.prev().attr('data-hidden'),
			tooltip = $this.prev().attr('data-tooltip'),
			$header = $this.find('li.header'),
			$content = $this.find('.topiclist.forums');

		if (!$header.length || !$content.length) {
			return;
		}

		// Add button
		var $button = $('<a>')
			.addClass('collapse-btn collapse-' + ((hidden) ? 'show' : 'hide'))
			.attr({
				href: collapsible_categories_url + '/' + id,
				title: tooltip,
				'data-ajax': 'phpbb_collapse',
				'data-overlay': true
			});
		$header.append($button);

		// Hide hidden forums on load
		if (hidden) {
			$content.hide();
		}
	});

	phpbb.addAjaxCallback('phpbb_collapse', function(res) {
		if (res.success) {
			$(this)
				.toggleClass('collapse-show collapse-hide')
				.closest('.forabg')
				.find('.topiclist.forums')
				.slideToggle('fast');
		}
	});

})(jQuery); // Avoid conflicts with other libraries

