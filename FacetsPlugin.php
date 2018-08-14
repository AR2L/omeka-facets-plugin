<?php

class FacetsPlugin extends Omeka_Plugin_AbstractPlugin
{

    protected $_hooks = array(
        'install',
        'uninstall',
        'initialize',
        'define_routes'
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
    }

    public function hookDefineRoutes($args)
    {
        if (is_admin_theme()) {
            return;
        }

        //Itemtypes browse
        $router = $args['router'];
        $router->addRoute(
        		'facets-browse',
        		new Zend_Controller_Router_Route(
        				"facets/browse",
        				array(
        						'module'    => 'facets',
        						'controller' => 'facets',
        						'action'     => 'browse'
        				)
        		)
        );

    }
}
