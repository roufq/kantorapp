<?php

require_once __DIR__ . '/vendor/autoload.php';

try {
    $userClass = new ReflectionClass('App\\Models\\User');
    echo "User class loaded successfully without redeclaration error.\n";
    echo "Class file: " . $userClass->getFileName() . "\n";
} catch (Exception $e) {
    echo "Error loading User class: " . $e->getMessage() . "\n";
}
