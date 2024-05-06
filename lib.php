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
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/theme/boost/lib.php');

/**
 * Get SCSS to prepend.
 *
 * Not always required in a child theme of Boost but here so we can add our own pre SCSS easily.
 *
 * @param theme_config $theme The theme config object.
 * @return string SCSS.
 */
function theme_lexa_get_pre_scss($theme) {
    global $CFG;

    static $boostuniontheme = null;
    if (empty($boostuniontheme)) {
        $boostuniontheme = theme_config::load('boost_union'); // Needs to be the Boost Union theme so that we get its settings.
    }

    $scss = theme_boost_union_get_pre_scss($boostuniontheme);

    // Pre scss.  Note:  Does not work with themedir.
    $scss .= file_get_contents($CFG->dirroot . '/theme/lexa/scss/lexapre.scss');

    // Prepend pre-scss.
    if (!empty($boostuniontheme->settings->scsspre)) {
        $scss .= $boostuniontheme->settings->scsspre;
    }

    // Navbar scrolled layout;
    if (!empty($theme->settings->navbarscrolledlayout)) {
        $scss .= '$navbar-scrolled-layout: ' . $theme->settings->navbarscrolledlayout .';' . PHP_EOL;
    }

    return $scss;
}

/**
 * Returns the main SCSS content.
 *
 * Not always required in a child theme of Boost but here so we can add our own SCSS easily.
 *
 * @param theme_config $theme The theme config object.
 * @return string SCSS.
 */
function theme_lexa_get_main_scss_content($theme) {
    global $CFG;

    static $boostuniontheme = null;
    if (empty($boostuniontheme)) {
        $boostuniontheme = theme_config::load('boost_union'); // Needs to be the Boost Union theme so that we get its settings.
    }
    $scss = theme_boost_union_get_main_scss_content($boostuniontheme);

    // Changed scss.  Note:  Does not work with themedir.
    $scss .= file_get_contents($CFG->dirroot . '/theme/lexa/scss/lexa.scss');

    return $scss;
}

/**
 * Inject additional SCSS.
 *
 * Not always required in a child theme of Boost but here so we can add our own additional SCSS easily.
 *
 * @param theme_config $theme The theme config object.
 * @return string SCSS.
 */
function theme_lexa_get_extra_scss($theme) {
    static $boostuniontheme = null;
    if (empty($boostuniontheme)) {
        $boostuniontheme = theme_config::load('boost_union'); // Needs to be the Boost Union theme so that we get its settings.
    }

    $scss = theme_boost_union_get_extra_scss($boostuniontheme);

    $scss .= !empty($boostuniontheme->settings->scss) ? $boostuniontheme->settings->scss : '';

    return $scss;
}
