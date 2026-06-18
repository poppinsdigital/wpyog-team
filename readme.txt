=== WPYog Team ===
Contributors: wpyog
Tags: team, staff, members, directory, shortcode
Requires at least: 5.0
Tested up to: 7.0
Requires PHP: 7.0
Stable tag: 1.0.20
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create modern team showcases, staff directories, and company profile sections for WordPress — lightweight, responsive, and easy to manage.

== Description ==

WPYog Team helps you create modern team showcases, staff directories, faculty listings, and company profile sections directly inside WordPress.

Built for businesses and organizations that want a clean and professional way to display their teams without bloated page builders or complicated setups.

= Live Demo =

[View Live Demo](https://demo.poppinsdigital.com/wpyog-team/)

**Perfect for:**

* Businesses and startups
* Agencies and studios
* Schools and educational institutes
* Healthcare organizations
* Nonprofits and communities
* Corporate staff directories
* Speaker and event pages

**Key Features:**

* Responsive team grid layouts
* Team member profiles with image, designation, and bio
* Social media profile links
* Department and category support
* Shortcode support
* Elementor-friendly
* Lightweight and fast
* Mobile responsive layouts
* Easy team member management
* Clean and modern design

**Designed for Real Business Websites**

WPYog Team is built from real-world client requirements and production WordPress workflows.

Unlike bloated team plugins filled with unnecessary complexity, WPYog Team focuses on simplicity, speed, usability, and clean frontend presentation.

**Lightweight & Flexible**

The plugin is designed to work smoothly with most modern WordPress themes and page builders including Elementor.

Use shortcodes anywhere — pages, posts, sidebars, landing pages, about sections, or dedicated team pages.

**Why WPYog Team:**

* Easy to use
* Clean modern layouts
* Business-focused workflows
* Fast and lightweight
* Mobile-friendly
* Built for practical real-world use cases

**More Plugins from WPYog:**

* [WPYog Documents](https://wordpress.org/plugins/wpyog-documents/) — Document and resource management

Built by [Poppins Digital](https://poppinsdigital.com/).

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/wpyog-team` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Go to **WPYog Team** in your WordPress admin menu to start adding team members.

== Usage ==

**1. Add Team Members**

Go to: WP Admin → WPYog Team

Add the following for each member:

* Team member name
* Designation
* Profile image
* Bio/description
* Social media links
* Department/category

**2. Publish Team Members**

Publish your team members just like regular WordPress posts.

**3. Display Team Showcase**

Use the shortcode below on any page, post, or widget area:

`[wpyogteam id="YOUR_TEAM_ID"]`

You can find the Team ID on the Team edit screen or in the Teams list.

**4. Display by Department or Category**

`[wpyog_team category="management"]`

**5. Customize Your Team Page**

The plugin works with most modern WordPress themes and page builders including Elementor.

You can use it to create:

* About Us pages
* Faculty directories
* Leadership sections
* Medical expert profiles
* Startup team pages
* Speaker showcase sections

== Frequently Asked Questions ==

= How do I display my team on a page? =

Use the shortcode `[wpyogteam id="YOUR_TEAM_ID"]` on any page, post, or widget area. You can find the Team ID on the Team edit screen.

= Can I filter by department or category? =

Members are grouped into Teams. Create a Team, assign members to it, then use `[wpyogteam id="YOUR_TEAM_ID"]` to display that team anywhere.

= Does it work with Elementor? =

Yes, WPYog Team is Elementor-friendly and works with most modern WordPress themes and page builders.

= Is it mobile responsive? =

Yes, all layouts are fully mobile responsive.

== Screenshots ==

1. Team grid layout on the frontend
2. Team member profile popup
3. Team member edit screen in WordPress admin
4. Team settings and shortcode configuration

== Changelog ==

= 1.0.20 =
* Fix: Modal popup was left-aligned instead of centred on Twenty Twenty-Five and other block themes. Centering is now done via `position:absolute` + `translate(-50%, -50%)` on the modal itself, making it immune to block theme flex/grid overrides.

= 1.0.19 =
* Fix: Popup member data (`ptTeamData`) was missing from the page on block themes running WordPress 6.6 and later. `wp_add_inline_script()` was silently dropped because the script handle had already been printed before the shortcode block rendered. Data is now embedded directly in the shortcode HTML output, guaranteeing it is always present.

= 1.0.18 =
* Improvement: Frontend JavaScript completely rewritten in vanilla JS — no jQuery dependency. Eliminates popup failures on block themes (such as Twenty Twenty-Five) where jQuery may not be loaded. Reduces frontend payload.
* Fix: Inline member data script no longer wrapped in a jQuery IIFE, preventing silent failures when jQuery is unavailable.

= 1.0.17 =
* New: Export / Import feature. Export all team members and teams as a JSON file and import them on any other site. Supports staging-to-live migration. Member photos are re-downloaded automatically on import via media sideloading.

= 1.0.16 =
* Stability improvements and bug fixes.

= 1.0.0 =
* Initial release with popup layout, team grid, social media links, and shortcode support.

== Upgrade Notice ==

= 1.0.20 =
Fixes popup centering on Twenty Twenty-Five and other block themes. Recommended update for all users.

= 1.0.19 =
Critical fix: popup data was not loading on block themes with WordPress 6.6+. Update immediately if using a block theme.

= 1.0.18 =
Frontend JavaScript rewritten without jQuery — improves compatibility with all block themes.

= 1.0.17 =
Adds Export / Import for easy migration between staging and live sites.
