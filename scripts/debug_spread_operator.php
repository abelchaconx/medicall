<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = Application::getInstance() ?: new Application(realpath(__DIR__ . '/../'));

// Load configuration
$app->singleton('config', function () {
    return new \Illuminate\Config\Repository();
});

try {
    echo "🔍 Searching for spread operator usage in Livewire components...\n\n";
    
    // Search for spread operator patterns in PHP files
    $patterns = [
        '...$',          // Spread operator at end of line
        '...args',       // Common spread usage
        '...\$',         // Spread with variables
        'array(...',     // Array with spread
        'call_user_func_array',  // Alternative to spread
    ];
    
    $dirs = [
        __DIR__ . '/../app/Http/Livewire/',
        __DIR__ . '/../app/Http/Controllers/',
        __DIR__ . '/../app/Models/',
        __DIR__ . '/../livewire/',
        __DIR__ . '/../resources/views/livewire/',
    ];
    
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) continue;
        
        echo "📁 Checking directory: $dir\n";
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') continue;
            
            $content = file_get_contents($file->getPathname());
            
            foreach ($patterns as $pattern) {
                if (strpos($content, $pattern) !== false) {
                    echo "🚨 Found '$pattern' in: " . $file->getPathname() . "\n";
                    
                    // Show context around the match
                    $lines = explode("\n", $content);
                    foreach ($lines as $lineNum => $line) {
                        if (strpos($line, $pattern) !== false) {
                            $start = max(0, $lineNum - 2);
                            $end = min(count($lines) - 1, $lineNum + 2);
                            
                            echo "📍 Context (lines " . ($start + 1) . "-" . ($end + 1) . "):\n";
                            for ($i = $start; $i <= $end; $i++) {
                                $marker = ($i === $lineNum) ? ">>> " : "    ";
                                echo $marker . ($i + 1) . ": " . $lines[$i] . "\n";
                            }
                            echo "\n";
                        }
                    }
                }
            }
        }
    }
    
    echo "✅ Search completed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " (line " . $e->getLine() . ")\n";
}
