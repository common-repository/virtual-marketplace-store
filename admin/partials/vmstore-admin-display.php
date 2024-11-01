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

<div class="wrap vmstore-form">
  <h1>VMStore Settings</h1>

  <div class="v-box-heading">

  <ul class="action-tabs">
      <li class="action-tab vmstore-core-settings" data-target="vmstore-core-settings">
          <a href="#vmstore-core-settings">
              General Settings
          </a>
      </li>
      <li class="action-tab vmstore-sync-settings" data-target="vmstore-sync-settings">
          <a href="#vmstore-sync-settings">
              Sync / Automation Settings
          </a>
      </li>
      <li class="action-tab vmstore-theming-settings" data-target="vmstore-theming-settings">
          <a href="#vmstore-theming-settings">
              Theming Settings
          </a>
      </li>
      <li class="action-tab vmstore-misc-settings" data-target="vmstore-misc-settings">
          <a href="#vmstore-misc-settings">
              Misc Settings
          </a>
      </li>
<!--       <li class="action-tab action-btn vmstore-setup-wizard action-right" data-target="vmstore-setup-wizard">
          <a class="primary-action" href="#vmstore-setup-wizard">
              Setup Wizard
          </a>
      </li> -->
  </ul>

  </div>

  <div id="vmstore_options">
    <?php
    // This prints out all hidden setting fields
    settings_fields( $this->plugin_name . '_options' );
    do_settings_sections( $this->plugin_name . '-admin' );
    ?>

    <div class="vmstore-sync-settings vmstore-settings-section" style="display: none;">
      <h3 class="form-header">Sync Store Data</h3>

      <div class="control-group">
      <div class="v-button-group v-button-group-small">
        <button onclick="javascript: sync_all()">Sync All</button><button onclick="javascript: sync_packages()">Sync Packages</button><button onclick="javascript: sync_products()">Sync Products</button><button onclick="javascript: package_products()">Package Products</button><button onclick="javascript: sync_custom_categories()">Sync Custom Categories</button>
      </div>
      </div>
      <div class="control-group">
        <div id="vmstore_sync_feedback" class="help-class">
            Awaiting Sync Request
        </div>
      </div>

      <hr />

      <h3 class="form-header">Automated Sync</h3>

      <div class="control-group">
        <button class="primary-action" onclick="javascript: toggle_cron()">Toggle Automated Sync</button>
      </div>
      <div class="control-group">
        <div id="vmstore_cron_status" class="help-class">
          <?php

          $tz = date_default_timezone_get();
          $tzString = get_option('timezone_string');
          if (isset($tzString) && $tzString !== "") {
            date_default_timezone_set($tzString);
          }

          $cronTime  = wp_next_scheduled ( 'vmstore_sync_event' );
          $cronTimestamp = date_i18n(get_option('date_format') . " " . get_option('time_format'), $cronTime);
          date_default_timezone_set($tz);

          echo ($cronTime)?"Your store will resync hourly at: " . $cronTimestamp:"Your store will not resync automatically.";
          ?>
        </div>
      </div>
    </div>

    <div class="vmstore-misc-settings vmstore-settings-section" style="display: none;">
      <h3 class="form-header">Delete Store Data</h3>
      <div class="control-group"><div class="help-class">
        This clears out all packages and products on your site.
      </div></div>
      <div class="control-group">
        <button class="remove-button" onclick="javascript: delete_store_data()">Delete Store Data</button>
      </div>
      <div class="control-group">
        <div id="vmstore_delete_feedback" class="help-class">
            
        </div>
      </div>
    </div>

<!--     <div class="vmstore-setup-wizard vmstore-settings-section" style="display: none;">
      <div class="vmstore-setup-steps">
        <div class="vmstore-setup-step vmstore-step-1">
          <h1>Step 1 of 3</h1>
        </div>
        <div class="vmstore-setup-step vmstore-step-2" style="display: none;">
          <h1>Step 2 of 3</h1>          
        </div>
        <div class="vmstore-setup-step vmstore-step-3" style="display: none;">
          <h1>Step 3 of 3</h1>          
        </div>
        <button class="primary-action" onclick="">Next Step</button>
      </div>
    </div> -->

  </div>
</div>

<script type="text/javascript">
  jQuery(function() {
    loadAdminToggles();
  });
</script>