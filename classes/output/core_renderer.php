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

use core\exception\coding_exception;
use core\output\html_writer;
use core_block\output\block_contents;
use core_block\output\block_move_target;
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
     * Returns standard main content placeholder.
     * Designed to be called in theme layout.php files.
     *
     * @return string HTML fragment.
     */
    public function main_content() {
        // This is here because it is the only place we can inject the "main" role over the entire main content area
        // without requiring all theme's to manually do it, and without creating yet another thing people need to
        // remember in the theme.  But in 'Lexa' this is needed to be overridden because of the landing block region
        // being complimentary, therefore the 'main' role is specified in the the drawers.mustache file.
        // This is an unfortunate hack. DO NO EVER add anything more here.
        // DO NOT add classes.
        // DO NOT add an id.
        return '<div>' . $this->unique_main_content_token . '</div>';
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
                    preg_match('/\[(.*?)\]/', $bc->title, $matches);
                    if (isset($matches[1])) {
                        $bc->attributes['class'] .= ' '.$matches[1];
                    }
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
     * Returns HTML to display course custom fields.
     *
     * @param core_course_list_element $course
     * @return string
     */
    protected function course_custom_fields(core_course_list_element $course): string {

        $content = '';

        if ($course->has_custom_fields()) {
            $handler = \core_course\customfield\course_handler::create();
            $customfields = $handler->display_custom_fields_data($course->get_custom_fields());
            // TODO show enrolled users.
            if(get_config('theme_boost_union', 'enrolledusersenabled') == THEME_BOOST_UNION_SETTING_SELECT_YES) {
                $enrolleduser = themehelper::get_number_of_enrolled_users($course->id);
                $customfields .= '<div class="customfield customfield_text customfield_enrolledusers">
                <span class="customfieldname">' . get_string('participants', 'theme_boost_union') .
                '</span><span class="customfieldseparator">: </span><span class="customfieldvalue">' . $enrolleduser .
                '<span class="mx-2 fa fa-user-o"></span></span></div>';
            }
            $content .= \html_writer::tag('div', $customfields, ['class' => 'customfields-container']);
        }

        return $content;
    }

    /**
     * Generates the course card footer html
     *
     * @param core_course_list_element $course
     *
     * @return string
     *
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    protected function course_card_footer(core_course_list_element $course) {
        if (isloggedin()) {

            $content = html_writer::start_tag('div', ['class' => 'card-footer']);
            $content .= $this->course_custom_fields($course);
            $content .= html_writer::start_tag('div', ['class' => 'pull-right']);
            $content .= html_writer::link(new moodle_url('/course/view.php', ['id' => $course->id]),
                get_string('view', 'core'), ['class' => 'card-link btn btn-primary']);

            $content .= html_writer::end_tag('div'); // End pull-right.

            $content .= html_writer::end_tag('div'); // End card-footer.
        }

        return $content;
    }
}
