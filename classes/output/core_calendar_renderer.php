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
class core_calendar_renderer extends \core_calendar_renderer {
    /**
     * Creates a button to add a new event.
     *
     * @param int $courseid
     * @param int $unused1
     * @param int $unused2
     * @param int $unused3
     * @param int $unused4
     * @return string
     */
    public function add_event_button($courseid, $unused1 = null, $unused2 = null, $unused3 = null, $unused4 = null) {
        $data = [
            'defaulteventcontext' => (\context_course::instance($courseid))->id,
        ];
        return $this->render_from_template('core_calendar/add_event_button', $data);
    }

    /**
     * Returns today as a string to be displayed.  Uses localised language string format.
     */
    public function today() {
        $data = [
            'today' => userdate(time(), get_string('strftimedayshort', 'core_langconfig')),
        ];

        return $this->render_from_template('theme_lexa/today', $data);
    }
}
