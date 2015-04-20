(function($) { // Avoid conflicts with other libraries

	'use strict';

	$('a.collapse-btn').each(function() {
		var $this = $(this),
			hidden = $this.attr('data-hidden'),
			$content = $this.closest('.forabg').find('.topiclist.forums');

		// Unhide the collapse buttons (makes using them JS dependent)
		$this.show();

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

