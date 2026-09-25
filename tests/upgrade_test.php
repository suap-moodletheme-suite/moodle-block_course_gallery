<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace block_course_gallery;

/**
 * PHPUnit tests for block_course_gallery upgrade steps.
 *
 * @package    block_course_gallery
 * @category   test
 * @copyright  2026 Kelson da Costa Medeiros <kelsoncm@gmail.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \xmldb_block_course_gallery_upgrade
 */
final class upgrade_test extends \advanced_testcase {
    /**
     * Test upgrade populates empty block instance categories with eligible course categories.
     */
    public function test_upgrade_populates_empty_block_instances(): void {
        global $CFG, $DB;

        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator();

        // Create categories and courses with self enrolment.
        $cat1 = $generator->create_category(['name' => 'Category 1']);
        $cat2 = $generator->create_category(['name' => 'Category 2']);

        $course1 = $generator->create_course(['category' => $cat1->id, 'fullname' => 'Course 1']);
        $course2 = $generator->create_course(['category' => $cat2->id, 'fullname' => 'Course 2']);

        $enrolplugin = enrol_get_plugin('self');
        $enrolplugin->add_instance($course1);
        $enrolplugin->add_instance($course2);

        // Instance 1: empty configuration.
        $instance1 = (object)[
            'blockname' => 'course_gallery',
            'parentcontextid' => 1,
            'showinsubcontexts' => 0,
            'pagetypepattern' => 'site-index',
            'subpagepattern' => null,
            'defaultregion' => 'side-pre',
            'defaultweight' => 0,
            'configdata' => '',
            'timecreated' => time(),
            'timemodified' => time(),
        ];
        $instance1->id = $DB->insert_record('block_instances', $instance1);

        // Instance 2: already configured with cat1.
        $config2 = new \stdClass();
        $config2->categories = [(int)$cat1->id];
        $instance2 = (object)[
            'blockname' => 'course_gallery',
            'parentcontextid' => 1,
            'showinsubcontexts' => 0,
            'pagetypepattern' => 'site-index',
            'subpagepattern' => null,
            'defaultregion' => 'side-pre',
            'defaultweight' => 0,
            'configdata' => base64_encode(serialize($config2)),
            'timecreated' => time(),
            'timemodified' => time(),
        ];
        $instance2->id = $DB->insert_record('block_instances', $instance2);

        set_config('version', 2026092508, 'block_course_gallery');

        require_once($CFG->libdir . '/upgradelib.php');
        require_once(__DIR__ . '/../db/upgrade.php');

        $result = xmldb_block_course_gallery_upgrade(2026092508);
        $this->assertTrue($result);

        // Verify Instance 1 was updated with both cat1 and cat2.
        $record1 = $DB->get_record('block_instances', ['id' => $instance1->id]);
        $configdata1 = unserialize(base64_decode($record1->configdata));
        $this->assertNotEmpty($configdata1->categories);
        $this->assertContains((int)$cat1->id, $configdata1->categories);
        $this->assertContains((int)$cat2->id, $configdata1->categories);

        // Verify Instance 2 was not overwritten.
        $record2 = $DB->get_record('block_instances', ['id' => $instance2->id]);
        $configdata2 = unserialize(base64_decode($record2->configdata));
        $this->assertEquals([(int)$cat1->id], $configdata2->categories);
    }
}
