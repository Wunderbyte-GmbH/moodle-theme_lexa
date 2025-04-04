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

namespace theme_lexa\output\core_user\myprofile;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/user/classes/output/myprofile/renderer.php');

use core_user\output\myprofile\category;
use core_user\output\myprofile\node;


/**
 * Custom myprofile renderer for theme_lexa.
 *
 * @package    theme_lexa
 * @copyright  2025 Thomas Winkler
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \core_user\output\myprofile\renderer {
    /**
     * Render a category.
     *
     * @param category $category
     *
     * @return string
     */
    public function render_category(category $category) {
        $classes = $category->classes;

        if (empty($classes)) {
            $return = \html_writer::start_tag(
                'section',
                ['class' => 'node_category card d-inline-block w-100 mb-3', 'data-profile-block' => $category->name]
            );
            $return .= \html_writer::start_tag(
                'div',
                ['class' => 'card-body']
            );
        } else {
            $return = \html_writer::start_tag(
                'section',
                ['class' => 'node_category card d-inline-block w-100 mb-3' . $classes]
            );
            $return .= \html_writer::start_tag(
                'div',
                ['class' => 'card-body']
            );
        }
        $return .= \html_writer::tag('h3', $category->title, ['class' => 'lead']);
        $nodes = $category->nodes;
        if (empty($nodes)) {
            // No nodes, nothing to render.
            return '';
        }
        $return .= \html_writer::start_tag('ul');
        foreach ($nodes as $node) {
            $return .= $this->render($node);
        }
        $return .= \html_writer::end_tag('ul');
        $return .= \html_writer::end_tag('div');
        $return .= \html_writer::end_tag('section');
        return $return;
    }
    /**
     * Render a node.
     *
     * @param node $node
     *
     * @return string
     */
    public function render_node(node $node) {
        $return = '';
        if (is_object($node->url)) {
            $header = \html_writer::link($node->url, $node->title, ['data-node-title' => $node->name]);
        } else {
            $header = $node->title;
        }
        $icon = $node->icon;
        if (!empty($icon)) {
            $header .= $this->render($icon);
        }
        $content = $node->content;
        $classes = $node->classes;
        if (!empty($content)) {
            if ($header) {
                // There is some content to display below this make this a header.
                $return = \html_writer::tag('dt', $header, ['data-node-title' => $node->name]);
                $return .= \html_writer::tag('dd', $content, ['data-node-title' => $node->name]);

                $return = \html_writer::tag('dl', $return, ['data-node-title' => $node->name]);
            } else {
                $return = \html_writer::span($content);
            }
            if ($classes) {
                $return = \html_writer::tag('li', $return, ['class' => 'contentnode ' . $classes, 'data-node-title' => $node->name]);
            } else {
                $return = \html_writer::tag('li', $return, ['class' => 'contentnode', 'data-node-title' => $node->name]);
            }
        } else {
            $return = \html_writer::span($header);
            $return = \html_writer::tag('li', $return, ['class' => $classes, 'data-node-title' => $node->name]);
        }

        return $return;
    }
}
