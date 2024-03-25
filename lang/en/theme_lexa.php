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

$string['choosereadme'] = '<div class="clearfix"><h2>Lexa</h2>'.
'<h3>About</h3>'.
'<p>Lexa is a child theme of the Boost theme.</p>'.
'<h3>Theme Credits</h3>'.
'<p>Author: G J Barnard<br>'.
'Contact: <a href="http://moodle.org/user/profile.php?id=442195">Moodle profile</a><br>'.
'Website: <a href="https://gjbarnard.co.uk">gjbarnard.co.uk</a>'.
'</p>'.
'<h3>More information</h3>'.
'<p><a href="lexa/Readme.md">How to use this theme.</a></p>'.
'</div></div>';

$string['configtitle'] = 'Lexa';
$string['pluginname'] = 'Lexa';

$string['region-side-pre'] = 'Right';

// Settings.
$string['configtabtitle'] = 'Settings';

// General.
$string['generalheading'] = 'General';
$string['generalheadingsub'] = 'General settings';
$string['generalheadingdesc'] = 'Configure the general settings.';

$string['mod_booking_codes'] = 'Booking module code(s)';
$string['mod_booking_codesdesc'] = 'Enter the <a href="https://moodle.org/plugins/mod_booking" title="Booking module" target="_blank">Booking module codes</a>';

// Footer.
$string['footerheading'] = 'Footer';
$string['footerheadingsub'] = 'Footer settings';
$string['footerheadingdesc'] = 'Configure the footer settings.';

$string['footercourseofferings'] = 'Course offerings';
$string['footercourseofferingsdesc'] = 'Enter the course offerings, for example:
<pre>
First item|http://www.moodle.com/|||fa-solid fa-user-graduate
Second item|http://www.moodle.com/feedback/
Third item
English only|http://moodle.com|English only item|en
German only|http://moodle.de|Deutsch|de,de_du,de_kids
</pre>';

$string['footercommunities'] = 'Communities';
$string['footercommunitiesdesc'] = 'Enter the communities, for example:
<pre>
Fourth item|http://www.moodle.com/
Fifth item|http://www.moodle.com/feedback/|||fa-solid fa-people-roof
Sixth item
English only|http://moodle.com|English only item|en
German only|http://moodle.de|Deutsch|de,de_du,de_kids
</pre>';

$string['footercontactus'] = 'Contact us';
$string['footercontactusdesc'] = 'Enter the contacts, for example:
<pre>
Seventh item|http://www.moodle.com/
Eighth item|http://www.moodle.com/feedback/
Ninth item
English only|http://moodle.com|English only item|en
German only|http://moodle.de|Deutsch|de,de_du,de_kids
</pre>';

$string['footersocial'] = 'Social';
$string['footersocialdesc'] = 'Enter the social links, for example:
<pre>
|http://www.facebook.com/|My Facebook||fa-brands fa-facebook
|http://www.instagram.com/|My Instagram||fa-brands fa-instagram
|http://www.youtube.com/|My Youtube||fa-brands fa-youtube
</pre>';

$string['footerformat'] = 'In the format of \'text|url|title|langs|fontawesome classes\'.  Only enter what you require.  If you don\'t need something but do need another thing further along then leave it blank but still use the \'|\' delimiter.  For example: \'text|url|||fontawesome classes\'.';
$string['footerfontawesomenote'] = 'To find the FontAwesome(Free) classes for the icon you wish to use, go to <a href="https://fontawesome.com/search?o=r&m=free" target="_blank">Font Awesome</a> and search for the icon.  Both CSS classes are required, such as \'fa-solid fa-user-graduate\'.  The installed version of FontAwesome is 6.4.0, so if it is for above then it will not be available.';

// Privacy.
$string['privacynote'] = 'Note: The Lexa theme stores has settings that pertain to its configuration.  Specific user settings are described in the \'Plugin privacy registry\'.  For the other settings, it is your responsibility to ensure that no user data is entered in any of the free text fields.  Setting a setting will result in that action being logged within the core Moodle logging system against the user whom changed it, this is outside of the themes control, please see the core logging system for privacy compliance for this.  When uploading images, you should avoid uploading images with embedded location data (EXIF GPS) included or other such personal data.  It would be possible to extract any location / personal data from the images.  Please examine the code carefully to be sure that it complies with your interpretation of your privacy laws.  I am not a lawyer and my analysis is based on my interpretation.  If you have any doubt then remove the theme forthwith.';
$string['privacy:closed'] = 'Closed';
$string['privacy:open'] = 'Open';
$string['privacy:metadata:preference:draweropenindex'] = 'The state of the course index.';
$string['privacy:request:preference:draweropenindex'] = 'The user preference "{$a->name}" has the value "{$a->value}" which represents "{$a->decoded}" for the state of the course index.';
$string['privacy:metadata:preference:draweropenblock'] = 'The state of the block drawer.';
$string['privacy:request:preference:draweropenblock'] = 'The user preference "{$a->name}" has the value "{$a->value}" which represents "{$a->decoded}" for the state of the block drawer.';
