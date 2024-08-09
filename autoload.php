<?php
$namespace_mappings = [
    'App\\' => __DIR__ . '/src/'
];

// var_dump($_ENV);
spl_autoload_register(function ($class) use ($namespace_mappings) {
    foreach ($namespace_mappings as $prefix => $base_dir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }
        $relative_class = substr($class, $len);
        $relative_class = str_replace('\\', '/', $relative_class);
        $file = $base_dir . $relative_class . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});
