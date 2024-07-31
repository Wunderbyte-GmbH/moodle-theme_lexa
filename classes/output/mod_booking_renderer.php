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
 * @package theme_lexa
 * @copyright 2023 Wunderbyte GmbH <info@wunderbyte.at>
 * @author Christian Badusch
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_lexa\output;

use local_urise\shortcodes;
use mod_booking;
use html_writer;
use mod_booking\output\bookingoption_description;
use mod_booking\output\col_availableplaces;
use mod_booking\price;
use mod_booking\singleton_service;

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
     * Render output for action column.
     * @param object $data
     * @return string
     */
    public function render_col_availableplaces($data) {
        $o = '';
        $data = $data->export_for_template($this);
        $o .= $this->render_from_template('mod_booking/col_availableplaces', $data);
        return $o;
    }

     /**
      * This function is called for each data row to allow processing of the
      * booking value.
      *
      * @param object $values Contains object with all the values of record.
      * @return string $coursestarttime Returns course start time as a readable string.
      * @throws coding_exception
      */
    public function prepare_bookings($values, $id) {

        $settings = singleton_service::get_instance_of_booking_option_settings((int)$id);
        // Render col_bookings using a template.

        $buyforuser = price::return_user_to_buy_for();

        $data = new col_availableplaces($values, $settings, $buyforuser);
        $output = singleton_service::get_renderer('local_urise');
        return $output->render_col_availableplaces($data);
    }

     /**
      * This function is called for each data row to allow processing of the
      * booking option tags (botags).
      *
      * @param object $values Contains object with all the values of record.
      * @return string $sports Returns course start time as a readable string.
      * @throws coding_exception
      */
    public function prepare_botags($id) {

        $settings = singleton_service::get_instance_of_booking_option_settings((int)$id);

        $botagsstring = '';

        if (isset($settings->customfields) && isset($settings->customfields['botags'])) {
            $botagsarray = $settings->customfields['botags'];
            if (!empty($botagsarray)) {

                if (!is_array($botagsarray)) {
                    $botagsarray = (array)$botagsarray;
                }
                foreach ($botagsarray as $botag) {
                    if (!empty($botag)) {
                        $botagsstring .=
                            "<span class='urise-table-botag rounded-sm bg-info text-light
                             pl-2 pr-2 pt-1 pb-1 mr-1 d-inline-block text-center'>
                            $botag
                            </span>";
                    } else {
                        continue;
                    }
                }
                if (!empty($botagsstring)) {
                    return $botagsstring;
                } else {
                    return '';
                }
            }
        }
        return '';
    }

    /**
     * This function is called for each data row to allow processing of the
     * booking option coursestarttime
     *
     * @param object $values Contains object with all the values of record.
     * @return string $sports Returns course start time as a readable string.
     * @throws coding_exception
     */
    public function render_col_coursestarttime($data) {
        $o = '';
        // Check if multiple dates.
        $data = $data->export_for_template($this);
        if (!empty($data['datestrings']) && count($data['datestrings']) > 1) {
            $data['firstDate'] = $data['datestrings'][0]['datestring'];
        }
        $o .= $this->render_from_template('mod_booking/col_coursestarttime', $data);
        return $o;
    }

    /**
     * Renders the booking option description view.
     *
     * This function processes the booking option description data, prepares necessary
     * details for display, and renders them using a template.
     *
     * @param bookingoption_description $data The booking option description data object.
     * @return string The rendered HTML output.
     */
    public function render_bookingoption_description_view(bookingoption_description $data) {
        $o = '';
        $data = $data->export_for_template($this);
        $data['title'] = strip_tags( $data['title']);
        // Prepare Data for template.
        if (!empty($data['bookinginformation'])) {
            $data['bookings'] = $this->prepare_bookings($data['bookinginformation'], $data['modalcounter']);
        }
        if (!empty($data['kurssprache'])) {
            $data['kurssprache'] = $this->prepare_kurssprache($data['kurssprache'], $data['modalcounter'] );
        }
        if (!empty($data['format'])) {
            $data['format'] = $this->prepare_format($data['format'], $data['modalcounter'] );
        }
        if (!empty($data['kompetenzen'])) {
            $data['competency'] = $this->prepare_kompetenzen($data['modalcounter'] );
        }
        $data['botags'] = $this->prepare_botags($data['modalcounter']);
        $data['showcollapse'] = $this->prepare_dates($data);
        $settings = singleton_service::get_instance_of_booking_option_settings((int)$data['modalcounter']);

        if (!empty($settings->entity)) {
            $data['entities'] = $settings->entity;
        }

        $o .= $this->render_from_template('mod_booking/bookingoption_description_view', $data);
        return $o;
    }

    /**
     * Prepare and format Kompetenzen (competencies) information based on custom fields.
     *
     * This method retrieves and formats the custom 'Kompetenzen' fields associated with
     * a given booking option. It returns a string of HTML elements representing these
     * competencies, each wrapped in a span tag with specific classes for styling.
     *
     * @param int $id The unique identifier for the booking option settings.
     * @return string|null A string containing formatted HTML elements for each competency,
     *                     or null if no competencies are found.
     * @throws InvalidArgumentException When the ID is not a valid integer.
     */
    public function prepare_kompetenzen($id) {

        $settings = singleton_service::get_instance_of_booking_option_settings((int)$id);

        if (isset($settings->customfields) && isset($settings->customfields['kompetenzen'])) {
            if (is_array($settings->customfields['kompetenzen'])) {

                $returnorgas = [];
                $organisations = shortcodes::get_kompetenzen();
                foreach ($settings->customfields['kompetenzen'] as $orgaid) {

                    if (isset($organisations[$orgaid])) {
                        $returnorgas[] = html_writer::tag(
                            'span',
                            $organisations[$orgaid]['localizedname'],
                            ['class' => 'bg-secondary pl-1 pr-1 mr-1 rounded category']
                        );

                    }
                }

                return implode(", ", $returnorgas);
            } else {
                $value = $settings->customfields['kompetenzen'];
                $message = "<span class='bg-secondary orga'>$value</span>";
                return $message;
            }
        }
    }

    /**
     * Retrieve course language from custom fields.
     *
     * @param mixed $values Unused parameter.
     * @param int $id Booking option settings ID.
     * @return string|null The course language or null if not found.
     */
    public function prepare_kurssprache($values, $id) {
        $settings = singleton_service::get_instance_of_booking_option_settings((int)$id);

        if (isset($settings->customfieldsfortemplates) && isset($settings->customfieldsfortemplates['kurssprache'])) {
            $value = $settings->customfieldsfortemplates['kurssprache']['value'];
            return format_string($value);
        }
    }


    /**
     * Retrieve course format from custom fields.
     *
     * @param mixed $values Unused parameter.
     * @param int $id Booking option settings ID.
     * @return string|null The course language or null if not found.
     */
    public function prepare_format($values, $id) {
        $settings = singleton_service::get_instance_of_booking_option_settings((int)$id);

        if (isset($settings->customfieldsfortemplates) && isset($settings->customfieldsfortemplates['format'])) {
            $value = $settings->customfieldsfortemplates['format']['value'];
            return $value;
        }
    }


    /**
     * Prepare dates collapse.
     *
     * @param mixed $values
     * @return bool The course language or null if not found.
     */
    public function prepare_dates($values) {

        $maxdates = get_config('booking', 'collapseshowsettings') ?? 2; // Hardcoded fallback on two.
        // Show a collapse button for the dates.
        if (!empty($values['dates']) && count($values['dates']) > $maxdates) {
            return true;
        } else {
            return false;
        }
    }

}
