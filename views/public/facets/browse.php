<?php
	$facetsElements = json_decode(get_option('facets_elements'), true);
	$hideSingleEntries = (bool)get_option('facets_hide_single_entries');
	$facetsCollapsible = (bool)get_option('facets_collapsible');
	$facetsDirection = (string)get_option('facets_direction');

	$table = get_db()->getTable('Element');
	$select = $table->getSelect()
		->where('element_sets.name = \'Dublin Core\'')
		->order('elements.element_set_id')
		->order('ISNULL(elements.order)')
		->order('elements.order');
	$elements = $table->fetchObjects($select);
	
	if ($recordType == 'item') {
		$subsetSQL = str_replace('`items`.*', '`items`.`id`', (string)get_db()->getTable('Item')->getSelectForFindBy($params));
	} else {
		$subsetSQL = str_replace('`collections`.*', '`collections`.`id`', (string)get_db()->getTable('Collection')->getSelectForFindBy($params));
	}
?>

<div class="facets-container <?php echo "facets-layout-" . $facetsDirection; ?>">
	<button id="facets-title" <?php if ($facetsCollapsible) echo "class=\"facets-collapsible\""; ?>><?php echo html_escape(__('Refine search by')) ?>...</button>
	<div id="facets-body">
		<?php 
			if ($description = get_option('facets_description')) {
				echo "<p class=\"description\">" . $description . "</p>\n";
			}
		?>
		
		<form action="index.html" method="post" <?php echo ($facetsDirection == 'horizontal' ? 'class="flex"': 'class=""'); ?>>
			<?php
				if ($recordType == 'collection') {
					foreach ($elements as $element) {
						if (isFacetActive($recordType, $element->name, $facetsElements)) {
							$isDate = in_array($element->name, array('Date'));
							$facetElement = $facetsElements['elements'][$element->name];
							if ($html = get_dc_facet_select('collection', $subsetSQL, $element->name, $isDate, $hideSingleEntries, (isset($facetElement['sort']) ? $facetElement['sort'] : ''), (isset($facetElement['popularity']) ? $facetElement['popularity'] : ''))) {
								echo "<div class=\"container-" . $facetsDirection . "\">\n";
								echo "<label for=\"\">" . html_escape(__($element->name)) . "</label>\n";
								echo $html;
								echo "</div>\n";
							}
						}
					}
				} else {	
					foreach ($elements as $element) {
						if (isFacetActive($recordType, $element->name, $facetsElements)) {
							$isDate = in_array($element->name, array('Date'));
							$facetElement = $facetsElements['elements'][$element->name];
							if ($html = get_dc_facet_select('item', $subsetSQL, $element->name, $isDate, $hideSingleEntries, (isset($facetElement['sort']) ? $facetElement['sort'] : ''), (isset($facetElement['popularity']) ? $facetElement['popularity'] : ''))) {
								echo "<div class=\"container-" . $facetsDirection . "\">\n";
								echo "<label for=\"\">" . html_escape(__($element->name)) . "</label>\n";
								echo $html;
								echo "</div>\n";
							}
						}
					}

					if ((bool)get_option('facets_item_types_active')) {
						if ($html = get_item_types_facet_select($subsetSQL, $hideSingleEntries, get_option('facets_item_types_sort'), get_option('facets_item_types_popularity'))) {
							echo "<div class=\"container-" . $facetsDirection . "\">\n";
							echo "<label for=\"\">" . html_escape(__('Item Type')) . "</label>\n";
							echo $html;
							echo "</div>\n";
						}
					}

					if ((bool)get_option('facets_collections_active')) {
						if ($html = get_collections_facet_select($subsetSQL, $hideSingleEntries, get_option('facets_collections_sort'), get_option('facets_collections_popularity'))) {
							echo "<div class=\"container-" . $facetsDirection . "\">\n";
							echo "<label for=\"\">" . html_escape(__('Collection')) . "</label>\n";
							echo $html;
							echo "</div>\n";
						}
					}

					if ((bool)get_option('facets_tags_active')) {
						if ($html = get_tags_facet_select($subsetSQL, $hideSingleEntries, get_option('facets_tags_sort'), get_option('facets_tags_popularity'))) {
							echo "<div class=\"container-" . $facetsDirection . "\">\n";
							echo "<label for=\"\">" . html_escape(__('Tags')) . "</label>\n";
							echo $html;
							echo "</div>\n";
						}
					}
				}
			?>
		</form>
	</div>
</div>
<script type="text/javascript">
	// force block collapsible if screen size small
	jQuery(window).on('load', function() {
		if (jQuery(window).width() < 768) {
			jQuery('#facets-title').addClass('facets-collapsed');
			jQuery('#facets-body').addClass('hidden');
		}
	});
	// hides facets block (but for title) on load
	jQuery('#facets-body').next().addClass('hidden');
	// submit results of refining search to reload the page
	window.jQuery(document).ready(function() {
		window.jQuery('select').change(function() {
			var option = window.jQuery(this).find('option:selected');
			if (typeof(option.data('url')) !== 'undefined') window.location.href = option.data('url');
		});
	});
	// collapse/expands facets block
	jQuery('#facets-title').click(function () {
		$header = jQuery(this);
		if (!$header.hasClass('facets-collapsible') && !$header.hasClass('facets-collapsed')) return;
		//getting the next element
		$content = $header.next();
		//open up the content needed - toggle the slide- if visible, slide up, if not slidedown.
		$content.slideToggle(300, function () {
			//execute this after slideToggle is done
			//change icon of header based on visibility of content div
			$header.toggleClass('facets-collapsible facets-collapsed');
		});
	});
</script>
