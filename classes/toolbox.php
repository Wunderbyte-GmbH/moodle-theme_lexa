<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Lexa theme.
 *
 * @package    theme_lexa
 * @copyright  2024 G J Barnard.
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace theme_lexa;

use moodle_url;

/**
 * The theme's toolbox.
 */
class toolbox {
    /**
     * @var toolbox Singleton instance of us.
     */
    protected static $instance = null;

    /**
     * @var themeconfigs Theme configurations.
     */
    protected static $themeconfigs = [];

    /**
     * This is a lonely object.
     */
    private function __construct() {
    }

    /**
     * Gets the toolbox singleton.
     *
     * @return toolbox The toolbox instance.
     */
    public static function get_instance() {
        if (!is_object(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Add the settings to the theme.
     *
     * @param admin_settingpage $settings The core settings page reference.
     */
    public function add_settings(\admin_settingpage &$settings) {
        global $ADMIN;

        $settings = null;

        $ADMIN->add('themes', new \admin_category('theme_lexa', get_string('configtitle', 'theme_lexa')));
        $lsettings = new \theme_boost_admin_settingspage_tabs('themesettingslexa', get_string('configtabtitle', 'theme_lexa'));

        if ($ADMIN->fulltree) {
            // The settings pages we create.
            $settingspages = [
                'general' => new \admin_settingpage(
                    'theme_lexa_general',
                    get_string('generalheading', 'theme_lexa')
                ),
                'footer' => new \admin_settingpage(
                    'theme_lexa_footer',
                    get_string('footerheading', 'theme_lexa')
                ),
            ];

            // General settings.
            $settingspages['general']->add(
                new \admin_setting_heading(
                    'theme_lexa_generalheading',
                    get_string('generalheadingsub', 'theme_lexa'),
                    format_text(get_string('generalheadingdesc', 'theme_lexa'), FORMAT_MARKDOWN) . PHP_EOL .
                    format_text(get_string('privacynote', 'theme_lexa'), FORMAT_MARKDOWN)
                )
            );

            // Booking module code(s).
            $name = 'theme_lexa/mod_booking_codes';
            $title = get_string('mod_booking_codes', 'theme_lexa');
            $description = get_string('mod_booking_codesdesc', 'theme_lexa');
            $default = '';
            $setting = new \admin_setting_configtext($name, $title, $description, $default);
            $settingspages['general']->add($setting);

            // Navbar scrolled layout.
            $name = 'theme_lexa/navbarscrolledlayout';
            $title = get_string('navbarscrolledlayout', 'theme_lexa');
            $description = get_string('navbarscrolledlayoutdesc', 'theme_lexa');
            $default = 'repeat(3,minmax(0,1fr))';
            $setting = new \admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('purge_all_caches');
            $settingspages['general']->add($setting);

            // Footer settings.
            $settingspages['footer']->add(
                new \admin_setting_heading(
                    'theme_lexa_footerheading',
                    get_string('footerheadingsub', 'theme_lexa'),
                    format_text(get_string('footerheadingdesc', 'theme_lexa'), FORMAT_MARKDOWN) . PHP_EOL .
                    format_text(get_string('privacynote', 'theme_lexa'), FORMAT_MARKDOWN)
                )
            );

            // Course offerings.
            $name = 'theme_lexa/footercourseofferings';
            $title = get_string('footercourseofferings', 'theme_lexa');
            $description = get_string('footercourseofferingsdesc', 'theme_lexa').
                PHP_EOL.get_string('footerformat', 'theme_lexa').
                PHP_EOL.get_string('footerfontawesomenote', 'theme_lexa');
            $default = '';
            $setting = new \admin_setting_configtextarea($name, $title, $description, $default);
            $settingspages['footer']->add($setting);

            // Communites.
            $name = 'theme_lexa/footercommunities';
            $title = get_string('footercommunities', 'theme_lexa');
            $description = get_string('footercommunitiesdesc', 'theme_lexa').
                PHP_EOL.get_string('footerformat', 'theme_lexa').
                PHP_EOL.get_string('footerfontawesomenote', 'theme_lexa');
            $default = '';
            $setting = new \admin_setting_configtextarea($name, $title, $description, $default);
            $settingspages['footer']->add($setting);

            // Contact us.
            $name = 'theme_lexa/footercontactus';
            $title = get_string('footercontactus', 'theme_lexa');
            $description = get_string('footercontactusdesc', 'theme_lexa').
                PHP_EOL.get_string('footerformat', 'theme_lexa').
                PHP_EOL.get_string('footerfontawesomenote', 'theme_lexa');
            $default = '';
            $setting = new \admin_setting_configtextarea($name, $title, $description, $default);
            $settingspages['footer']->add($setting);

            // Social.
            $name = 'theme_lexa/footersocial';
            $title = get_string('footersocial', 'theme_lexa');
            $description = get_string('footersocialdesc', 'theme_lexa').
                PHP_EOL.get_string('footerformat', 'theme_lexa').
                PHP_EOL.get_string('footerfontawesomenote', 'theme_lexa');
            $default = '';
            $setting = new \admin_setting_configtextarea($name, $title, $description, $default);
            $settingspages['footer']->add($setting);

            // Add the settings pages if they have more than just the settings page heading.
            foreach (array_values($settingspages) as $settingspage) {
                $lsettings->add($settingspage);
            }
        }
        $ADMIN->add('theme_lexa', $lsettings);
    }

    /**
     * Converts a string into a structured array of custom_menu_items which can
     * then be added to a custom menu.
     *
     * Structure:
     *     text|url|title|langs|fontawesome classes
     * The number of hyphens at the start determines the depth of the item. The
     * languages are optional, comma separated list of languages the line is for.
     *
     * Example structure:
     *     First item|http://www.moodle.com/|||fa-solid fa-user-graduate
     *     Second item|http://www.moodle.com/feedback/
     *     Third item
     *     English only|http://moodle.com|English only item|en
     *     German only|http://moodle.de|Deutsch|de,de_du,de_kids
     *
     * @param string $text the items definition.
     * @param string $language the language code, null disables multilang support.
     * @return array Of named items.
     */
    public static function convert_text_to_items($text, $language = null) {
        $items = [];
        $lines = explode("\n", $text);
        foreach ($lines as $linenumber => $line) {
            $line = trim($line);
            if (strlen($line) == 0) {
                continue;
            }
            // Parse item settings.
            $item = [];
            $itemvisible = true;
            $settings = explode('|', $line);
            foreach ($settings as $i => $setting) {
                $setting = trim($setting);
                if ($setting !== '') {
                    switch ($i) {
                        case 0: // Menu text.
                            $item['itemtext'] = $setting;
                            break;
                        case 1: // URL.
                            try {
                                $item['itemurl'] = new moodle_url($setting);
                            } catch (moodle_exception $exception) {
                                // We're not actually worried about this, we don't want to mess up the display
                                // just for a wrongly entered URL.
                                $item['itemurl'] = null;
                            }
                            break;
                        case 2: // Title attribute.
                            $item['itemtitle'] = $setting;
                            break;
                        case 3: // Language.
                            if (!empty($language)) {
                                $itemlanguages = array_map('trim', explode(',', $setting));
                                $itemvisible &= in_array($language, $itemlanguages);
                            }
                            break;
                        case 4: // Font awesome icon class.
                            $item['faclasses'] = $setting;
                            break;
                    }
                }
            }
            if ($itemvisible) {
                $items[] = $item;
            }
        }
        return $items;
    }

    /**
     * Finds the given setting in the theme.
     *
     * @param string $settingname Setting name.
     * @param string $format format_text format or false.
     * @param string $themename null(default of 'adaptable' used)|theme name.
     * @param string $settingdefault null|supplied default.
     *
     * @return any null|value of setting.
     */
    public static function get_setting($settingname, $format = false, $themename = null, $settingdefault = null) {

        if (empty($themename)) {
            $themename = 'lexa';
        }
        if (empty(self::$themeconfigs[$themename])) {
            self::$themeconfigs[$themename] = \theme_config::load($themename);
        }

        $setting = (!empty(self::$themeconfigs[$themename]->settings->$settingname)) ?
            self::$themeconfigs[$themename]->settings->$settingname : $settingdefault;

        if (!$format) {
            return $setting;
        } else if ($format === 'format_text') {
            return format_text($setting, FORMAT_PLAIN);
        } else if ($format === 'format_moodle') {
            return format_text($setting, FORMAT_MOODLE);
        } else if ($format === 'format_html') {
            return format_text($setting, FORMAT_HTML);
        } else {
            return format_string($setting);
        }
    }
}
