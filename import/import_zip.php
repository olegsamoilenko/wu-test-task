<?php

/**
 * Downloads a ZIP archive with an XLSX file of postcodes, extracts it,
 * converts the XLSX to CSV using raw XML parsing (no external libraries),
 * then runs the import script to load data into the database.
 * Also logs time and memory usage, and cleans up temporary files.
 */

require __DIR__ . '/../vendor/autoload.php';
$pdo = require __DIR__ . '/../src/db.php';

$startTime = microtime(true);

$zipUrl = 'https://www.ukrposhta.ua/files/shares/out/postindex.zip';
$zipPath = __DIR__ . '/../storage/postindex.zip';
$xlsxPath = __DIR__ . '/../storage/postindex.xlsx';
$csvPath = __DIR__ . '/../storage/postindex.csv';

echo "Download the ZIP...\n";
file_put_contents($zipPath, file_get_contents($zipUrl));

// Extract XLSX from ZIP
echo "Unpacking the ZIP...\n";
$zip = new ZipArchive;
if ($zip->open($zipPath) === TRUE) {
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $name = $zip->getNameIndex($i);
        if (str_ends_with($name, '.xlsx')) {
            copy("zip://$zipPath#$name", $xlsxPath);
            break;
        }
    }
    $zip->close();
} else {
    die("Unable to unzip a zip archive\n");
}

// Convert XLSX to CSV using raw XML parsing
echo "Convert XLSX → CSV..\n";
$zip = new ZipArchive;
if ($zip->open($xlsxPath) !== TRUE) {
    die("Can't open xlsx\n");
}

// Parse shared strings and sheet data from raw XLSX
$stringsXml = $zip->getFromName('xl/sharedStrings.xml');
$sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
$zip->close();

$strings = [];
if ($stringsXml) {
    $xml = simplexml_load_string($stringsXml);
    foreach ($xml->si as $si) {
        $text = (string) $si->t;
        $strings[] = $text;
    }
}

$sheet = simplexml_load_string($sheetXml);
$sheet->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
$rows = $sheet->xpath('//a:sheetData/a:row');

// Write CSV file manually
$fp = fopen($csvPath, 'w');
foreach ($rows as $row) {
    $line = [];
    foreach ($row->c as $cell) {
        $value = (string) $cell->v;
        if ((string)$cell['t'] === 's') {
            $value = $strings[(int)$value] ?? '';
        }
        $line[] = $value;
    }
    fputcsv($fp, $line, ';');
}

fclose($fp);

echo "CSV saved: $csvPath\n";

// Run import script
echo "Start importing...\n";
require __DIR__ . '/import_csv.php';

echo "Clean up temporary files...\n";

// Clean up all temporary files
@unlink($zipPath);
@unlink($xlsxPath);
@unlink($csvPath);

$endTime = microtime(true);
$executionTime = round($endTime - $startTime, 2);
$memory = round(memory_get_peak_usage(true) / 1024 / 1024, 2);

echo "Temporary files are cleared!\n";
echo "Execution time: {$executionTime} sec\n";
echo "Memory peak: {$memory} MB\n";