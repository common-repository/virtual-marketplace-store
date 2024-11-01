(function( $ ) {
	'use strict';

  $(document).ready(function() {
    lightbox.option({
        'resizeDuration': 200,
        'wrapAround': true
      });


    var toggles = $('.vmstore-product-toggle');
    var tagToggles = $('.vmpackage-card-toggle');

    //only configure control for multi-product packages
    if (toggles.length > 1) {
      toggles.click(function (e) {
        toggleProduct(this);
      });

      hideAllProducts();
      toggleProduct($('.vmstore-product-toggle:first-of-type'));
    }

    if (tagToggles.length > 1) {
      tagToggles.click(function(e) {
        toggleTag(this);
      });

      hideAllCards();
      toggleTag($('.vmpackage-card-toggle:first-of-type'))
    }

    jQuery('.vmp_faq').click(function() {toggleFaq(this);});
  });

  function toggleProduct(toggle) {
    hideAllProducts();
    deactivateAllToggles();
    jQuery(toggle).addClass('vmstore-active');
    var target = jQuery(toggle).data("target");
    jQuery('.' + target).removeClass('vmstore-hidden');
  }

  function toggleTag(toggle) {
    var toggle = jQuery(toggle);
    hideAllCards();
    deactivateAllTagToggles();
    toggle.addClass('vmstore-active');
    var target = toggle.data("target");
    jQuery('.' + target).show();
  }

  function toggleFaq(faq) {
    var faq = jQuery(faq);
    faq.toggleClass("faq-active");
  }

  function deactivateAllTagToggles() {
    $('.vmpackage-card-toggle').removeClass('vmstore-active');
  }

  function hideAllCards() {
    $('.vmstore-card').hide();
  }

  function deactivateAllToggles() {
    $('.vmstore-product-toggle').removeClass('vmstore-active');
  }
  function hideAllProducts() {
    $('.product-content-container').addClass('vmstore-hidden');
  }

})( jQuery );