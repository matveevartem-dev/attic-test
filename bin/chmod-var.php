<?php

declare(strict_types=1);

use FilesystemIterator as Filesystem;
use RecursiveDirectoryIterator as Directory;
use RecursiveIteratorIterator as Iterator;

$path = __DIR__ . '/../var';
$mode = 0777;

if (is_dir($path)) {
    chmod($path, $mode);

    $iterator = new Iterator(
        new Directory($path, Filesystem::SKIP_DOTS | Filesystem::CURRENT_AS_PATHNAME),
        Iterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        $mode = 0777;
        chmod($item, 0755);
    }
}

exit(0);
