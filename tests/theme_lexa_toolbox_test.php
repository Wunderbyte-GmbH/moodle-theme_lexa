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
 * Define unit tests for the toolbox class.
 *
 * @package    theme_lexa
 * @copyright  2024 G J Barnard.
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

/**
 * Toolbox unit tests for the Lexa theme.
 * @group theme_lexa
 */
class theme_lexa_toolbox_test extends advanced_testcase {
    protected function setUp(): void {
        $this->resetAfterTest(true);

        set_config('theme', 'lexa');
    }

    public function test_convert_text_to_items() {
        $toolbox = \theme_lexa\toolbox::get_instance();

        $testitems = 'First item|https://testsite.localhost/|First item title'. PHP_EOL.
            'Second item|https://testsite.localhost/|Second item title|en'. PHP_EOL.
            'Zweite item|https://testsite.localhost/|Zweite item title|de';

        $enitems = $toolbox->convert_text_to_items($testitems, 'en');
        $this->assertCount(2, $enitems);
        $this->assertEquals('First item', $enitems[0]['itemtext']);
        $this->assertEquals('https://testsite.localhost/', $enitems[0]['itemurl']->out());
        $this->assertEquals('First item title', $enitems[0]['itemtitle']);
        $this->assertEquals('Second item', $enitems[1]['itemtext']);
        $this->assertEquals('https://testsite.localhost/', $enitems[1]['itemurl']->out());
        $this->assertEquals('Second item title', $enitems[1]['itemtitle']);

        $deitems = $toolbox->convert_text_to_items($testitems, 'de');
        $this->assertCount(2, $deitems);
        $this->assertEquals('First item', $deitems[0]['itemtext']);
        $this->assertEquals('https://testsite.localhost/', $deitems[0]['itemurl']->out());
        $this->assertEquals('First item title', $deitems[0]['itemtitle']);
        $this->assertEquals('Zweite item', $deitems[1]['itemtext']);
        $this->assertEquals('https://testsite.localhost/', $deitems[1]['itemurl']->out());
        $this->assertEquals('Zweite item title', $deitems[1]['itemtitle']);

        $allitems = $toolbox->convert_text_to_items($testitems);
        $this->assertCount(3, $allitems);
        $this->assertEquals('First item', $allitems[0]['itemtext']);
        $this->assertEquals('https://testsite.localhost/', $allitems[0]['itemurl']->out());
        $this->assertEquals('First item title', $allitems[0]['itemtitle']);
        $this->assertEquals('Second item', $allitems[1]['itemtext']);
        $this->assertEquals('https://testsite.localhost/', $allitems[1]['itemurl']->out());
        $this->assertEquals('Second item title', $allitems[1]['itemtitle']);
        $this->assertEquals('Zweite item', $allitems[2]['itemtext']);
        $this->assertEquals('https://testsite.localhost/', $allitems[2]['itemurl']->out());
        $this->assertEquals('Zweite item title', $allitems[2]['itemtitle']);
    }
}