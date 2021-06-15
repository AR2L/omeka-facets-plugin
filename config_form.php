<?php 
	$view = get_view();
?>
<style type = "text/css">
	.boxes {
		text-align: center;
		vertical-align: middle;
	}
	.field select {
		margin-bottom: 0;
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

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('facets_collapsible', __('Block collapsible')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, facets block will be collapsible (tip: as it saves space, it\'s particularly useful with horizontal layout).'); ?>
		</p>
		<?php echo $view->formCheckbox('facets_collapsible', get_option('facets_collapsible'), null, array('1', '0')); ?>
    </div>
</div>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('facets_direction', __('Block layout')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('The layout direction of the facets block.'); ?>
		</p>
		<?php echo $view->formSelect(
			'facets_direction',
			get_option('facets_direction'),
			array(),
			array(
				'horizontal' => __('Horizontal (useful for themes with top main navigation menu)'),
				'vertical' => __('Vertical (useful for themes with side main navigation menu)')
			)); 
		?>
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
					<th class="boxes"><?php echo __('Sort order'); ?></th>
					<th class="boxes"><?php echo __('Popularity'); ?></th>
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
							"elements[{$element->name}][item]",
							'1', 
							array(
								'disableHidden' => true,
								'checked' => isset($settings['elements'][$element->name]['item'])
							)
						); ?>
					</td>
					<td class="boxes">
						<?php echo $view->formCheckbox(
							"elements[{$element->name}][collection]",
							'1', 
							array(
								'disableHidden' => true,
								'checked' => isset($settings['elements'][$element->name]['collection'])
							)
						); ?>
					</td>
					<td class="boxes">
						<?php echo $view->formSelect(
							"elements[{$element->name}][sort]",
							$settings['elements'][$element->name]['sort'],
							array(),
							array(
								'alpha' => __('Alphabetical'),
								'count_alpha' => __('Popularity first, then alphabetical')
							)); 
						?>
					</td>
					<td class="boxes">
						<?php echo $view->formCheckbox(
							"elements[{$element->name}][popularity]",
							'1', 
							array(
								'disableHidden' => true,
								'checked' => isset($settings['elements'][$element->name]['popularity'])
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
		<?php echo $view->formCheckbox('facets_item_types_active', get_option('facets_item_types_active'), null, array('1', '0')); ?>
    </div>
</div>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('facets_item_types_sort', __('Sort order')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('The sort order used for values.'); ?>
		</p>
		<?php echo $view->formSelect(
			'facets_item_types_sort',
			get_option('facets_item_types_sort'),
			array(),
			array(
				'alpha' => __('Alphabetical'),
				'count_alpha' => __('Popularity first, then alphabetical')
			)); 
		?>
    </div>
</div>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('facets_item_types_popularity', __('Show popularity')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, values\'s popularity will be displayed.'); ?>
		</p>
		<?php echo $view->formCheckbox('facets_item_types_popularity', get_option('facets_item_types_popularity'), null, array('1', '0')); ?>
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
		<?php echo $view->formCheckbox('facets_collections_active', get_option('facets_collections_active'), null, array('1', '0')); ?>
    </div>
</div>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('facets_collections_sort', __('Sort order')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('The sort order used for values.'); ?>
		</p>
		<?php echo $view->formSelect(
			'facets_collections_sort',
			get_option('facets_collections_sort'),
			array(),
			array(
				'alpha' => __('Alphabetical'),
				'count_alpha' => __('Popularity first, then alphabetical')
			)); 
		?>
    </div>
</div>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('facets_collections_popularity', __('Show popularity')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, values\'s popularity will be displayed.'); ?>
		</p>
		<?php echo $view->formCheckbox('facets_collections_popularity', get_option('facets_collections_popularity'), null, array('1', '0')); ?>
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
		<?php echo $view->formCheckbox('facets_tags_active', get_option('facets_tags_active'), null, array('1', '0')); ?>
    </div>
</div>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('facets_tags_sort', __('Sort order')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('The sort order used for values.'); ?>
		</p>
		<?php echo $view->formSelect(
			'facets_tags_sort',
			get_option('facets_tags_sort'),
			array(),
			array(
				'alpha' => __('Alphabetical'),
				'count_alpha' => __('Popularity first, then alphabetical')
			)); 
		?>
    </div>
</div>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('facets_tags_popularity', __('Show popularity')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, values\'s popularity will be displayed.'); ?>
		</p>
		<?php echo $view->formCheckbox('facets_tags_popularity', get_option('facets_tags_popularity'), null, array('1', '0')); ?>
    </div>
</div>
