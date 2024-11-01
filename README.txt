=== ThemeMove Core ===
Contributors: thememoveclub
Donate link: https://thememove.com
Tags: thememove, core, import
Requires at least: 4.9
Tested up to: 5.3
Stable tag: trunk
Requires PHP: 5.6
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A simple plugin for ThemeMove's themes on WordPress.org and ThemeMove Club.

== Description ==

A simple plugin for ThemeMove's themes on WordPress.org and ThemeMove Club. You can check theme update, import/export demo data or customize the theme.

### Features:

- Validate and manage your license key
- Check theme updates, get the patch by version
- Generate child theme
- Import / Export demo data
- Get system information

#### Need help?
Please take a look at [our articles](https://thememove.ticksy.com/articles/) first.

If you can't find an answer there, please look through the [support forums](https://thememove.ticksy.com/) or start your own topic.


== Installation ==
1. In your WordPress admin panel, go to *Plugins > New Plugin*, search for **ThemeMove Core** and click "*Install now*"
1. Alternatively, download the plugin and upload the contents of `thememove-core.zip` to your plugins directory, which usually is `/wp-content/plugins/`.
1. Activate the plugin


== Frequently Asked Questions ==

= Does this plugin work with ThemeMove's themes on ThemeForest? =
No. It only works with the themes on WordPress.org and [ThemeMove Club](https://thememove.com).

= Where can I find my license keys to activate the theme? =
[You can find your license keys here](https://thememove.com/dashboard/licenses/)

= I don't receive the theme update message =
We use transient to save the theme information on your site. It will be automatically refreshed after 1 day.
You can try to clear those update caches by going to *Dashboard > Updates*. If you still don't see the update message, please try to use [this plugin](https://wordpress.org/plugins/artiss-transient-cleaner/) to delete all update caches.

= I can't import demo data because of the system requirement =
The importer requires minimum PHP configuration as follow:
- max_execution_time 300
- memory_limit 256MB
- post_max_size 100MB
- upload_max_filesize 100MB

You can verify your PHP configuration limits in the *System Info* tab in *ThemeMove Core > Tools*. You can do this on your own, or contact your web host and ask them to adjust those configurations.

= I want to ignore all import issues. Is it possible? =
Yes, it's. Please add the code below to the functions.php file in your child theme

`
add_filter( 'tmc_ignore_import_issues', '__return_true' );
`

*Note: We do not guarantee the demo data will be imported properly when you ignore the import issues*

= I see a message when import the demo data: 'Could not download the media package. The URL is not defined'. What should I do? =
Please contact our support staff in the [support forums](https://thememove.ticksy.com/) or start your own topic.
He will send you the media package and give you an instruction to install it on your server.

= After importing the demo content, my Media Library image files weren't showing up as proper thumbnails – it seemed as if the thumbnails were missing. =
You should use [this plugin](https://wordpress.org/plugins/regenerate-thumbnails/).

= ThemeMove Core is awesome! Can I contribute? =
Yes you can! Join in on our [GitHub repository](https://github.com/ThemeMove/thememove-core)

== Screenshots ==

1. License Form
2. Your license information
3. Install required plugins and Import demo data
4. Import demo data
5. Choose what to import
6. Import content popup
7. Import is successful!
8. Child theme generator
9. System information
10. Export data screen

== Changelog ==

= 1.3.0 - Dec 19, 2019 =
- Support Import/Export Elfsight Instagram Feed widgets
- Add 2 new CMB2 Field: Buttonset & Switch

= 1.2.2 - Dec 2, 2019 =
- Fix wp-color-picker-alpha conflict with Kirki

= 1.2.1 - Nov 27, 2019 =
- Compatible with WordPress 5.3
- Fix bug when updating theme, new theme folder has a strange name

= 1.2.0 - Oct 16, 2019 =
- Add download URL to patches box
- Fix update count number in the admin menu

= 1.1.4 - Oct 09, 2019 =
- Add new param `$demo_slug` to `tmc_demo_steps` filter
- Remove PHP Notice when copying media package

= 1.1.3 - Oct 09, 2019 =
Support read import data files for other plugins

= 1.1.2 - Oct 08, 2019 =
Support export data to files for other plugins

= 1.1.1 - Sep 23, 2019 =
Fix PHP notice when enqueueing custom CSS for CMB2 fields

= 1.1.0 - Sep 23, 2019 =
Add custom fields for CMB2

= 1.0.0 - Sep 20, 2019 =
Initial release
