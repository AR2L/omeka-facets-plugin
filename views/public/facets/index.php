<?php
  // $itemsArray = array();
  $facetCollection = array();
  $facetItemType = array();
  $facetTag = array();
?>
<?php foreach ($items as $item): ?>
  <!-- item Ids -->
  <?php //$itemsArray[] = $item->id; ?>

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
<?php
  // tags
  $tags = $this->get_tags_for_items_array($itemsArray);
  foreach ($tags as $tag) {
    $facetTag[$tag->name] = $tag->id;
  }

  $facetTag = array_unique($facetTag);

  // collections
  $collections = $this->get_collections_for_items_array($itemsArray);
  foreach ($collections as $collection) {
   $facetCollection[$collection->id] = $collection->getDisplayTitle();
  }

  $facetCollection = array_unique($facetCollection);

  // item types
  $itemTypes = $this->get_item_types_for_items_array($itemsArray);
  foreach ($itemTypes as $itemType) {
    $facetItemType[$itemType->id] = $itemType->name;
  }

  $facetItemType = array_unique($facetItemType);
   ?>
    <div class="search-container">
      <div class="container">
        <h4>AFFINER LES RESULTATS</h4>
        <form class="" action="index.html" method="post">
          <?php if($html = $this->get_dc_facet_select($itemsArray, 'Creator')): ?>
          <div class="container-fluid">
            <label for="">PAR AUTEUR</label>
          </div>
          <?php echo $html; ?>
          <?php endif; ?>
          <?php if($html = $this->get_dc_facet_select($itemsArray, 'Contributor')): ?>
            <div class="container-fluid">
              <label for="">PAR CONTRIBUTEUR</label>
            </div>
            <?php echo $html; ?>
          <?php endif; ?>
          <?php if($html = $this->get_dc_facet_select($itemsArray, 'Publisher')): ?>
            <div class="container-fluid">
              <label for="">PAR EDITEUR</label>
            </div>
            <?php echo $html; ?>
          <?php endif; ?>
          <?php if($html = $this->get_dc_facet_select($itemsArray, 'Date')): ?>
            <div class="container-fluid">
              <label for="">PAR SIECLE</label>
            </div>
            <?php echo $html; ?>
          <?php endif; ?>
          <?php if($pageOrigin !== "Sujets"):?>
          <div class="container-fluid">
            <label for="">PAR SUJET</label>
          </div>
          <div class="select-arrow">
            <select class="" name="">
              <option value="" data-url="<?php echo $this->getFieldUrl('tag_id'); ?>">Sélectionner...</option>
              <?php foreach($facetTag as $tagName => $tagId):?>
                <option value="<?php echo $tagId; ?>" data-url="<?php echo $this->getFieldUrl('tag_id',$tagId); ?>" <?php echo (isset($_GET['tag_id']) && $tagId == $_GET['tag_id'] ? "selected": ""); ?>><?php echo $tagName ?></option>
              <?php endforeach;?>
            </select>
          </div>
        <?php endif; ?>
          <?php if($pageOrigin !== "Thématiques"):?>
          <div class="container-fluid">
            <label for="">PAR THÉMATIQUE</label>
          </div>
          <div class="select-arrow">
            <select class="" name="">
              <option value="" data-url="<?php echo $this->getFieldUrl('collection'); ?>">Sélectionner...</option>
              <?php foreach($facetCollection as $collectionId => $collectionName):?>
                <option value="<?php echo $collectionId ?>" data-url="<?php echo $this->getFieldUrl('collection',$collectionId); ?>" <?php echo (isset($_GET['collection']) && $collectionId == $_GET['collection'] ? "selected": ""); ?>><?php echo $collectionName ?></option>
              <?php endforeach;?>
            </select>
          </div>
          <?php endif; ?>
          <?php if($pageOrigin !== "Collections"):?>
          <div class="container-fluid">
            <label for="">PAR TYPE</label>
          </div>
          <div class="select-arrow">
            <select class="" name="">
              <option value="" data-url="<?php echo $this->getFieldUrl('type'); ?>">Sélectionner...</option>
              <?php foreach($facetItemType as $itemTypeId => $itemTypeName):?>
                <option value="<?php echo $itemTypeId ?>" data-url="<?php echo $this->getFieldUrl('type',$itemTypeId); ?>" <?php echo (isset($_GET['type']) && $itemTypeId == $_GET['type'] ? "selected": ""); ?>><?php echo $itemTypeName ?></option>
              <?php endforeach;?>
            </select>
            </select>
          </div>
        <?php endif; ?>
          <?php if($html = $this->get_dc_facet_select($itemsArray, 'Provenance')): ?>
            <div class="container-fluid">
              <label for="">PAR ETABLISSEMENT<br> DE CONSERVATION</label>
            </div>
            <?php echo $html; ?>
          <?php endif; ?>
          <!-- <div class="container-fluid">
            <button type="submit" name="button">Filtrer</button>
          </div> -->
        </form>
      </div>
    </div>
