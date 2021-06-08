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
define('FACETS_MINIMUM_AMOUNT', 10);

require_once FACETS_PLUGIN_DIR . '/helpers/FacetsFunctions.php';

class FacetsPlugin extends Omeka_Plugin_AbstractPlugin
{
	protected $_hooks = array(
		'install',
		'uninstall',
		'initialize',
        'config_form',
        'config',
		'define_routes',
		'public_head',
		'public_items_browse',
		'public_items_facets'
	);

	/**
	 * Installs the plugin.
	 */
	public function hookInstall()
	{
		set_option('facets_public_hook', 'public_items_browse');
        set_option('facets_description', '');
        set_option('facets_hide_single_entries', 0);
        set_option('facets_sort_order', 'count_alpha');

		$defaults = array(
            'item_elements' => array()
        );
        set_option('facets_elements', json_encode($defaults));
        set_option('facets_item_types', 1);
        set_option('facets_collections', 1);
        set_option('facets_tags', 0);
	}

	/**
	 * Uninstalls the plugin.
	 */
	public function hookUninstall()
	{
		delete_option('facets_public_hook');
        delete_option('facets_description');
        delete_option('facets_hide_single_entries');
        delete_option('facets_sort_order');
        delete_option('facets_elements');
        delete_option('facets_item_types');
        delete_option('facets_collections');
        delete_option('facets_tags');
	}

	/**
	 * Initialize this plugin.
	 */
	public function hookInitialize()
	{
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
        set_option('facets_sort_order', $post['facets_sort_order']);

		$settings = array(
            'item_elements' => isset($post['item_elements']) ? $post['item_elements'] : array()
        );
        set_option('facets_elements', json_encode($settings));

        set_option('facets_item_types', $post['facets_item_types']);
        set_option('facets_collections', $post['facets_collections']);
        set_option('facets_tags', $post['facets_tags']);
   }

	public function hookPublicHead($args)
	{
		queue_css_file('facets');
	}

	public function hookDefineRoutes($args)
	{
		if (is_admin_theme()) {
			return;
		}

		//Itemtypes browse
		$router = $args['router'];
		$router->addRoute(
			'facets',
			new Zend_Controller_Router_Route (
				"facets",
				array(
					'module'	 => 'facets',
					'controller' => 'facets',
					'action'	 => 'browse'
				)
			)
		);
	}

	public function hookPublicItemsBrowse($args)
	{
		if (get_option('facets_public_hook') == 'public_items_browse') {
			self::showFacets($args);
		}
	}
	
	public function hookPublicItemsFacets($args)
	{
		if (get_option('facets_public_hook') == 'public_items_facets') {
			self::showFacets($args);
		}
	}

	public function showFacets($args)
	{
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$controller = $request->getControllerName();
		$action = $request->getActionName();
		
		// checks whether it's one of the facet cases
		if (!($controller == 'search' && $action == 'index') && !($controller == 'items' && $action == 'browse')) return;
		
		$itemsArray = array();
		$params = array('advanced' => $_GET['advanced'], 'collection' => $_GET['collection'], 'type' => $_GET['type'], 'tags' => $_GET['tags']);
		$items = get_records('item', $params, null);

		if (count($items) > 0) {
			if ($controller == 'items') {
				foreach ($items as $item) {
					$itemsArray[] = $item->id;
				}
				echo get_view()->partial('facets/browse.php', array(
					'itemsArray' => $itemsArray
				));
			} elseif ($controller == 'search') {
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
						// site-wide search was performend just on Items
					}
				}
			}
		}
	}
}