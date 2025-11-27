<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace qbank_competenciesbyquestion;

defined('MOODLE_INTERNAL') || die();

use core_question\local\bank\plugin_features_base;
use core_question\local\bank\view;

/**
 * Plugin features for the qbank_competenciesbyquestion plugin.
 */
class plugin_features extends plugin_features_base {

    /**
     * Colonne aggiuntive nella question bank.
     * Per ora nessuna; le aggiungeremo in seguito.
     *
     * @param view $qbank
     * @return array
     */
    public function get_question_columns(view $qbank): array {
        return [];
    }
}
