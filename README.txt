=== Virtual Marketplace Store ===
Contributors: Vendasta
Tags: marketplace, store, saas, agency
Requires at least: 3.0.1
Tested up to: 5.1.1
Stable tag: 5.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Render your products, packages and categories from the Vendasta marketplace and sync them to your website.

== Description ==

Vendasta’s white-label platform empowers you to provide marketing solutions to local businesses under your brand. Part of the platform is the Marketplace where you can discover ready-to-resell digital solutions, add your existing traditional products and services, and create bundled solutions---all of which you can all display and sell in your client-facing Store. In a few clicks you can resell G-Suite, Review Management, or become a managed WordPress hosting reseller.
 
The Virtual Marketplace Store plugin allows you to customize, embed, and automatically sync your Store in your Wordpress website. By default Partners have access to an iframe store which is very easy to use, but does not provide the best customer experience. This plugin lets you render your products inline on your website and customize them with your own styles and calls to action.
 
After you’ve created a Vendasta account, the first step we recommend is to build out your Store by adding your existing products and services or any of the  from Vendasta’s Marketplace.

How do I get set up?:

*   Don’t have an account? Sign up [here](https://www.vendasta.com/get-started-today?utm_campaign=vmp-wp-plugin&utm_medium=link&utm_source=wordpress-org)--it’s free! [https://www.vendasta.com/get-started-today?utm_campaign=vmp-wp-plugin&utm_medium=link&utm_source=wordpress-org](https://www.vendasta.com/get-started-today)
*   From the Vendasta Partner Center, configure your Store: Partner Center → Marketplace → Manage Store
*   You will need your Partner ID. You should see it at the top left corner of your screen: “Your Company Name (PID)”
*   In Wordpress, after installing the plugin, add your Partner ID in the settings page as the “Public Store ID”
*   Once your Public Store ID is set, sync and package your store using the buttons in the “Sync Store Data” section
*   Set up a default “Store” page with your preferred URL slug
*   Add the [list_packages /] shortcode to your store page to display all your packages (which in turn include your products and related tags)
*   Finally, customize your package headers, footers, and calls to action via the Theming Settings

If you’d like to try it before creating a Vendasta account you can use our demo PID: ABC

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `vmstore.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add your Vendasta Partner ID into the settings page as the Public Store ID
4. Sync packages, products and package products together
5. Create a store page and use the [list_packages /] shortcode to render a card list of your packages

== Frequently Asked Questions ==

= Can I override the content from the Vendasta marketplace? =

Yes! There is a feature to lockdown the product and package title/tagline/description content against changes.

= Can I customize the layout of the store? =

You can set the default header, footer and call to action of the package pages via the settings page. Any other changes can be made via CSS and child theme markup overrides are comin soon!

== Changelog ==

= 1.2.6 =

* Fixed UTC timezone php notice bug on default installs
* Changed clearfix class name so it doesn't conflict with other clearfixes
* Added a grey circle around fallback package icons
* Added toggle control for FAQs

= 1.2.5 =

* Exclude "All" category from the package display
* Handle an empty state when icons and / or banners are missing from packages / products
* Re-introduce "Package Products" function which is integral to the multi-product packages

= 1.2.4 =

* Fixed a css namespace bug
* Removed LMI category auto creation in favor of custom categories
* Removed product LMI packaging in favor of custom categories

= 1.2.3 =

* Added support for custom categories
* Added showfilter parameter to the [list_packages /] shortcode
* Refactor of admin js+php remote post functions and navigation

= 1.2.2 =

* Fixed a bug that prevented the CRON sync from firing
* Updated the Admin UX/UI to match Vendasta front end styles

= 1.2.1 =

* Added a sort parameter to the package request so that ordering is consistent
* Ordering packages ASC by the MENU_ORDER page attribute instead of the default DESC

= 1.2.0 =

* Added package ordering using the menu_order page attribute
* Added draft functionality to unpublish packages that no longer appear in the API
* Added cron to routinely update products / packages

= 1.1.0 =

* Added tag classes to the list_packages output
* Added tag toggles to the list_packages output
* Added a javascript filter function to the [list_packages /] shortcode's card output
* Package template changes to include markup
* CSS changes to make the default card to pop up on hover and for their buttons to look a bit better
* Re-ordered the packageFieldMap so that package properties can be injected into the header / footer / cta

= 1.0.0 =
Initial submission to WordPress.org for approval