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
 * A custom renderer class that extends the plugin_renderer_base and is used by the booking module.
 *
 * @package mod_booking
 * @copyright 2023 Wunderbyte GmbH <info@wunderbyte.at>
 * @author David Bogner, Andraž Prinčič
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_lexa\output;

use ErrorException;
use Exception;
use mod_booking;
use mod_booking\booking;
use mod_booking\output\instance_description;
use mod_booking\output\bookingoption_description;
use mod_booking\output\business_card;
use mod_booking\output\bookingoption_changes;
use mod_booking\output\signin_downloadform;
use mod_booking\output\report_edit_bookingnotes;
use tabobject;
use html_writer;
use plugin_renderer_base;
use moodle_url;
use Throwable;
use user_selector_base;
use html_table_cell;
use html_table;
use html_table_row;
use rating;
use rating_manager;
use popup_action;
use stdClass;
use templatable;

/**
 * A custom renderer class that extends the plugin_renderer_base and is used by the booking module.
 *
 * @package mod_booking
 * @copyright 2023 Wunderbyte GmbH <info@wunderbyte.at>
 * @author David Bogner, Andraž Prinčič
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_booking_renderer extends \mod_booking\output\renderer {
    /**
     * Function to print booking option single view on optionview.php
     * @param bookingoption_description $data
     * @return string
     */
    public function render_bookingoption_description_view(bookingoption_description $data) {
        $o = '';
        $data = $data->export_for_template($this);

        if (!empty($data['description'])) {
            $data['summary'] = $this->truncate_to_max_words($data['description']);
        }
        
        if (!empty($data['region'])) {
            $data['formattedregions'] = $this->format_region_list($data['region']);
        }

        try {
            $o .= $this->render_from_template('mod_booking/bookingoption_description_view', $data);
        } catch (Exception $e) {
            $o .= get_string('bookingoptionupdated', 'mod_booking');
        }
        return $o;
    }

    /**
     * Helper to format the region list as HTML.
     *
     * @param string $regionstring
     * @return string
     */
    private function format_region_list(string $regionstring): string {
        $regions = explode(',', $regionstring);

        if (empty($regions)) {
            return '';
        }

        $firstregion = array_shift($regions);
        $count = count($regions);

        $output = '<span class="yellowtag">';
        $output .= '<span class="bl first-bl">' . htmlspecialchars($firstregion) . '</span>';

        if ($count > 0) {
            $output .= '<span class="more-bl">';
            $output .= '<span class="more-bl-badge">+' . $count . '</span>';
            $output .= '<span class="more-bl-list">';
            foreach ($regions as $region) {
                $output .= '<span class="bl">' . htmlspecialchars(trim($region)) . '</span>';
            }
            $output .= '</span></span>';
        }

        $output .= '</span>';

        return $output;
    }

    /**
     * Helper to format the region list as HTML.
     *
     * @param string $content
     * @param int $maxwords
     * @return string
     */
    private function truncate_to_max_words(string $content, int $maxwords = 100): string {
        $text = strip_tags($content);
        $words = preg_split('/\s+/', trim($text));
        if (count($words) > $maxwords) {
            $words = array_slice($words, 0, $maxwords);
            return implode(' ', $words) . '...';
        }
        return implode(' ', $words);
    }
    
}
