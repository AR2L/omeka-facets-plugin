<?php
/**
 * @copyright Jean-Baptiste HEREN, 2018
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package FacetsPlugin
 */

if (!defined('FACETS_PLUGIN_DIR')) {
    define('FACETS_PLUGIN_DIR', dirname(__FILE__));
}

require_once FACETS_PLUGIN_DIR . '/helpers/FacetsFunctions.php';

class FacetsPlugin extends Omeka_Plugin_AbstractPlugin
{

    protected $_hooks = array(
        'install',
        'uninstall',
        'initialize',
        'define_routes',
        'public_head',
        'items_browse_sql',
        'public_items_browse'
    );

    /**
     * Installs the plugin.
     */
    public function hookInstall()
    {
    }

    /**
     * Uninstalls the plugin.
     */
    public function hookUninstall()
    {
    }

    /**
     * Initialize this plugin.
     */
    public function hookInitialize()
    {
      get_view()->addHelperPath(dirname(__FILE__) . '/views/helpers', 'Facets_View_Helper_');
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
        		new Zend_Controller_Router_Route(
        				"facets",
        				array(
        						'module'    => 'facets',
        						'controller' => 'facets',
        						'action'     => 'browse'
        				)
        		)
        );

    }

    /**
     * Deal with search terms (advanced search).
     *
     * Allows to get refinements when a base Dublin Core is used.
     *
     * @internal This hook is used twice, first to prepare select for the
     * current page, second to get the total count.
     *
     * @internal This should be used, because the filter is not enough for
     * requests with a refinable element that should contains something.
     *
     * @internal MySql 5.5 has a limit: group by is done before order by, and it
     * is complex to skirt.
     *
     * @see DublinCoreExtendedPlugin::filterItemsBrowseParams()
     *
     * @param Omeka_Db_Select $select
     * @param array $params
     */
    public function hookItemsBrowseSql($args)
    {
        $db = $this->_db;
        $select = $args['select'];
        $params = $args['params'];

    }

    public function hookPublicItemsBrowse($args)
    {
      $view = $args['view'];
      $items = $args['items'];

      // $params = $args['params'];
      $itemsArray = array();
      $allItems = get_db()->getTable('Item')->findAll();
      // $allItems = $this->_helper->db->findBy($params);
      foreach($allItems as $item){
        $itemsArray[] = $item->id;
      }

      echo get_view()->partial('facets/browse.php',array(
          'itemsArray' => $itemsArray,
          'items' => $items
        ));
    }
}
