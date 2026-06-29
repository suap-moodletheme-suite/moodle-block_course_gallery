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
 * Edit form for course gallery block.
 *
 * @package    block_course_gallery
 * @copyright  2025 Your Name <you@example.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_course_gallery_edit_form extends block_edit_form {
    /**
     * Define form elements.
     *
     * @param MoodleQuickForm $mform The form.
     */
    protected function specific_definition($mform) {
        // Seção para as configurações.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $mform->addElement('text', 'config_gallery_title', get_string('add_title', 'block_course_gallery'));
        $mform->setDefault('config_gallery_title', 'Cursos abertos do IFRN');
        $mform->setType('config_gallery_title', PARAM_TEXT);

        $mform->addElement('text', 'config_max_courses', get_string('max_courses', 'block_course_gallery'));
        $mform->setType('config_max_courses', PARAM_INT);
        $mform->setDefault('config_max_courses', 9);
    }
}
