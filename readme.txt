=== Koumpounophobia ===
Contributors: toppa
Donate link: http://www.toppa.com/koumpounophobia-wordpress-plugin
Tags: post, admin, button, editor, jquery, quicktag
Requires at least: 2.5
Tested up to: 2.8.6
Stable tag: 0.5

A plugin for adding custom buttons to the WordPress HTML Editor.

== Description ==

Koumpounophobia is powered by jQuery, and enhances the WordPress HTML Editor button bar in 5 ways:

   1. It replaces the anchor and image buttons with new versions that provide modal input dialogs with more options (image width, height, etc.)
   2. It adds two new buttons: div and span, each with their own modal input dialogs (for class, style, etc. attributes)
   3. It lets you add your own buttons and create custom modal dialogs for them
   4. It provides an API for other plugins to add buttons and custom modal dialogs
   5. You can control which Koumpounophobia-based buttons will appear in the button bar

The Koumpounophobia Settings menu lets you control which buttons appear on the button bar, and provides a form for creating your own buttons. It also provides instructions for creating custom modal dialogs. Koumpounophobia's display/dialogs.html file provides a good set of examples for creating your own modal dialogs.

A .pot file is included for localization, and a Russian translation is included.

My <a href="http://wordpress.org/extend/plugins/post-to-post-links-ii/">Post-to-Post Links II plugin</a> uses Koumpounophobia to add its own button to the HTML Editor. That plugin is a good example to work from if you're a plugin author.

If you're wondering about the name, Koumpounophobia is a phobia of buttons. I figured it was appropriate since there haven't been any improvements to the WordPress HTML Editor in years. ;-)

The latest version of Koumpounophobia works well in the browsers I have tested so far: Firefox 3, Internet Explorer 7 and 8, and Google Chrome. Please <a href="http://www.toppa.com/contact/">let me know</a> if you encounter any problems in your browser. (I've noticed the HTML Editor is "jumpy" in Internet Explorer 8. This is not related to Koumpounophobia. The scroll position within the editor will sometimes change randomly even without Koumpounophobia installed).

**Installation Instructions**

Download the zip file, unzip it, and copy the "koumpounophobia" folder to your plugins directory. Then activate it from your plugin panel. After successful activation, you'll see some changes to your HTML Editor buttons bar. You can use the Koumpounophobia Settings menu to control the changes to your button bar.

== Frequently Asked Questions ==

Please go to <a href="http://www.toppa.com/koumpounophobia-wordpress-plugin">the Koumpounophobia page for more information</a>.

== Changelog ==

= 0.5 =
* Fixed CSS display of dialog header

= 0.4 =
* Fixed scroll position bug with Firefox 3 – the editor’s scroll position will now stay in place when using a Koumpounophobia modal dialog.
* Works with IE 7 and 8: now inserts tags at the cursor position (previously, they were always inserted at the top of the editor, regardless of the cursor position).

= 0.3 =
* Got auto width and height working for modal dialogs
* Can now update options for its own buttons on reactivation if there are changes

= 0.2 =
* Added jQuery UI Dialog for controlling the input dialogs
* Fixed bug with buttons for self closing tags that don’t use an input dialog (the button mistakenly tried to add a closing tag)
* Simplified the HTML and CSS for the form input dialogs

= 0.1 =
* Beta release

