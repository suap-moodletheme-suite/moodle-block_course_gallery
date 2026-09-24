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

defined('MOODLE_INTERNAL') || die();

/**
 * PHPUnit tests for course_repository class.
 *
 * @package    block_course_gallery
 * @category   test
 * @copyright  2026 Kelson da Costa Medeiros <kelsoncm@gmail.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \block_course_gallery\course_repository
 */
class course_repository_test extends \advanced_testcase {

    /**
     * Test get_eligible_category_ids with empty configuration.
     */
    public function test_get_eligible_category_ids_empty() {
        $this->resetAfterTest(true);
        $repository = new course_repository();
        $this->assertEmpty($repository->get_eligible_category_ids([]));
    }

    /**
     * Test category hierarchy expansion and subcategory inclusion.
     */
    public function test_get_eligible_category_ids_subcategories() {
        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator();

        $catparent = $generator->create_category(['name' => 'Parent Category']);
        $catchild = $generator->create_category(['name' => 'Child Category', 'parent' => $catparent->id]);

        $repository = new course_repository();
        $eligible = $repository->get_eligible_category_ids([$catparent->id]);

        $this->assertContains((int)$catparent->id, $eligible);
        $this->assertContains((int)$catchild->id, $eligible);
    }

    /**
     * Test hidden category and hidden ancestor rules.
     */
    public function test_get_eligible_category_ids_hidden_ancestor() {
        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator();

        $catparent = $generator->create_category(['name' => 'Parent Category', 'visible' => 0]);
        $catchild = $generator->create_category(['name' => 'Child Category', 'parent' => $catparent->id, 'visible' => 1]);

        $repository = new course_repository();
        $eligible = $repository->get_eligible_category_ids([$catchild->id]);

        $this->assertEmpty($eligible);
    }

    /**
     * Test querying courses with category scoping and enrolments.
     */
    public function test_get_courses_category_scoping() {
        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator();

        $cat1 = $generator->create_category(['name' => 'Category 1']);
        $cat2 = $generator->create_category(['name' => 'Category 2']);

        $course1 = $generator->create_course(['category' => $cat1->id, 'fullname' => 'Course 1 in Cat 1']);
        $course2 = $generator->create_course(['category' => $cat2->id, 'fullname' => 'Course 2 in Cat 2']);

        // Add self enrol method.
        $enrolplugin = enrol_get_plugin('self');
        $enrolplugin->add_instance($course1);
        $enrolplugin->add_instance($course2);

        $repository = new course_repository();

        // Scope to Cat 1.
        $courses = $repository->get_courses([$cat1->id]);
        $courseids = array_keys($courses);

        $this->assertContains((int)$course1->id, $courseids);
        $this->assertNotContains((int)$course2->id, $courseids);
    }
}
