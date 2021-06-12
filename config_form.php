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
		<?php echo $view->formLabel('facets_public_hook', __('Public hooks')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('The hooks you want to use: Omeka\'s default <code>public_items_browse</code> and <code>public_collections_browse</code>, already coded into every page (so no changes are needed), normally showing up at the end of the page; or the plugin\'s own <code>public_facets</code>, that has to be coded into relevant theme pages but can be used wherever deemed right.'); ?>
		</p>
		<?php echo $view->formRadio('facets_public_hook',
			get_option('facets_public_hook'),
			null,
			array(
				'default' => 'public_items_browse & public_collections_browse',
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

<h2><?php echo __('Dublin Core Elements'); ?></h2>

<div class="field">
	<div class="seven columns alpha">
		<p class="explanation">
			<?php echo __('The Dublin Core elements that can be used for search refinement.'); ?>
		</p>
	</div>
	<div class="seven columns alpha">
		<table id="facets_elements-table">
			<thead>
				<tr>
					<th class="boxes" rowspan="2"><?php echo __('Element name'); ?></th>
					<th class="boxes" colspan="2"><?php echo __('Items'); ?></th>
					<th class="boxes" colspan="2"><?php echo __('Collections'); ?></th>
				</tr>
				<tr>
					<th class="boxes"><?php echo __('Active'); ?></th>
					<th class="boxes"><?php echo __('Show popularity'); ?></th>
					<th class="boxes"><?php echo __('Active'); ?></th>
					<th class="boxes"><?php echo __('Show popularity'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
				foreach ($elements as $element):
			?>
				<tr>
					<td><?php echo __($element->name); ?></td>
					<td class="boxes">
						<?php echo $view->formSelect(
							"item_elements[{$element->set_name}][{$element->name}][active]",
							$settings['item_elements'][$element->set_name][$element->name]['active'],
							array(),
							array(
								'' => __('Not active'),
								'alpha' => __('Alphabetical sort'),
								'count_alpha' => __('Popularity + alphabetical sort')
							)); 
						?>
					</td>
					<td class="boxes">
						<?php echo $view->formCheckbox(
							"item_elements[{$element->set_name}][{$element->name}][showPopularity]",
							'1', 
							array(
								'disableHidden' => true,
								'checked' => isset($settings['item_elements'][$element->set_name][$element->name]['showPopularity'])
							)
						); ?>
					</td>
					<td class="boxes">
						<?php echo $view->formSelect(
							"collection_elements[{$element->set_name}][{$element->name}][active]",
							$settings['collection_elements'][$element->set_name][$element->name]['active'],
							array(),
							array(
								'' => __('Not active'),
								'alpha' => __('Alphabetical sort'),
								'count_alpha' => __('Popularity + alphabetical sort')
							)); 
						?>
					</td>
					<td class="boxes">
						<?php echo $view->formCheckbox(
							"collection_elements[{$element->set_name}][{$element->name}][showPopularity]",
							'1', 
							array(
								'disableHidden' => true,
								'checked' => isset($settings['collection_elements'][$element->set_name][$element->name]['showPopularity'])
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

<h2><?php echo __('Item Types'); ?></h2>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('facets_item_types_active', __('Active')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, search refinement by Item Type will be available (Items only).'); ?>
		</p>
		<?php echo $view->formSelect(
			'facets_item_types_active',
			get_option('facets_item_types_active'),
			array(),
			array(
				'' => __('Not active'),
				'alpha' => __('Alphabetical sort'),
				'count_alpha' => __('Popularity + alphabetical sort')
			)); 
		?>
    </div>
</div>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('facets_item_types_show_popularity', __('Show popularity')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, values\'s popularity will be displayed.'); ?>
		</p>
		<?php echo $view->formCheckbox('facets_item_types_show_popularity', get_option('facets_item_types_show_popularity'), null, array('1', '0')); ?>
    </div>
</div>

<h2><?php echo __('Collections'); ?></h2>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('facets_collections_active', __('Active')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, search refinement by Collection will be available (Items only).'); ?>
		</p>
		<?php echo $view->formSelect(
			'facets_collections_active',
			get_option('facets_collections_active'),
			array(),
			array(
				'' => __('Not active'),
				'alpha' => __('Alphabetical sort'),
				'count_alpha' => __('Popularity + alphabetical sort')
			)); 
		?>
    </div>
</div>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('facets_collections_show_popularity', __('Show popularity')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, values\'s popularity will be displayed.'); ?>
		</p>
		<?php echo $view->formCheckbox('facets_collections_show_popularity', get_option('facets_collections_show_popularity'), null, array('1', '0')); ?>
    </div>
</div>

<h2><?php echo __('Tags'); ?></h2>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('facets_tags_active', __('Active')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, search refinement by Tag will be available (Items only).'); ?>
		</p>
		<?php echo $view->formSelect(
			'facets_tags_active',
			get_option('facets_tags_active'),
			array(),
			array(
				'' => __('Not active'),
				'alpha' => __('Alphabetical sort'),
				'count_alpha' => __('Popularity + alphabetical sort')
			)); 
		?>
    </div>
</div>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('facets_tags_show_popularity', __('Show popularity')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, values\'s popularity will be displayed.'); ?>
		</p>
		<?php echo $view->formCheckbox('facets_tags_show_popularity', get_option('facets_tags_show_popularity'), null, array('1', '0')); ?>
    </div>
</div>
