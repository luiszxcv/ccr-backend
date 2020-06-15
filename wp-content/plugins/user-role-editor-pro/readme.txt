=== User Role Editor Pro ===
Contributors: Vladimir Garagulya (https://www.role-editor.com)
Tags: user, role, editor, security, access, permission, capability
Requires at least: 4.4
Tested up to: 5.2.3
Stable tag: 4.52
Requires PHP: 5.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

User Role Editor WordPress plugin makes user roles and capabilities changing easy. Edit/add/delete WordPress user roles and capabilities.

== Description ==

User Role Editor WordPress plugin allows you to change user roles and capabilities easy.
Just turn on check boxes of capabilities you wish to add to the selected role and click "Update" button to save your changes. That's done. 
Add new roles and customize its capabilities according to your needs, from scratch of as a copy of other existing role. 
Unnecessary self-made role can be deleted if there are no users whom such role is assigned.
Role assigned every new created user by default may be changed too.
Capabilities could be assigned on per user basis. Multiple roles could be assigned to user simultaneously.
You can add new capabilities and remove unnecessary capabilities which could be left from uninstalled plugins.
Multi-site support is provided.

== Installation ==

Installation procedure:

1. Deactivate plugin if you have the previous version installed.
2. Extract "user-role-editor-pro.zip" archive content to the "/wp-content/plugins/user-role-editor-pro" directory.
3. Activate "User Role Editor Pro" plugin via 'Plugins' menu in WordPress admin menu. 
4. Go to the "Settings"-"User Role Editor" and adjust plugin options according to your needs. For WordPress multisite URE options page is located under Network Admin Settings menu.
5. Go to the "Users"-"User Role Editor" menu item and change WordPress roles and capabilities according to your needs.

In case you have a free version of User Role Editor installed: 
Pro version includes its own copy of a free version (or the core of a User Role Editor). So you should deactivate free version and can remove it before installing of a Pro version. 
The only thing that you should remember is that both versions (free and Pro) use the same place to store their settings data. 
So if you delete free version via WordPress Plugins Delete link, plugin will delete automatically its settings data. Changes made to the roles will stay unchanged.
You will have to configure lost part of the settings at the User Role Editor Pro Settings page again after that.
Right decision in this case is to delete free version folder (user-role-editor) after deactivation via FTP, not via WordPress.

== Changelog ==
= [4.52] 03.10.2019 =
* Core version: 4.51.3
* New: Content Restrictions add-on: It's possible to add front-end content view restrictions directly to post categories or any other custom taxonomies.
* Update: Meta boxes access add-on: It's possible to block/hide "Page Attributes" Gutenberg sidebar component blocking "Page"->"Page Attributes" meta box.
* Fix: Content view restrictions add-on: Redirection from not available for view front/home page works as expected ("Page not found" error message was shown earlier).
* Fix: Admin menu access add-on: When menu link is the same as the link of the single unblocked submenu item (all the rest items of the same submenu were blocked), menu link was removed as a blocked one.
* Fix: Export single role: Exported file may lose 1-2 last characters and get wrong extension (.pdf in addition to expected .dat). Content type header was replaced with 'application/octet-stream';
* Fix: Import single role: Error processing was enhanced for the cases of incorrect JSON data input. URE shows error message now instead of page reloading in silence.
* Fix: Settings->User Role Editor->Multisite->Activate access restrictions to User Role Editor for single site administrator: after turning ON this option URE produced PHP fatal error: Uncaught Error: Call to undefined method URE_Lib_Pro::filter_existing_caps_input() in /wp-content/plugins/user-role-editor-pro/pro/includes/classes/settings-pro.php on line 192


= [4.51.1] 15.06.2019 =
* Core version: 4.51.1
* Fix: Post/Pages/Custom post types (CPT) edit restrictions add-on:
*   - When CPT excluded from restrictions via 'ure_restrict_edit_post_type' filter, its categories list is not restricted for selection too.
* Update: Meta boxes access add-on: It's possible to automatically block Gutenberg components (right sidebar) - just block corresponding meta boxes: Categories, Tags, Featured Image, Excerpt, Discussion, Slug (Permalink).
* Core version was updated to 4.51.1
* Fix: Superadmin could not revoke capabilities from 'administrator' role under WordPress multisite.

Full list of changes is available in changelog.txt file.
