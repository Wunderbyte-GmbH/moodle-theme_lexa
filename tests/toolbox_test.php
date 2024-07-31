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

namespace theme_lexa;

/**
 * Toolbox unit tests for the Lexa theme.
 * @group theme_lexa
 */
class toolbox_test extends \advanced_testcase {
    protected function setUp(): void {
        $this->resetAfterTest(true);

        set_config('theme', 'lexa');
    }

    /**
     * Test convert text to items.
     *
     * @covers ::convert_text_to_items
     */
    public function test_convert_text_to_items() {
        $toolbox = \theme_lexa\toolbox::get_instance();

        $testitems = 'First item|https://testsite.localhost/|First item title'. PHP_EOL.
            'Second item|https://testsite.localhost/|Second item title|en'. PHP_EOL.
            'Zweite item|https://testsite.localhost/|Zweite item title|de'. PHP_EOL.
            '|https://testsite.localhost/|FontAwesome test||fa-solid fa-font-awesome';

        $enitems = $toolbox->convert_text_to_items($testitems, 'en');
        $this->assertCount(3, $enitems);
        $this->assertEquals('First item', $enitems[0]['itemtext']);
        $this->assertEquals('https://testsite.localhost/', $enitems[0]['itemurl']->out());
        $this->assertEquals('First item title', $enitems[0]['itemtitle']);
        $this->assertEquals('Second item', $enitems[1]['itemtext']);
        $this->assertEquals('https://testsite.localhost/', $enitems[1]['itemurl']->out());
        $this->assertEquals('Second item title', $enitems[1]['itemtitle']);
        $this->assertEquals('https://testsite.localhost/', $enitems[2]['itemurl']->out());
        $this->assertEquals('FontAwesome test', $enitems[2]['itemtitle']);
        $this->assertEquals('fa-solid fa-font-awesome', $enitems[2]['faclasses']);

        $deitems = $toolbox->convert_text_to_items($testitems, 'de');
        $this->assertCount(3, $deitems);
        $this->assertEquals('First item', $deitems[0]['itemtext']);
        $this->assertEquals('https://testsite.localhost/', $deitems[0]['itemurl']->out());
        $this->assertEquals('First item title', $deitems[0]['itemtitle']);
        $this->assertEquals('Zweite item', $deitems[1]['itemtext']);
        $this->assertEquals('https://testsite.localhost/', $deitems[1]['itemurl']->out());
        $this->assertEquals('Zweite item title', $deitems[1]['itemtitle']);
        $this->assertEquals('https://testsite.localhost/', $deitems[2]['itemurl']->out());
        $this->assertEquals('FontAwesome test', $deitems[2]['itemtitle']);
        $this->assertEquals('fa-solid fa-font-awesome', $deitems[2]['faclasses']);

        $allitems = $toolbox->convert_text_to_items($testitems);
        $this->assertCount(4, $allitems);
        $this->assertEquals('First item', $allitems[0]['itemtext']);
        $this->assertEquals('https://testsite.localhost/', $allitems[0]['itemurl']->out());
        $this->assertEquals('First item title', $allitems[0]['itemtitle']);
        $this->assertEquals('Second item', $allitems[1]['itemtext']);
        $this->assertEquals('https://testsite.localhost/', $allitems[1]['itemurl']->out());
        $this->assertEquals('Second item title', $allitems[1]['itemtitle']);
        $this->assertEquals('Zweite item', $allitems[2]['itemtext']);
        $this->assertEquals('https://testsite.localhost/', $allitems[2]['itemurl']->out());
        $this->assertEquals('Zweite item title', $allitems[2]['itemtitle']);
        $this->assertEquals('https://testsite.localhost/', $allitems[3]['itemurl']->out());
        $this->assertEquals('FontAwesome test', $allitems[3]['itemtitle']);
        $this->assertEquals('fa-solid fa-font-awesome', $allitems[3]['faclasses']);
    }
}
