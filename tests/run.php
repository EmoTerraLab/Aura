<?php
/**
 * Simple Test Runner for Aura
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/ProtocolTestCase.php';

$testFiles = glob(__DIR__ . '/**/*Test.php');
if (empty($testFiles)) {
    $testFiles = [];
}

// Also include the ones in the current dir if any
$testFiles = array_merge($testFiles, glob(__DIR__ . '/*Test.php'));

// Remove duplicates
$testFiles = array_unique($testFiles);

$passed = 0;
$failed = 0;
$errors = [];

foreach ($testFiles as $file) {
    require_once $file;
    
    // Get class name from file path
    $className = str_replace([__DIR__ . '/', '.php', '/'], ['', '', '\\'], $file);
    // If it's in a subdirectory like Protocols/AragonProtocolTest.php, 
    // we need to handle the namespace.
    // Assuming Tests namespace for all.
    
    // Simple way: read the file to find the class name
    $content = file_get_contents($file);
    if (preg_match('/class\s+([a-zA-Z0-9_]+)/', $content, $matches)) {
        $className = $matches[1];
        if (preg_match('/namespace\s+([a-zA-Z0-9_\\\\]+)/', $content, $nsMatches)) {
            $className = $nsMatches[1] . '\\' . $className;
        }
    }

    echo "Running tests in $className...\n";

    if (!class_exists($className)) {
        echo "  [ERROR] Class $className not found in $file\n";
        continue;
    }

    $reflection = new ReflectionClass($className);
    if ($reflection->isAbstract()) continue;

    foreach ($reflection->getMethods() as $method) {
        if (strpos($method->name, 'test') === 0) {
            $testCase = new $className();
            if (method_exists($testCase, 'setUp')) {
                $testCase->setUp();
            }

            try {
                echo "  -> {$method->name}: ";
                $testCase->{$method->name}();
                echo "PASSED\n";
                $passed++;
            } catch (Throwable $e) {
                echo "FAILED\n";
                echo "     [!] " . $e->getMessage() . "\n";
                // echo "     " . $e->getTraceAsString() . "\n";
                $failed++;
                $errors[] = "$className::{$method->name} failed: " . $e->getMessage();
            }
        }
    }
}

echo "\n------------------------------------------------\n";
echo "Tests completed: " . ($passed + $failed) . "\n";
echo "Passed: $passed\n";
echo "Failed: $failed\n";

if ($failed > 0) {
    echo "\nErrors:\n";
    foreach ($errors as $error) {
        echo "- $error\n";
    }
    exit(1);
}

exit(0);
