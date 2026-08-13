<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$blade = app('blade.compiler');
$code = file_get_contents('resources/views/bugs/index.blade.php');
try {
    $compiled = $blade->compileString($code);
    file_put_contents('test_compiled.php', $compiled);
    echo "Compiled successfully";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage();
}
