<?php

/**
 * Imports postcode records from a CSV file into the database.
 * Adds new records, updates existing ones, and deletes those missing from the import.
 * Skips records that have been added manually via API.
 */

require __DIR__ . '/../vendor/autoload.php';

/** @var PDO $pdo Database connection */
$pdo = require __DIR__ . '/../src/db.php';

$filename = __DIR__ . '/../storage/postindex.csv';
if (!file_exists($filename)) {
    die("CSV not found\n");
}

$handle = fopen($filename, 'r');
if ($handle === false) {
    die("File could not be opened\n");
}

// Read CSV headers
$headers = fgetcsv($handle, 0, ';');
if ($headers === false) {
    die("Error reading headers з CSV");
}

// Remove BOM and normalize quotes
$headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);
$headers = array_map(function ($h) {
    return str_replace(['’', '`'], "'", $h);
}, $headers);

$importedCodes = [];

// Process each row
while (($row = fgetcsv($handle, 0, ';')) !== false) {
    $data = array_combine($headers, $row);

    $postCode = trim($data['Поштовий індекс відділення зв\'язку (Post code of post office)']);

    if (!$postCode) continue;

    $importedCodes[] = $postCode;

    // Check if the record exists
    $stmt = $pdo->prepare("SELECT * FROM post_indexes WHERE post_office_post_code = ?");
    $stmt->execute([$postCode]);
    $existing = $stmt->fetch();

    // Prepare data for insert/update
    $values = [
        'postal_code'      => $data['Поштовий індекс (Postal code)'] ?? null,
        'region_ua'        => $data['Область'] ?? null,
        'region_en'        => $data['Region (Oblast)'] ?? null,
        'district_old_ua'  => $data['Район (старий)'] ?? null,
        'district_new_ua'  => $data['Район (новий)'] ?? null,
        'district_new_en'  => $data['District new (Raion new)'] ?? null,
        'settlement_ua'    => $data['Населений пункт'] ?? null,
        'settlement_en'    => $data['Settlement'] ?? null,
        'post_office_ua'   => $data["Вiддiлення зв'язку"] ?? null,
        'post_office_en'   => $data['Post office'] ?? null,
    ];

    // Update existing or insert new record
    if ($existing) {
        $updateSql = "
            UPDATE post_indexes SET
                postal_code = :postal_code,
                region_ua = :region_ua,
                region_en = :region_en,
                district_old_ua = :district_old_ua,
                district_new_ua = :district_new_ua,
                district_new_en = :district_new_en,
                settlement_ua = :settlement_ua,
                settlement_en = :settlement_en,
                post_office_ua = :post_office_ua,
                post_office_en = :post_office_en,
                updated_at = NOW()
            WHERE post_office_post_code = :post_office_post_code
        ";
        $stmt = $pdo->prepare($updateSql);
    } else {
        $insertSql = "
            INSERT INTO post_indexes (
                post_office_post_code, postal_code,
                region_ua, region_en,
                district_old_ua, district_new_ua, district_new_en,
                settlement_ua, settlement_en,
                post_office_ua, post_office_en
            ) VALUES (
                :post_office_post_code, :postal_code,
                :region_ua, :region_en,
                :district_old_ua, :district_new_ua, :district_new_en,
                :settlement_ua, :settlement_en,
                :post_office_ua, :post_office_en
            )
        ";
        $stmt = $pdo->prepare($insertSql);
    }

    // Execute insert or update
    $stmt->execute(array_merge($values, ['post_office_post_code' => $postCode]));
}

// Delete records that were not found in the CSV and were not added via API
$inList = implode(',', array_fill(0, count($importedCodes), '?'));
$deleteSql = "DELETE FROM post_indexes WHERE added_via_api = FALSE AND post_office_post_code NOT IN ($inList)";
$pdo->prepare($deleteSql)->execute($importedCodes);

echo "Import completed!\n";