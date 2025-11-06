<?php
/**
 * Security Branch - Quick Security Test Script
 * 
 * Run this script to quickly verify critical security features
 * 
 * Usage: php security_test.php
 */

echo "🔒 Security Branch - Quick Security Test\n";
echo "==========================================\n\n";

$testsPassed = 0;
$testsFailed = 0;
$criticalIssues = [];

// Test 1: Check if servePhoto method exists
echo "Test 1: Checking servePhoto() method exists...\n";
$controllerPath = __DIR__ . '/app/Http/Controllers/SeniorController.php';
if (file_exists($controllerPath)) {
    $content = file_get_contents($controllerPath);
    if (strpos($content, 'public function servePhoto') !== false) {
        echo "✅ PASS: servePhoto() method exists\n";
        $testsPassed++;
    } else {
        echo "❌ FAIL: servePhoto() method NOT found\n";
        $testsFailed++;
        $criticalIssues[] = "servePhoto() method missing";
    }
} else {
    echo "⚠️  SKIP: Controller file not found\n";
}

// Test 2: Check if photos are stored in private storage
echo "\nTest 2: Checking photo storage uses private disk...\n";
if (file_exists($controllerPath)) {
    $content = file_get_contents($controllerPath);
    if (strpos($content, "->store('senior-photos', 'private')") !== false) {
        echo "✅ PASS: Photos stored in private storage\n";
        $testsPassed++;
    } else {
        echo "❌ FAIL: Photos may still be in public storage\n";
        $testsFailed++;
        $criticalIssues[] = "Photos not in private storage";
    }
}

// Test 3: Check if OSCA IDs are redacted in logs
echo "\nTest 3: Checking OSCA ID redaction in logs...\n";
$authControllerPath = __DIR__ . '/app/Http/Controllers/Api/SeniorAuthController.php';
if (file_exists($authControllerPath)) {
    $content = file_get_contents($authControllerPath);
    if (strpos($content, '[REDACTED]') !== false || strpos($content, "Log::info('Direct login attempt for OSCA ID: [REDACTED]')") !== false) {
        echo "✅ PASS: OSCA IDs are redacted in logs\n";
        $testsPassed++;
    } else {
        echo "❌ FAIL: OSCA IDs may not be redacted\n";
        $testsFailed++;
        $criticalIssues[] = "OSCA IDs not redacted";
    }
}

// Test 4: Check if contact_number is removed from API
echo "\nTest 4: Checking contact_number removed from API...\n";
if (file_exists($authControllerPath)) {
    $content = file_get_contents($authControllerPath);
    // Check if contact_number is commented out or removed
    if (strpos($content, "'contact_number' => \$senior->contact_number") === false) {
        echo "✅ PASS: contact_number removed from API response\n";
        $testsPassed++;
    } else {
        echo "❌ FAIL: contact_number still in API response\n";
        $testsFailed++;
        $criticalIssues[] = "contact_number exposed in API";
    }
}

// Test 5: Check SQL injection prevention
echo "\nTest 5: Checking SQL injection prevention...\n";
if (file_exists($controllerPath)) {
    $content = file_get_contents($controllerPath);
    if (strpos($content, '$allowedSortFields') !== false && strpos($content, "!in_array(\$sortField, \$allowedSortFields)") !== false) {
        echo "✅ PASS: SQL injection prevention implemented\n";
        $testsPassed++;
    } else {
        echo "❌ FAIL: SQL injection prevention may be missing\n";
        $testsFailed++;
        $criticalIssues[] = "SQL injection prevention missing";
    }
}

// Test 6: Check file upload validation
echo "\nTest 6: Checking file upload validation...\n";
if (file_exists($controllerPath)) {
    $content = file_get_contents($controllerPath);
    if (strpos($content, "'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'") !== false) {
        echo "✅ PASS: File upload validation present\n";
        $testsPassed++;
    } else {
        echo "❌ FAIL: File upload validation may be missing\n";
        $testsFailed++;
        $criticalIssues[] = "File upload validation missing";
    }
}

// Test 7: Check secure photo route exists
echo "\nTest 7: Checking secure photo route...\n";
$routesPath = __DIR__ . '/routes/web.php';
if (file_exists($routesPath)) {
    $content = file_get_contents($routesPath);
    if (strpos($content, "Route::get('/seniors/{id}/photo'") !== false || strpos($content, "seniors.photo") !== false) {
        echo "✅ PASS: Secure photo route exists\n";
        $testsPassed++;
    } else {
        echo "❌ FAIL: Secure photo route missing\n";
        $testsFailed++;
        $criticalIssues[] = "Secure photo route missing";
    }
}

// Summary
echo "\n" . str_repeat("=", 42) . "\n";
echo "📊 TEST SUMMARY\n";
echo str_repeat("=", 42) . "\n";
echo "✅ Tests Passed: $testsPassed\n";
echo "❌ Tests Failed: $testsFailed\n";
echo "📝 Total Tests: " . ($testsPassed + $testsFailed) . "\n\n";

if ($testsFailed > 0) {
    echo "🚨 CRITICAL ISSUES FOUND:\n";
    foreach ($criticalIssues as $issue) {
        echo "   - $issue\n";
    }
    echo "\n⚠️  DO NOT MERGE until these issues are fixed!\n";
} else {
    echo "✅ All security checks passed!\n";
    echo "⚠️  However, manual testing is still required.\n";
    echo "   See SECURITY_TESTING_CHECKLIST.md for full test plan.\n";
}

echo "\n";


