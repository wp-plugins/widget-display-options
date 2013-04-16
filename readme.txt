=== Widget Display Options ===
Contributors: dojodigital 
Donate link: http://dojodigital.com/themes-and-plugins/
Tags: widgets, conditionals
Requires at least: 3.0
Tested up to: 3.5.1
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds a section to all widgets allowing control over when each widget is displayed and the ability to add custom classes to each widget wrapper.

== Description ==

Widget Display Options adds fields to all registered widgets that allow you to contextually hide or show any widget based on a set of chosen conditions. Many of the conditions accept parameters so you could, for example, only show a widget on the single view of a given page or post, or hide it on the archive pages of a given post type or category, etc.

Additionally this plugin provides an input field to add custom classes to the widget wrappers.

**Available Conditions:**

_Items marked with an * accept parameters._

*   Front Page
*   Blog Index
*   Page*
*   Single*
*   Single Post Type*
*   Post Type*
*   Search Results
*   Page Template*
*   Sticky Post*
*   Attachment
*   404
*   Archive (common)
*   Date Archive
*   Tag Archive*
*   Category Archive*
*   Taxonomy Archive*
*   Post Type Archive*
*   Author Archive*

For a complete description visit the online [usage manual](http://dojodigital.com/themes-and-plugins/widget-display-options-manual/).

== Installation ==

1.  Download the widget-display-options.zip file 
2.  Extract the zip file so that you have a folder called "widget-display-options"
3.  Upload the "widget-display-options" folder to the /wp-content/plugins/ directory
4.  Activate the plugin through the Plugins menu in WordPress
5.  Configure your settings in the Settings > Widget Display Options panel
6.  Visit the widgets page under Appearance > Widgets and you should find the extra input fields at the bottom of any active widgets.  
    

To Uninstall Widget Display Options

1.  Deactivate Widget Display Options through the Plugins menu in WordPress.
2.  Click the "delete" link to delete the Widget Display Options plugin. This will remove all of the Widget Display Options files from your plugins directory.

== Frequently Asked Questions ==

= How are the widgets hidden? =
Widget Display Options tests the conditionals before any of the widget code has been run and suppresses the output if the conditions are met. It does NOT use CSS to hide the widgets.

= Why am I getting script errors related to widgets that are hidden? =
When a widget is "hidden" by this plugin it's code is never output to the page. If there are any scripts loaded that require the widget markup to be present you could generate a script error if the widget markup is unavailable. This is an edge case, as most well written scripts will test for the existence of an element before trying to work with it. 

= How can I get support? =
We are not able to provide anything other than community based support for this version of Widget Display Options. Please consider upgrading to [Widget Display Options Pro](http://dojodigital.com/downloads/widget-display-options-pro/) for access to our premium support forums.

== Screenshots ==

1. Widget Display Options adds extra input fields to the bottom of your active widgets.
2. Check the box labeled "Conditional Display" to access the display options.

== Changelog ==

= 1.0 =
* New: This is the initial release.

== Upgrade Notice ==