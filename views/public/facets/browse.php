<?php
  // $itemsArray = array();
  $facetCollection = array();
  $facetItemType = array();
  $facetTag = array();

?>
<?php
if (isset($items)):
  foreach ($items as $item): ?>
  <!-- item Ids -->

  <!-- collection (Thématique) -->
  <?php if($collection = $item->getCollection()): ?>
    <?php $collection_name = metadata($collection, array('Dublin Core', 'Title')); ?>
    <?php $collection_id = $collection->id; ?>
    <?php $facetCollection[$collection_id] = $collection_name; ?>
  <?php endif; ?>

  <!-- itemType (Collection) -->
  <?php if($itemType = $item->getItemType()): ?>
    <?php $itemType_name = $itemType->name; ?>
    <?php $itemType_id = $itemType->id; ?>
    <?php $facetItemType[$itemType_id] = $itemType_name; ?>
  <?php endif; ?>

  <?php endforeach; ?>
<?php else: ?>

<?php endif; ?>
<?php
  // tags
  $tags = get_tags_for_items_array($itemsArray);
  foreach ($tags as $tag) {
    $facetTag[$tag->name] = $tag->id;
  }

  $facetTag = array_unique($facetTag);

  // collections
  $collections = get_collections_for_items_array($itemsArray);
  foreach ($collections as $collection) {
   $facetCollection[$collection->id] = $collection->getDisplayTitle();
  }

  $facetCollection = array_unique($facetCollection);

  // item types
  $itemTypes = get_item_types_for_items_array($itemsArray);
  foreach ($itemTypes as $itemType) {
    $facetItemType[$itemType->id] = $itemType->name;
  }

  $facetItemType = array_unique($facetItemType);
   ?>
    <div class="search-container">
      <div class="container">
        <h4>AFFINER LES RESULTATS</h4>
        <form class="" action="index.html" method="post">
          <?php if($html = get_dc_facet_select($itemsArray, 'Creator')): ?>
          <div class="container-fluid">
            <label for=""><?php echo html_escape(__('Creator')); ?></label>
          </div>
          <?php echo $html; ?>
          <?php endif; ?>
          <?php if($html = get_dc_facet_select($itemsArray, 'Contributor')): ?>
            <div class="container-fluid">
              <label for=""><?php echo html_escape(__('Contributor')); ?></label>
            </div>
            <?php echo $html; ?>
          <?php endif; ?>
          <?php if($html = get_dc_facet_select($itemsArray, 'Publisher')): ?>
            <div class="container-fluid">
              <label for=""><?php echo html_escape(__('Publisher')); ?></label>
            </div>
            <?php echo $html; ?>
          <?php endif; ?>
          <?php if($html = get_dc_facet_select($itemsArray, 'Date')): ?>
            <div class="container-fluid">
              <label for=""><?php echo html_escape(__('Date')); ?></label>
            </div>
            <?php echo $html; ?>
          <?php endif; ?>
          <div class="container-fluid">
            <label for=""><?php echo html_escape(__('Tag')); ?></label>
          </div>
          <div class="select-arrow">
            <select class="" name="">
              <option value="" data-url="<?php echo getFieldUrl('tag_id'); ?>">Sélectionner...</option>
              <?php foreach($facetTag as $tagName => $tagId):?>
                <option value="<?php echo $tagId; ?>" data-url="<?php echo getFieldUrl('tag_id',$tagId); ?>" <?php echo (isset($_GET['tag_id']) && $tagId == $_GET['tag_id'] ? "selected": ""); ?>><?php echo $tagName ?></option>
              <?php endforeach;?>
            </select>
          </div>
          <div class="container-fluid">
            <label for=""><?php echo html_escape(__('Collection')); ?></label>
          </div>
          <div class="select-arrow">
            <select class="" name="">
              <option value="" data-url="<?php echo getFieldUrl('collection'); ?>">Sélectionner...</option>
              <?php foreach($facetCollection as $collectionId => $collectionName):?>
                <option value="<?php echo $collectionId ?>" data-url="<?php echo getFieldUrl('collection',$collectionId); ?>" <?php echo (isset($_GET['collection']) && $collectionId == $_GET['collection'] ? "selected": ""); ?>><?php echo $collectionName ?></option>
              <?php endforeach;?>
            </select>
          </div>
          <div class="container-fluid">
            <label for=""><?php echo html_escape(__('Type')); ?></label>
          </div>
          <div class="select-arrow">
            <select class="" name="">
              <option value="" data-url="<?php echo getFieldUrl('type'); ?>">Sélectionner...</option>
              <?php foreach($facetItemType as $itemTypeId => $itemTypeName):?>
                <option value="<?php echo $itemTypeId ?>" data-url="<?php echo getFieldUrl('type',$itemTypeId); ?>" <?php echo (isset($_GET['type']) && $itemTypeId == $_GET['type'] ? "selected": ""); ?>><?php echo $itemTypeName ?></option>
              <?php endforeach;?>
            </select>
            </select>
          </div>
          <?php if($html = get_dc_facet_select($itemsArray, 'Provenance')): ?>
            <div class="container-fluid">
              <label for=""><?php echo html_escape(__('Provenance')); ?></label>
            </div>
            <?php echo $html; ?>
          <?php endif; ?>
          <!-- <div class="container-fluid">
            <button type="submit" name="button">Filtrer</button>
          </div> -->
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
