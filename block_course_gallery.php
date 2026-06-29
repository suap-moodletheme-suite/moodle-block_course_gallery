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
 * Block course_gallery is defined here.
 *
 * @package     block_course_gallery
 * @copyright   2025 Your Name <you@example.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_course_gallery extends block_base
{
    /**
     * Initializes class member variables.
     */
    public function init() {
        // Needed by Moodle to differentiate between blocks.
        $this->title = get_string('pluginname', 'block_course_gallery');
    }

    /**
     * Returns the block contents.
     *
     * @return stdClass The block contents.
     */
    public function get_content() {

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        global $OUTPUT;

        $data = [
            'gallery_title' => $this->config->gallery_title ?? "Cursos abertos do IFRN",
        ];

        $this->content = new stdClass();
        $this->content->text = $OUTPUT->render_from_template('block_course_gallery/header', $data);
        $this->content->footer = '';

        $this->content->text .= $this->render_courses();
        $this->content->text .= $OUTPUT->render_from_template('block_course_gallery/pagination', []);

        return $this->content;
    }

    /**
     * Defines configuration data.
     *
     * The function is called immediately after init().
     */
    public function specialization() {
        // Para retirar o título.
        $this->title = '';

        global $CFG;
        $coursesrequesturl = $CFG->wwwroot . '/blocks/course_gallery/api/get_courses.php';
        $this->page->requires->js_call_amd(
            'block_course_gallery/main',
            'init',
            [$coursesrequesturl, $this->config->max_courses ?? 3]
        );

        // Initialize noUiSlider.
        $this->page->requires->css(new moodle_url('https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.css'));
        $this->page->requires->js(new moodle_url('https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.js'), true);

        $this->page->requires->js_call_amd('block_course_gallery/noUiSlider', 'init');
    }

    /**
     * Renders the skeleton loader markup for courses.
     *
     * @return string The rendered HTML.
     */
    protected function render_courses() {
        global $OUTPUT;

        $output = html_writer::start_div('course-area');

        for ($i = 0; $i < 8; $i++) {
            $output .= html_writer::start_div('course-gallery-card');

            $output .= html_writer::start_div('course-image-container skeleton-image');
            $output .= html_writer::tag('div', '', ['class' => 'skeleton-image-placeholder skeleton']);
            $output .= html_writer::end_div();

            $output .= html_writer::tag('span', '', ['class' => 'skeleton-category skeleton']);
            $output .= html_writer::tag('p', '', ['class' => 'skeleton-name skeleton']);

            $output .= html_writer::end_div();
        }

        $output .= html_writer::end_div();

        return $output;
    }

    /**
     * Sets the applicable formats for the block.
     *
     * @return string[] Array of pages and permissions.
     */
    public function applicable_formats() {
        return [
            'admin' => false,
            'site-index' => true,
            'course-view' => false,
            'mod' => false,
            'my' => true,
        ];
    }
}
