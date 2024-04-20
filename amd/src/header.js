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
 * JS module for the header.
 *
 * @module     theme_lexa/header
 * @copyright  2024 G J Barnard.
 *               {@link https://gjbarnard.co.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

import jQuery from 'jquery';
import log from 'core/log';

/**
 * Has this module been initialised?
 *
 * @type {boolean}
 */
let initialised = false;

/**
 * Scrolled?
 *
 * @type {boolean}
 */
let scrolled = false;

/**
 * Header height.
 *
 * @type {int} In pixels.
 */
let headerHeight = 0;

/**
 * Window scroll Y.
 *
 * @type {int} In pixels.
 */
let windowScrollY = 0;

/**
 * Body element.
 *
 * @type {element} The body element.
 */
const body = document.querySelector('body');

const stickyheader = () => {
    windowScrollY = window.scrollY;
    log.debug("windowScrollY: " + windowScrollY);
    if (windowScrollY > headerHeight) {
        // Greater than first row.
        if (!scrolled) {
            body.classList.add('lexascrolled');
            document.documentElement.style.setProperty('--lexa-navbar-pos', headerHeight + 'px');
            scrolled = true;
        }
    } else {
        if (scrolled) {
            body.classList.remove('lexascrolled');
            scrolled = false;
        }
        document.documentElement.style.setProperty('--lexa-navbar-pos', (headerHeight + (headerHeight - windowScrollY)) + 'px');
    }
};

/**
 * Function to intialise this module.
 *
 * @param {int} theHeaderHeight The header height.
 */
export const init = (theHeaderHeight) => {
    log.debug('Lexa theme header JS init: ' + theHeaderHeight);
    if (initialised) {
        log.debug('Lexa theme header JS init already initialised');
        return;
    }
    initialised = true;
    headerHeight = theHeaderHeight;
    jQuery(document).ready(function() {
        document.addEventListener('scroll', () => {
            stickyheader();
        });
        stickyheader();
    });
};
