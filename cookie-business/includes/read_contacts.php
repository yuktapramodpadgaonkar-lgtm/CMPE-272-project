<?php
/**
 * Reads company contacts from a text file and returns them for display.
 * Contacts are stored in data/contacts.txt (one entry per line or section).
 */
function get_contacts_from_file($file_path = null) {
    if ($file_path === null) {
        $file_path = __DIR__ . '/../data/contacts.txt';
    }
    if (!file_exists($file_path)) {
        return ['error' => 'Contacts file not found.'];
    }
    $content = file_get_contents($file_path);
    if ($content === false) {
        return ['error' => 'Could not read contacts file.'];
    }
    $lines = explode("\n", $content);
    $contacts = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '===') === 0) {
            continue;
        }
        if (strpos($line, ':') !== false) {
            list($label, $value) = explode(':', $line, 2);
            $contacts[] = ['label' => trim($label), 'value' => trim($value)];
        } else {
            $contacts[] = ['label' => '', 'value' => $line];
        }
    }
    return $contacts;
}
