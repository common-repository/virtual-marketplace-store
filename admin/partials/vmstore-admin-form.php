<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.vendasta.com
 * @since      1.0.0
 *
 * @package    Vmstore
 * @subpackage Vmstore/admin/partials
 */
?>
<div class="vmstore-core-settings vmstore-settings-section" style="display: none;">
<h3 class="form-header">Account Settings</h3>
<div class="control-group"><div class="help-class">Required info to collect your product data</div></div>

<?php

echo vwformtools\FormHelpers::gen_field("Public Store ID", 'pid', $vmstore_pid, "text");
echo vwformtools\FormHelpers::gen_field("Store Market ID", 'mid', $vmstore_mid, "text");
echo vwformtools\FormHelpers::gen_field("Store Slug", 'slug', $vmstore_slug, "text");

?>
<br />

<button class="add-button" onclick="javascript: save_vmstore_options()">Save Options</button>

<div class="control-group">
  <div class="vmstore_save_feedback" class="help-class">
      Waiting for settings to be saved
  </div>
</div>

<hr />

<h3 class="form-header">Usage</h3>
<div class="control-group">
  <div class="help-class">Embed your store into a post or page with this shortcode:</div>
  <label><input id="shortcode" type="text" value='[list_packages /]' disabled="disabled" /></label>
</div>
<div class="control-group">
  <div class="help-class">Notes: The shortcode includes options for the category filters as follows:</div>
  <ul>
    <li><strong>showfilter</strong>: defaults to 'true' but can be set to 'false', this will exclude the category filters</li>
<!--     <li><strong>sortfilter</strong>: defaults to 'alphabetical' but can be set to 'custom', this will order the filters by the custom category order</li> -->
  </ul>
  <div class="help-class">The shortcode with all these options looks like this:</div>
  <label><input id="shortcode" type="text" value='[list_packages showfilter="true" /]' disabled="disabled" /></label>
</div>

</div>

<div class="vmstore-theming-settings vmstore-settings-section" style="display: none;">
<h3 class="form-header">Theming Settings</h3>
<div class="control-group"><div class="help-class">Configurable options for your Store and CTAs</div></div>
<?php

echo vwformtools\FormHelpers::gen_field("Package Header", 'package_header', stripcslashes($vmstore_package_header), "textarea");
echo vwformtools\FormHelpers::gen_field("Package Footer", 'package_footer', stripcslashes($vmstore_package_footer), "textarea");
echo vwformtools\FormHelpers::gen_field("Call to Action", 'cta', stripcslashes($vmstore_cta), "textarea");

?>

<br />
<button class="add-button" onclick="javascript: save_vmstore_options()">Save Options</button>

<div class="control-group">
  <div class="vmstore_save_feedback" class="help-class">
      Waiting for settings to be saved
  </div>
</div>

</div>