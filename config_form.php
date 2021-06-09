<?php 
	$view = get_view();
?>
<style type = "text/css">
	.boxes {
		text-align: center;
		vertical-align: middle;
	}
</style>

<h2><?php echo __('Use'); ?></h2>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('facets_public_hook', __('Public hook')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('The hooks you want to use: Omeka\'s default <code>public_items_browse</code> and <code>public_collections_browse</code>, already coded into every page (so no changes are needed), normally showing up at the end of the page; or the plugin\'s own <code>public_facets</code>, that has to be coded into relevant theme pages but can be used wherever deemed right.'); ?>
		</p>
		<?php echo $view->formRadio('facets_public_hook',
			get_option('facets_public_hook'),
			null,
			array(
				'default' => 'public_items_browse + public_collections_browse',
				'custom' => 'public_facets',
			)); 
		?>
    </div>
</div>

<h2><?php echo __('Aspect'); ?></h2>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('facets_description', __('Description')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('Text to be used as additional description (tip: keep it short).'); ?>
		</p>
		<?php echo $view->formText('facets_description', get_option('facets_description')); ?>
    </div>
</div>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('facets_hide_single_entries', __('Hide single entries')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, values with just one entry will not be listed (unless there\'s less than %s entries in the list).', FACETS_MINIMUM_AMOUNT); ?>
		</p>
		<?php echo $view->formCheckbox('facets_hide_single_entries', get_option('facets_hide_single_entries'), null, array('1', '0')); ?>
    </div>
</div>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('facets_sort_order', __('Sort order')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('The sorting order for values in select fields.'); ?>
		</p>
		<?php echo $view->formRadio('facets_sort_order',
			get_option('facets_sort_order'),
			null,
			array(
				'alpha' => __('Alphabetical'),
				'count_alpha' => __('Popularity first, then alphabetical'),
			)); 
		?>
    </div>
</div>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('facets_hide_popularity', __('Hide popularity')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, values\'s popularity will not be displayed (sorting order will still take it into consideration, if required).'); ?>
		</p>
		<?php echo $view->formCheckbox('facets_hide_popularity', get_option('facets_hide_popularity'), null, array('1', '0')); ?>
    </div>
</div>

<h2><?php echo __('Elements'); ?></h2>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('facets_elements-table', __('Dublin Core')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('The Dublin Core elements that can be used for search refinement.'); ?>
		</p>
		<table id="facets_elements-table">
			<thead>
				<tr>
					<th class="boxes"><?php echo __('Element name'); ?></th>
					<th class="boxes"><?php echo __('Item'); ?></th>
					<th class="boxes"><?php echo __('Collection'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
				foreach ($elements as $element):
			?>
				<tr>
					<td><?php echo __($element->name); ?></td>
					<td class="boxes">
						<?php echo $view->formCheckbox(
							"item_elements[{$element->set_name}][{$element->name}]",
							'1', 
							array(
								'disableHidden' => true,
								'checked' => isset($settings['item_elements'][$element->set_name][$element->name])
							)
						); ?>
					</td>
					<td class="boxes">
						<?php echo $view->formCheckbox(
							"collection_elements[{$element->set_name}][{$element->name}]",
							'1', 
							array(
								'disableHidden' => true,
								'checked' => isset($settings['collection_elements'][$element->set_name][$element->name])
							)
						); ?>
					</td>
				</tr>
			<?php 
				endforeach;
			?>
			</tbody>
		</table>
	</div>
</div>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('facets_item_types', __('Item Types')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, search refinement by Item Type will be available (Items only).'); ?>
		</p>
		<?php echo $view->formCheckbox('facets_item_types', get_option('facets_item_types'), null, array('1', '0')); ?>
    </div>
</div>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('facets_collections', __('Collections')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, search refinement by Collection will be available (Items only).'); ?>
		</p>
		<?php echo $view->formCheckbox('facets_collections', get_option('facets_collections'), null, array('1', '0')); ?>
    </div>
</div>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('facets_tags', __('Tags')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, search refinement by Tag will be available (Items only).'); ?>
		</p>
		<?php echo $view->formCheckbox('facets_tags', get_option('facets_tags'), null, array('1', '0')); ?>
    </div>
</div>
