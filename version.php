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
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;

$plugin->version = 2024110505;
$plugin->requires = 2024100700.00; // 4.5 (Build: 20241007).
$plugin->supported = [405, 405];
$plugin->component = 'theme_lexa';
$plugin->maturity = MATURITY_ALPHA;
$plugin->release = '405.0.1';
$plugin->dependencies = [
    'theme_boost'  => 2024100700, // M4.5.
    'theme_boost_union'  => 2024100701, // v4.5-r2.
];
