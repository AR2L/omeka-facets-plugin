<?php
	$facetParameters = json_decode(get_option('facets_parameters'), true);
	$facetsCollapsible = (bool)get_option('facets_collapsible');
	$facetsDirection = (string)get_option('facets_direction');
	$facetsCount = (int)get_option('facets_count');
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
			if (get_option('facets_description') == 1) {
				echo "<p class=\"description\">" . __("Select values for one or more Elements to narrow down your search.") . "</p>\n";
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
							$html = getFacetCheckboxesForElement($recordType, $subsetSQL, $element->id, $isDate, (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''), $facetsCount);
						} else {
							$html = getFacetSelectForElement($recordType, $subsetSQL, $element->id, $isDate, (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''));
						}

						if ($html != '') printHtml($html, $element->id, $facetsDirection, $element->name);
					}
				}

				if ($recordType == 'item') {
					// Collection (only for items)
					if (isFacetActive('item', null, $facetParameters, 'item_types')) {
						$facetParameter = $facetParameters['item_types'];
						if (isset($facetParameter['style']) && $facetParameter['style'] == 'checkbox') {
							$html = getFacetCheckboxesForItemType($subsetSQL, (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''), $facetsCount);
						} else {
							$html = getFacetSelectForItemType($subsetSQL, (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''));
						}

						if ($html != '') printHtml($html, 'item-type', $facetsDirection, 'Item Type');
					}

					// Item Type (only for items)
					if (isFacetActive('item', null, $facetParameters, 'collections')) {
						$facetParameter = $facetParameters['collections'];
						if (isset($facetParameter['style']) && $facetParameter['style'] == 'checkbox') {
							$html = getFacetCheckboxesForCollection($subsetSQL, (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''), $facetsCount);
						} else {
							$html = getFacetSelectForCollection($subsetSQL, (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''));
						}

						if ($html != '') printHtml($html, 'collection', $facetsDirection, 'Collection');
					}

					// Tags (only for items)
					if (isFacetActive('item', null, $facetParameters, 'tags')) {
						$facetParameter = $facetParameters['tags'];
						if (isset($facetParameter['style']) && $facetParameter['style'] == 'checkbox') {
							$html = getFacetCheckboxesForTag($subsetSQL, (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''), $facetsCount);
						} else {
							$html = getFacetSelectForTag($subsetSQL, (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''));
						}

						if ($html != '') printHtml($html, 'tags', $facetsDirection, 'Tags');
					}
				}
				
				// Owner
				if (isFacetActive($recordType, null, $facetParameters, 'users')) {
					$facetParameter = $facetParameters['users'];
					if (isset($facetParameter['style']) && $facetParameter['style'] == 'checkbox') {
						$html = getFacetCheckboxesForUser($recordType, $subsetSQL, (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''), $facetsCount);
					} else {
						$html = getFacetSelectForUser($recordType, $subsetSQL, (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''));
					}

					if ($html != '') printHtml($html, 'user', $facetsDirection, 'Owner');
				}
				
				// Public
				if (isFacetActive($recordType, null, $facetParameters, 'public')) {
					$facetParameter = $facetParameters['public'];
					if (isset($facetParameter['style']) && $facetParameter['style'] == 'checkbox') {
						$html = getFacetCheckboxesForExtra($recordType, $subsetSQL, 'public', (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''), $facetsCount);
					} else {
						$html = getFacetSelectForExtra($recordType, $subsetSQL, 'public', (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''));
					}

					if ($html != '') printHtml($html, 'public', $facetsDirection, 'Public');
				}
				
				// Featured
				if (isFacetActive($recordType, null, $facetParameters, 'featured')) {
					$facetParameter = $facetParameters['featured'];
					if (isset($facetParameter['style']) && $facetParameter['style'] == 'checkbox') {
						$html = getFacetCheckboxesForExtra($recordType, $subsetSQL, 'featured', (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''), $facetsCount);
					} else {
						$html = getFacetSelectForExtra($recordType, $subsetSQL, 'featured', (isset($facetParameter['sort']) ? $facetParameter['sort'] : ''), (isset($facetParameter['popularity']) ? $facetParameter['popularity'] : ''));
					}

					if ($html != '') printHtml($html, 'featured', $facetsDirection, 'Featured');
				}
			?>
		</form>
	</div>
</div>
