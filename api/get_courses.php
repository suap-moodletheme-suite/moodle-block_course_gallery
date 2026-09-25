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
 * API to fetch open courses for the block gallery.
 *
 * @package    block_course_gallery
 * @copyright  2025 Your Name <you@example.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// phpcs:ignore moodle.Files.RequireLogin.Missing
require_once('../../../config.php');

use core_course\course;

$currentpage = max(0, optional_param('page', 0, PARAM_INT));
$coursesperpage = max(1, optional_param('limit', 8, PARAM_INT));
$query = optional_param('search', '', PARAM_TEXT);
$workload = optional_param('workload', '', PARAM_TEXT);
$certificate = optional_param('certificate', '', PARAM_TEXT);
$learningpath = optional_param('learningpath', '', PARAM_TEXT);
$blockinstanceid = optional_param('blockinstanceid', 0, PARAM_INT);

// Nega requisições que não sejam internas.
$host = $_SERVER['HTTP_HOST'];
$referer = isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER']) : null;
if (!$referer || $referer['host'] !== $host) {
    echo json_encode(['error' => 'Access denied.']);
    die;
}

if (empty($blockinstanceid)) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(['error' => "Parâmetro 'blockinstanceid' é obrigatório."]);
    exit;
}

$blockinstance = $DB->get_record('block_instances', ['id' => $blockinstanceid]);
if (!$blockinstance) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(['error' => 'Instância de bloco inválida.']);
    exit;
}

$config = !empty($blockinstance->configdata) ? unserialize(base64_decode($blockinstance->configdata)) : new stdClass();
$configuredcategories = [];
if (!empty($config->categories)) {
    if (is_array($config->categories)) {
        $configuredcategories = $config->categories;
    } else if (is_string($config->categories)) {
        $configuredcategories = explode(',', $config->categories);
    }
}
if (!empty($learningpath)) {
    $learningpathvalues = explode(',', $learningpath);

    foreach ($learningpathvalues as $value) {
        if (empty($value) || !is_numeric($value) || $value <= 0) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "error" => "Parâmetro 'learningpath' inválido. Certifique-se de que os valores são numéricos.",
            ]);
            exit;
        }
    }
}

$categoriesrecords = $DB->get_records('course_categories', null, '', 'id, name');
$categories = [];
foreach ($categoriesrecords as $category) {
    $categoryobj = new stdClass();
    $categoryobj->name = $category->name;
    $categoryobj->url = "{$CFG->wwwroot}/course/management.php?categoryid={$category->id}";
    $categories[$category->id] = $categoryobj;
}

$repository = new \block_course_gallery\course_repository($DB);
$filters = [
    'search' => $query,
    'learningpath' => $learningpath,
];

$courses = $repository->get_courses($configuredcategories, $filters);

$coursesresponse = [];
foreach ($courses as $course) {
    $imageurl = \core_course\external\course_summary_exporter::get_course_image($course);
    if (empty($imageurl)) {
        $imageurl = "{$CFG->wwwroot}/blocks/course_gallery/pix/default-course-image.webp";
    }

    $category = $categories[$course->category];
    $customfieldsmetadata = \core_course\customfield\course_handler::create()->export_instance_data_object($course->id, true);

    $rawdatas = \core_course\customfield\course_handler::create()->get_instance_data($course->id, true);
    foreach ($rawdatas as $data) {
        $shortname = $data->get_field()->get('shortname');
        if ($shortname === 'tem_certificado') {
            $customfieldsmetadata->tem_certificado = $data->get_value();
        }
    }

    $courselang = isset($customfieldsmetadata->linguagem_conteudo) ? $customfieldsmetadata->linguagem_conteudo : '';

    if (!empty($workload)) {
        $workloadvalues = explode(',', $workload);
        $isvalidworkload = false;

        $min = (int) $workloadvalues[0];
        $max = isset($workloadvalues[1]) ? (int) $workloadvalues[1] : $min;

        if ($min > $max) {
            [$min, $max] = [$max, $min];
        }

        $courseworkload = (int) $customfieldsmetadata->carga_horaria;

        if ($courseworkload == 0) {
            continue;
        }

        if ($courseworkload < $min || $courseworkload > $max) {
            continue;
        }
    }

    if (!empty($certificate)) {
        $certificatevalues = explode(',', $certificate);
        $isvalidcertificate = false;
        foreach ($certificatevalues as $value) {
            if ($customfieldsmetadata->tem_certificado == $value) {
                $isvalidcertificate = true;
                break;
            }
        }
        if (!$isvalidcertificate) {
            continue;
        }
    }

    $courseresponse = new stdClass();
    $courseresponse->has_certificate = $customfieldsmetadata->tem_certificado;
    $courseresponse->workload = $customfieldsmetadata->carga_horaria;
    $courseresponse->lang = $customfieldsmetadata->linguagem_conteudo;
    $courseresponse->id = $course->id;
    $courseresponse->fullname = $course->fullname;
    $courseresponse->category_name = $category->name;
    $courseresponse->category_url = $category->url;
    $courseresponse->image_url = $imageurl;
    $courseresponse->url = "{$CFG->wwwroot}/course/view.php?id={$course->id}";

    $coursesresponse[] = $courseresponse;
}

$size = count($coursesresponse);
$coursesresponse = array_slice($coursesresponse, $currentpage * $coursesperpage, $coursesperpage);

echo json_encode(['total' => $size, 'courses' => $coursesresponse, 'baseurl' => $CFG->wwwroot]);
die;
