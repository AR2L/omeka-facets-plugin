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
		<?php echo $view->formLabel('facets_checkbox_minimum_amount', __('Minimum checkbox count')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('For all fields using the checkbox style, minimum amount of values to be displayed (0 means all values displayed).', FACETS_MINIMUM_AMOUNT); ?>
		</p>
		<?php echo $view->formText('facets_checkbox_minimum_amount', get_option('facets_checkbox_minimum_amount')); ?>
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

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('facets_collapsible', __('Block collapsible')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('If checked, facets block will be collapsible (tip: as it saves space, it\'s particularly useful with horizontal theme layouts).'); ?>
		</p>
		<?php echo $view->formCheckbox('facets_collapsible', get_option('facets_collapsible'), null, array('1', '0')); ?>
    </div>
</div>

<h2><?php echo __('Elements'); ?></h2>

<div class="field">
	<div class="inputs seven columns alpha">
		<p class="explanation">
			<?php echo __('The elements that can be used for search refinement. Item and Collection columns to (de)activate, Style column to choose the facet style, Sort order column to choose sorting order, Popularity column to show counters near names.'); ?>
		</p>
		<table id="facets_elements-table">
			<thead>
				<tr>
					<th class="boxes"><?php echo __('Element name'); ?></th>
					<th class="boxes"><?php echo __('Item'); ?></th>
					<th class="boxes"><?php echo __('Collection'); ?></th>
					<th class="boxes"><?php echo __('Style'); ?></th>
					<th class="boxes"><?php echo __('Sort order'); ?></th>
					<th class="boxes"><?php echo __('Popularity'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
					$current_element_set = null;
					foreach ($elements as $element):
						if ($element->set_name != $current_element_set):
							$current_element_set = $element->set_name;
				?>
				<tr>
					<th colspan="6">
						<strong><?php echo __($current_element_set); ?></strong>
					</th>
				</tr>
				<?php 	endif; ?>
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
						<?php 
							if ($element->set_name == 'Dublin Core') {
								echo $view->formCheckbox(
									"elements[{$element->name}][collection]",
									'1', 
									array(
										'disableHidden' => true,
										'checked' => isset($settings['elements'][$element->name]['collection'])
									)
								); 
							} else {
								echo "&nbsp";
							}								
						?>
					</td>
					<td class="boxes">
						<?php 
							$type = (isset($settings['elements'][$element->name]['type']) ? $settings['elements'][$element->name]['type'] : '');
							echo $view->formSelect(
								"elements[{$element->name}][type]",
								$type,
								array(),
								array(
									'dropdown' => __('Dropdown'),
									'checkbox' => __('Checkbox')
								)
							); 
						?>
					</td>
					<td class="boxes">
						<?php 
							$sortOrder = (isset($settings['elements'][$element->name]['sort']) ? $settings['elements'][$element->name]['sort'] : '');
							echo $view->formSelect(
								"elements[{$element->name}][sort]",
								$sortOrder,
								array(),
								array(
									'alpha' => __('Alphabetical'),
									'count_alpha' => __('Popularity first, then alphabetical')
								)
							); 
						?>
					</td>
					<td class="boxes">
						<?php 
							echo $view->formCheckbox(
								"elements[{$element->name}][popularity]",
								'1', 
								array(
									'disableHidden' => true,
									'checked' => isset($settings['elements'][$element->name]['popularity'])
								)
							); 
						?>
					</td>
				</tr>
			<?php endforeach; ?>
				<tr>
					<th colspan="6">
						<strong><?php echo __('Item Types'); ?></strong>
					</th>
				</tr>
				<tr>
					<td><?php echo __('Item Types'); ?></td>
					<td class="boxes">
						<?php 
							echo $view->formCheckbox(
								"facets_item_types_active",
								get_option('facets_item_types_active'), 
								null, 
								array('1', '0')
							); 
						?>
					</td>
					<td class="boxes">
						&nbsp
					</td>
					<td class="boxes">
						<?php 
							echo $view->formSelect(
								"facets_item_types_style",
								get_option('facets_item_types_style'),
								array(),
								array(
									'dropdown' => __('Dropdown'),
									'checkbox' => __('Checkbox')
								)
							); 
						?>
					</td>
					<td class="boxes">
						<?php 
							echo $view->formSelect(
								'facets_item_types_sort',
								get_option('facets_item_types_sort'),
								array(),
								array(
									'alpha' => __('Alphabetical'),
									'count_alpha' => __('Popularity first, then alphabetical')
								)
							); 
						?>
					</td>
					<td class="boxes">
						<?php 
							echo $view->formCheckbox(
								'facets_item_types_popularity', 
								get_option('facets_item_types_popularity'), 
								null, 
								array('1', '0')
							); 
						?>
					</td>
				</tr>				
				<tr>
					<th colspan="6">
						<strong><?php echo __('Collections'); ?></strong>
					</th>
				</tr>
				<tr>
					<td><?php echo __('Collections'); ?></td>
					<td class="boxes">
						<?php 
							echo $view->formCheckbox(
								"facets_collections_active",
								get_option('facets_collections_active'), 
								null, 
								array('1', '0')
							); 
						?>
					</td>
					<td class="boxes">
						&nbsp
					</td>
					<td class="boxes">
						<?php 
							echo $view->formSelect(
								"facets_collections_style",
								get_option('facets_collections_style'),
								array(),
								array(
									'dropdown' => __('Dropdown'),
									'checkbox' => __('Checkbox')
								)
							); 
						?>
					</td>
					<td class="boxes">
						<?php 
							echo $view->formSelect(
								'facets_collections_sort',
								get_option('facets_collections_sort'),
								array(),
								array(
									'alpha' => __('Alphabetical'),
									'count_alpha' => __('Popularity first, then alphabetical')
								)
							); 
						?>
					</td>
					<td class="boxes">
						<?php 
							echo $view->formCheckbox(
								'facets_collections_popularity', 
								get_option('facets_collections_popularity'), 
								null, 
								array('1', '0')
							); 
						?>
					</td>
				</tr>				
				<tr>
					<th colspan="6">
						<strong><?php echo __('Tags'); ?></strong>
					</th>
				</tr>
				<tr>
					<td><?php echo __('Tags'); ?></td>
					<td class="boxes">
						<?php 
							echo $view->formCheckbox(
								"facets_tags_active",
								get_option('facets_tags_active'), 
								null, 
								array('1', '0')
							); 
						?>
					</td>
					<td class="boxes">
						&nbsp
					</td>
					<td class="boxes">
						<?php 
							echo $view->formSelect(
								"facets_tags_style",
								get_option('facets_tags_style'),
								array(),
								array(
									'dropdown' => __('Dropdown'),
									'checkbox' => __('Checkbox')
								)
							); 
						?>
					</td>
					<td class="boxes">
						<?php 
							echo $view->formSelect(
								'facets_tags_sort',
								get_option('facets_tags_sort'),
								array(),
								array(
									'alpha' => __('Alphabetical'),
									'count_alpha' => __('Popularity first, then alphabetical')
								)
							); 
						?>
					</td>
					<td class="boxes">
						<?php 
							echo $view->formCheckbox(
								'facets_tags_popularity', 
								get_option('facets_tags_popularity'), 
								null, 
								array('1', '0')
							); 
						?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
