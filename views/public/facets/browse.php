<?php
	$facetsElements = json_decode(get_option('facets_elements'), true);
	$hideSingleEntries = (bool)get_option('facets_hide_single_entries');
	$facetsCollapsible = (bool)get_option('facets_collapsible');
	$facetsDirection = (string)get_option('facets_direction');
	$checkboxMinCount = (int)get_option('facets_checkbox_minimum_amount');
	$dateFields = array('Date', 'Date Available', 'Date Created', 'Date Accepted', 'Date Copyrighted', 'Date Submitted', 'Date Issued', 'Date Modified', 'Date Valid');

	$table = get_db()->getTable('Element');
	$select = $table->getSelect()
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

<div id="facets-container" class="<?php echo "facets-layout-" . $facetsDirection; ?>">
	<button id="facets-title" <?php if ($facetsCollapsible) echo "class=\"facets-collapsed\""; ?>><?php echo html_escape(__('Refine search')) ?></button>
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
							$isDate = in_array($element->name, $dateFields);
							$facetElement = $facetsElements['elements'][$element->name];
							if (isset($facetElement['type']) && $facetElement['type'] == 'checkbox') {
								$html = get_element_facet_checkboxes('collection', $subsetSQL, $element->id, $isDate, $hideSingleEntries, (isset($facetElement['sort']) ? $facetElement['sort'] : ''), (isset($facetElement['popularity']) ? $facetElement['popularity'] : ''), $checkboxMinCount);
							} else {
								$html = get_element_facet_select('collection', $subsetSQL, $element->id, $isDate, $hideSingleEntries, (isset($facetElement['sort']) ? $facetElement['sort'] : ''), (isset($facetElement['popularity']) ? $facetElement['popularity'] : ''));
							}
							if ($html != '') {
								echo "<div id=\"facets-field-" . $element->id . "\" class=\"facets-container-" . $facetsDirection . "\">\n";
								echo "<label for=\"\">" . html_escape(__($element->name)) . "</label>\n";
								echo $html . "\n";
								echo "</div>\n";
							}
						}
					}
				} else {	
					foreach ($elements as $element) {
						if (isFacetActive($recordType, $element->name, $facetsElements)) {
							$isDate = in_array($element->name, $dateFields);
							$facetElement = $facetsElements['elements'][$element->name];
							if (isset($facetElement['type']) && $facetElement['type'] == 'checkbox') {
								$html = get_element_facet_checkboxes('item', $subsetSQL, $element->id, $isDate, $hideSingleEntries, (isset($facetElement['sort']) ? $facetElement['sort'] : ''), (isset($facetElement['popularity']) ? $facetElement['popularity'] : ''), $checkboxMinCount);
							} else {
								$html = get_element_facet_select('item', $subsetSQL, $element->id, $isDate, $hideSingleEntries, (isset($facetElement['sort']) ? $facetElement['sort'] : ''), (isset($facetElement['popularity']) ? $facetElement['popularity'] : ''));
							}
							if ($html != '') {
								echo "<div id=\"facets-field-" . $element->id . "\" class=\"facets-container-" . $facetsDirection . "\">\n";
								echo "<label for=\"\">" . html_escape(__($element->name)) . "</label>\n";
								echo $html . "\n";
								echo "</div>\n";
							}								
						}
					}

					if ((bool)get_option('facets_item_types_active')) {
						if (get_option('facets_item_types_style') == 'checkbox') {
							$html = get_item_types_facet_checkboxes($subsetSQL, $hideSingleEntries, get_option('facets_item_types_sort'), get_option('facets_item_types_popularity'), $checkboxMinCount);
						} else {
							$html = get_item_types_facet_select($subsetSQL, $hideSingleEntries, get_option('facets_item_types_sort'), get_option('facets_item_types_popularity'));
						}
						if ($html != '') {
							echo "<div id=\"facets-field-item-type\" class=\"facets-container-" . $facetsDirection . "\">\n";
							echo "<label for=\"\">" . html_escape(__('Item Type')) . "</label>\n";
							echo $html . "\n";
							echo "</div>\n";
						}
					}

					if ((bool)get_option('facets_collections_active')) {
						if (get_option('facets_collections_style') == 'checkbox') {
							$html = get_collections_facet_checkboxes($subsetSQL, $hideSingleEntries, get_option('facets_collections_sort'), get_option('facets_collections_popularity'), $checkboxMinCount);
						} else {
							$html = get_collections_facet_select($subsetSQL, $hideSingleEntries, get_option('facets_collections_sort'), get_option('facets_collections_popularity'));
						}
						if ($html != '') {
							echo "<div id=\"facets-field-collection\" class=\"facets-container-" . $facetsDirection . "\">\n";
							echo "<label for=\"\">" . html_escape(__('Collection')) . "</label>\n";
							echo $html . "\n";
							echo "</div>\n";
						}
					}

					if ((bool)get_option('facets_tags_active')) {
						if (get_option('facets_tags_style') == 'checkbox') {
							$html = get_tags_facet_checkboxes($subsetSQL, $hideSingleEntries, get_option('facets_tags_sort'), get_option('facets_tags_popularity'), $checkboxMinCount);
						} else {
							$html = get_tags_facet_select($subsetSQL, $hideSingleEntries, get_option('facets_tags_sort'), get_option('facets_tags_popularity'));
						}
						if ($html != '') {
							echo "<div id=\"facets-field-tag\" class=\"facets-container-" . $facetsDirection . "\">\n";
							echo "<label for=\"\">" . html_escape(__('Tags')) . "</label>\n";
							echo $html . "\n";
							echo "</div>\n";
						}
					}
				}
			?>
		</form>
	</div>
</div>
