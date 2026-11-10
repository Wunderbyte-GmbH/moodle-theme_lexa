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

namespace theme_lexa\output\navigation;

use moodle_url;
use renderer_base;

/**
 * Primary navigation renderable.
 *
 * This file combines primary nav, custom menu, lang menu and
 * usermenu into a standardized format for the frontend.
 *
 * @package     theme_lexa
 * @copyright   2024 G J Barnard.
 *               {@link https://gjbarnard.co.uk}
 * @copyright   2023 bdecent GmbH <https://bdecent.de>
 * @copyright   based on code 2021 onwards Peter Dias
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class primary extends \theme_boost_union\output\navigation\primary {
    /** @var \moodle_page $page the moodle page that the navigation belongs to */
    private $page = null;

    /**
     * primary constructor.
     * @param \moodle_page $page
     */
    public function __construct($page) {
        $this->page = $page;
        parent::__construct($page);
    }

    /**
     * Combine the various menus into a standardized output.
     *
     * Modifications compared to the original function:
     * * Build the smart menus and its items as navigation nodes.
     * * Generate the nodes for different locations based on the menus locations.
     * * Combine the smart menus nodes with core primary menus.
     *
     * @param renderer_base|null $output
     * @return array
     */
    public function export_for_template(?renderer_base $output = null): array {
        $retr = parent::export_for_template($output);

        // Use our lang.
        $languagemenu = new \theme_lexa\output\core\output\language_menu($this->page);
        $retr['lang'] = $languagemenu->export_for_template($output);

        $retr['megamenu'] = $this->attach_megamenu($retr['moremenu']['nodearray'] ?? [], $retr['mobileprimarynav'] ?? []);

        return $retr;
    }

    /**
     * Turns the primary menu item that links to the configured page into the mega menu trigger.
     *
     * The menu content is maintained in local_urise. The item keeps its link, so it still works without JavaScript.
     *
     * @param array $nodes The primary menu nodes.
     * @param array $mobilenodes The nodes of the mobile drawer, whose matching item is marked too.
     * @return array|null The mega menu for the template, or null if there is none.
     */
    protected function attach_megamenu(array $nodes, array $mobilenodes = []): ?array {
        if (!class_exists(\local_urise\megamenu\menu::class)) {
            return null;
        }
        // Editors can preview the draft with ?megamenupreview=1.
        $preview = optional_param('megamenupreview', false, PARAM_BOOL)
            && has_capability('local/urise:canedit', \context_system::instance());
        $megamenu = \local_urise\megamenu\menu::export_for_template($preview);
        if (empty($megamenu)) {
            return null;
        }

        $attachurl = new moodle_url($megamenu['attachurl']);
        $matches = function (array $nodes) use ($attachurl): ?object {
            foreach ($nodes as $node) {
                if (!is_object($node) || !empty($node->haschildren) || empty($node->url)) {
                    continue;
                }
                $nodeurl = new moodle_url(html_entity_decode($node->url, ENT_QUOTES));
                if ($nodeurl->compare($attachurl, URL_MATCH_EXACT)) {
                    return $node;
                }
            }
            return null;
        };

        $node = $matches($nodes);
        if (!$node) {
            return null;
        }
        $node->ismegamenu = true;
        $megamenu['label'] = strip_tags($node->text);
        // Inside the tabs of the templates, "label" is the tab's label, so the menu's label gets its own name.
        $megamenu['menulabel'] = $megamenu['label'];

        // The mobile drawer only gets the menu if it has the item, e.g. not on the login page.
        $mobilenode = $matches($mobilenodes);
        $megamenu['mobile'] = (bool)$mobilenode;
        if ($mobilenode) {
            $mobilenode->ismegamenu = true;
        }
        return $megamenu;
    }

    /**
     * Get/Generate the user menu.
     *
     * Modifications compared to the original function:
     * Add a 'Set preferred language' link to the lang menu if the addpreferredlang setting is enabled in Boost Union.
     *
     * @param renderer_base $output
     * @return array
     */
    public function get_user_menu(renderer_base $output): array {
        // Bypasses Boost Union version of 'get_user_menu' which only has code for 'addpreferredlang' thus not relevant here.
        $retr = $this->core_get_user_menu($output);

        if (!empty($retr)) {
            global $USER;
            if (!is_siteadmin($USER)) {
                $context = $this->page->context;
                $roleassignments = get_user_roles($context, $USER->id);
                if (!empty($roleassignments)) {
                    $rolenames = role_get_names($context);
                    if (!empty($rolenames)) {
                        $firstassignment = reset($roleassignments);
                        $retr['userrole'] = $rolenames[$firstassignment->roleid]->localname;
                    }
                }
            } else {
                $retr['userrole'] = get_string('administrator');
            }
        }

        return $retr;
    }

    /**
     * Core Get/Generate the user menu without the language menu.
     *
     * NOTE: Change this if core get_user_menu() changes in lib/classes/navigation/output/primary.php.
     *
     * Modifications compared to the original function:
     * Add a 'Set preferred language' link to the lang menu if the addpreferredlang setting is enabled in Boost Union.
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

        $modifiedarray = array_map(function ($value) {
            $value->divider = $value->itemtype == 'divider';
            $value->link = $value->itemtype == 'link';
            if (isset($value->pix) && !empty($value->pix)) {
                $value->pixicon = $value->pix;
                unset($value->pix);
            }
            return $value;
        }, $info->navitems);

        // Add divider before the last item.
        $modifiedarray[count($modifiedarray) - 2]->divider = true;
        $usermenudata['items'] = $modifiedarray;
        $usermenudata['submenus'] = array_values($submenusdata);

        return $usermenudata;
    }
}
