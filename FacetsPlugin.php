<?php
/**
 * @copyright Jean-Baptiste HEREN, 2018
 * @copyright Daniele BINAGHI, 2021
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package FacetsPlugin
 */

if (!defined('FACETS_PLUGIN_DIR')) {
	define('FACETS_PLUGIN_DIR', dirname(__FILE__));
}
define('FACETS_MINIMUM_AMOUNT', 5);

require_once FACETS_PLUGIN_DIR . '/helpers/FacetsFunctions.php';

class FacetsPlugin extends Omeka_Plugin_AbstractPlugin
{
	protected $_hooks = array(
		'install',
		'uninstall',
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
		set_option('facets_description', '');
		set_option('facets_hide_single_entries', 0);
		set_option('facets_collapsable', 0);
		set_option('facets_direction', 'vertical');

		$defaults = array(
			'elements' => array('item', 'collection', 'sort', 'popularity')
		);
		set_option('facets_elements', json_encode($defaults));
		set_option('facets_item_types_active', 0);
		set_option('facets_item_types_sort', '');
		set_option('facets_item_types_popularity', 0);
		set_option('facets_collections_active', 1);
		set_option('facets_collections_sort', 'count_alpha');
		set_option('facets_collections_popularity', 1);
		set_option('facets_tags_active', 0);
		set_option('facets_tags_sort', '');
		set_option('facets_tags_popularity', 0);
	}

	/**
	 * Uninstalls the plugin.
	 */
	public function hookUninstall()
	{
		delete_option('facets_public_hook');
		delete_option('facets_description');
		delete_option('facets_hide_single_entries');
		delete_option('facets_collapsable');
		delete_option('facets_direction');
		delete_option('facets_elements');
		delete_option('facets_item_types_active');
		delete_option('facets_item_types_sort');
		delete_option('facets_item_types_popularity');
		delete_option('facets_collections_active');
		delete_option('facets_collections_sort');
		delete_option('facets_collections_popularity');
		delete_option('facets_tags_active');
		delete_option('facets_tags_sort');
		delete_option('facets_tags_popularity');
	}

	/**
	 * Initialize this plugin.
	 */
	public function hookInitialize()
	{
        add_translation_source(dirname(__FILE__) . '/languages');

		get_view()->addHelperPath(dirname(__FILE__) . '/views/helpers', 'Facets_View_Helper_');

		$settings = json_decode(get_option('facets_elements'), true);
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
			->where('element_sets.name = \'Dublin Core\'')
			->order('elements.element_set_id')
			->order('ISNULL(elements.order)')
			->order('elements.order');
		$elements = $table->fetchObjects($select);

		include('config_form.php');
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
		set_option('facets_collapsable', $post['facets_collapsable']);
		set_option('facets_direction', $post['facets_direction']);

		$settings = array(
			'elements' => isset($post['elements']) ? $post['elements'] : array()
		);
		set_option('facets_elements', json_encode($settings));

		set_option('facets_item_types_active', $post['facets_item_types_active']);
		set_option('facets_item_types_sort', $post['facets_item_types_sort']);
		set_option('facets_item_types_popularity', $post['facets_item_types_popularity']);
		set_option('facets_collections_active', $post['facets_collections_active']);
		set_option('facets_collections_sort', $post['facets_collections_sort']);
		set_option('facets_collections_popularity', $post['facets_collections_popularity']);
		set_option('facets_tags_active', $post['facets_tags_active']);
		set_option('facets_tags_sort', $post['facets_tags_sort']);
		set_option('facets_tags_popularity', $post['facets_tags_popularity']);
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
 		
		if ($controller == 'items' && $action == 'browse') {
			$params = array(
				'advanced' => $_GET['advanced'], 
				'collection' => $_GET['collection'], 
				'type' => $_GET['type'], 
				'tags' => $_GET['tags']
			);

			if (recordTypeActive('item', $settings['elements']) && count(get_records('item', $params, null)) > 0) {
				echo get_view()->partial('facets/browse.php', array(
					'params' => $params,
					'recordType' => 'item'
				));
			}
		} elseif ($controller == 'collections' && $action == 'browse') {
			$params = array(
				'advanced' => $_GET['advanced']
			);
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
