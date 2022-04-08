<?php
	$facetParameters = json_decode(get_option('facets_parameters'), true);
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
				// Elements
				foreach ($elements as $element) {
					if (isFacetActive($recordType, $element->name, $facetParameters)) {
						$isDate = in_array($element->name, $dateFields);
						$facetParameter = $facetParameters['elements'][$element->name];
						if (isset($facetParameter['style']) && $facetParameter['style'] == 'checkbox') {
							$html = getFacetCheckboxesForElement($recordType, $subsetSQL, $element->id, $isDate, $hideSingleEntries, (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''), $checkboxMinCount);
						} else {
							$html = getFacetSelectForElement($recordType, $subsetSQL, $element->id, $isDate, $hideSingleEntries, (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''));
						}

						if ($html != '') printHtml($html, $element->id, $facetsDirection, $element->name);
					}
				}

				if ($recordType == 'item') {
					// Collection (only for items)
					if (isFacetActive('item', null, $facetParameters, 'item_types')) {
						$facetParameter = $facetParameters['item_types'];
						if (isset($facetParameter['style']) && $facetParameter['style'] == 'checkbox') {
							$html = getFacetCheckboxesForItemType($subsetSQL, $hideSingleEntries, (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''), $checkboxMinCount);
						} else {
							$html = getFacetSelectForItemType($subsetSQL, $hideSingleEntries, (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''));
						}

						if ($html != '') printHtml($html, 'item-type', $facetsDirection, 'Item Type');
					}

					// Item Type (only for items)
					if (isFacetActive('item', null, $facetParameters, 'collections')) {
						$facetParameter = $facetParameters['collections'];
						if (isset($facetParameter['style']) && $facetParameter['style'] == 'checkbox') {
							$html = getFacetCheckboxesForCollection($subsetSQL, $hideSingleEntries, (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''), $checkboxMinCount);
						} else {
							$html = getFacetSelectForCollection($subsetSQL, $hideSingleEntries, (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''));
						}

						if ($html != '') printHtml($html, 'collection', $facetsDirection, 'Collection');
					}

					// Tags (only for items)
					if (isFacetActive('item', null, $facetParameters, 'tags')) {
						$facetParameter = $facetParameters['tags'];
						if (isset($facetParameter['style']) && $facetParameter['style'] == 'checkbox') {
							$html = getFacetCheckboxesForTag($subsetSQL, $hideSingleEntries, (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''), $checkboxMinCount);
						} else {
							$html = getFacetSelectForTag($subsetSQL, $hideSingleEntries, (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''));
						}

						if ($html != '') printHtml($html, 'tags', $facetsDirection, 'Tags');
					}
				}
				
				// Owner
				if (isFacetActive($recordType, null, $facetParameters, 'users')) {
					$facetParameter = $facetParameters['users'];
					if (isset($facetParameter['style']) && $facetParameter['style'] == 'checkbox') {
						$html = getFacetCheckboxesForUser($recordType, $subsetSQL, $hideSingleEntries, (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''), $checkboxMinCount);
					} else {
						$html = getFacetSelectForUser($recordType, $subsetSQL, $hideSingleEntries, (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''));
					}

					if ($html != '') printHtml($html, 'user', $facetsDirection, 'Owner');
				}
				
				// Public
				if (isFacetActive($recordType, null, $facetParameters, 'public')) {
					$facetParameter = $facetParameters['public'];
					if (isset($facetParameter['style']) && $facetParameter['style'] == 'checkbox') {
						$html = getFacetCheckboxesForExtra($recordType, $subsetSQL, 'public', $hideSingleEntries, (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''), $checkboxMinCount);
					} else {
						$html = getFacetSelectForExtra($recordType, $subsetSQL, 'public', $hideSingleEntries, (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''));
					}

					if ($html != '') printHtml($html, 'public', $facetsDirection, 'Public');
				}
				
				// Featured
				if (isFacetActive($recordType, null, $facetParameters, 'featured')) {
					$facetParameter = $facetParameters['featured'];
					if (isset($facetParameter['style']) && $facetParameter['style'] == 'checkbox') {
						$html = getFacetCheckboxesForExtra($recordType, $subsetSQL, 'featured', $hideSingleEntries, (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''), $checkboxMinCount);
					} else {
						$html = getFacetSelectForExtra($recordType, $subsetSQL, 'featured', $hideSingleEntries, (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''));
					}

					if ($html != '') printHtml($html, 'featured', $facetsDirection, 'Featured');
				}
			?>
		</form>
	</div>
</div>
