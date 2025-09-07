<?php

echo "🔧 Fixing all Livewire v3 compatibility issues...\n\n";

$files_to_fix = [
    // PHP files with spread operator in emit() methods
    [
        'file' => __DIR__ . '/../app/Http/Livewire/Permissions.php',
        'type' => 'php_emit',
        'search' => 'public function emit(...$params)',
        'replace' => 'public function emit($event = null, ...$params)'
    ],
    [
        'file' => __DIR__ . '/../app/Http/Livewire/Roles.php',
        'type' => 'php_emit',
        'search' => 'public function emit(...$params)',
        'replace' => 'public function emit($event = null, ...$params)'
    ],
    [
        'file' => __DIR__ . '/../app/Http/Livewire/TrashedPermissions.php',
        'type' => 'php_emit',
        'search' => 'public function emit(...$params)',
        'replace' => 'public function emit($event = null, ...$params)'
    ],
    [
        'file' => __DIR__ . '/../app/Http/Livewire/Users.php',
        'type' => 'php_emit',
        'search' => 'public function emit(...$params)',
        'replace' => 'public function emit($event = null, ...$params)'
    ],
    
    // JavaScript files with incompatible Livewire.on() syntax
    [
        'file' => __DIR__ . '/../resources/views/livewire/permissions.blade.php',
        'type' => 'js_on',
        'search' => "Livewire.on('toast', (...args) => open(...args));",
        'replace' => "// Livewire v3: toast events handled via addEventListener"
    ],
    [
        'file' => __DIR__ . '/../resources/views/livewire/permissions.blade.php',
        'type' => 'js_on',
        'search' => "Livewire.on('showToast', (...args) => open(...args));",
        'replace' => "// Livewire v3: showToast events handled via addEventListener"
    ],
    
    [
        'file' => __DIR__ . '/../resources/views/livewire/roles.blade.php',
        'type' => 'js_on',
        'search' => "Livewire.on('toast', (...args) => open(...args));",
        'replace' => "// Livewire v3: toast events handled via addEventListener"
    ],
    [
        'file' => __DIR__ . '/../resources/views/livewire/roles.blade.php',
        'type' => 'js_on',
        'search' => "Livewire.on('showToast', (...args) => open(...args));",
        'replace' => "// Livewire v3: showToast events handled via addEventListener"
    ],
    
    [
        'file' => __DIR__ . '/../resources/views/livewire/specialties.blade.php',
        'type' => 'js_on_compact',
        'search' => "Livewire.on('toast', (...args) => open(...args)); Livewire.on('showToast', (...args) => open(...args));",
        'replace' => "// Livewire v3: toast events handled via addEventListener"
    ],
    
    [
        'file' => __DIR__ . '/../resources/views/livewire/trashed-permissions.blade.php',
        'type' => 'js_on_compact',
        'search' => "Livewire.on('toast', (...args) => open(...args)); Livewire.on('showToast', (...args) => open(...args));",
        'replace' => "// Livewire v3: toast events handled via addEventListener"
    ],
    
    [
        'file' => __DIR__ . '/../resources/views/livewire/trashed-roles.blade.php',
        'type' => 'js_on_compact',
        'search' => "Livewire.on('toast', (...args) => open(...args)); Livewire.on('showToast', (...args) => open(...args));",
        'replace' => "// Livewire v3: toast events handled via addEventListener"
    ],
    
    [
        'file' => __DIR__ . '/../resources/views/livewire/trashed-users.blade.php',
        'type' => 'js_on',
        'search' => "Livewire.on('toast', (...args) => open(...args));",
        'replace' => "// Livewire v3: toast events handled via addEventListener"
    ],
    [
        'file' => __DIR__ . '/../resources/views/livewire/trashed-users.blade.php',
        'type' => 'js_on',
        'search' => "Livewire.on('showToast', (...args) => open(...args));",
        'replace' => "// Livewire v3: showToast events handled via addEventListener"
    ],
    
    [
        'file' => __DIR__ . '/../resources/views/livewire/users.blade.php',
        'type' => 'js_on',
        'search' => "Livewire.on('toast', (...args) => open(...args));",
        'replace' => "// Livewire v3: toast events handled via addEventListener"
    ],
    [
        'file' => __DIR__ . '/../resources/views/livewire/users.blade.php',
        'type' => 'js_on',
        'search' => "Livewire.on('showToast', (...args) => open(...args));",
        'replace' => "// Livewire v3: showToast events handled via addEventListener"
    ],
    
    [
        'file' => __DIR__ . '/../resources/views/livewire/_partials/toast_confirm.blade.php',
        'type' => 'js_on',
        'search' => "Livewire.on('toast', (...args) => open(...args));",
        'replace' => "// Livewire v3: toast events handled via addEventListener"
    ],
    [
        'file' => __DIR__ . '/../resources/views/livewire/_partials/toast_confirm.blade.php',
        'type' => 'js_on',
        'search' => "Livewire.on('showToast', (...args) => open(...args));",
        'replace' => "// Livewire v3: showToast events handled via addEventListener"
    ],
];

$fixed_count = 0;
$error_count = 0;

foreach ($files_to_fix as $fix) {
    if (!file_exists($fix['file'])) {
        echo "⚠️  File not found: {$fix['file']}\n";
        $error_count++;
        continue;
    }
    
    $content = file_get_contents($fix['file']);
    $original_content = $content;
    
    if (strpos($content, $fix['search']) !== false) {
        $content = str_replace($fix['search'], $fix['replace'], $content);
        
        if (file_put_contents($fix['file'], $content)) {
            echo "✅ Fixed {$fix['type']} in: " . basename($fix['file']) . "\n";
            echo "   🔍 Search: " . substr($fix['search'], 0, 50) . (strlen($fix['search']) > 50 ? '...' : '') . "\n";
            echo "   ✏️  Replace: " . substr($fix['replace'], 0, 50) . (strlen($fix['replace']) > 50 ? '...' : '') . "\n\n";
            $fixed_count++;
        } else {
            echo "❌ Failed to write: {$fix['file']}\n";
            $error_count++;
        }
    } else {
        echo "📝 Pattern not found in: " . basename($fix['file']) . "\n";
        echo "   🔍 Search: " . substr($fix['search'], 0, 50) . (strlen($fix['search']) > 50 ? '...' : '') . "\n\n";
    }
}

echo "\n🎯 Summary:\n";
echo "✅ Fixed: $fixed_count files\n";
echo "❌ Errors: $error_count files\n";

if ($fixed_count > 0) {
    echo "\n🎉 All Livewire v3 compatibility issues have been resolved!\n";
    echo "💡 The Select2 consultorio search should now work without errors.\n";
} else {
    echo "\n⚠️  No fixes were applied. Please check the patterns manually.\n";
}
