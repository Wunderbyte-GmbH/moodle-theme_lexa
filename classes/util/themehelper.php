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
 * Custom theme_boost_union functions
 *
 * @package    theme_boost_union
 * @copyright  2023 Thomas Winkler
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_lexa\util;

use moodle_url;

defined('MOODLE_INTERNAL') || die();

/**
 * Class to get some extras info in Moodle.
 *
 * @package    theme_boost_union
 * @copyright  2023 Thomas Winkler
 *
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class themehelper {

    /**
     * Returns the first course's summary issue
     *
     * @param \core_course_list_element $course
     * @param string $courselink
     *
     * @return string
     */
    public static function get_course_summary_image($course, $courselink) {
        global $CFG;
        $contentimage = '';
        foreach ($course->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            $url = \moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php",
                '/' . $file->get_contextid() . '/' . $file->get_component() . '/' .
                $file->get_filearea() . $file->get_filepath() . $file->get_filename(), !$isimage);
            if ($isimage) {
                $contentimage = \html_writer::link($courselink, \html_writer::empty_tag('img', array(
                    'src' => $url,
                    'alt' => $course->fullname,
                    'class' => 'card-img-top w-100')));
                break;
            }
        }

        if (empty($contentimage)) {
            $url = $CFG->wwwroot . "/theme/boost_union/pix/default_course.jpg";

            $contentimage = \html_writer::link($courselink, \html_writer::empty_tag('img', array(
                'src' => $url,
                'alt' => $course->fullname,
                'class' => 'card-img-top w-100')));
        }

        return $contentimage;
    }

    /**
     * Returns the user picture
     *
     * @param null $userobject
     * @param int $imgsize
     *
     * @return \moodle_url
     * @throws \coding_exception
     */
    public static function get_user_picture($userobject = null, $imgsize = 100) {
        global $USER, $PAGE;

        if (!$userobject) {
            $userobject = $USER;
        }

        $userimg = new \user_picture($userobject);

        $userimg->size = $imgsize;

        return $userimg->get_url($PAGE);
    }

    /**
     * Returns nuber of enrolled users
     *
     * @param int $courseid
     * @return int $numenrolments
     */
    public static function get_number_of_enrolled_users($courseid) {
        $context = \context_course::instance($courseid);
        $enrolledusers = get_enrolled_users($context);

        $numenrolments = count($enrolledusers);
        return $numenrolments;
    }

    /**
     * Returns rating of the course if setting is activated
     *
     * @param int $courseid
     * @return string
     */
    public static function get_course_rating($courseid) {
        global $CFG;
       /*
       if (!get_config('theme_boost_union', 'courserating')) {
            return '';
        } */
        $plugin = \core_plugin_manager::instance()->get_plugin_info('tool_courserating');
        if ($plugin) {
            $persratingdata = \tool_courserating\local\models\summary::get_for_course($courseid);
            $rating = $persratingdata->get('avgrating');
            if ($rating) {
                $courserating = '';
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= floor($rating)) {
                        // Output a filled star
                        $courserating .= '<i class="fa fa-star text-warning"></i>';
                    } else if ($rating - $i + 0.5 >= 0) {
                        // Output a half-filled star
                        $courserating .= '<i class="fa fa-star-half-o text-warning"></i>';
                    } else {
                        // Output an unfilled star
                        $courserating .= '<i class="fa fa-star-o text-warning"></i>';
                    }
                }
            } else {
                return '';
            }
        } else {
            return '';
        }
        return $courserating;
    }
}
