(function($) { // Avoid conflicts with other libraries

	'use strict';

	$('a.category[data-collapse] + .forabg').each(function() {
		var $this = $(this),
			$prev = $this.prev(),
			hidden = $prev.attr('data-hidden'),
			collapse = $prev.attr('data-collapse'),
			tooltip = $prev.attr('data-tooltip'),
			$header = $this.find('li.header'),
			$content = $this.find('.topiclist.forums');

		if (!$header.length || !$content.length) {
			return;
		}

		// Add button
		var $button = $('<a>')
			.addClass('collapse-btn collapse-' + ((hidden) ? 'show' : 'hide'))
			.attr({
				href: collapse,
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

