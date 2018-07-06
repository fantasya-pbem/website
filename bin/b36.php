#!/usr/bin/php
<?php
if ($argc >= 2 && $argc <= 3) {
    if ($argc === 2) {
        $option = '';
        $id     = $argv[1];
    } elseif ($argc === 3) {
        $option = $argv[1];
        $id     = $argv[2];
    }
    if ($option === '-e') {
        $option = '';
    }

    if ($option === '-d') {
        if (preg_match('/^[0-9a-zA-Z]+$/', $id) === 1) {
            echo base_convert($id, 36, 10) . PHP_EOL;
            return 0;
        }
    }

    if (empty($option)) {
        if (preg_match('/^[0-9]+$/', $id) === 1) {
            echo base_convert($id, 10, 36) . PHP_EOL;
            return 0;
        }
    }
}
?>
Usage: b36 [OPTION] ID
Available options:
    -d   Decode ID to Base-10.
    -e   Encode ID to Base-36 (default).

<?php exit(1); ?>
