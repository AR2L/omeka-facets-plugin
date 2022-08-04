<?php
/**
 * @copyright Jean-Baptiste HEREN, 2018
 * @copyright Daniele BINAGHI, 2021-2022
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package FacetsPlugin
 */

define('FACETS_PLUGIN_DIR', dirname(__FILE__));

define('FACETS_LANGUAGE', array(
	'ShowMore' => __('show all'),
	'ShowLess' => __('show less')
));

require_once FACETS_PLUGIN_DIR . '/helpers/FacetsFunctions.php';

class FacetsPlugin extends Omeka_Plugin_AbstractPlugin
{
	protected $_hooks = array(
		'install',
		'uninstall',
		'upgrade',
		'initialize',
		'config_form',
		'config',
		'collections_browse_sql',
		'public_head',
		'public_items_browse',
		'public_collections_browse',
		'public_facets'
	);

	/**
	 * Installs the plugin.
	 */
	public function hookInstall()
	{
		set_option('facets_public_hook', 'default');
		set_option('facets_description', 0);
		set_option('facets_direction', 'vertical');
		set_option('facets_collapsible', 0);
		set_option('facets_count', 0);

		$defaults = array(
			'elements' => array('item', 'collection', 'style', 'sort', 'popularity'),
			'item_types' => array('item', 'style', 'sort', 'popularity'),
			'collections' => array('item', 'style', 'sort', 'popularity'),
			'tags' => array('item', 'style', 'sort', 'popularity'),
			'users' => array('item', 'collection', 'style', 'sort', 'popularity'),
			'public' => array('item', 'collection', 'style', 'sort', 'popularity'),
			'featured' => array('item', 'collection', 'style', 'sort', 'popularity')			
		);
		set_option('facets_parameters', json_encode($defaults));
	}

	/**
	 * Uninstalls the plugin.
	 */
	public function hookUninstall()
	{
		delete_option('facets_public_hook');
		delete_option('facets_description');
		delete_option('facets_direction');
		delete_option('facets_collapsible');
		delete_option('facets_count');
		delete_option('facets_parameters');
	}

	public function hookUpgrade($args)
	{
        $oldVersion = $args['old_version'];
        $newVersion = $args['new_version'];

        if (version_compare($oldVersion, '2.5', '<')) {
			$parameters = json_decode(str_replace('type', 'style', get_option('facets_elements')), true);
			
			$parameters['item_types']['item'] = get_option('facets_item_types_active');
			$parameters['item_types']['style'] = get_option('facets_item_types_style');
			$parameters['item_types']['sort'] = get_option('facets_item_types_sort');
			$parameters['item_types']['popularity'] = get_option('facets_item_types_popularity');
			$parameters['collections']['item'] = get_option('facets_collections_active');
			$parameters['collections']['style'] = get_option('facets_collections_style');
			$parameters['collections']['sort'] = get_option('facets_collections_sort');
			$parameters['collections']['popularity'] = get_option('facets_collections_popularity');
			$parameters['tags']['item'] = get_option('facets_tags_active');
			$parameters['tags']['style'] = get_option('facets_tags_style');
			$parameters['tags']['sort'] = get_option('facets_tags_sort');
			$parameters['tags']['popularity'] = get_option('facets_tags_popularity');
			$parameters['users'] = array('item', 'collection', 'style', 'sort', 'popularity');
			$parameters['public'] = array('item', 'collection', 'style', 'sort', 'popularity');
			$parameters['featured'] = array('item', 'collection', 'style', 'sort', 'popularity');
			set_option('facets_parameters', json_encode($parameters));

			delete_option('facets_elements');
			delete_option('facets_item_types_active');
			delete_option('facets_item_types_style');
			delete_option('facets_item_types_sort');
			delete_option('facets_item_types_popularity');
			delete_option('facets_collections_active');
			delete_option('facets_collections_style');
			delete_option('facets_collections_sort');
			delete_option('facets_collections_popularity');
			delete_option('facets_tags_active');
			delete_option('facets_tags_style');
			delete_option('facets_tags_sort');
			delete_option('facets_tags_popularity');
		} elseif (version_compare($oldVersion, '2.9', '<')) {
			set_option('facets_description', (get_option('facets_description') != '' ? 1 : 0));
			set_option('facets_count', 0);
			delete_option('facets_checkbox_minimum_amount');
			delete_option('facets_hide_single_entries');
		}
	}

	/**
	 * Initialize this plugin.
	 */
	public function hookInitialize()
	{
		add_translation_source(dirname(__FILE__) . '/languages');

		get_view()->addHelperPath(dirname(__FILE__) . '/views/helpers', 'Facets_View_Helper_');

		$settings = json_decode(get_option('facets_parameters'), true);
		$this->_settings = $settings;
	}

	/**
	 * Shows plugin configuration page.
	 */
	public function hookConfigForm($args)
	{
		$settings = $this->_settings;

		$table = get_db()->getTable('Element');
		$select = $table->getSelect()
			->order('elements.element_set_id')
			->order('ISNULL(elements.order)')
			->order('elements.order');
		$elements = $table->fetchObjects($select);

		include 'config_form.php';
	}

	/**
	 * Handle the config form.
	 */
	public function hookConfig($args)
	{
		$post = $args['post'];
		
		set_option('facets_public_hook', $post['facets_public_hook']);
		set_option('facets_description', $post['facets_description']);
		set_option('facets_hide_single_entries', $post['facets_hide_single_entries']);
		set_option('facets_direction', $post['facets_direction']);
		set_option('facets_collapsible', $post['facets_collapsible']);
		set_option('facets_count', $post['facets_count']);

		$settings = array(
			'elements' => isset($post['elements']) ? $post['elements'] : array(),
			'item_types' => isset($post['item_types']) ? $post['item_types'] : array(),
			'collections' => isset($post['collections']) ? $post['collections'] : array(),
			'tags' => isset($post['tags']) ? $post['tags'] : array(),
			'users' => isset($post['users']) ? $post['users'] : array(),
			'public' => isset($post['public']) ? $post['public'] : array(),
			'featured' => isset($post['featured']) ? $post['featured'] : array()			
		);
		set_option('facets_parameters', json_encode($settings));
	}

	/**
	 * Hook into collections_browse_sql
	 *
	 * @select array $args
	 * @params array $args
	 */
	public function hookCollectionsBrowseSql($args)
	{
		$db = $this->_db;
		$select = $args['select'];
		$params = $args['params'];

		// if (array_key_exists('_advanced_0', $select->getPart('from')) return;
		
		if (strpos($select, '_advanced_') !== false) return;
		
		if ($advancedTerms = @$params['advanced']) {
			$where = '';
			$advancedIndex = 0;
			foreach ($advancedTerms as $v) {
				// Do not search on blank rows.
				if (empty($v['element_id']) || empty($v['type'])) {
					continue;
				}

				$value = isset($v['terms']) ? $v['terms'] : null;
				$type = $v['type'];
				$elementId = (int) $v['element_id'];
				$alias = "_advanced_{$advancedIndex}";

				$joiner = isset($v['joiner']) && $advancedIndex > 0 ? $v['joiner'] : null;

				$negate = false;
				// Determine what the WHERE clause should look like.
				switch ($type) {
					case 'does not contain':
						$negate = true;
					case 'contains':
						$predicate = "LIKE " . $db->quote('%'.$value .'%');
						break;

					case 'is not exactly':
						$negate = true;
					case 'is exactly':
						$predicate = ' = ' . $db->quote($value);
						break;

					case 'is empty':
						$negate = true;
					case 'is not empty':
						$predicate = 'IS NOT NULL';
						break;

					case 'starts with':
						$predicate = "LIKE " . $db->quote($value.'%');
						break;

					case 'ends with':
						$predicate = "LIKE " . $db->quote('%'.$value);
						break;

					case 'does not match':
						$negate = true;
					case 'matches':
						if (!strlen($value)) {
							continue 2;
						}
						$predicate = 'REGEXP ' . $db->quote($value);
						break;

					default:
						throw new Omeka_Record_Exception(__('Invalid search type given!'));
				}

				$predicateClause = "{$alias}.text {$predicate}";

				// Note that $elementId was earlier forced to int, so manual quoting
				// is unnecessary here
				$joinCondition = "{$alias}.record_id = collections.id AND {$alias}.record_type = 'Collection' AND {$alias}.element_id = $elementId";

				if ($negate) {
					$joinCondition .= " AND {$predicateClause}";
					$whereClause = "{$alias}.text IS NULL";
				} else {
					$whereClause = $predicateClause;
				}

				$select->joinLeft(array($alias => $db->ElementText), $joinCondition, array());
				if ($where == '') {
					$where = $whereClause;
				} elseif ($joiner == 'or') {
					$where .= " OR $whereClause";
				} else {
					$where .= " AND $whereClause";
				}

				$advancedIndex++;
			}

			if ($where) {
				$select->where($where);
			}
		}			
	}

	public function hookPublicHead($args)
	{
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$controller = $request->getControllerName();
		$action = $request->getActionName();
		
		if (($action == 'browse' && ($controller == 'items' || $controller == 'collections')) || ($controller == 'search' && $action == 'index')) {
			$language = json_encode(FACETS_LANGUAGE);
			queue_js_string("facetsLanguage = {language: $language};");
			queue_js_file('facets');
			queue_css_file('facets');
		}
	}

	public function hookPublicItemsBrowse($args)
	{
		$settings = $this->_settings;
		if (get_option('facets_public_hook') == 'default') {
			self::showFacets($args);
		}
	}
	
	public function hookPublicCollectionsBrowse($args)
	{
		$settings = $this->_settings;
		if (get_option('facets_public_hook') == 'default') {
			self::showFacets($args);
		}
	}
	
	public function hookPublicFacets($args)
	{
		if (get_option('facets_public_hook') == 'custom') {
			self::showFacets($args);
		}
	}

	public function showFacets($args)
	{
		$settings = $this->_settings;
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$controller = $request->getControllerName();
		$action = $request->getActionName();
		$params = array();
		
		if ($controller == 'items' && $action == 'browse') {
			if (isset($_GET['advanced'])) $params['advanced'] = $_GET['advanced'];
			if (isset($_GET['collection'])) $params['collection'] = $_GET['collection'];
			if (isset($_GET['type'])) $params['type'] = $_GET['type'];
			if (isset($_GET['tags'])) $params['tags'] = $_GET['tags'];
			if (isset($_GET['user'])) $params['user'] = $_GET['user'];
			if (isset($_GET['public'])) $params['public'] = $_GET['public'];
			if (isset($_GET['featured'])) $params['featured'] = $_GET['featured'];
			if (isset($_GET['search'])) $params['search'] = $_GET['search'];
			if (recordTypeActive('item', $settings['elements']) && count(get_records('item', $params, null)) > 0) {
				echo get_view()->partial('facets/browse.php', array(
					'params' => $params,
					'recordType' => 'item'
				));
			}
		} elseif ($controller == 'collections' && $action == 'browse') {
			if (isset($_GET['advanced'])) $params['advanced'] = $_GET['advanced'];
			if (isset($_GET['user'])) $params['user'] = $_GET['user'];
			if (isset($_GET['public'])) $params['public'] = $_GET['public'];
			if (isset($_GET['featured'])) $params['featured'] = $_GET['featured'];
			if (recordTypeActive('collection', $settings['elements']) && count(get_records('collection', $params, null)) > 0) {
				echo get_view()->partial('facets/browse.php', array(
					'params' => $params,
					'recordType' => 'collection'
				));
			}
		} elseif ($controller == 'search' && $action == 'index') {
			// this would be for the site-wide simple search;
			// main problem is that, differently from advanced search,
			// it uses its own "search_texts" table, so results from that
			// would have to be crossjoined with the "advanced" ones used
			// by the facets;
			// besides, advanced search works only for Items, so one might
			// want to check whether the site-wide search has been performed
			// just on Items.
			if ($recordTypes = $_GET['record_types']) {
				if (count($recordTypes) == 1 && in_array('Item', $recordTypes)) {
					// site-wide search was performed just on Items
				}
			}
		}
	}
}
