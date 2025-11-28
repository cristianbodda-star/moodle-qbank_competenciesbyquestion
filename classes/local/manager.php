<?php
// Helper methods to work with question/competency mapping.

defined('MOODLE_INTERNAL') || die();

namespace qbank_competenciesbyquestion\local;

use stdClass;

/**
 * Manager class for qbank_competenciesbyquestion.
 */
class manager {

    /**
     * Returns the record from qbank_competenciesbyquestion for a given question.
     *
     * @param int $questionid
     * @return stdClass|null
     */
    public static function get_mapping(int $questionid): ?stdClass {
        global $DB;

        return $DB->get_record('qbank_competenciesbyquestion', ['questionid' => $questionid]) ?: null;
    }

    /**
     * Returns the competency record for a given question, or null if none.
     *
     * @param int $questionid
     * @return stdClass|null
     */
    public static function get_competency_for_question(int $questionid): ?stdClass {
        global $DB;

        $mapping = self::get_mapping($questionid);
        if (!$mapping) {
            return null;
        }

        return $DB->get_record('competency', ['id' => $mapping->competencyid]) ?: null;
    }

    /**
     * Creates/updates/deletes the mapping for a question.
     *
     * @param int $questionid
     * @param int|null $competencyid  Competency id or null/0 to remove mapping.
     */
    public static function set_competency_for_question(int $questionid, ?int $competencyid): void {
        global $DB;

        $existing = self::get_mapping($questionid);

        if (empty($competencyid)) {
            if ($existing) {
                $DB->delete_records('qbank_competenciesbyquestion', ['questionid' => $questionid]);
            }
            return;
        }

        if ($existing) {
            $existing->competencyid = $competencyid;
            $DB->update_record('qbank_competenciesbyquestion', $existing);
        } else {
            $record = new stdClass();
            $record->questionid = $questionid;
            $record->competencyid = $competencyid;
            $DB->insert_record('qbank_competenciesbyquestion', $record);
        }
    }

    /**
     * Options for the competency selector (id => label).
     *
     * @return array
     */
    public static function get_competency_options(): array {
        global $DB;

        $records = $DB->get_records('competency', null, 'shortname ASC');
        $options = [0 => get_string('competency_none', 'qbank_competenciesbyquestion')];

        foreach ($records as $c) {
            $label = $c->shortname ?: ('ID ' . $c->id);
            if (!empty($c->idnumber)) {
                $label .= ' (' . $c->idnumber . ')';
            }
            $options[$c->id] = $label;
        }

        return $options;
    }
}
