<?php
	$facetCollections = array();
	$facetItemTypes = array();
	$facetTags = array();
	$facetsElements = json_decode(get_option('facets_elements'), true);
	$hideSingleEntries = (bool)get_option('facets_hide_single_entries');
	$sortOrder = get_option('facets_sort_order');
	$hidePopularity = (bool)get_option('facets_hide_popularity');

	$table = get_db()->getTable('Element');
	$select = $table->getSelect()
		->where('element_sets.name = \'Dublin Core\'')
		->order('elements.element_set_id')
		->order('ISNULL(elements.order)')
		->order('elements.order');
	$elements = $table->fetchObjects($select);
?>

<div class="search-container">
	<div class="container">
		<h4><?php echo html_escape(__('Refine search by')) ?>...</h4>

		<?php 
			if ($description = get_option('facets_description')) {
				echo "<h5>" . $description . "</h5>\n";
			}
		?>
		<form class="" action="index.html" method="post">
			<?php
				foreach ($elements as $element) {
					if (isFacetActive($element->name, $facetsElements)) {
						$isDate = in_array($element->name, array('Date'));
						if ($html = get_dc_facet_select($itemsArray, $element->name, $isDate, $hideSingleEntries, $sortOrder, $hidePopularity)) {
							echo "<div class=\"container-fluid\">\n";
							echo "<label for=\"\">" . html_escape(__($element->name)) . "</label>\n";
							echo "</div>";
							echo $html;
						}
					}
				}
			?>

			<?php
				if (get_option('facets_item_types')) {
					if ($html = get_item_types_facet_select($itemsArray, $hideSingleEntries, $sortOrder, $hidePopularity)) {
						echo "<div class=\"container-fluid\">\n";
						echo "<label for=\"\">" . html_escape(__('Item Type')) . "</label>\n";
						echo "</div>";
						echo $html;
					}
				}
			?>
			
			<?php
				if (get_option('facets_collections')) {
					if ($html = get_collections_facet_select($itemsArray, $hideSingleEntries, $sortOrder, $hidePopularity)) {
						echo "<div class=\"container-fluid\">\n";
						echo "<label for=\"\">" . html_escape(__('Collection')) . "</label>\n";
						echo "</div>";
						echo $html;
					}
				}
			?>

			<?php
				if (get_option('facets_tags')) {
					if ($html = get_tags_facet_select($itemsArray, $hideSingleEntries, $sortOrder, $hidePopularity)) {
						echo "<div class=\"container-fluid\">\n";
						echo "<label for=\"\">" . html_escape(__('Tags')) . "</label>\n";
						echo "</div>";
						echo $html;
					}
				}
			?>
		</form>
	</div>
</div>
<script type="text/javascript">
	window.jQuery( document ).ready(function() {
		window.jQuery('select').change(function() {
			var option = window.jQuery(this).find('option:selected');
			window.location.href = option.data("url");
		});
	});
</script>
