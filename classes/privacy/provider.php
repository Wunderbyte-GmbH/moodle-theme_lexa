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
 * Provider class file. As required for any data privacy information required.
 *
 * @package    theme_lexa
 * @copyright  2024 G J Barnard.
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace theme_lexa\privacy;

use core_privacy\local\request\writer;
use core_privacy\local\metadata\collection;

/**
 * Privacy provider.
 */
class provider implements
    // This plugin has data.
    \core_privacy\local\metadata\provider,

    // This plugin has some sitewide user preferences to export.
    \core_privacy\local\request\user_preference_provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection $items The initialised item collection to add items to.
     * @return  collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $items): collection {
        $items->add_user_preference('drawer-open-index', 'privacy:metadata:preference:draweropenindex');
        $items->add_user_preference('drawer-open-block', 'privacy:metadata:preference:draweropenblock');
        return $items;
    }

    /**
     * Store all user preferences for the plugin.
     *
     * @param int $userid The user id of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid) {
        $preferences = get_user_preferences(null, null, $userid);
        foreach ($preferences as $name => $value) {
            $blockid = null;
            $matches = [];
            if ($name == 'drawer-open-index') {
                $decoded = ($value) ? get_string('privacy:open', 'theme_lexa') : get_string('privacy:closed', 'theme_lexa');

                writer::export_user_preference(
                    'theme_lexa',
                    $name,
                    $value,
                    get_string('privacy:request:preference:draweropenindex', 'theme_lexa', (object) [
                        'name' => $name,
                        'value' => $value,
                        'decoded' => $decoded,
                    ])
                );
            } else if ($name == 'drawer-open-block') {
                $decoded = ($value) ? get_string('privacy:open', 'theme_lexa') : get_string('privacy:closed', 'theme_lexa');

                writer::export_user_preference(
                    'theme_lexa',
                    $name,
                    $value,
                    get_string('privacy:request:preference:draweropenblock', 'theme_lexa', (object) [
                        'name' => $name,
                        'value' => $value,
                        'decoded' => $decoded,
                    ])
                );
            }
        }
    }
}
