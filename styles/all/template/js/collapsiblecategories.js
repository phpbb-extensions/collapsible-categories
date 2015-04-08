(function ($) {  // Avoid conflicts with other libraries

	'use strict';

	$(function () {
		$('a.category[data-id] + .forabg').each(function () {
			var $this = $(this),
				id = $this.prev().attr('data-id'),
				hidden = $this.prev().attr('data-hidden'),
				$header = $this.find('li.header'),
				$content = $this.find('.topiclist.forums');

			if (!$header.length || !$content.length) {
				return;
			}

			// Add button
			$header.append('<a class="collapse-btn collapse-' + ((hidden) ? 'show' : 'hide') + '" data-id="' + id + '" data-ajax="phpbb_collapse"></a>');

			// Hide hidden forums on load
			if (hidden) {
				$content.hide();
			}
		});

		// Since we dynamically create our AJAX button, phpBB's does not ajaxify it
		// so we reproduce phpBB's ajaxify on our buttons here
		$('.collapse-btn').each(function () {
			var $this = $(this),
				ajax = $this.attr('data-ajax');

			if (ajax !== 'false') {
				var fn = (ajax !== 'true') ? ajax : null;
				phpbb.ajaxify({
					selector: this,
					refresh: $this.attr('data-refresh') !== undefined,
					callback: fn
				});
			}
		});

		phpbb.addAjaxCallback('phpbb_collapse', function (res) {
			var $this = $(this),
				forum = $this.attr('data-id');
			if ('just for test of animation') { // TODO: Remove this line can use the one below if ajax server side is working
			//if (res.success) {
				$this
					.toggleClass('collapse-show collapse-hide')
					.closest('.forabg')
					.find('.topiclist.forums')
					.slideToggle("fast")
				;
			}
		});
	});

})(jQuery); // Avoid conflicts with other libraries

