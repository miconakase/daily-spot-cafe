<?php
// One-time repair for text saved by older code, which HTML-escaped values
// before storing them (so "Café & Co" became "Café &amp; Co" and editor
// HTML became "&lt;p&gt;..."). Decodes those values and re-cleans product
// descriptions. Run ONCE per database:
//   php database/fix-escaped-text.php            (shows what would change)
//   php database/fix-escaped-text.php --apply    (writes the changes)
if (PHP_SAPI !== 'cli') {
    exit("Run from the command line.\n");
}

require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/helpers/HtmlSanitizer.php';

$apply = in_array('--apply', $argv, true);
$conn  = Database::getConnection();

// table => [primary key, [columns]]
$targets = [
    'products'         => ['product_id', ['product_name', 'short_description', 'long_description']],
    'categories'       => ['category_id', ['category_name', 'description']],
    'product_variants' => ['variant_id', ['variant_type', 'size_label']],
    'user_details'     => ['user_id', ['first_name', 'middle_name', 'last_name', 'suffix', 'phone_number', 'email']],
    'orders'           => ['order_id', ['notes']],
];

$changes = 0;
foreach ($targets as $table => [$pk, $columns]) {
    $rows = $conn->query("SELECT $pk, " . implode(', ', $columns) . " FROM $table")->fetch_all(MYSQLI_ASSOC);

    foreach ($rows as $row) {
        $updates = [];
        foreach ($columns as $col) {
            $old = $row[$col];
            if ($old === null || strpos($old, '&') === false) {
                continue;
            }
            $new = html_entity_decode($old, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if ($col === 'long_description') {
                $new = HtmlSanitizer::isBlank($new) ? '' : HtmlSanitizer::clean($new);
            }
            if ($new !== $old) {
                $updates[$col] = $new;
                printf("%s #%s %s:\n  - %s\n  + %s\n", $table, $row[$pk], $col, $old, $new);
            }
        }

        if ($updates && $apply) {
            $set  = implode(', ', array_map(fn($c) => "$c = ?", array_keys($updates)));
            $stmt = $conn->prepare("UPDATE $table SET $set WHERE $pk = ?");
            $values = array_values($updates);
            $values[] = $row[$pk];
            $stmt->bind_param(str_repeat('s', count($updates)) . 'i', ...$values);
            $stmt->execute();
        }
        $changes += count($updates);
    }
}

echo $changes === 0
    ? "Nothing to fix.\n"
    : ($apply ? "Fixed $changes value(s).\n" : "$changes value(s) would change. Re-run with --apply to save.\n");
