<?php
// Question bank column to display/ edit the competency linked to each question.

defined('MOODLE_INTERNAL') || die();

namespace qbank_competenciesbyquestion\columns;

use core_question\local\bank\column_base;
use moodle_url;
use pix_icon;
use qbank_competenciesbyquestion\local\manager;

/**
 * Column showing the competency mapped to a question.
 */
class competency_column extends column_base {

    /**
     * Internal name of the column.
     *
     * @return string
     */
    public function get_name(): string {
        return 'competencies';
    }

    /**
     * Required fields from the main question query.
     *
     * @return array
     */
    protected function get_required_fields(): array {
        // 'q' is the default alias for the question table.
        return ['q.id'];
    }

    /**
     * Column title.
     *
     * @return string
     */
    protected function get_title(): string {
        return get_string('columncompetencies', 'qbank_competenciesbyquestion');
    }

    /**
     * Column is not sortable.
     *
     * @return bool
     */
    public function is_sortable() {
        return false;
    }

    /**
     * Display content of the cell for a given question.
     *
     * @param \stdClass $question
     * @param string[] $rowclasses
     */
    protected function display_content($question, $rowclasses): void {
        global $OUTPUT;

        $competency = manager::get_competency_for_question($question->id);

        if ($competency) {
            $text = format_string($competency->shortname);
        } else {
            $text = get_string('competency_none', 'qbank_competenciesbyquestion');
        }

        echo $text . ' ';

        // Link to edit page.
        $url = new moodle_url('/question/bank/competenciesbyquestion/edit.php', ['id' => $question->id]);
        $icon = new pix_icon('t/edit', get_string('editcompetency', 'qbank_competenciesbyquestion'));
        echo $OUTPUT->action_icon($url, $icon);
    }
}
