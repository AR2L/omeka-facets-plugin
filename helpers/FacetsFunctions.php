<?php
	/**
	 * Helper to display a Facets Block
	 */
	// class Facets_View_Helper_Facets extends Zend_View_Helper_Abstract
	// {

	/**
	 * Return HTML Select associated with Array of facets tag values.
	 *
	 * @param itemsArray
	 * @param hideSingleEntries
	 * @param sortOrder
	 * @param hidePopularity
	 * @return html.
	 */
	function get_tags_facet_select($itemsArray = array(), $hideSingleEntries = false, $sortOrder = 'count_alpha', $hidePopularity = false) {
		// Return, if no Item is available
		if (empty($itemsArray)) return "";

		// Define Order by clause
		if ($sortOrder == 'count_alpha') {
			$orderBy = array('tagCount DESC', 'name ASC');
		} else {
			$orderBy = array('name ASC');
		}

		// Get the database.
		$db = get_db();
		// Get the table.
		$table = $db->getTable('Tag');
		// Build the select query.
		$select = $table->getSelectForFindBy();
		$table->filterByTagType($select, 'Item');
		$select->where('items.id IN ('. implode(', ', $itemsArray) . ')');
		$select->order($orderBy);

		if ($tags = $table->fetchObjects($select)) {
			// Build array
			$facetTags = array();
			foreach ($tags as $tag) {
				$facetTags[$tag->id]['id'] = $tag->id;
				$facetTags[$tag->id]['name'] = $tag->name;
				$facetTags[$tag->id]['count'] = $tag->tagCount;
			}
			
			// Stores data for selected tag, if any
			$selectedTagName = $_GET['tags'];

			// Remove single entries if required
			if ($hideSingleEntries && count($facetTags) > FACETS_MINIMUM_AMOUNT) {
				$facetTags = array_filter($facetTags, "isNotSingleEntry");
			}			

			$addOptions = false;
			// get current parameters to check if one is selected
			if ($selectedTagName != '') {
				$html  = "<div class=\"select-cross\"><select class=\"facet-selected\" name=\"tag\">";
				$html .= "<option value=\"\" data-url=\"" . getFieldUrl('tags', null) . "\"> " . html_escape(__('Remove filter')) . "...</option>";
				$html .= "<option selected value=\"\">" . $selectedTagName . "</option>";
			} elseif (count($facetTags) > 0) {
				$html  = "<div class=\"select-arrow\"><select class=\"facet\" name=\"tag\">";
				$html .= "<option value=\"\">" . html_escape(__('Select')) . "...</option>";
				$addOptions = true;
			}

			if ($addOptions) {
				foreach ($facetTags as $tag) {
					$html .= "<option value=\"" . $tag['id'] . "\" data-url=\"" . getFieldUrl('tags', $tag['name']) . "\">" . $tag['name'] . ($hidePopularity ? "" : " (" . $tag['count'] . ")") . "</option>";
				}
			}
			$html .= "</select></div>";
		} else {
			$html = false;
		}

		return $html;
	}
	
	/**
	 * Return HTML Select associated with Array of facets collection values.
	 *
	 * @param itemsArray
	 * @param hideSingleEntries
	 * @param sortOrder
	 * @param hidePopularity
	 * @return html.
	 */
	function get_collections_facet_select($itemsArray = array(), $hideSingleEntries = false, $sortOrder = 'count_alpha', $hidePopularity = false) {
		// Return, if no Item is available
		if (empty($itemsArray)) return "";

		// Get the database.
		$db = get_db();
		// Get the table.
		$table = $db->getTable('Collection');
		// Build the select query.
		$select = $table->getSelect()
			->columns('COUNT(collections.id) AS count')
			->joinInner(array('items' => $db->Items),
				'collections.id = items.collection_id', array())
			->where('items.id IN ('. implode(', ', $itemsArray) . ')')
			->group('collections.id');

		if ($collections = $table->fetchObjects($select)) {
			// Build array
			$facetCollections = array();
			foreach ($collections as $collection) {
				$facetCollections[$collection->id]['id'] = $collection->id;
				$facetCollections[$collection->id]['name'] = $collection->getDisplayTitle();
				$facetCollections[$collection->id]['count'] = $collection->count;
			}
			
			// Stores data for selected collection, if any
			if ($collection_id = $_GET['collection']) {
				$selectedCollection = $facetCollections[$collection_id];
			}

			// Remove single entries if required
			if ($hideSingleEntries && count($facetCollections) > FACETS_MINIMUM_AMOUNT) {
				$facetCollections = array_filter($facetCollections, "isNotSingleEntry");
			}			

			// Sort array
			if ($sortOrder == 'count_alpha') {
				array_multisort(array_column($facetCollections, 'count'), SORT_DESC, array_column($facetCollections, 'name'), SORT_ASC, $facetCollections);
			} else {
				array_multisort(array_column($facetCollections, 'name'), SORT_ASC, $facetCollections);
			}

			$addOptions = false;
			// get current parameters to check if one is selected
			if (isset($selectedCollection)) {
				$html  = "<div class=\"select-cross\"><select class=\"facet-selected\" name=\"collection\">";
				$html .= "<option value=\"\" data-url=\"" . getFieldUrl('collection', null) . "\"> " . html_escape(__('Remove filter')) . "...</option>";
				$html .= "<option selected value=\"\">" . $selectedCollection['name'] . "</option>";
			} elseif (count($facetCollections) > 0) {
				$html  = "<div class=\"select-arrow\"><select class=\"facet\" name=\"collection\">";
				$html .= "<option value=\"\">" . html_escape(__('Select')) . "...</option>";
				$addOptions = true;
			}

			if ($addOptions) {
				foreach ($facetCollections as $collection) {
					$html .= "<option value=\"" . $collection['id'] . "\" data-url=\"" . getFieldUrl('collection', $collection['id']) . "\">" . $collection['name'] . ($hidePopularity ? "" : " (" . $collection['count'] . ")") . "</option>";
				}
			}
			$html .= "</select></div>";
		} else {
			$html = false;
		}

		return $html;
	}

	/**
	 * Return HTML Select associated with Array of facets item type values.
	 *
	 * @param itemsArray
	 * @param hideSingleEntries
	 * @param sortOrder
	 * @param hidePopularity
	 * @return html.
	 */
	function get_item_types_facet_select($itemsArray = array(), $hideSingleEntries = false, $sortOrder = 'count_alpha', $hidePopularity = false) {
		// Return, if no Item is available
		if (empty($itemsArray)) return "";

		// Define Order by clause
		if ($sortOrder == 'count_alpha') {
			$orderBy = array('count DESC', 'text ASC');
		} else {
			$orderBy = array('text ASC');
		}

		// Get the database.
		$db = get_db();
		// Get the table.
		$table = $db->getTable('ItemType');
		// Build the select query.
		$select = $table->getSelect()
			->columns('COUNT(item_types.id) AS count')
			->joinInner(array('items' => $db->Items),
				'item_types.id = items.item_type_id', array())
			->where('items.id IN ('. implode(', ', $itemsArray) . ')')
			->group('item_types.id')
			->order($orderBy);

		if ($itemTypes = $table->fetchObjects($select)) {
			// Build array
			$facetItemTypes = array();
			foreach ($itemTypes as $itemType) {
				$facetItemTypes[$itemType->id]['id'] = $itemType->id;
				$facetItemTypes[$itemType->id]['name'] = $itemType->name;
				$facetItemTypes[$itemType->id]['count'] = $itemType->count;
			}
			
			// Stores data for selected item type, if any
			if ($itemType_id = $_GET['type']) {
				$selectedItemType = $facetItemTypes[$itemType_id];
			}

			// Remove single entries if required
			if ($hideSingleEntries && count($facetItemTypes) > FACETS_MINIMUM_AMOUNT) {
				$facetItemTypes = array_filter($facetItemTypes, "isNotSingleEntry");
			}			

			$addOptions = false;
			// get current parameters to check if one is selected
			if (isset($selectedItemType)) {
				$html  = "<div class=\"select-cross\"><select class=\"facet-selected\" name=\"type\">";
				$html .= "<option value=\"\" data-url=\"" . getFieldUrl('type', null) . "\"> " . html_escape(__('Remove filter')) . "...</option>";
				$html .= "<option selected value=\"\">" . $selectedItemType['name'] . "</option>";
			} elseif (count($facetItemTypes) > 0) {
				$html  = "<div class=\"select-arrow\"><select class=\"facet\" name=\"type\">";
				$html .= "<option value=\"\">" . html_escape(__('Select')) . "...</option>";
				$addOptions = true;
			}

			if ($addOptions) {
				foreach ($facetItemTypes as $itemType) {
					$html .= "<option value=\"" . $itemType['id'] . "\" data-url=\"" . getFieldUrl('type', $itemType['id']) . "\">" . $itemType['name'] . ($hidePopularity ? "" : " (" . $itemType['count'] . ")") . "</option>";
				}
			}
			$html .= "</select></div>";
		} else {
			$html = false;
		}

		return $html;
	}
	
	/**
	 * Return HTML Select associated with Array of facets values.
	 *
	 * @param itemsArray
	 * @param dcElementName
	 * @param isDate
	 * @param hideSingleEntries
	 * @param sortOrder
	 * @param hidePopularity
	 * @return html.
	 */
	function get_dc_facet_select($itemsArray = array(), $dcElementName = 'Title', $isDate = false, $hideSingleEntries = false, $sortOrder = 'count_alpha', $hidePopularity = false) {
		if (empty($itemsArray)) return "";
		
		// Get the database.
		$db = get_db();
		// Get the table.
		$table = $db->getTable('ElementText');
		// Create the orderby rules
		if ($isDate) {
			$groupBy = 'year';
			if ($sortOrder == 'count_alpha') {
				$orderBy = array('count DESC', 'year DESC');
			} else {
				$orderBy = array('year DESC');
			}
		} else {
			$groupBy = 'text';
			if ($sortOrder == 'count_alpha') {
				$orderBy = array('count DESC', 'text ASC');
			} else {
				$orderBy = array('text ASC');
			}
		}
		// Build the select query.
		$select = $table->getSelect()
			->columns(array('SUBSTR(element_texts.text, 1, 4) AS year', 'COUNT(text) AS count'))
			->joinInner(array('elements' => $db->Elements),
				'element_texts.element_id = elements.id', array())
			->joinInner(array('element_sets' => $db->ElementSet),
				'element_sets.id = elements.element_set_id', array())
			->joinInner(array('items' => $db->Item),
				'items.id = element_texts.record_id', array())
			->where('element_sets.name = '. $db->quote('Dublin Core'))
			->where('elements.name = '. $db->quote($dcElementName))
			->where('items.id IN ('. implode(', ', $itemsArray) . ')')
			->group($groupBy)
			->order($orderBy);
		
		// Build table
		if ($elements = $table->fetchObjects($select)) {
			$facet = array();
			foreach ($elements as $element) {
				if ($isDate) {
					$facet[$element->year] = $facet[$element->year] + $element->count;
				} else {
					$facet[$element->text] = $element->count;
				}
			}
			$element_id = $element->element_id;

			// remove single entries if required
			if ($hideSingleEntries && count($facet) > FACETS_MINIMUM_AMOUNT) {
				$facet = array_filter($facet, "isNotSingleEntry");
			}			

			// get current parameters to check if one is selected
			// Get the current facets.
			if (!empty($_GET['advanced'])) {
				$search = $_GET['advanced'];
				foreach ($search as $Searchindex => $SearchArray){
					if (isset($SearchArray['element_id']) && $SearchArray['element_id'] == $element_id) {
						$term = $SearchArray['terms'];
						break;
					}
				}
			}

			$addOptions = false;
			if (isset($term)){
				$html =	"<div class=\"select-cross\"><select id=\"" . $element_id . "\" class=\"facet-selected\" name=\"" . $dcElementName . "\">";
				$url = getElementFieldUrl($element_id, null, $isDate);
				$html .= "<option value=\"\" data-url=\"" . $url . "\"> " . html_escape(__('Remove filter')) . "...</option>";
				$html .= "<option selected value=\"\">$term</option>";
			} elseif (count($facet) > 0) {
				$html =	"<div class=\"select-arrow\"><select id=\"" . $element_id . "\" class=\"facet\" name=\"" . $dcElementName . "\">";
				$html .= "<option value=\"\">" . html_escape(__('Select')) . "...</option>";
				$addOptions = true;
			}

			if ($addOptions) {
				foreach ($facet as $name => $count) {
					$url = getElementFieldUrl($element_id, $name, $isDate);
					$html .= "<option value=\"" . $name . "\" data-url=\"" . $url . "\">" . $name . ($hidePopularity ? "" : " (" . $count . ")") . "</option>";
				}
			}
			$html .= "</select></div>";
		} else {
			$html = false;
		}

		return $html;
	}

	/**
	 * Add an Element Field to Search to the current URL.
	 *
	 * @param string $field The Element id.
	 * @param string $value The Element value.
	 * @return string The new URL.
	 */
	function getElementFieldUrl($field_id, $value = null, $isDate = false)
	{
		// Get the current facets.
		if (!empty($_GET['advanced'])) {
			$search = $_GET['advanced'];
			// unset current element filter if already set
			foreach ($search as $Searchindex => $SearchArray){
				if (isset($SearchArray['element_id']) && $SearchArray['element_id'] == $field_id){
					unset ($search[$Searchindex]);
				}
			}
		} else {
			$search = array();
		}
		if (!is_null($value)) {
			if ($isDate) {
				$search[] = array('element_id'=>$field_id, 'type'=>'starts with', 'terms'=>$value);
			} else {
				$search[] = array('element_id'=>$field_id, 'type'=>'is exactly', 'terms'=>$value);
			}
		}
		$params['advanced'] = $search;
		if (isset($_GET['origin'])) $params['origin'] = $_GET['origin'];
		if (isset($_GET['origin-title'])) $params['origin-title'] = $_GET['origin-title'];
		if (isset($_GET['type'])) $params['type'] = $_GET['type'];
		if (isset($_GET['collection'])) $params['collection'] = $_GET['collection'];
		if (isset($_GET['tag_id'])) $params['tag_id'] = $_GET['tag_id'];
		if (isset($_GET['tag'])) $params['tag'] = $_GET['tag'];
		if (isset($_GET['tags'])) $params['tags'] = $_GET['tags'];
		if (isset($_GET['search'])) $params['search'] = $_GET['search'];

		// Rebuild the route.
		// return $_SERVER['HTTP_HOST'] . "?" . http_build_query($params);
		return 'browse?' . http_build_query($params);
	}

	/**
	 * Add an Element Field to Search to the current URL.
	 *
	 * @param string $filter The filter field name (tags|tag_id|type|collection).
	 * @param string $value The Element value.
	 * @return string The new URL.
	 */
	function getFieldUrl($filter, $value = null)
	{
		// Get the current facets.
		if (!empty($_GET['advanced'])) {
			$search = $_GET['advanced'];
		} else {
			$search = array();
		}
		// set previous parameters
		$params['advanced'] = $search;
		if (isset($_GET['origin'])) $params['origin'] = $_GET['origin'];
		if (isset($_GET['origin-title'])) $params['origin-title'] = $_GET['origin-title'];
		if (isset($_GET['type'])) $params['type'] = $_GET['type'];
		if (isset($_GET['collection'])) $params['collection'] = $_GET['collection'];
		if (isset($_GET['tag_id'])) $params['tag_id'] = $_GET['tag_id'];
		if (isset($_GET['tag'])) $params['tag'] = $_GET['tag'];
		if (isset($_GET['tags'])) $params['tags'] = $_GET['tags'];
		if (isset($_GET['search'])) $params['search'] = $_GET['search'];

		// set(unset) current
		if(!is_null($value)){
			$params[$filter] = $value;
		} else {
			unset($params[$filter]);
		}

		// Rebuild the route.
		// return $_SERVER['HTTP_HOST'] . "?" . http_build_query($params);
		return 'browse?' . http_build_query($params);
	}
	
	function isFacetActive($element_name, $settings) {
		return ($settings['item_elements']['Dublin Core'][$element_name] == 1);
	}
	
	function isNotSingleEntry($count) {
		return ($count != 1);
	}
?>
