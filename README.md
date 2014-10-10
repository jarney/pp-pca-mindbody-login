Wordpress MINDBODY plugin
======================

This is a [WordPress](https://wordpress.com/) [plugin](http://codex.wordpress.org/Plugin_API) 
designed to integrate WordPress websites with the [MINDBODY](https://www.mindbodyonline.com/) system.
The plugin provides a variety of shortcodes which cause specific behavior.  The plugin
contains a "Settings" menu which allows the API credentials to be set.

# Installing:

In order to install the plugin, [download the plugin from here](https://github.com/jarney/pp-pca-mindbody-login/archive/master.zip).
Once downloaded, go to the Plugins section of WordPress and click the 'Add New' button.
Next, select the 'Upload' menu item and browse to the downloaded zip file.  Once the
zip file is selected, click the 'Install Now' button to install the plugin in your site.

Once the plugin is installed, activate the plugin.

Finally, go to the Settings page "PP PCA Mindbody Settings" and enter the Mindbody API credentials
and the Mindbody site credentials for your site.  These credentials should be kept secret and not shared
with anyone because they control access to your client information, much of which, they would want kept secret.

# Shortcodes defined:

* pp-pca-mindbody-login 
If this shortcode appears on a page, then the user must first enter their
MINDBODY username and password before this page is visible.  No permissions
other than having a valid MINDBODY account are required.  When the user is logged in,
this shortcode expands into a "Logout" link allowing the user to log out.

Example usage:
```
[pp-pca-mindbody-login]
Nothing on this page will be accessible until
the user authenticates with the MINDBODY username/password.
```

* pp-pca-mindbody-class-list
This shortcode expands into a table of all of the classes that this user
is enrolled in.  The list of classes includes any classes from the last 120 days
and 120 days into the future.  Any such classes will appear on this list.  In addition
to the class, if there is a page which contains a custom field of "mindbody" with a value
of a class ID the user is enrolled in, then a link to that page will be shown for that
class.

Example usage:
```
[pp-pca-mindbody-class-list]
```

* pp-pca-mindbody-class-list-all
This shortcode expands into a table of all classes that are found in the MINDBODY system
along with the class ID for that class.  This page was intended to provide a way that
administrators can associate the classes in MINDBODY with the IDs used to tie them into
Wordpress posts.

Example usage:
```
[pp-pca-mindbody-class-list-all]
```

* pp-pca-mindbody-class
This shortcode protects content so that only persons enrolled in an appropriate
class can see the content.  Note that any content outside this tag is visible to
anyone who can log in.

Example usage:
```
[pp-pca-mindbody-class id=2215,2265,2272]
Only users enrolled in classes 2215, 2265, and 2272 can see this content.
[/pp-pca-mindbody-class]
```

* pp-pca-mindbody-content-menu
This shortcode expands to emit some Javascript which creates a menu of buttons
to hide and reveal content.  This would mainly be used to make efficient
use of a single-page layout course.

Example usage:
```
<div id=btns></div>
<div id=panes>
<div id=wk1 class=wks>Menu Item One</div>
<div id=wk2 class=wks>Menu Item Two</div>
<div id=wk3 class=wks>Menu Item Three</div>
<div id=wk4 class=wks>Menu Item Four</div>
</div>
[pp-pca-mindbody-content-menu]
```

This example produces a menu with four items on it and shows only the first item.
It also shows four buttons, one for each menu item.  It displays the menu item
whose menu button is pressed.
