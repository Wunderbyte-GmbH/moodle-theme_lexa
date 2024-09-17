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
 * Contains Lexa version of class \core\output\language_menu
 *
 * @package    theme_lexa
 * @copyright  2024 G J Barnard.
 *               {@link https://gjbarnard.co.uk}
 * @copyright  2021 Adrian Greeve <adrian@moodle.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace theme_lexa\output\core\output;

/**
 * Class for creating the language menu
 *
 * @package    core
 * @category   output
 * @copyright  2021 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class language_menu extends \core\output\language_menu {

    /**
     * Export the data.
     *
     * @param \renderer_base $output
     * @return array with the title for the menu and an array of items.
     */
    public function export_for_template(\renderer_base $output): array {
        // Early return if a lang menu does not exists.
        if (!$this->show_language_menu()) {
            return [];
        }

        $nodes = [];
        $activelanguage = '';

        // Add the lang picker if needed.
        foreach ($this->langs as $langtype => $langname) {
            $isactive = $langtype == $this->currentlang;
            $attributes = [];
            if (!$isactive) {
                // Set the lang attribute for languages different from the page's current language.
                $attributes[] = [
                    'key' => 'lang',
                    'value' => get_html_lang_attribute_value($langtype),
                ];
            }
            $node = [
                'title' => $langname,
                'text' => $langname,
                'link' => true,
                'isactive' => $isactive,
                'url' => $isactive ? new \moodle_url('#') : new \moodle_url($this->page->url, ['lang' => $langtype]),
            ];
            if (!empty($attributes)) {
                $node['attributes'] = $attributes;
            }

            $nodes[] = $node;

            if ($isactive) {
                $activelanguage = strtoupper($langtype);
            }
        }

        return [
            'title' => $activelanguage,
            'items' => $nodes,
        ];
    }
}
