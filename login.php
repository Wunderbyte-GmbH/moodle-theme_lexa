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
 * Theme Boost Union - Local login page
 *
 * This file is copied, reduced and modified from /login/index.php.
 *
 * @package   theme_boost_union
 * @copyright 2023 Alexander Bias <bias@alexanderbias.de>
 *            based on code 1999 onwards Martin Dougiamas
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Include config.php.
// Let codechecker ignore the next line because otherwise it would complain about a missing login check
// after requiring config.php which is really not needed.
require(__DIR__.'/../../config.php'); // phpcs:disable moodle.Files.RequireLogin.Missing

// Require the necessary libraries.
require_once($CFG->dirroot.'/lib/authlib.php');
require_once($CFG->dirroot.'/theme/boost_union/lib.php');

unset($SESSION->loginerrormsg);
unset($SESSION->logininfomsg);

$errorcode = optional_param('errorcode', 0, PARAM_INT);

// Set page URL.
$PAGE->set_url('/theme/lexa/login.php');

// Set page layout.
$PAGE->set_pagelayout('login');

// Set page context.
$PAGE->set_context(context_system::instance());

// Set page title.
$PAGE->set_title(get_string('loginsite'));

// Start page output.
echo $OUTPUT->header();

if ($errorcode) {
    if ($errorcode == AUTH_LOGIN_UNAUTHORISED) {
        $errormsg = get_string("unauthorisedlogin", "", $frm->username);
    } else if ($errorcode == AUTH_LOGIN_FAILED_RECAPTCHA) {
        $errormsg = get_string('missingrecaptchachallengefield');
    } else {
        $errormsg = get_string("invalidlogin");
        $errorcode = 3;
    }
}

// Prepare the local login form.
$templatecontext = [];
$templatecontext['errormsg'] = $errormsg;
$templatecontext['loginurl'] = new moodle_url('/login/index.php');
$templatecontext['guesturl'] = new moodle_url('/login/signup.php');
$templatecontext['forgetpassword'] = new moodle_url('/login/forgot_password.php');
$templatecontext['saml'] = new moodle_url('/auth/saml2/login.php?wants&idp=3c014ae427321cca2476687bc9724e54&passive=off');
$templatecontext['logintoken'] = \core\session\manager::get_login_token();
// Output the local login form.
echo $OUTPUT->render_from_template('theme_lexa/localloginform', $templatecontext);

// Finish page.
echo $OUTPUT->footer();
