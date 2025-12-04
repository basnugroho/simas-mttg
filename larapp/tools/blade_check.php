<?php
$dir = __DIR__ . '/../resources/views';
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$files = [];
foreach ($it as $f) {
    if ($f->isFile() && preg_match('/\.blade\.php$/', $f->getFilename())) {
        $path = $f->getPathname();
        // ignore administrator example files that use shorthand section syntax
        if (strpos($path, DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR) !== false) continue;
        $files[] = $path;
    }
}
$directives = ['if'=>'endif','section'=>['endsection','show','stop'],'component'=>'endcomponent','push'=>'endpush','php'=>'endphp'];
$errors = [];
foreach ($files as $file) {
    $text = file_get_contents($file);
    $counts = [];
    foreach ($directives as $open=>$close) {
        preg_match_all('/@' . $open . '\b/', $text, $mOpen);
        $o = count($mOpen[0]);
        $c = 0;
        if (is_array($close)) {
            foreach ($close as $cl) {
                preg_match_all('/@' . $cl . '\b/', $text, $mClose);
                $c += count($mClose[0]);
            }
        } else {
            preg_match_all('/@' . $close . '\b/', $text, $mClose);
            $c = count($mClose[0]);
        }
        if ($o !== $c) $errors[$file][$open] = ['open'=>$o,'close'=>$c];
    }
}
if (!$errors) {
    echo "OK: no mismatched directive counts found\n";
    exit(0);
}
foreach ($errors as $f => $info) {
    echo "File: $f\n";
    foreach ($info as $d => $counts) {
        echo "  @$d vs @{$d} (expected @{$d}/@{$directives[$d]}): open={$counts['open']}, close={$counts['close']}\n";
    }
}
exit(1);
