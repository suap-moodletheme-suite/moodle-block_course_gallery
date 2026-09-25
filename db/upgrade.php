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

/**
 * Upgrade steps for block_course_gallery.
 *
 * @package    block_course_gallery
 * @copyright  2026 Kelson da Costa Medeiros <kelsoncm@gmail.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute block_course_gallery upgrade steps.
 *
 * @param int $oldversion The version we are upgrading from.
 * @return bool True on success.
 */
function xmldb_block_course_gallery_upgrade($oldversion) {
    global $DB;

    if ($oldversion < 2026092508) {
        $sql = "SELECT DISTINCT c.category
                  FROM {course} c
            INNER JOIN {enrol} e ON (c.id = e.courseid)
                 WHERE c.visible = 1 AND c.id != 1 AND e.enrol = 'self' AND e.status = 0";
        $records = $DB->get_records_sql($sql);
        $categoryids = [];
        if (!empty($records)) {
            foreach ($records as $record) {
                if (!empty($record->category)) {
                    $categoryids[] = (int) $record->category;
                }
            }
        }

        if (empty($categoryids)) {
            $catfield = $DB->get_fieldset_select('course_categories', 'id', '');
            if (!empty($catfield)) {
                $categoryids = array_map('intval', $catfield);
            }
        }

        $categoryids = array_values(array_unique($categoryids));

        if (!empty($categoryids)) {
            $blockinstances = $DB->get_records('block_instances', ['blockname' => 'course_gallery']);
            foreach ($blockinstances as $instance) {
                $config = !empty($instance->configdata) ? unserialize(base64_decode($instance->configdata)) : new stdClass();
                if (!is_object($config)) {
                    $config = new stdClass();
                }

                $hascategories = false;
                if (!empty($config->categories)) {
                    if (is_array($config->categories) && count($config->categories) > 0) {
                        $hascategories = true;
                    } else if (is_string($config->categories) && trim($config->categories) !== '') {
                        $hascategories = true;
                    }
                }

                if (!$hascategories) {
                    $config->categories = $categoryids;
                    $instance->configdata = base64_encode(serialize($config));
                    $DB->update_record('block_instances', $instance);
                }
            }
        }

        upgrade_block_savepoint(true, 2026092508, 'course_gallery');
    }

    return true;
}
