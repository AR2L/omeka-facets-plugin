<?php 
	$view = get_view();
?>
<style type = "text/css">
	.boxes, .boxes-left {
		vertical-align: middle;
	}
	.boxes {
		text-align: center;
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
					<td class="boxes-left"><?php echo __($element->name); ?></td>
					<td class="boxes">
						<?php 
							echo $view->formCheckbox(
								"elements[{$element->name}][item]",
								'1', 
								array(
									'disableHidden' => true,
									'checked' => (isset($settings['elements'][$element->name]['item']) && $settings['elements'][$element->name]['item'] == 1)
								)
							); 
						?>
					</td>
					<td class="boxes">
						<?php 
							if ($element->set_name == 'Dublin Core') {
								echo $view->formCheckbox(
									"elements[{$element->name}][collection]",
									'1', 
									array(
										'disableHidden' => true,
										'checked' => (isset($settings['elements'][$element->name]['collection']) && $settings['elements'][$element->name]['collection'] == 1)
									)
								); 
							} else {
								echo "&nbsp";
							}								
						?>
					</td>
					<td class="boxes">
						<?php 
							$style = (isset($settings['elements'][$element->name]['style']) ? $settings['elements'][$element->name]['style'] : '');
							echo $view->formSelect(
								"elements[{$element->name}][style]",
								$style,
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
									'checked' => (isset($settings['elements'][$element->name]['popularity']) && $settings['elements'][$element->name]['popularity'] == 1)
								)
							); 
						?>
					</td>
				</tr>
			<?php endforeach; ?>
				<tr>
					<th colspan="6">
						<strong><?php echo __('Extra parameters'); ?></strong>
					</th>
				</tr>
			<?php 
				$extraParameters = array(
					'item_types' => array('label' => 'Item Type', 'collection' => 0),
					'collections' => array('label' => 'Collection', 'collection' => 0),
					'tags' => array('label' => 'Tag', 'collection' => 0),
					'users' => array('label' => 'Owner', 'collection' => 1),
					'public' => array('label' => 'Public', 'collection' => 1),
					'featured' => array('label' => 'Featured', 'collection' => 1)
				);
				foreach ($extraParameters as $key => $value):
			?>
				<tr>
					<td class="boxes-left"><?php echo __($value['label']); ?></td>
					<td class="boxes">
						<?php 
							echo $view->formCheckbox(
								"{$key}[item]",
								'1', 
								array(
									'disableHidden' => true,
									'checked' => (isset($settings[$key]['item']) && $settings[$key]['item'] == 1)
								)
							); 
						?>
					</td>
					<td class="boxes">
						<?php 
							if ($value['collection']) {
								echo $view->formCheckbox(
									"{$key}[collection]",
									'1', 
									array(
										'disableHidden' => true,
										'checked' => isset($settings[$key]['collection'])
									)
								); 
							} else {
								echo "&nbsp;";
							}
						?>
					</td>
					<td class="boxes">
						<?php 
							$style = (isset($settings[$key]['style']) ? $settings[$key]['style'] : '');
							echo $view->formSelect(
								"{$key}[style]",
								$style,
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
							$sort = (isset($settings[$key]['sort']) ? $settings[$key]['sort'] : '');
							echo $view->formSelect(
								"{$key}[sort]",
								$sort,
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
								"{$key}[popularity]", 
								'1', 
								array(
									'disableHidden' => true,
									'checked' => (isset($settings[$key]['popularity']) && $settings[$key]['popularity'] == 1)
								)
							); 
						?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
