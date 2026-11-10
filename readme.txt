=== WPYog Team ===
Contributors: wpyog
Tags: team, staff, members, directory, shortcode
Requires at least: 5.0
Tested up to: 7.1
Requires PHP: 7.0
Stable tag: 1.0.27
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
* Two layouts: Popup and Single Page
* Circle profile images (optional)
* Previous / Next navigation on member pages (optional)
* Clean member URLs such as /team/member-name/ with an editable slug
* Export / Import between sites
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

**4. Choose a Layout and Display Options**

Open your team and set:

* **Layout:** Popup opens the member profile in a modal. Single Page links each card to the member's own page with photo, designation, name, bio, contact details and social links.
* **Show profile image in circle:** crops member photos to a circle in the grid, popup and single page.
* **Show previous / next navigation:** (Single Page layout) adds Previous and Next links at the bottom of each member page, following the member order of the team. Untick to hide them. On by default.
* **Columns, margin and padding:** control the grid.

**5. Change the Member Page URL**

Go to: Team > Settings

Member pages use `/team/member-name/` by default. Change the "Member page URL slug" to anything you like, for example `team-member` or `staff`. Permalinks refresh automatically after saving. Reserved WordPress slugs are blocked, and you get a warning if the slug clashes with an existing page that has child pages.

Developers can also set the slug with the `wpyog_team_member_slug` filter.

**6. Customize Your Team Page**

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

= How do I open member details on their own page instead of a popup? =

Edit your team and choose the **Single Page** layout. Each card then links to the member's own page.

= How do I turn off the Previous / Next links on member pages? =

Edit the team (Single Page layout) and untick **Show previous / next navigation**, then click Update.

= How do I show profile photos as circles? =

Edit the team and tick **Show profile image in circle**. It applies to the grid, popup and single page.

= Can I change the member page URL? =

Yes. Go to Team > Settings and change the **Member page URL slug**. The default is `team`, giving URLs like `/team/jane-doe/`. If you have a page at `/team/` with child pages, pick a different slug such as `team-member` to avoid clashes.

= A member page shows a 404 after I changed the slug. =

Go to Settings > Permalinks and click Save Changes once to refresh the rules.

= If a member is in more than one team, which team does the member page follow? =

The first team that uses the Single Page layout (lowest team ID). If none uses it, the lowest team ID. This team decides the circle image setting and the Previous / Next order.

= Is it mobile responsive? =

Yes, all layouts are fully mobile responsive.

== Screenshots ==

1. Team grid layout on the frontend
2. Team member profile popup
3. Team member edit screen in WordPress admin
4. Team settings and shortcode configuration
5. Single Page layout with circle profile image and Previous / Next navigation

== Changelog ==

= 1.0.27 =
* Change: Member pages now use `/team/member-name/` by default (was `/wpy_member/`).
* New: Team > Settings page with a "Member page URL slug" field. Permalinks refresh automatically on save. Warns if the slug clashes with an existing page that has child pages, and blocks reserved WordPress slugs.

= 1.0.26 =
* Improvement: Default styling for the Previous / Next navigation. Bordered buttons with hover state, theme link underlines removed, stacked on mobile.

= 1.0.25 =
* New: "Show previous / next navigation" option in team settings (Single Page layout). On by default; untick it to hide the Previous / Next links on member pages.
* Fix: Member pages now find their team even when the member list was stored with string IDs (e.g. after some imports), so navigation and circle images apply reliably.
* Improvement: Export / Import includes the navigation setting.

= 1.0.24 =
* Improvement: Single Page layout now matches the popup. Designation, name and subtitle sit left-aligned in the right column next to the photo.
* New: Previous / Next navigation between team members on member pages (follows the team's member order). Replaces the Back link.
* Improvement: Clean member URLs. The `?pt_team=` parameter is gone; the member page works out its team automatically.
* Change: Member page URLs now use `/wpy_member/member-name/` instead of `/wpyog_member/member-name/`. Permalinks are refreshed automatically on update. Use the `wpyog_team_member_slug` filter to change the slug.

= 1.0.23 =
* New: "Single Page" layout. Clicking a member opens their own page with photo, designation, bio, contact details and social links, instead of a popup.
* New: "Show profile image in circle" option in team settings. Applies to the grid, popup and single page layouts.
* Improvement: Export / Import now includes the circle image setting.

= 1.0.22 =
* Fix: Removed explicit `suppress_filters => true` from internal queries (was bypassing other plugins' query filters).
* Fix: Import file path now sanitized before use.
* Fix: Updated "Tested up to" to the current WordPress version.
* Housekeeping: Documented existing nonce checks and template-local variables for the WP.org code scanner (no functional change).

= 1.0.21 =
* Fix: Member photos were force-cropped to a 500×500 square on upload, cutting off portrait photos. Photos are now resized (not cropped) up to 800px, keeping their original aspect ratio.
* Fix: Modal popup photo panel used object-fit:cover, which cropped portrait/landscape photos to fit the fixed sidebar box. It now shows the full photo without cropping.

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

= 1.0.27 =
Member page URLs change to /team/member-name/ by default. You can change the slug under Team > Settings.

= 1.0.26 =
Styles the Previous / Next navigation on member pages.

= 1.0.25 =
Adds an option to turn off previous / next navigation on Single Page member pages.

= 1.0.24 =
Member page URLs change from /wpyog_member/ to /wpy_member/. Single Page layout now matches the popup and has previous / next navigation.

= 1.0.23 =
Adds the Single Page layout and a circle profile image option. Existing teams are unchanged.

= 1.0.22 =
Internal code quality fixes ahead of WordPress.org review. No visible changes for existing users.

= 1.0.21 =
Recommended update if you use non-square member photos.

= 1.0.20 =
Fixes popup centering on Twenty Twenty-Five and other block themes. Recommended update for all users.

= 1.0.19 =
Critical fix: popup data was not loading on block themes with WordPress 6.6+. Update immediately if using a block theme.

= 1.0.18 =
Frontend JavaScript rewritten without jQuery — improves compatibility with all block themes.

= 1.0.17 =
Adds Export / Import for easy migration between staging and live sites.
