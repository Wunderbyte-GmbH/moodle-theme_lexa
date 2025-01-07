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

namespace theme_lexa\output\navigation;

use core\output\renderer_base;
use stdClass;

/**
 * Lexa theme.
 * Primary navigation renderable.
 *
 * @package    theme_lexa
 * @copyright  2021 onwards Peter Dias
 * @copyright  2023 bdecent GmbH <https://bdecent.de>
 * @copyright  2024 G J Barnard.  Based upon work done by Peter Dias and bdecent GmbH.
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class primary extends \theme_boost_union\output\navigation\primary {
    /**
     * Get/Generate the user menu.
     *
     * Modifications compared to the original function:
     * * Add a 'Set preferred language' link to the lang menu if the addpreferredlang setting is enabled in Boost Union.
     *
     * Note: If 'get_user_menu' in '\theme_boost_union\output\navigation\primary' then change this.
     *
     * @param renderer_base $output
     * @return array
     */
    public function get_user_menu(renderer_base $output): array {

        // If not any Boost Union user menu modification is enabled.
        // (This if-clause is already built in a way that we could add more Boost Union user menu modifications
        // in the future).
        $addpreferredlangsetting = get_config('theme_boost_union', 'addpreferredlang');
        if (!isset($addpreferredlangsetting) || $addpreferredlangsetting == THEME_BOOST_UNION_SETTING_SELECT_NO) {
            // Directly return the output of the parent function.  NOTE: This is a line change from Boost Union.
            // It calls the 'parent' (in '\core\navigation\output\primary') instead.
            return $this->core_get_user_menu($output);

            // Otherwise, process the Boost Union user menu modifications.
        } else {
            // Get the output of the parent function.  NOTE: This is a line change from Boost Union.
            // It calls the 'parent' (in '\core\navigation\output\primary') instead.
            $parentoutput = $this->core_get_user_menu($output);

            // If addpreferredlangsetting is enabled and if there are submenus in the output.
            if ($addpreferredlangsetting == THEME_BOOST_UNION_SETTING_SELECT_YES &&
                    array_key_exists('submenus', $parentoutput)) {
                // Get the needle.
                $needle = get_string('languageselector');

                // Iterate over the submenus.
                foreach ($parentoutput['submenus'] as $sm) {
                    // Search the 'Language' submenu.
                    if ($sm->title == $needle) {
                        // Create and inject a divider node.
                        $dividernode = [
                            'title' => '####',
                            'itemtype' => 'divider',
                            'divider' => 1,
                            'link' => '',
                        ];
                        $sm->items[] = $dividernode;

                        // Create and inject the 'Set preferred language' link.
                        $spfnode = [
                            'title' => get_string('setpreferredlanglink', 'theme_boost_union'),
                            'text' => get_string('setpreferredlanglink', 'theme_boost_union'),
                            'link' => true,
                            'isactive' => false,
                            'url' => new \core\url('/user/language.php'),
                        ];
                        $sm->items[] = $spfnode;

                        // No need to look further.
                        break;
                    }
                }
            }

            // Return the output.
            return $parentoutput;
        }
    }

    /**
     * Get/Generate the user menu.
     *
     * This is leveraging the data from user_get_user_navigation_info and the logic in $OUTPUT->user_menu().
     *
     * Note: If 'get_user_menu' in '\core\navigation\output\primary' then change this.
     *
     * @param renderer_base $output
     * @return array
     */
    public function core_get_user_menu(renderer_base $output): array {
        global $CFG, $USER, $PAGE;
        require_once($CFG->dirroot . '/user/lib.php');

        $usermenudata = [];
        $submenusdata = [];
        $info = user_get_user_navigation_info($USER, $PAGE);
        if (isset($info->unauthenticateduser)) {
            $info->unauthenticateduser['content'] = get_string($info->unauthenticateduser['content']);
            $info->unauthenticateduser['url'] = get_login_url();

            // Start of additional code to '\core\navigation\output\primary' version of 'get_user_menu'.
            if (!empty($CFG->customusermenuitems)) {
                // Remove 'profile.php' if there.
                $customusermenuitemslines = explode("\n", $CFG->customusermenuitems);
                $customusermenuitems = [];
                foreach ($customusermenuitemslines as $line) {
                    $line = trim($line);
                    if (!str_contains($line, 'profile.php')) {
                        $customusermenuitems[] = $line;
                    }
                }
                $customusermenuitems = implode("\n", $customusermenuitems);

                $customitems = user_convert_text_to_menu_items($customusermenuitems, $PAGE);
                $navitems = [];
                $custommenucount = 0;
                foreach ($customitems as $item) {
                    $navitems[] = $item;
                    if ($item->itemtype !== 'divider' && $item->itemtype !== 'invalid') {
                        $custommenucount++;
                    }
                }
                if ($custommenucount > 0) {
                    // Only add a divider if we have customusermenuitems.
                    $divider = new stdClass();
                    $divider->itemtype = 'divider';
                    $navitems[] = $divider;

                    $modifiedarray = array_map(function($value) {
                        $value->divider = $value->itemtype == 'divider';
                        $value->link = $value->itemtype == 'link';
                        if (isset($value->pix) && !empty($value->pix)) {
                            $value->pixicon = $value->pix;
                            unset($value->pix);
                        }
                        return $value;
                    }, $navitems);
                    $info->items = $modifiedarray;
                }
            }
            // End of additional code to '\core\navigation\output\primary' version of 'get_user_menu'.

            return (array) $info;
        }

        // Gather all the avatar data to be displayed in the user menu.
        $usermenudata['avatardata'][] = [
            'content' => $info->metadata['useravatar'],
            'classes' => 'current',
        ];
        $usermenudata['userfullname'] = $info->metadata['realuserfullname'] ?? $info->metadata['userfullname'];

        // Logged in as someone else.
        if ($info->metadata['asotheruser']) {
            $usermenudata['avatardata'][] = [
                'content' => $info->metadata['realuseravatar'],
                'classes' => 'realuser',
            ];
            $usermenudata['metadata'][] = [
                'content' => get_string('loggedinas', 'moodle', $info->metadata['userfullname']),
                'classes' => 'viewingas',
            ];
        }

        // Gather all the meta data to be displayed in the user menu.
        $metadata = [
            'asotherrole' => [
                'value' => 'rolename',
                'class' => 'role role-##GENERATEDCLASS##',
            ],
            'userloginfail' => [
                'value' => 'userloginfail',
                'class' => 'loginfailures',
            ],
            'asmnetuser' => [
                'value' => 'mnetidprovidername',
                'class' => 'mnet mnet-##GENERATEDCLASS##',
            ],
        ];
        foreach ($metadata as $key => $value) {
            if (!empty($info->metadata[$key])) {
                $content = $info->metadata[$value['value']] ?? '';
                $generatedclass = strtolower(preg_replace('#[ ]+#', '-', trim($content)));
                $customclass = str_replace('##GENERATEDCLASS##', $generatedclass, ($value['class'] ?? ''));
                $usermenudata['metadata'][] = [
                    'content' => $content,
                    'classes' => $customclass,
                ];
            }
        }

        $modifiedarray = array_map(function($value) {
            $value->divider = $value->itemtype == 'divider';
            $value->link = $value->itemtype == 'link';
            if (isset($value->pix) && !empty($value->pix)) {
                $value->pixicon = $value->pix;
                unset($value->pix);
            }
            return $value;
        }, $info->navitems);

        // Include the language menu as a submenu within the user menu.
        $languagemenu = new \core\output\language_menu($PAGE);
        $langmenu = $languagemenu->export_for_template($output);
        if (!empty($langmenu)) {
            $languageitems = $langmenu['items'];
            // If there are available languages, generate the data for the the language selector submenu.
            if (!empty($languageitems)) {
                $langsubmenuid = uniqid();
                // Generate the data for the link to language selector submenu.
                $language = (object) [
                    'itemtype' => 'submenu-link',
                    'submenuid' => $langsubmenuid,
                    'title' => get_string('language'),
                    'divider' => false,
                    'submenulink' => true,
                ];

                // Place the link before the 'Log out' menu item which is either the last item in the menu or
                // second to last when 'Switch roles' is available.
                $menuposition = count($modifiedarray) - 1;
                if (has_capability('moodle/role:switchroles', $PAGE->context)) {
                    $menuposition = count($modifiedarray) - 2;
                }
                array_splice($modifiedarray, $menuposition, 0, [$language]);

                // Generate the data for the language selector submenu.
                $submenusdata[] = (object)[
                    'id' => $langsubmenuid,
                    'title' => get_string('languageselector'),
                    'items' => $languageitems,
                ];
            }
        }

        // Add divider before the last item.
        $modifiedarray[count($modifiedarray) - 2]->divider = true;
        $usermenudata['items'] = $modifiedarray;
        $usermenudata['submenus'] = array_values($submenusdata);

        return $usermenudata;
    }
}
