<?php

// Fix existing records in MySQL database table gkr_cari
require_once __DIR__ . '/../app/Config/Paths.php';
$paths = new Config\Paths();
require_once $paths->systemDirectory . '/bootstrap.php';

$app = Config\Services::codeigniter(new \Config\App());
$app->initialize();

$db = \Config\Database::connect();

echo "Memulai perbaikan data gkr_cari...\n";

// Update judul, alt, deskripsi yang memuat (fg ...) atau (Fg ...)
$sql1 = "UPDATE gkr_cari 
         SET judul = REGEXP_REPLACE(judul, '\\\\(?\\\\b(fg|Fg|FG)\\\\s*[-_]?\\\\s*([0-9]+)\\\\)?', '(FG-\\\\2)'),
             alt = REGEXP_REPLACE(alt, '\\\\(?\\\\b(fg|Fg|FG)\\\\s*[-_]?\\\\s*([0-9]+)\\\\)?', '(FG-\\\\2)'),
             deskripsi = REGEXP_REPLACE(deskripsi, '\\\\(?\\\\b(fg|Fg|FG)\\\\s*[-_]?\\\\s*([0-9]+)\\\\)?', '(FG-\\\\2)')
         WHERE judul REGEXP '\\\\b(fg|Fg)\\\\b' OR deskripsi REGEXP '\\\\b(fg|Fg)\\\\b' OR alt REGEXP '\\\\b(fg|Fg)\\\\b'";

try {
    $db->query($sql1);
    echo "Query REGEXP_REPLACE berhasil dieksekusi.\n";
} catch (\Throwable $e) {
    echo "Fallback query PHP iteration...\n";
    $query = $db->query("SELECT id, judul, alt, deskripsi, kode_bom, kata_kunci FROM gkr_cari");
    $rows = $query->getResultArray();
    $updatedCount = 0;
    
    $patternBOM = '/\(?\b(?:fg|Fg|FG)\s*[-_]?\s*([0-9]+)\)?/i';

    foreach ($rows as $r) {
        $newJudul = preg_replace($patternBOM, '(FG-$1)', $r['judul'] ?? '');
        $newAlt = preg_replace($patternBOM, '(FG-$1)', $r['alt'] ?? '');
        $newDesc = preg_replace($patternBOM, '(FG-$1)', $r['deskripsi'] ?? '');
        
        $newKodeBom = $r['kode_bom'];
        if (!empty($newKodeBom) && $newKodeBom !== '-' && $newKodeBom !== 'FG-') {
            $digits = preg_replace('/[^0-9]/', '', $newKodeBom);
            if (!empty($digits)) {
                $newKodeBom = 'FG-' . $digits;
            }
        }

        if ($newJudul !== $r['judul'] || $newAlt !== $r['alt'] || $newDesc !== $r['deskripsi'] || $newKodeBom !== $r['kode_bom']) {
            $db->table('gkr_cari')->where('id', $r['id'])->update([
                'judul'     => $newJudul,
                'alt'       => $newAlt,
                'deskripsi' => $newDesc,
                'kode_bom'  => $newKodeBom
            ]);
            $updatedCount++;
        }
    }
    echo "Berhasil memperbarui $updatedCount baris data di gkr_cari.\n";
}

echo "Selesai.\n";
