<?php
// Page to assign a competency to a question.

require(__DIR__ . '/../../../config.php');

use context;
use context_course;
use moodle_url;
use qbank_competenciesbyquestion\local\manager;

// ID della domanda.
$questionid = required_param('id', PARAM_INT);

// Recupera la domanda.
$question = $DB->get_record('question', ['id' => $questionid], '*', MUST_EXIST);

// Contesto della domanda (in Moodle 4.x la tabella question ha il campo contextid).
$context = \context::instance_by_id($question->contextid);

// Controllo permessi: chi può modificare le domande.
require_capability('moodle/question:editall', $context);

// URL di ritorno al deposito delle domande.
$coursecontext = $context->get_course_context(false);
if ($coursecontext instanceof context_course) {
    $returnurl = new moodle_url('/question/edit.php', ['courseid' => $coursecontext->instanceid]);
} else {
    $returnurl = new moodle_url('/question/edit.php');
}

// Impostazioni pagina.
$PAGE->set_url(new moodle_url('/question/bank/competenciesbyquestion/edit.php', ['id' => $questionid]));
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('editcompetencypagetitle', 'qbank_competenciesbyquestion'));
$PAGE->set_heading(get_string('editcompetencypagetitle', 'qbank_competenciesbyquestion'));

// Costruzione form "a mano" (niente moodleform per semplificare il codice nel repo).
if (optional_param('save', false, PARAM_BOOL) && confirm_sesskey()) {
    $competencyid = optional_param('competencyid', 0, PARAM_INT);
    manager::set_competency_for_question($questionid, $competencyid ?: null);
    redirect($returnurl, get_string('editcompetencysaved', 'qbank_competenciesbyquestion'));
}

// Dati attuali.
$current = manager::get_competency_for_question($questionid);
$currentid = $current ? $current->id : 0;

$options = manager::get_competency_options();

echo $OUTPUT->header();

echo html_writer::tag('h3', format_string($question->name));

echo html_writer::start_tag('form', ['method' => 'post', 'action' => $PAGE->url]);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $questionid]);

echo html_writer::start_div();
echo html_writer::label(get_string('competency', 'qbank_competenciesbyquestion'), 'id_competencyid');
echo html_writer::select($options, 'competencyid', $currentid, []);
echo html_writer::end_div();

echo html_writer::start_div(['style' => 'margin-top: 1em;']);
echo html_writer::empty_tag('input', [
    'type' => 'submit',
    'name' => 'save',
    'value' => get_string('savechanges')
]);
echo html_writer::end_div();

echo html_writer::end_tag('form');

echo $OUTPUT->footer();
