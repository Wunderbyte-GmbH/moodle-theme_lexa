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

namespace theme_lexa\output;

/**
 * Core renderer.
 */
class core_renderer extends \theme_boost_union\output\core_renderer {
    /**
     * Return the height of a single row of the header.
     *
     * @return int Header row height.
     */
    public function get_header_height() {
        // Navbar height divided by two as there are two rows.
        // Todo, can be a setting if needed etc.
        return 60;
    }

    /**
     * Return the site's logo URL, if any.
     *
     * @param int $maxwidth The maximum width, or null when the maximum width does not matter.
     * @param int $maxheight The maximum height, or null when the maximum height does not matter.
     * @return moodle_url|false
     */
    public function get_logo_url($maxwidth = null, $maxheight = 200) {
        return $this->image_url('UniVie_Logo_blue', 'theme_lexa');
    }

    /**
     * Return the site's logo URL, if any.
     *
     * @param int $maxwidth The maximum width, or null when the maximum width does not matter.
     * @param int $maxheight The maximum height, or null when the maximum height does not matter.
     * @return moodle_url|false
     */
    public function get_logo_url_plain($maxwidth = null, $maxheight = 200) {
        return $this->image_url('UniVie_Logo_white', 'theme_lexa');
    }

    /**
     * Return the site's compact logo URL, if any.
     *
     * @param int $maxwidth The maximum width, or null when the maximum width does not matter.
     * @param int $maxheight The maximum height, or null when the maximum height does not matter.
     * @return moodle_url|false
     */
    public function get_compact_logo_url($maxwidth = 300, $maxheight = 300) {
        return $this->image_url('UniVie_Logo_blue', 'theme_lexa');
    }

    /**
     * Return the site's second compact logo URL, if any.
     *
     * @param int $maxwidth The maximum width, or null when the maximum width does not matter.
     * @param int $maxheight The maximum height, or null when the maximum height does not matter.
     * @return moodle_url|false
     */
    public function get_compact_second_logo_url($maxwidth = 300, $maxheight = 300) {
        return $this->image_url('LeXA_Logo_blue_one_line', 'theme_lexa');
    }

    /**
     * Get the mod booking codes.
     *
     * @return string Markup if any.
     */
    public function get_modbookingcodes() {
        if ($this->page->pagelayout == 'mycourses') {
            $toolbox = \theme_lexa\toolbox::get_instance();
            $modbookingcodes = $toolbox->get_setting('mod_booking_codes');
            if (!empty($modbookingcodes)) {
                return format_text($modbookingcodes, FORMAT_PLAIN);
            }
        }
        return '';
    }

    /**
     * Get the footer course offerings.
     *
     * @return string Markup if any.
     */
    public function render_footercourseofferings() {
        $toolbox = \theme_lexa\toolbox::get_instance();
        $courseofferings = $toolbox->get_setting('footercourseofferings');
        if (!empty($courseofferings)) {
            return $this->render_footerlist($courseofferings, $toolbox);
        }
    }

    /**
     * Get the footer communities.
     *
     * @return string Markup if any.
     */
    public function render_footercommunities() {
        $toolbox = \theme_lexa\toolbox::get_instance();
        $communities = $toolbox->get_setting('footercommunities');
        if (!empty($communities)) {
            return $this->render_footerlist($communities, $toolbox);
        }
    }

    /**
     * Get the footer contact us.
     *
     * @return string Markup if any.
     */
    public function render_footercontactus() {
        $toolbox = \theme_lexa\toolbox::get_instance();
        $contactus = $toolbox->get_setting('footercontactus');
        if (!empty($contactus)) {
            return $this->render_footerlist($contactus, $toolbox);
        }
    }

    /**
     * Get the footer social.
     *
     * @return string Markup if any.
     */
    public function render_footersocial() {
        $toolbox = \theme_lexa\toolbox::get_instance();
        $contactus = $toolbox->get_setting('footersocial');
        if (!empty($contactus)) {
            return $this->render_footerlist($contactus, $toolbox, true);
        }
    }

    /**
     * Get the footer list.
     *
     * @return string Markup if any.
     */
    private function render_footerlist($text, $toolbox, $targetblank = false) {
        $items = $toolbox->convert_text_to_items($text, current_language());
        if (!empty($items)) {
            $context = [
                'items' => $items,
                'targetblank' => $targetblank,
            ];
            return $this->render_from_template('theme_lexa/footerlist', $context);
        }
    }
}
