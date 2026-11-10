<?php
require_once __DIR__ . '/../config/app.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Submit the Add/Edit form to save a record.');
}

verify_csrf();

$slug = (string) ($_POST['module'] ?? '');
$module = get_module($slug);

$id = filter_var($_POST['id'] ?? '0', FILTER_VALIDATE_INT);

if ($id === false || $id < 0) {
    http_response_code(400);
    exit('Invalid record ID.');
}

$values = [];
$errors = [];

foreach ($module['fields'] as $name => $field) {

    $value = trim((string) ($_POST[$name] ?? ''));
    $required = $field['required'] ?? true;

    if ($required && $value === '') {
        $errors[] = $field['label'] . ' is required.';
        continue;
    }

    if ($value === '' && !$required) {
        $values[$name] = null;
        continue;
    }

    switch ($field['type']) {

        // NUMBER VALIDATION
        case 'number':

            $integer = filter_var($value, FILTER_VALIDATE_INT);

            $min = $field['min'] ?? null;
            $max = $field['max'] ?? null;

            if (
                $integer === false ||
                ($min !== null && $integer < $min) ||
                ($max !== null && $integer > $max)
            ) {

                $message = $field['label'] . ' must be a valid number';

                if ($min !== null && $max !== null) {
                    $message .= ' from ' . $min . ' to ' . $max;
                } elseif ($min !== null) {
                    $message .= ' with a minimum of ' . $min;
                } elseif ($max !== null) {
                    $message .= ' with a maximum of ' . $max;
                }

                $errors[] = $message . '.';

            } else {
                $values[$name] = $integer;
            }

            break;

        // DROPDOWN VALIDATION
        case 'select':

            $integer = filter_var($value, FILTER_VALIDATE_INT);

            $options = $pdo->query($field['sql'])
                           ->fetchAll(PDO::FETCH_COLUMN, 0);

            if (
                $integer === false ||
                $integer < 1 ||
                !in_array(
                    (string) $integer,
                    array_map('strval', $options),
                    true
                )
            ) {

                $errors[] = 'Please select a valid ' . $field['label'] . '.';

            } else {
                $values[$name] = $integer;
            }

            break;

        // DATE VALIDATION
        case 'date':

            if (!valid_date($value, 'Y-m-d')) {

                $errors[] = $field['label'] . ' must be a valid date.';

            } else {
                $values[$name] = $value;
            }

            break;

        // DATE AND TIME VALIDATION
        case 'datetime-local':

            if (!valid_date($value, 'Y-m-d\TH:i')) {

                $errors[] = $field['label'] . ' must be a valid date and time.';

            } else {
                $values[$name] = str_replace('T', ' ', $value) . ':00';
            }

            break;

        // TEXT VALIDATION
        default:

            $length = function_exists('mb_strlen')
                ? mb_strlen($value, 'UTF-8')
                : strlen($value);

            if (
                isset($field['max']) &&
                $length > $field['max']
            ) {

                $errors[] = $field['label'] . ' is too long.';

            } else {
                $values[$name] = $value;
            }

            break;
    }
}


// CROSS-FIELD VALIDATION FOR OLD BORROWING TEMPLATE
if (
    $slug === 'borrowings' &&
    isset($values['borrowDate'], $values['dueDate']) &&
    $values['dueDate'] < substr($values['borrowDate'], 0, 10)
) {

    $errors[] = 'Due date cannot be earlier than the borrowing date.';
}

if (
    $slug === 'borrowings' &&
    isset($values['borrowDate'], $values['returnDate']) &&
    $values['returnDate'] !== null &&
    $values['returnDate'] < substr($values['borrowDate'], 0, 10)
) {

    $errors[] = 'Return date cannot be earlier than the borrowing date.';
}


// RETURN TO FORM IF THERE ARE ERRORS
$returnForm = 'crud/form.php?module=' . $slug
    . ($id > 0 ? '&id=' . $id : '');

if ($errors) {

    $_SESSION['form_old'] = $_POST;

    flash('error', implode(' ', $errors));

    redirect_to($returnForm);
}


// CHECK IF RECORD EXISTS BEFORE UPDATING
if ($id > 0 && !find_record($pdo, $module, $id)) {

    flash('error', 'The record you tried to edit no longer exists.');

    redirect_to('crud/index.php?module=' . $slug);
}


// SAVE DATA TO DATABASE
$columns = array_keys($module['fields']);

try {

    // ADD NEW RECORD
    if ($id === 0) {

        $sql = 'INSERT INTO ' . $module['table']
             . ' (' . implode(', ', $columns) . ') VALUES ('
             . implode(', ', array_fill(0, count($columns), '?'))
             . ')';

        $stmt = $pdo->prepare($sql);

        $stmt->execute(array_values($values));

        flash(
            'success',
            $module['singular'] . ' record added successfully.'
        );

    } else {

        // UPDATE EXISTING RECORD
        $setParts = array_map(
            static fn($column) => $column . ' = ?',
            $columns
        );

        $sql = 'UPDATE ' . $module['table']
             . ' SET ' . implode(', ', $setParts)
             . ' WHERE ' . $module['pk'] . ' = ?';

        $stmt = $pdo->prepare($sql);

        $stmt->execute([...array_values($values), $id]);

        flash(
            'success',
            $module['singular'] . ' record updated successfully.'
        );
    }

    redirect_to('crud/index.php?module=' . $slug);

} catch (PDOException $error) {

    $_SESSION['form_old'] = $_POST;

    flash(
        'error',
        $error->getCode() === '23000'
            ? 'Invalid linked ID or duplicate key. Check your selected records.'
            : 'Unable to save this record. Check your database settings and values.'
    );

    redirect_to($returnForm);
}