<?php
/**
 * Validator — Input reading + type/rule helpers (return errors, no throw)
 *
 * Aligned with ISSUE-013 ("`cast()` returns errors, ไม่ throw") + FR-013
 * (default values).
 *
 * Design choice: validators do NOT throw. They return array<int,string>
 * of human-readable error messages; callers decide whether to surface
 * them via ErrorHandler::send(400, ...) or wrap the array in
 * ValidationException::withDetails().
 *
 * Usage:
 *
 *     require_once __DIR__ . '/../_includes/Validator.php';
 *
 *     $input   = Validator::readInput();                 // JSON → $_REQUEST fallback
 *     $errors  = Validator::requireKeys($input, ['target']);
 *     $errors  = array_merge($errors, Validator::cast($input, [
 *         'amount' => ['type' => 'float', 'min' => 0.01, 'max' => 9999999.99],
 *         'size'   => ['type' => 'int',   'min' => 50,   'max' => 1000,    'default' => 300],
 *     ]));
 *     if (!empty($errors)) {
 *         ErrorHandler::send(400, 'VALIDATION_ERROR', implode('; ', $errors));
 *     }
 *
 * @author  Dev (เดฟ)
 * @since   2.5.0
 * @see     docs/issues/open/ISSUE-013-shared-classes.md
 */

declare(strict_types=1);

final class Validator
{
    /**
     * Read input from JSON body or fall back to $_REQUEST.
     *
     * Behaviour:
     *   - POST with JSON body  → decoded JSON array
     *   - Any method with $_REQUEST content → $_REQUEST
     *   - Empty body → []
     *
     * @return array<string,mixed>
     */
    public static function readInput(): array
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        if ($method === 'POST' || $method === 'PUT' || $method === 'PATCH') {
            $raw = file_get_contents('php://input');
            if (is_string($raw) && $raw !== '') {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }
        }

        return $_REQUEST;
    }

    /**
     * Check that all given keys exist and are non-empty in $input.
     *
     * @param array<string,mixed> $input
     * @param array<int,string>   $keys
     * @return array<int,string>  list of error messages (empty = OK)
     */
    public static function requireKeys(array $input, array $keys): array
    {
        $errors = [];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $input)) {
                $errors[] = "Missing required field: {$key}";
                continue;
            }
            $value = $input[$key];
            // Treat '0', 0, false as present-but-falsy OK; empty string / null / missing = fail.
            if ($value === null || $value === '' || (is_array($value) && empty($value))) {
                $errors[] = "Field '{$key}' must not be empty";
            }
        }
        return $errors;
    }

    /**
     * Cast / sanitise $input according to a rule spec and accumulate errors.
     *
     * Each rule is keyed by the field name and supports:
     *
     *   'type'     string  scalar type: 'int', 'float', 'bool', 'string'
     *   'min'      numeric lower bound (inclusive)
     *   'max'      numeric upper bound (inclusive)
     *   'enum'     array<int|string> allowed values
     *   'pattern'  string  PCRE pattern (against string value)
     *   'default'  mixed   default when field is missing
     *   'required' bool    require field to be present (default true)
     *
     * Values are sanitised in-place via the provided $input reference
     * (use &$input in the caller).
     *
     * @param array<string,mixed> $input  by reference
     * @param array<string,array<string,mixed>> $rules
     * @return array<int,string>
     */
    public static function cast(array &$input, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $rule) {
            $required = $rule['required'] ?? true;
            $hasValue = array_key_exists($field, $input) && $input[$field] !== null && $input[$field] !== '';

            if (!$hasValue) {
                if ($required) {
                    if (!array_key_exists($field, $input)) {
                        $errors[] = "Missing required field: {$field}";
                    } else {
                        $errors[] = "Field '{$field}' must not be empty";
                    }
                    continue;
                }
                // Apply default for missing optional fields.
                if (array_key_exists('default', $rule)) {
                    $input[$field] = $rule['default'];
                }
                continue;
            }

            $value = $input[$field];
            $type  = $rule['type'] ?? null;

            // Type casting
            if ($type !== null) {
                switch ($type) {
                    case 'int':
                        if (!is_numeric($value)) {
                            $errors[] = "Field '{$field}' must be numeric";
                            continue 2;
                        }
                        $input[$field] = (int) $value;
                        $value = $input[$field];
                        break;
                    case 'float':
                        if (!is_numeric($value)) {
                            $errors[] = "Field '{$field}' must be numeric";
                            continue 2;
                        }
                        $input[$field] = (float) $value;
                        $value = $input[$field];
                        break;
                    case 'bool':
                        $input[$field] = filter_var(
                            $value,
                            FILTER_VALIDATE_BOOLEAN,
                            FILTER_NULL_ON_FAILURE
                        );
                        if ($input[$field] === null) {
                            $errors[] = "Field '{$field}' must be a boolean";
                            continue 2;
                        }
                        $value = $input[$field];
                        break;
                    case 'string':
                        $input[$field] = (string) $value;
                        $value = $input[$field];
                        break;
                    default:
                        // Unknown type — skip casting, leave value alone.
                        break;
                }
            }

            // Range checks
            if (isset($rule['min']) && is_numeric($value)) {
                if ($value < $rule['min']) {
                    $errors[] = "Field '{$field}' must be ≥ {$rule['min']}";
                }
            }
            if (isset($rule['max']) && is_numeric($value)) {
                if ($value > $rule['max']) {
                    $errors[] = "Field '{$field}' must be ≤ {$rule['max']}";
                }
            }

            // Enum check (string or int allowed)
            if (isset($rule['enum']) && is_array($rule['enum'])) {
                if (!in_array($value, $rule['enum'], true)) {
                    $allowed = implode(', ', array_map(static fn ($v) => (string) $v, $rule['enum']));
                    $errors[] = "Field '{$field}' must be one of: {$allowed}";
                }
            }

            // Regex pattern
            if (isset($rule['pattern']) && is_string($value)) {
                if (!preg_match($rule['pattern'], $value)) {
                    $errors[] = "Field '{$field}' has an invalid format";
                }
            }
        }

        return $errors;
    }
}
