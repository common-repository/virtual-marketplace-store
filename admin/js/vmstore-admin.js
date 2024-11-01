function save_vmstore_options()
{
  var data = jQuery('#vmstore_options').find('select, textarea, input').serializeArray();
  data.push({'name' : 'fn', 'value' : 'save'});
  data.push({'name' : 'action', 'value' :'vmstore'});

  jQuery('.vmstore_save_feedback').text("Settings save in progress");
  jQuery.post(
    ajaxurl,
    data,
    function(response){
      jQuery('.vmstore_save_feedback').text("Settings save complete");
      clear_permalinks();
    }
  );
}

function toggle_cron()
{
  jQuery.post(
    ajaxurl,
    {  
      'fn' : 'toggle_cron',
      'action': 'vmstore'
    },
    function(response) {
      jQuery('#vmstore_cron_status').text(response);
    }
  );
}

function sync_all()
{
  vmstore_admin_do_post("sync_all",
    "#vmstore_sync_feedback",
    "Store sync in progress",
    "Sync complete",
    "Cannot sync - not all settings are configured");
}

function sync_packages()
{
  vmstore_admin_do_post("sync_packages",
    "#vmstore_sync_feedback",
    "Package sync in progress",
    "Package sync complete",
    "Cannot sync - not all settings are configured");
}

function sync_products()
{
  vmstore_admin_do_post("sync_products",
    "#vmstore_sync_feedback",
    "Product sync in progress",
    "Product sync complete",
    "Cannot sync - not all settings are configured");
}

function package_products()
{
  vmstore_admin_do_post("package_products",
    "#vmstore_sync_feedback",
    "Products are being packaged",
    "Product packaging complete",
    "Cannot package - not all settings are configured");
}

function sync_custom_categories()
{
  vmstore_admin_do_post("sync_custom_categories",
    "#vmstore_sync_feedback",
    "Category sync in progress",
    "Category sync complete",
    "Cannot sync - not all settings are configured");
}

function delete_store_data()
{
  var c = confirm("Are you sure you want to delete all of your store data?");

  if (c) {
    vmstore_admin_do_post("delete_store_data",
    "#vmstore_delete_feedback",
    "Data is now being deleted",
    "Data deletion complete",
    "Cannot delete data - not all settings are configured");
  }
}

function vmstore_admin_do_post(remoteFunction, messageTarget, startMessage, completeMessage, errorMessage)
{
  jQuery(messageTarget).text(startMessage);
  jQuery.post(
    ajaxurl,
    {  
      'fn' : remoteFunction,
      'action': "vmstore"
    },
    function(response){
      var message = "Something went wrong ¯\_(ツ)_/¯";
      switch(response) {
        case "1":
          message = completeMessage;
          break;
        case "-1":
          message = errorMessage;
          break;
        default:
          message = "There was an error during sync - the api may be down";
      }
      jQuery(messageTarget).text(message);
    }
  );
}

function clear_permalinks()
{
  jQuery.post(
    ajaxurl,
    {  
      'fn' : 'flush_wipe',
      'action': 'vmstore'
    },
    function(response){
      // Silence is golden.
    }
  );
}

function SetupSelects()
{
  jQuery(".vmstore-select").select2();
  jQuery(".vmstore-select-multi").select2Sortable(
    {bindOrder: 'sortableStop'}
    );
}

function UpdateTextFieldFromSelect()
{
  //@TODO This function and control should be updated to remove the extra text input now that the order is bound on sortableStop
  var selects = jQuery('.vmstore-select-multi');

  selects.on("change", function(e) {

    var curSelect = jQuery(e.currentTarget);

    var curTextID = curSelect.prop("id").replace('vmstore-select-','');;
    var curText = jQuery('#' + curTextID);
    var selectValue = curSelect.val();

    var curUpdateRegionID = curTextID + "-update";
    var curUpdateRegion = jQuery('#' + curUpdateRegionID);
    curUpdateRegion.html("&nbsp");

    var joinedSelectValue = "";

      if (selectValue != null)
      {
        //Update Edit Region
      var length = selectValue.length;
      var elems = "";

      for (var i = 0; i < length; i++) {
        var label = curSelect.find('option[value="' + selectValue[i] + '"]');
        var temp = "";
        temp = '<a target="blank" href="post.php?post=' + label.val() + '&amp;action=edit">' + label.html() + '</a> ';
        var elems = elems + temp;
      }

      curUpdateRegion.html(elems);
        joinedSelectValue = selectValue.join(",");
      }
      
    curText.val(joinedSelectValue);
  });
}

function toggleAdminSection(section) {
  jQuery('.vmstore-settings-section').hide();
  jQuery('.action-tab.' + section).addClass('selected');
  jQuery('.' + section).show();
  window.location.hash = section;
}

function loadAdminToggles() {
  var searchParams = new URLSearchParams(window.location.search);
  if (searchParams.get('page') === 'vmstore-product-admin' )
  {
    var sectionTarget = window.location.hash.substr(1);
    if (window.location.hash === "") {
      toggleAdminSection('vmstore-core-settings');
    }
    else {
      toggleAdminSection(sectionTarget);
    }

    jQuery('.action-tabs .action-tab').click(function(e) {
      e.preventDefault();
      var elem = jQuery(this);
      jQuery('.action-tab.selected').removeClass('selected');
      toggleAdminSection(elem.data("target"));
    });

  }
}

(function( $ ) {
	'use strict';
  //SetupSelects();
  $(document).append("");

})( jQuery );
