jQuery(function ($) {

  let ids = {};
  let $instance = null;
  let galleryId;


  function filterItems(id=''){
    if( !!!$instance ) return;

    let indexes = ids[id];

    if( !!indexes ){
      for (let i = 0; i < $instance.itemsCount(); i++) {
        if(inArray(i, indexes)){
          $instance.enableItem(i);
        }else{
          $instance.disableItem(i);
        }
      }
    }else{
      for (let i = 0; i < $instance.itemsCount(); i++) {
        //$instance.enableItem(i);
        if(!!sirv_woo_product.showAllVariations){
          $instance.enableItem(i);
        }else{
          if (inArray(i, ids[sirv_woo_product.mainID])) {
            $instance.enableItem(i);
          }
        }
      }
    }

    $instance.jump(0);

    updateCaption(sirv_woo_product.mainID);
  }


  function inArray(val, arr){
    return arr.indexOf(val) !== -1;
  }

  function onMVStart(){
    let defaultID = sirv_woo_product.mainID;
    /* $.each( $('#sirv-woo-gallery_' + defaultID).children(), function () {
      let id = $(this).attr('data-view-id');
      let index = $(this).attr('data-order');
      addIndex(id + '', index * 1);
    }); */

    let data = $('#sirv-woo-gallery_data_' + defaultID).attr('data-gallery-json');
    try {
      ids = JSON.parse(data);
    } catch (error) {
      console.error('SIRV: can\'t get gallery data from json');
      console.error('SIRV: ' + error);
    }
  }


  function updateSMVCacheData(){
    if(!!!ids) return;

    let vIDs = Object.keys(ids);
    let fullIDs = {}

    vIDs = vIDs.filter(function(id){ return id != sirv_woo_product.mainID;});

    fullIDs[sirv_woo_product.mainID] = 'product';

    for(let i = 0; i < vIDs.length; i++){
      fullIDs[vIDs[i]] = 'variation';
    }

    $.ajax({
      url: sirv_woo_product.ajaxurl,
      data: {
          action: 'sirv_update_smv_cache_data',
          ids: fullIDs,
          mainID: sirv_woo_product.mainID,
      },
      type: 'POST',
      dataType: "json",
    }).done(function (response) {
      //debug
      //console.log(response);

    }).fail(function (jqXHR, status, error) {
      console.error("Error during ajax request: " + error);
    });
  }


  function addIndex(id, index){
    if ( !ids.hasOwnProperty(id) ){
      ids[id] = [index];
    }else{
      ids[id].push(index);
    }
  }


  function initializeCaption(){
    let id = sirv_woo_product.mainID;
    let isCaption = $('#sirv-woo-gallery_data_' + id).attr('data-is-caption');
    if(!!isCaption){
      let caption = getSlideCaption(id);
      if (!!!$('.sirv-woo-smv-caption_' + id).length) {
        $('#sirv-woo-gallery_' + id + ' .smv-slides-box').after('<div class="sirv-woo-smv-caption sirv-woo-smv-caption_' + id + '">'+ caption +'</div>');
      }
    }
  }


  function getSlideCaption(id){
    let $caption;

    if(!!galleryId){
      $caption = $($('#'+ galleryId +' .smv-slide.smv-shown .smv-content div')[0]);
    }else{
      $caption = $($('#sirv-woo-gallery_' + id + ' .smv-slide.smv-shown .smv-content div')[0]);
    }

    return $caption.attr('data-slide-caption') || '';
  }

  function updateCaption(id){
    $('.sirv-woo-smv-caption_' + id).html(getSlideCaption(id));
  }


  $(document).ready(function () {

    onMVStart();
    //updateSMVCacheData();

    $('input.variation_id').on('change', function () {
      let variation_id = $('input.variation_id').val();
      if (!!variation_id ) {
        filterItems(variation_id);
      }else{
        if (!!sirv_woo_product.showAllVariations){
          filterItems();
        } else { filterItems(sirv_woo_product.mainID); }

      }
    });

    $('.reset_variations').on('click', function () {
      filterItems(sirv_woo_product.mainID);
    });

    Sirv.on('viewer:ready', function (viewer) {
      $('.sirv-skeleton').removeClass('sirv-skeleton');
      $instance = Sirv.viewer.getInstance('#sirv-woo-gallery_' + sirv_woo_product.mainID);
      galleryId = $('#sirv-woo-gallery_' + sirv_woo_product.mainID + ' div.smv').attr('id');
      initializeCaption();
    });


    Sirv.on('viewer:afterSlideIn', function(slide){
        let id = sirv_woo_product.mainID;
        let caption = getSlideCaption(id);

        $('.sirv-woo-smv-caption_' + id).html(caption);
    });

  }); //end dom ready
}); // end closure
