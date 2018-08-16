<?php
/**
 * Helper to display a Facets Block
 */
// class Facets_View_Helper_Facets extends Zend_View_Helper_Abstract
// {

    /**
     * Return Tags associated with Array of Items Ids.
     *
     * @param itemsArray
     * @return array Tags.
     */
    function get_tags_for_items_array($itemsArray = array()) {

        // Get the database.
        $db = get_db();
        // Get the Tag table.
        $table = $db->getTable('Tag');
        // Build the select query.
        $select = $table->getSelectForFindBy();
        /**
         * If there is a Collection, we'll join to the Item table where the
         * collection_id is equal to the ID of our Collection.
         */
        if (count($itemsArray) > 0) {
            $table->filterByTagType($select, 'Item');
            $select->where('items.id IN ('. implode(', ', $itemsArray) . ')');
        }
        // Fetch some tags with our select.
        $tags = $table->fetchObjects($select);
        return $tags;
    }

    /**
     * Return Item Types associated with Array of Items Ids.
     *
     * @param itemsArray
     * @return array ItemType.
     */
    function get_item_types_for_items_array($itemsArray = array()) {

        // Get the database.
        $db = get_db();
        // Get the Tag table.
        $table = $db->getTable('ItemType');
        // Build the select query.
        $select = $table->getSelect();
        /**
         * If there is a Collection, we'll join to the Item table where the
         * collection_id is equal to the ID of our Collection.
         */
        if (count($itemsArray) > 0) {
          $select->joinInner(array('items' => $db->Items),
                      'item_types.id = items.item_type_id', array());
          $select->where('items.id IN ('. implode(', ', $itemsArray) . ')');
        }
        // Fetch some tags with our select.
        $item_types = $table->fetchObjects($select);
        return $item_types;
    }

    /**
     * Return Item Types associated with Array of Items Ids.
     *
     * @return array ItemType.
     */
    function get_item_types() {

        // Get the database.
        $db = get_db();
        // Get the Tag table.
        $table = $db->getTable('ItemType');
        // Build the select query.
        $select = $table->getSelect();

        // Fetch some tags with our select.
        $item_types = $table->fetchObjects($select);
        return $item_types;
    }

    /**
     * Return Collections associated with Array of Items Ids.
     *
     * @param itemsArray
     * @return array Collections.
     */
    function get_collections_for_items_array($itemsArray = array()) {

        // Get the database.
        $db = get_db();
        // Get the Tag table.
        $table = $db->getTable('Collection');
        // Build the select query.
        $select = $table->getSelect();
        /**
         * If there is a Collection, we'll join to the Item table where the
         * collection_id is equal to the ID of our Collection.
         */
        if (count($itemsArray) > 0) {
            $select->joinInner(array('items' => $db->Items),
                        'collections.id = items.collection_id', array());
            // $table->filterByTagType($select, 'Item');
            $select->where('items.id IN ('. implode(', ', $itemsArray) . ')');
        }
        // Fetch some tags with our select.
        $collections = $table->fetchObjects($select);
        return $collections;
    }

    /**
     * Return Tags associated with Array of Items Ids.
     *
     * @param itemsArray
     * @return array Tags.
     */
    function get_dc_for_items_array($itemsArray = array(), $dcElementName = null) {

        // Get the database.
        $db = get_db();
        // Get the Tag table.
        $table = $db->getTable('ElementText');
        // Build the select query.
        $select = $table->getSelect();
        $select->joinInner(array('elements' => $db->Elements),
                    'element_texts.element_id = elements.id', array());
        $select->joinInner(array('element_sets' => $db->ElementSet),
                    'element_sets.id = elements.element_set_id', array());
        $select->joinInner(array('items' => $db->Item),
                    'items.id = element_texts.record_id', array());
        $select->where('element_sets.name = '. $db->quote('Dublin Core'));
        $select->where('elements.name = '. $db->quote($dcElementName));
        $select->where('items.id IN ('. implode(', ', $itemsArray) . ')');

        //debug($select->query();

        /**
         * If there is a Collection, we'll join to the Item table where the
         * collection_id is equal to the ID of our Collection.
         */
        // Fetch some tags with our select.
        if(!empty($itemsArray)){
          $elements = $table->fetchObjects($select);
        }
        return $elements;
    }

    /**
     * Return HTML Select associated with Array of facets values.
     *
     * @param itemsArray
     * @return array Tags.
     */
    function get_dc_facet_select($itemsArray = array(), $dcElementName = null) {

      if(is_null($dcElementName)) {
        $dcElementName = "Title";
      }
      if(empty($itemsArray)){
        return "";
      }
      // Get the database.
      $db = get_db();
      // Get the Tag table.
      $table = $db->getTable('ElementText');
      // Build the select query.
      $select = $table->getSelect();
      $select->joinInner(array('elements' => $db->Elements),
                  'element_texts.element_id = elements.id', array());
      $select->joinInner(array('element_sets' => $db->ElementSet),
                  'element_sets.id = elements.element_set_id', array());
      $select->joinInner(array('items' => $db->Item),
                  'items.id = element_texts.record_id', array());
      $select->where('element_sets.name = '. $db->quote('Dublin Core'));
      $select->where('elements.name = '. $db->quote($dcElementName));
      $select->where('items.id IN ('. implode(', ', $itemsArray) . ')');

      // Fetch values
      if(!empty($itemsArray)){
        $elements = $table->fetchObjects($select);
        // print_r($elements);
      }
      // build table
      if(!empty($elements)){
        $facet = array();
        foreach ($elements as $element) {
          $facet[] = $element->text;
        }
        $element_id = $element->element_id;
        // make it unique
        // $facet = array_unique($facet);
        $facet = array_count_values($facet);
        arsort($facet);
        // print_r($facet);

        // get current parameters to check if one is selected
        // Get the current facets.
        if (!empty($_GET['advanced'])) {
            $search = $_GET['advanced'];
            foreach($search as $Searchindex => $SearchArray){
              if(isset($SearchArray['element_id']) && $SearchArray['element_id'] == $element_id){
                $term = $SearchArray['terms'];
                break;
              }
            }
        }

        if(isset($term)){
          $html =  "<div class=\"select-cross\"><select id=\"".$element_id."\" class=\"facet-selected\" name=\"".$dcElementName."\">";
          $url = getElementFieldUrl($element_id);
          $html .= "<option value=\"\" data-url=\"" . $url . "\">Dé-sélectionner...</option>";
          $html .= "<option selected value=\"\">$term</option>";
        } else {
          $html =  "<div class=\"select-arrow\"><select id=\"".$element_id."\" class=\"facet\" name=\"".$dcElementName."\">";
          $html .= "<option value=\"\">Sélectionner...</option>";
        }

        foreach($facet as $name => $count){
          $url = getElementFieldUrl($element_id, $name);
          $html .= "<option value=\"".$name."\" data-url=\"" . $url . "\">".$name." (".$count.")</option>";
        }
        // foreach($facet as $name){
        //   $html .= "<option value=\"".$name."\">".$name."</option>";
        // }
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
    function getElementFieldUrl($field_id, $value = null)
    {
        // Get the current facets.
        if (!empty($_GET['advanced'])) {
            $search = $_GET['advanced'];
            // unset current element filter if already set
            foreach($search as $Searchindex => $SearchArray){
              if(isset($SearchArray['element_id']) && $SearchArray['element_id'] == $field_id){
                unset($search[$Searchindex]);
              }
            }
        } else {
            $search = array();
        }
        if(!is_null($value)){
          $search[] = array('element_id'=>$field_id,'type'=>'is exactly','terms'=>$value);
        }
        $params['advanced'] = $search;
        if(isset($_GET['origin'])) $params['origin'] = $_GET['origin'];
        if(isset($_GET['origin-title'])) $params['origin-title'] = $_GET['origin-title'];
        if(isset($_GET['type'])) $params['type'] = $_GET['type'];
        if(isset($_GET['collection'])) $params['collection'] = $_GET['collection'];
        if(isset($_GET['tag_id'])) $params['tag_id'] = $_GET['tag_id'];
        if(isset($_GET['tag'])) $params['tag'] = $_GET['tag'];
        if(isset($_GET['tags'])) $params['tags'] = $_GET['tags'];
        if(isset($_GET['search'])) $params['search'] = $_GET['search'];

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
        if(isset($_GET['origin'])) $params['origin'] = $_GET['origin'];
        if(isset($_GET['origin-title'])) $params['origin-title'] = $_GET['origin-title'];
        if(isset($_GET['type'])) $params['type'] = $_GET['type'];
        if(isset($_GET['collection'])) $params['collection'] = $_GET['collection'];
        if(isset($_GET['tag_id'])) $params['tag_id'] = $_GET['tag_id'];
        if(isset($_GET['tag'])) $params['tag'] = $_GET['tag'];
        if(isset($_GET['tags'])) $params['tags'] = $_GET['tags'];
        if(isset($_GET['search'])) $params['search'] = $_GET['search'];

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
// }
?>
