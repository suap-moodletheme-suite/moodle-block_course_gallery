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

use moodle_database;

/**
 * Course repository class for block_course_gallery.
 *
 * Encapsulates SQL queries and category hierarchy/visibility scoping logic.
 *
 * @package    block_course_gallery
 * @copyright  2026 Kelson da Costa Medeiros <kelsoncm@gmail.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_repository {

    /** @var moodle_database Database instance. */
    protected $db;

    /**
     * Constructor.
     *
     * @param moodle_database|null $db Database instance to use, defaults to global $DB.
     */
    public function __construct(?moodle_database $db = null) {
        global $DB;
        $this->db = $db ?? $DB;
    }

    /**
     * Get eligible category IDs considering category hierarchy and visibility rules.
     *
     * A category is eligible if:
     * - It is one of the configured categories OR a descendant subcategory of a configured category.
     * - It is visible (visible = 1) AND none of its ancestor categories in path are hidden (visible = 0).
     *
     * @param array $configuredcatids Array of configured category IDs.
     * @return array Array of eligible category IDs.
     */
    public function get_eligible_category_ids(array $configuredcatids): array {
        if (empty($configuredcatids)) {
            return [];
        }

        $configuredcatids = array_values(array_unique(array_map('intval', array_filter($configuredcatids, function($v) {
            return is_numeric($v) && intval($v) > 0;
        }))));

        if (empty($configuredcatids)) {
            return [];
        }

        $allcategories = $this->db->get_records('course_categories', null, '', 'id, name, path, visible');
        if (empty($allcategories)) {
            return [];
        }

        // Identify fully visible categories (category visible = 1 AND all ancestors visible = 1).
        $visiblecatids = [];
        foreach ($allcategories as $cat) {
            if (empty($cat->path)) {
                continue;
            }
            $pathids = array_filter(explode('/', trim($cat->path, '/')), 'is_numeric');
            $allvisible = true;
            foreach ($pathids as $pathid) {
                if (isset($allcategories[$pathid]) && (int)$allcategories[$pathid]->visible === 0) {
                    $allvisible = false;
                    break;
                }
            }
            if ($allvisible) {
                $visiblecatids[$cat->id] = true;
            }
        }

        // Expand configured categories to include their descendants, only if eligible.
        $eligiblecatids = [];
        foreach ($configuredcatids as $catid) {
            if (!isset($allcategories[$catid]) || !isset($visiblecatids[$catid])) {
                continue;
            }

            $catpath = $allcategories[$catid]->path;
            $prefix = $catpath . '/';

            foreach ($allcategories as $candidate) {
                if (!isset($visiblecatids[$candidate->id])) {
                    continue;
                }
                if ($candidate->id == $catid || strpos($candidate->path, $prefix) === 0) {
                    $eligiblecatids[$candidate->id] = (int)$candidate->id;
                }
            }
        }

        return array_values($eligiblecatids);
    }

    /**
     * Get course records according to configured category IDs and filter criteria.
     *
     * @param array $configuredcatids Array of configured category IDs.
     * @param array $filters Filter params such as 'search', 'learningpath'.
     * @return array Array of course DB records matching the scope.
     */
    public function get_courses(array $configuredcatids, array $filters = []): array {
        $eligiblecatids = $this->get_eligible_category_ids($configuredcatids);
        if (empty($eligiblecatids)) {
            return [];
        }

        [$catsql, $catparams] = $this->db->get_in_or_equal($eligiblecatids, SQL_PARAMS_NAMED, 'cat');

        $sqlconditions = ["c.category {$catsql}"];
        $params = $catparams;

        $query = $filters['search'] ?? '';
        if (!empty($query)) {
            $sqlconditions[] = "LOWER(c.fullname) LIKE LOWER(:query)";
            $params['query'] = '%' . $query . '%';
        }

        $learningpath = $filters['learningpath'] ?? '';
        if (!empty($learningpath)) {
            $learningpathvalues = is_array($learningpath) ? $learningpath : explode(',', $learningpath);
            $learningpathvalues = array_map('intval', array_filter($learningpathvalues, 'is_numeric'));
            if (!empty($learningpathvalues)) {
                [$lpsql, $lpparams] = $this->db->get_in_or_equal($learningpathvalues, SQL_PARAMS_NAMED, 'lp');
                $sqlconditions[] = "c.id IN (SELECT courseid FROM {suap_learning_path_course} WHERE learningpathid {$lpsql})";
                $params = array_merge($params, $lpparams);
            }
        }

        $where = implode(' AND ', $sqlconditions);

        $sql = "
            SELECT c.id, c.fullname, c.category
            FROM {course} c
            INNER JOIN {enrol} e ON (c.id = e.courseid)
            WHERE c.visible = 1 AND c.id != 1 AND e.enrol = 'self' AND e.status = 0 AND {$where}
            ORDER BY c.id DESC
        ";

        return $this->db->get_records_sql($sql, $params);
    }
}
