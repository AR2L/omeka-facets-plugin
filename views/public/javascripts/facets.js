jQuery(document).ready(function () {
	// loads language variables
	var language = facetsLanguage.language;
	
	// submits results of refining search to reload the page
	window.jQuery('#facets-body select').change(function() {
		var option = window.jQuery(this).find('option:selected');
		if (typeof(option.data('url')) !== 'undefined') window.location.href = option.data('url');
	});
	
	// submits results of refining search to reload the page
	window.jQuery('#facets-body input:checkbox').change(function() {
		var checkbox = window.jQuery(this);
		if (typeof(checkbox.data('url')) !== 'undefined') window.location.href = checkbox.data('url');
	});

	// toggles extra values visibility
	jQuery('.facet-visibility-toggle').click(function () {
		var id = jQuery(this).data('element-id');
		var div = jQuery('#facet-extra-values-' + id);
		var link = jQuery('#facet-extra-link-' + id);
		div.toggleClass("hidden unhidden");
		if (div.hasClass('hidden')) {
			link.text(language.ShowMore);
		} else {
			link.text(language.ShowLess);
		}
	});

	// collapses/expands facets block
	jQuery('#facets-title').click(function () {
		var header = jQuery(this);
		if (!header.hasClass('facets-collapsible') && !header.hasClass('facets-collapsed')) return;
		//getting the next element
		var content = header.next();
		//open up the content needed - toggle the slide- if visible, slide up, if not slidedown.
		content.slideToggle(300, function () {
			//execute this after slideToggle is done
			//change icon of header based on visibility of content div
			header.toggleClass('facets-collapsible facets-collapsed');
		});
	});
});	
	
// forces block collapsible if screen size small
jQuery(window).on('load', function() {
	if (jQuery(window).width() < 768) {
		jQuery('#facets-title').addClass('facets-collapsed');
		jQuery('#facets-body').addClass('hidden');
	}

	// hides facets block (but for title) on load
	if (jQuery('#facets-title').hasClass('facets-collapsed')) {
		jQuery('#facets-body').addClass('hidden');
	}
});
