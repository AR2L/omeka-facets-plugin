# Facets

## Description

Plugin for Omeka Classic. Once installed and active, allows to insert a Faceted search refining block containing available metadata values extracted from browsing context. Works for both Items and Collections.

Settings allow to choose the hook to be used, which criteria to use for search refinement, whether to ignore single entries, sorting order for values, whether to show values popularity, whether to use an horizontal or vertical layout, whether to make the block collapsible, etc.

## Credits

Original development is part of the [Armarium](https://www.armarium-hautsdefrance.fr/) Project, managed by the french association [AR2L Hauts de France](http://www.ar2l-hdf.fr/).

Fixed and improved version by [DBinaghi](https://github.com/DBinaghi).

## Installation
Uncompress files and rename plugin folder "Facets".

Then install it like any other Omeka plugin.

## (optional) Theme customization

Facets will appear by default in the **Items browse** and **Collections browse** pages, where the `public_items_browse` and `public_collections_browse` hooks are set. It is possible to use instead a custom `public_facets` hook, that can be positioned wherever needed (`YOUR_THEME/items/browse.php` or `YOUR_THEME/common/header.php`, f.i.) according to the theme used; in such a case, the use of [get_specific_plugin_hook_output](http://omeka.readthedocs.io/en/latest/Reference/libraries/globals/get_specific_plugin_hook_output.html) function is suggested. 

For **Thanks, Roy** theme, for example, best way is to change `common/header.php` code using the custom hook, while unchecking **Block collapsible** option and setting **Vertical** as **Block layout**:
```php
<div id="search-container" role="search">
    <?php if (get_theme_option('use_advanced_search') === null || get_theme_option('use_advanced_search')): ?>
    <?php echo search_form(array('show_advanced' => true)); ?>
    <?php else: ?>
    <?php echo search_form(); ?>
    <?php endif; ?>
</div>
<?php echo get_specific_plugin_hook_output('Facets', 'public_facets', array('view' => $this)); ?>
```
For **Berlin** theme, instead, check **Block collapsible** option, set **Horizontal** as **Block layout** and then add the custom hook in `items/browse.php` and `collections/browse.php`:
```php
<?php echo item_search_filters(); ?>

<?php echo get_specific_plugin_hook_output('Facets', 'public_facets', array('view' => $this)); ?>

<?php echo pagination_links(); ?>
```

Background and foreground colors, as well as other parameters, can be customized in `facets.css` file (`views/public/css` folder).

## Plugin configuration

The elements used for search refinement can be selected in the configuration page; best practice suggestion is to activate only elements that are displayed in the browse page, and that are offering some kind of data aggregation (a unique id would not offer any really useful refinement).

Similarly, one can choose to also include **Item Types**, **Collections** and **Tags** to the facets block.

**Single values** can be exluded as not really significant, although they will be listed anyway if less than 5 values are available.

Facets block can be set up for **vertical** or **horizontal** layout, according to the theme layout; and it can be made **collapsible**, to use less vertical space (recommended for horizontal layout).

Style for facets can be either **dropdown selectbox**, or **checkbox**; this last one allows to choose more values, while with the first one one value excludes other. For checkbox style, a **minimum amount of values** can be set too.

Sorting order can be **alphabetical**, or **first by popularity and then alphabetical**. Popularity count can be shown, if needed.

**Date** element filter's behaviour is a bit different: dates are ordered decrescently, and only year is displayed; consequently, the matching will be with the beginning of the date (assuming the format is '_yyyy-mm-dd_'). By default, the following are considered date fields: '**Date**', '**Date Available**', '**Date Created**', '**Date Accepted**', '**Date Copyrighted**', '**Date Submitted**', '**Date Issued**', '**Date Modified**', '**Date Valid**' (the list can be changed in browse.php file).

## Licence
MIT Licence, please credit AR2L [AR2L Hauts de France](http://www.ar2l-hdf.fr/) and [Daniele Binaghi](https://github.com/DBinaghi).
