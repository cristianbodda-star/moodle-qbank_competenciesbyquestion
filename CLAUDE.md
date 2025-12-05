# CLAUDE.md - AI Assistant Guide for qbank_competenciesbyquestion

## Project Overview

**Plugin Name**: qbank_competenciesbyquestion
**Type**: Moodle Question Bank Plugin
**Purpose**: Maps Moodle quiz questions to competencies, allowing educators to track which competencies are assessed by each question.
**License**: GPL v3
**Minimum Moodle Version**: 4.0 (2022041900)
**Current Version**: 0.1.0 (MATURITY_ALPHA)
**Language**: PHP (Moodle-specific)

### Description
This plugin extends Moodle's question bank with a custom column that displays and allows editing of competency assignments for individual questions. It creates a mapping between questions and the Moodle competency framework.

---

## Repository Structure

```
moodle-qbank_competenciesbyquestion/
├── LICENSE                    # GPL v3 license
├── README.md                  # Italian-language project description
├── version.php                # Plugin version and metadata
├── edit.php                   # Page for assigning competencies to questions
├── classes/                   # PHP classes (Moodle autoloaded)
│   ├── plugin_feature.php     # Main plugin entry point
│   ├── columns/
│   │   └── competency_column.php  # Question bank column implementation
│   └── local/
│       └── manager.php        # Business logic for competency mapping
├── db/
│   └── install.xml            # Database schema definition (XMLDB format)
└── lang/
    ├── en/
    │   └── qbank_competenciesbyquestion.php  # English language strings
    └── it/
        └── qbank_competenciesbyquestion.php  # Italian language strings
```

---

## Key Files and Their Roles

### Core Plugin Files

#### `version.php` (Required)
Defines plugin metadata:
- `$plugin->component`: Full plugin name (`qbank_competenciesbyquestion`)
- `$plugin->version`: YYYYMMDDHH format (e.g., 2025112800)
- `$plugin->requires`: Minimum Moodle version required
- `$plugin->maturity`: MATURITY_ALPHA, MATURITY_BETA, MATURITY_RC, or MATURITY_STABLE
- `$plugin->release`: Human-readable version (e.g., '0.1.0')

**Important**: When updating code, increment `$plugin->version` to trigger database upgrades.

#### `classes/plugin_feature.php`
Entry point for the plugin. Extends `plugin_features_base` to register custom columns in the question bank.
- Location: `classes/plugin_feature.php`
- Namespace: `qbank_competenciesbyquestion`
- Key method: `get_question_columns(view $qbank): array`

#### `classes/columns/competency_column.php`
Implements the custom column displayed in the question bank.
- Location: `classes/columns/competency_column.php`
- Extends: `core_question\local\bank\column_base`
- Key methods:
  - `get_name()`: Returns column identifier ('competencies')
  - `get_title()`: Returns localized column header
  - `get_required_fields()`: Specifies SQL fields needed (`q.id`)
  - `display_content($question, $rowclasses)`: Renders cell content with edit icon
  - `is_sortable()`: Returns false (column is not sortable)

#### `classes/local/manager.php`
Business logic layer for managing question-competency mappings.
- Location: `classes/local/manager.php`
- Namespace: `qbank_competenciesbyquestion\local`
- Key methods:
  - `get_mapping(int $questionid): ?stdClass` - Fetches mapping record
  - `get_competency_for_question(int $questionid): ?stdClass` - Fetches competency details
  - `set_competency_for_question(int $questionid, ?int $competencyid): void` - Creates/updates/deletes mapping
  - `get_competency_options(): array` - Returns dropdown options for competency selector

#### `edit.php`
Standalone page for assigning competencies to questions.
- Location: `edit.php`
- URL: `/question/bank/competenciesbyquestion/edit.php?id={questionid}`
- Implements manual form (no MoodleForm class)
- Checks capability: `moodle/question:editall`
- Uses sesskey for CSRF protection

#### `db/install.xml`
Database schema definition in XMLDB format (Moodle's database abstraction).
- Location: `db/install.xml`
- Table: `qbank_competenciesbyquestion`
- Fields:
  - `id` (primary key, auto-increment)
  - `questionid` (FK to question table)
  - `competencyid` (FK to competency table)
- Indexes: Unique constraint on (questionid, competencyid)

#### `lang/en/qbank_competenciesbyquestion.php`
English language strings (default language).
- Location: `lang/en/qbank_competenciesbyquestion.php`
- Required string: `$string['pluginname']`
- All UI strings must be localized using `get_string()` function

---

## Moodle Coding Standards

### File Headers
Every PHP file must start with:
```php
<?php
// This file is part of Moodle - http://moodle.org/.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

defined('MOODLE_INTERNAL') || die();
```

**Exception**: Top-level scripts (like `edit.php`) use `require(__DIR__ . '/../../../config.php');` instead of `defined('MOODLE_INTERNAL')`.

### Naming Conventions

1. **Files**: Lowercase with underscores (snake_case)
   - Example: `competency_column.php`, `manager.php`

2. **Classes**: Match filename, use namespaces
   - Example: `namespace qbank_competenciesbyquestion\columns;`
   - Example: `class competency_column extends column_base`

3. **Namespaces**: Follow directory structure
   - Root: Plugin component name (`qbank_competenciesbyquestion`)
   - Subdirectories: Append to namespace (`qbank_competenciesbyquestion\local`, `qbank_competenciesbyquestion\columns`)

4. **Functions/Methods**: snake_case
   - Example: `get_competency_for_question()`, `set_competency_for_question()`

5. **Variables**: snake_case
   - Example: `$questionid`, `$competencyid`, `$currentid`

6. **Database Tables**: Plugin prefix + descriptive name
   - Example: `qbank_competenciesbyquestion`

7. **Language Strings**: Lowercase with underscores
   - Example: `columncompetencies`, `editcompetency`, `competency_none`

### Code Style

- **Indentation**: 4 spaces (no tabs)
- **Line length**: 132 characters maximum (Moodle standard)
- **Braces**: K&R style (opening brace on same line)
- **Spacing**: Space after control structures (`if (`, `foreach (`)
- **Type hints**: Use PHP 7+ type declarations (`: int`, `: ?stdClass`, `: void`)
- **Return types**: Always specify return type for methods
- **Visibility**: Always declare method visibility (`public`, `protected`, `private`)

### Documentation

Use PHPDoc comments for classes and methods:
```php
/**
 * Returns the competency record for a given question, or null if none.
 *
 * @param int $questionid
 * @return stdClass|null
 */
public static function get_competency_for_question(int $questionid): ?stdClass {
    // Implementation
}
```

---

## Database Access Patterns

### Global Database Object
Always use the global `$DB` object:
```php
global $DB;
```

### Common Operations

1. **Get single record**:
   ```php
   $record = $DB->get_record('table_name', ['field' => $value]);
   // Returns false if not found, use ?: null for nullable return
   ```

2. **Get multiple records**:
   ```php
   $records = $DB->get_records('table_name', $conditions, $sort);
   ```

3. **Insert record**:
   ```php
   $record = new stdClass();
   $record->field = $value;
   $id = $DB->insert_record('table_name', $record);
   ```

4. **Update record**:
   ```php
   $record->id = $existingid;
   $record->field = $newvalue;
   $DB->update_record('table_name', $record);
   ```

5. **Delete records**:
   ```php
   $DB->delete_records('table_name', ['field' => $value]);
   ```

### Database Schema Modifications

- Schema is defined in `db/install.xml` (for new installations)
- Changes require `db/upgrade.php` (for existing installations)
- Use XMLDB editor in Moodle admin interface to generate XML
- Always increment `$plugin->version` in `version.php` after schema changes

---

## Security and Permissions

### Capability Checks
Always check capabilities before allowing actions:
```php
require_capability('moodle/question:editall', $context);
```

Common question-related capabilities:
- `moodle/question:viewall` - View questions
- `moodle/question:editall` - Edit questions
- `moodle/question:add` - Add new questions

### CSRF Protection
Use sesskey for form submissions:
```php
// In form
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);

// When processing
if (optional_param('save', false, PARAM_BOOL) && confirm_sesskey()) {
    // Process form
}
```

### Parameter Validation
Always validate and sanitize input:
```php
$questionid = required_param('id', PARAM_INT);
$competencyid = optional_param('competencyid', 0, PARAM_INT);
```

Common parameter types:
- `PARAM_INT` - Integer
- `PARAM_BOOL` - Boolean
- `PARAM_TEXT` - Plain text
- `PARAM_ALPHA` - Alphabetic characters only
- `PARAM_ALPHANUMEXT` - Alphanumeric with extended characters

### Output Escaping
Always escape output:
```php
echo format_string($text);  // For user-entered strings
echo s($text);              // For simple strings (htmlspecialchars)
echo $OUTPUT->action_icon($url, $icon);  // For icons/actions
```

---

## Language String Management

### String Definition
Define strings in `lang/en/qbank_competenciesbyquestion.php`:
```php
$string['stringkey'] = 'String value';
```

### String Usage
Retrieve strings using:
```php
get_string('stringkey', 'qbank_competenciesbyquestion');
get_string('savechanges');  // Core Moodle string (no component)
```

### Required Strings
- `pluginname` - Plugin name (REQUIRED)
- All UI text should be in language files, never hardcoded

### Multiple Languages
- English is the default language (`lang/en/`)
- Additional languages in `lang/{langcode}/`
- Example: Italian in `lang/it/`

---

## Page Setup Pattern

Standard pattern for standalone pages:
```php
require(__DIR__ . '/../../../config.php');  // Load Moodle core

// Get parameters
$questionid = required_param('id', PARAM_INT);

// Get database records
$question = $DB->get_record('question', ['id' => $questionid], '*', MUST_EXIST);

// Determine context
$context = context::instance_by_id($question->contextid);

// Check permissions
require_capability('moodle/question:editall', $context);

// Set up page
$PAGE->set_url(new moodle_url('/question/bank/competenciesbyquestion/edit.php', ['id' => $questionid]));
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pagetitle', 'qbank_competenciesbyquestion'));
$PAGE->set_heading(get_string('pageheading', 'qbank_competenciesbyquestion'));

// Output
echo $OUTPUT->header();
// ... page content ...
echo $OUTPUT->footer();
```

---

## Development Workflow

### Branch Management
- Development branch: `claude/claude-md-misj8m6zxpld0259-01M3jFRm19bML4rJnqHBNHHv`
- All commits must go to this branch
- Branch prefix must be `claude/` with matching session ID

### Git Workflow
1. Make changes to code
2. Commit with descriptive messages:
   ```bash
   git add .
   git commit -m "Clear, descriptive commit message"
   ```
3. Push to origin:
   ```bash
   git push -u origin claude/claude-md-misj8m6zxpld0259-01M3jFRm19bML4rJnqHBNHHv
   ```
4. Retry with exponential backoff (2s, 4s, 8s, 16s) if network failures occur

### Testing
Currently, this plugin has no automated tests. When adding tests:
- Create `tests/` directory
- Use PHPUnit for unit tests
- File naming: `{class_name}_test.php`
- Run tests: `vendor/bin/phpunit question/bank/competenciesbyquestion/tests/`

### Version Bumping
When making changes:
1. Update `$plugin->version` in `version.php` (YYYYMMDDHH format)
2. Update `$plugin->release` if user-facing version changes
3. Change `$plugin->maturity` when reaching milestones (ALPHA → BETA → RC → STABLE)

---

## Common Tasks for AI Assistants

### Adding a New Language String
1. Edit `lang/en/qbank_competenciesbyquestion.php`
2. Add: `$string['newkey'] = 'English text';`
3. Use in code: `get_string('newkey', 'qbank_competenciesbyquestion')`
4. If Italian translation exists, also update `lang/it/qbank_competenciesbyquestion.php`

### Adding a Database Field
1. Edit `db/install.xml` manually or use XMLDB editor
2. Create `db/upgrade.php` with upgrade logic:
   ```php
   function xmldb_qbank_competenciesbyquestion_upgrade($oldversion) {
       global $DB;
       $dbman = $DB->get_manager();

       if ($oldversion < 2025112801) {
           // Upgrade steps
           upgrade_plugin_savepoint(true, 2025112801, 'qbank', 'competenciesbyquestion');
       }

       return true;
   }
   ```
3. Increment `$plugin->version` in `version.php` to match upgrade version

### Adding a New Method to Manager
1. Edit `classes/local/manager.php`
2. Add static method with PHPDoc:
   ```php
   /**
    * Brief description.
    *
    * @param int $param Description
    * @return string|null Description
    */
   public static function method_name(int $param): ?string {
       global $DB;
       // Implementation
   }
   ```
3. Follow existing patterns (static methods, type hints, null returns)

### Modifying the Question Bank Column
1. Edit `classes/columns/competency_column.php`
2. Modify `display_content()` method for visual changes
3. Use `$OUTPUT` global for rendering elements
4. Use `format_string()` or `s()` for output escaping
5. Use `new moodle_url()` for creating links
6. Use `new pix_icon()` for creating icons

---

## Moodle-Specific Globals

### Common Global Objects
```php
global $DB;       // Database access
global $OUTPUT;   // Output rendering
global $PAGE;     // Page setup
global $USER;     // Current user
global $CFG;      // Configuration settings
global $COURSE;   // Current course (if in course context)
```

### Output Rendering
Use `$OUTPUT` for all rendering:
```php
echo $OUTPUT->header();                          // Page header
echo $OUTPUT->heading($text);                    // Heading
echo $OUTPUT->footer();                          // Page footer
echo $OUTPUT->action_icon($url, $icon);          // Action icon
echo $OUTPUT->notification($message, $type);     // Notification message
```

### URL Creation
Always use `moodle_url`:
```php
$url = new moodle_url('/question/bank/competenciesbyquestion/edit.php', ['id' => $questionid]);
```

### HTML Writing
Use `html_writer` for generating HTML:
```php
echo html_writer::tag('h3', $heading);
echo html_writer::start_tag('form', $attributes);
echo html_writer::empty_tag('input', $attributes);
echo html_writer::select($options, $name, $selected, []);
echo html_writer::end_tag('form');
```

---

## Plugin Architecture

### Class Autoloading
Moodle automatically loads classes from `classes/` based on namespace:
- `qbank_competenciesbyquestion\columns\competency_column` → `classes/columns/competency_column.php`
- `qbank_competenciesbyquestion\local\manager` → `classes/local/manager.php`
- No `require_once` needed for classes

### Plugin Integration Points

1. **Question Bank Columns**: Implement `plugin_features_base::get_question_columns()`
2. **Custom Pages**: Create PHP files in plugin root (e.g., `edit.php`)
3. **Database Tables**: Define in `db/install.xml`
4. **Language Strings**: Define in `lang/{langcode}/{component}.php`
5. **Capabilities**: Define in `db/access.php` (not yet implemented in this plugin)
6. **Settings**: Define in `settings.php` (not yet implemented in this plugin)

### Question Bank Column Lifecycle
1. Moodle loads plugin via `plugin_feature.php`
2. Calls `get_question_columns()` to register columns
3. For each question row:
   - Calls `get_required_fields()` to add SQL fields
   - Calls `display_content()` to render cell

---

## Debugging Tips

### Enable Debugging
In Moodle admin:
- Site administration → Development → Debugging
- Set to DEVELOPER level for detailed errors

### Common Issues

1. **Class not found**: Check namespace matches directory structure
2. **Database error**: Check `db/install.xml` syntax, run upgrade
3. **Permission denied**: Check capability requirements
4. **String not found**: Check language string exists in `lang/en/`
5. **Page not rendering**: Check for PHP errors, verify `require()` path

### Debugging Code
```php
debugging('Debug message: ' . $variable, DEBUG_DEVELOPER);
var_dump($variable);  // Avoid in production
error_log(print_r($variable, true));  // Server logs
```

---

## Code Quality Checklist

Before committing code:

- [ ] All PHP files start with GPL header and `defined('MOODLE_INTERNAL') || die();`
- [ ] All classes use proper namespaces
- [ ] All methods have type hints and return types
- [ ] All user-facing strings use `get_string()`
- [ ] All database queries use `$DB` object
- [ ] All user input is validated with `required_param()` / `optional_param()`
- [ ] All output is escaped with `format_string()` / `s()` / `$OUTPUT`
- [ ] All forms use sesskey for CSRF protection
- [ ] All pages check required capabilities
- [ ] PHPDoc comments for all public methods
- [ ] Version number incremented if needed
- [ ] Code follows Moodle coding style (4 spaces, K&R braces)

---

## Resources

### Official Moodle Documentation
- Moodle Developer Documentation: https://moodledev.io/
- Question Bank API: https://moodledev.io/docs/apis/subsystems/question
- Coding Style: https://moodledev.io/general/development/policies/codingstyle
- Database API: https://moodledev.io/docs/apis/core/dml
- Security: https://moodledev.io/docs/security

### Plugin Development
- Plugin Types: https://moodledev.io/docs/apis/plugintypes
- Question Bank Plugins: https://moodledev.io/docs/apis/plugintypes/qbank
- XMLDB Documentation: https://moodledev.io/general/development/tools/xmldb

---

## Current State Assessment

### Implemented Features
- ✅ Basic plugin structure
- ✅ Database schema (single table)
- ✅ Question bank column display
- ✅ Edit page for assigning competencies
- ✅ English and Italian language support
- ✅ Manager class with CRUD operations

### Missing/Incomplete Features
- ⚠️ No automated tests (PHPUnit)
- ⚠️ No `db/access.php` (custom capabilities)
- ⚠️ No `settings.php` (admin settings page)
- ⚠️ No `db/upgrade.php` (database upgrade logic)
- ⚠️ No validation of competency existence before saving
- ⚠️ No handling of question deletion (orphaned mappings)
- ⚠️ Manual form implementation (could use MoodleForm)
- ⚠️ No event triggering (Moodle event system)
- ⚠️ No privacy API implementation (GDPR compliance)
- ⚠️ No backup/restore support

### Known Issues
- Plugin is in ALPHA maturity (not production-ready)
- Italian comments mixed with English code
- No error handling for database operations
- No logging of competency changes

---

## AI Assistant Guidelines

### When Reading Code
1. Check file location to understand context (plugin root vs. classes/)
2. Verify namespace matches directory structure
3. Look for security checks (capabilities, sesskey, param cleaning)
4. Check for proper output escaping

### When Writing Code
1. Always follow Moodle coding standards
2. Use existing patterns from the codebase
3. Add PHPDoc comments for new methods
4. Test parameter validation and security
5. Use language strings instead of hardcoded text
6. Increment version number if needed
7. Never skip security checks

### When Modifying Database
1. Edit `db/install.xml` for schema changes
2. Create `db/upgrade.php` for existing installations
3. Increment plugin version number
4. Test with clean install and upgrade scenarios

### When Communicating
- Reference specific files with line numbers: `edit.php:39`
- Explain Moodle-specific concepts when relevant
- Provide code examples following Moodle standards
- Suggest testing steps when applicable

---

## Examples

### Example: Adding a New Language String
```php
// 1. Edit lang/en/qbank_competenciesbyquestion.php
$string['newfeature'] = 'New Feature';

// 2. Use in code
$title = get_string('newfeature', 'qbank_competenciesbyquestion');
```

### Example: Adding a Manager Method
```php
// In classes/local/manager.php

/**
 * Checks if a question has any competency assigned.
 *
 * @param int $questionid
 * @return bool
 */
public static function question_has_competency(int $questionid): bool {
    global $DB;
    return $DB->record_exists('qbank_competenciesbyquestion', ['questionid' => $questionid]);
}
```

### Example: Modifying Column Display
```php
// In classes/columns/competency_column.php

protected function display_content($question, $rowclasses): void {
    global $OUTPUT;

    $competency = manager::get_competency_for_question($question->id);

    if ($competency) {
        // Show competency with badge
        echo html_writer::tag('span', format_string($competency->shortname), ['class' => 'badge badge-info']);
    } else {
        echo html_writer::tag('span', get_string('competency_none', 'qbank_competenciesbyquestion'), ['class' => 'text-muted']);
    }

    // Edit link
    $url = new moodle_url('/question/bank/competenciesbyquestion/edit.php', ['id' => $question->id]);
    $icon = new pix_icon('t/edit', get_string('editcompetency', 'qbank_competenciesbyquestion'));
    echo ' ' . $OUTPUT->action_icon($url, $icon);
}
```

---

## Final Notes

This is an early-stage Moodle plugin (ALPHA) that provides basic competency mapping for question bank questions. The codebase follows Moodle conventions but lacks several production-ready features (tests, events, privacy API, backup/restore).

When working on this plugin:
1. Prioritize security (capability checks, param cleaning, output escaping)
2. Follow Moodle coding standards strictly
3. Use existing Moodle APIs instead of reinventing functionality
4. Add tests when implementing new features
5. Document all changes clearly
6. Consider upgrade paths for existing installations

For questions or clarifications about Moodle development, consult the official Moodle Developer Documentation at https://moodledev.io/.
