<?php
/**
 * Environment Variables Loader
 * Loads variables from .env file into $_ENV superglobal
 */

function loadEnv($path = '.env') {
    if (!file_exists($path)) {
        return false;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse key=value pairs
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove BOM (Byte Order Mark) if present
            $key = str_replace("\xEF\xBB\xBF", '', $key);
            
            // Remove quotes if present
            if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                $value = substr($value, 1, -1);
            }
            
            // Set environment variable
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
    
    return true;
}

// Load environment variables
// Look for .env file in the project root directory
$envPath = __DIR__ . '/../.env';
loadEnv($envPath);
?>
