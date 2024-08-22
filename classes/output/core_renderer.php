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

use block_contents;
use block_move_target;
use coding_exception;
use html_writer;
use stdClass;

/**
 * Core renderer.
 */
class core_renderer extends \theme_boost_union\output\core_renderer {
    /**
     * Returns the CSS classes to apply to the body tag.
     *
     * @since Moodle 2.5.1 2.6
     * @param array $additionalclasses Any additional classes to apply.
     * @return string
     */
    public function body_css_classes(array $additionalclasses = []) {
        if ($this->page->pagelayout == 'frontpage') {
            $bodyclasses = str_replace('limitedwidth', '', $this->page->bodyclasses);
        } else {
            $bodyclasses = $this->page->bodyclasses;
        }
        return $bodyclasses . ' ' . implode(' ', $additionalclasses);
    }

    /**
     * Return the height of the first row of the header.
     *
     * @return int Header first row height.
     */
    public function get_header_first_row_height() {
        // Todo, can be a setting if needed etc.
        return 57;
    }

    /**
     * Return the height of the second row of the header.
     *
     * @return int Header second row height.
     */
    public function get_header_second_row_height() {
        // Todo, can be a setting if needed etc.
        return 95;
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
     * Allow plugins to provide some content to be rendered in the navbar.
     * The plugin must define a PLUGIN_render_navbar_output function that returns
     * the HTML they wish to add to the navbar.
     *
     * @return string HTML for the navbar
     */
    public function navbar_plugin_output() {
        $output = '<span id="lexa-npo" class="d-flex h-100 align-items-center">';

        if ($pluginsfunction = get_plugins_with_function('render_navbar_output')) {
            foreach ($pluginsfunction as $plugintype => $plugins) {
                foreach ($plugins as $pluginfunction) {
                    $output .= $pluginfunction($this);
                }
            }
        }

        // Give subsystems an opportunity to inject extra html content. The callback
        // must always return a string containing valid html.
        foreach (\core_component::get_core_subsystems() as $name => $path) {
            if ($path) {
                $output .= component_callback($name, 'render_navbar_output', [$this], '');
            }
        }

         $output .= '</span>';

        return $output;
    }

    /**
     * Wrapper for header elements.
     *
     * This renderer function is copied and modified from /lib/outputrenderers.php
     *
     * @return string HTML to display the main header.
     */
    public function full_header() {
        if (($this->page->pagetype == 'site-index') ||
            ($this->page->pagetype == 'course-index-category')) {
            return '';
        }
        return parent::full_header();
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
     * Get the HTML for blocks in the given region.
     *
     * @since Moodle 2.5.1 2.6
     * @param string $region The region to get HTML for.
     * @param array $classes Wrapping tag classes.
     * @param string $tag Wrapping tag.
     * @param boolean $fakeblocksonly Include fake blocks only.
     * @return string HTML.
     */
    public function blocks($region, $classes = [], $tag = 'aside', $fakeblocksonly = false) {
        $displayregion = $this->page->apply_theme_region_manipulations($region);
        $classes = (array)$classes;
        $classes[] = 'block-region';
        $attributes = [
            'id' => 'block-region-'.preg_replace('#[^a-zA-Z0-9_\-]+#', '-', $displayregion),
            'class' => join(' ', $classes),
            'data-blockregion' => $displayregion,
            'data-droptarget' => '1',
        ];
        $editing = $this->page->user_is_editing();
        $content = '';

        if ($editing) {
            $content .= $this->block_region_title($displayregion);
        }

        if ($this->page->blocks->region_has_content($displayregion, $this)) {
            $content .= html_writer::tag('h2', get_string('blocks'), ['class' => 'sr-only']) .
                $this->blocks_for_region($displayregion, $fakeblocksonly);
        } else {
            $content .= html_writer::tag('h2', get_string('blocks'), ['class' => 'sr-only']);
        }
        return html_writer::tag($tag, $content, $attributes);
    }

    /**
     * Get the HTML for block title in the given region.
     *
     * @param string $region The region to get HTML for.
     *
     * @return string HTML.
     */
    protected function block_region_title($region) {
        return html_writer::tag(
            'p',
            get_string('region-' . $region, 'theme_lexa'),
            ['class' => 'block-region-title col-12 text-center font-italic font-weight-bold']
        );
    }

    /**
     * Output all the blocks in a particular region.
     *
     * @param string $region the name of a region on this page.
     * @param boolean $fakeblocksonly Output fake block only.
     * @return string the HTML to be output.
     */
    public function blocks_for_region($region, $fakeblocksonly = false) {
        $blockcontents = $this->page->blocks->get_content_for_region($region, $this);
        $lastblock = null;
        $zones = [];
        foreach ($blockcontents as $bc) {
            if ($bc instanceof block_contents) {
                $zones[] = $bc->title;
            }
        }
        $output = '';
        $notediting = !$this->page->user_is_editing();

        foreach ($blockcontents as $bc) {
            if ($bc instanceof block_contents) {
                if ($fakeblocksonly && !$bc->is_fake()) {
                    // Skip rendering real blocks if we only want to show fake blocks.
                    continue;
                }
                if (!empty($bcadditionalclasses)) {
                    $bc->attributes['class'] .= ' '.$bcadditionalclasses;
                }
                if (($notediting) && ($region == 'landing')) {
                    $output .= html_writer::tag('div',
                        $this->block(
                            $bc,
                            $region,
                            ['notitle' => true, 'landingblock' => true]
                        ),
                        ['class' => 'landingblock']
                    );
                } else {
                    $output .= $this->block($bc, $region);
                }
                $lastblock = $bc->title;
            } else if ($bc instanceof block_move_target) {
                if (!$fakeblocksonly) {
                    $output .= $this->block_move_target($bc, $zones, $lastblock, $region);
                }
            } else {
                throw new coding_exception('Unexpected type of thing (' . get_class($bc) . ') found in list of block contents.');
            }
        }
        return $output;
    }

    /**
     * Prints a nice side block with an optional header.
     *
     * @param block_contents $bc HTML for the content
     * @param string $region the region the block is appearing in.
     * @return string the HTML to be output.
     */
    public function block(block_contents $bc, $region, $blockoptions = []) {
        $bc = clone($bc); // Avoid messing up the object passed in.
        if (empty($bc->blockinstanceid) || !strip_tags($bc->title)) {
            $bc->collapsible = block_contents::NOT_HIDEABLE;
        }

        $id = !empty($bc->attributes['id']) ? $bc->attributes['id'] : uniqid('block-');
        $context = new stdClass();
        $context->skipid = $bc->skipid;
        $context->blockinstanceid = $bc->blockinstanceid ?: uniqid('fakeid-');
        $context->dockable = $bc->dockable;
        $context->id = $id;
        $context->hidden = $bc->collapsible == block_contents::HIDDEN;
        $context->skiptitle = strip_tags($bc->title);
        $context->showskiplink = !empty($context->skiptitle);
        $context->arialabel = $bc->arialabel;
        $context->ariarole = !empty($bc->attributes['role']) ? $bc->attributes['role'] : 'complementary';
        $context->class = $bc->attributes['class'];
        $context->type = $bc->attributes['data-block'];
        if (empty($blockoptions['notitle'])) {
            $context->title = $bc->title;
        }
        $context->content = $bc->content;
        $context->annotation = $bc->annotation;
        $context->footer = $bc->footer;
        $context->hascontrols = !empty($bc->controls);
        if ($context->hascontrols) {
            $context->controls = $this->block_controls($bc->controls, $id);
        }

        if (!empty($blockoptions['landingblock'])) {
            $template = 'theme_lexa/landing_block';
        } else {
            $template = 'core/block';
        }
        return $this->render_from_template($template, $context);
    }

    /**
     * Hide user's role?
     *
     * @return bool Hide the role.
     */
    public function hideusersrole() {
        $toolbox = \theme_lexa\toolbox::get_instance();
        return $toolbox->get_setting('hideuseruserrole');
    }

    /**
     * Show the popover?
     *
     * @return bool.
     */
    public function has_footer_popover() {
        if ($this->page->pagetype == 'site-index') {
            return false;
        }
        return true;
    }

    /* Note: Done this way such that the 'footer' context does not need to be created by the theme, and
             thus every layout overridden.  Rather that the 'footer' is overridden in a Mustache way only. */
    /**
     * Has footer course offerings?
     *
     * @return bool List has a value.
     */
    public function has_footercourseofferings() {
        return $this->has_footerlist('footercourseofferings');
    }

    /**
     * Get the footer course offerings.
     *
     * @return string Markup if any.
     */
    public function get_footercourseofferings() {
        return $this->render_footerlist('footercourseofferings');
    }

    /**
     * Has footer communities?
     *
     * @return bool List has a value.
     */
    public function has_footercommunities() {
        return $this->has_footerlist('footercommunities');
    }

    /**
     * Get the footer communities.
     *
     * @return string Markup if any.
     */
    public function get_footercommunities() {
        return $this->render_footerlist('footercommunities');
    }

    /**
     * Has footer contact us?
     *
     * @return bool List has a value.
     */
    public function has_footercontactus() {
        return $this->has_footerlist('footercontactus');
    }

    /**
     * Get the footer contact us.
     *
     * @return string Markup if any.
     */
    public function get_footercontactus() {
        return $this->render_footerlist('footercontactus');
    }

    /**
     * Has a footer social?
     *
     * @return bool List has a value.
     */
    public function has_footersocial() {
        return $this->has_footerlist('footersocial');
    }

    /**
     * Get the footer social.
     *
     * @return string Markup if any.
     */
    public function get_footersocial() {
        return $this->render_footerlist('footersocial', true);
    }

    /**
     * Has the given footer list?
     *
     * @param string $listname Name of the list, the setting name.
     *
     * @return bool Setting has a value.
     */
    private function has_footerlist($listname) {
        return (!empty($this->get_footerlist($listname)));
    }

    /**
     * Get the given footer list.
     *
     * @param string $listname Name of the list, the setting name.
     *
     * @return string Setting value if any.
     */
    private function get_footerlist($listname) {
        $toolbox = \theme_lexa\toolbox::get_instance();
        return $toolbox->get_setting($listname);
    }

    /**
     * Render the given footer list.
     *
     * @param string $listname Name of the list, the setting name.
     * @param bool $targetblank If the links should open in a new tab / page.
     *
     * @return string Markup if any.
     */
    private function render_footerlist($listname, $targetblank = false) {
        $list = $this->get_footerlist($listname);
        if (!empty($list)) {
            $toolbox = \theme_lexa\toolbox::get_instance();
            $items = $toolbox->convert_text_to_items($list, current_language());
            if (!empty($items)) {
                $context = [
                    'items' => $items,
                    'targetblank' => $targetblank,
                ];
                return $this->render_from_template('theme_lexa/footerlist', $context);
            }
        }
        return '';
    }
}
