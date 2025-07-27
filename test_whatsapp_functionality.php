<?php

// Test the WhatsApp functionality
// Run this in the browser: /test_whatsapp_functionality.php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\FileReturn;
use App\Models\Staff;
use Carbon\Carbon;

echo "<h2>WhatsApp Functionality Test</h2>";

// Test phone number formatting
function formatPhoneForWhatsApp($phone) {
    if (!$phone) return '';
    
    // Remove all non-numeric characters
    $cleaned = preg_replace('/[^0-9]/', '', $phone);
    
    // Convert Malaysian numbers: replace leading 0 with 60
    if (substr($cleaned, 0, 1) === '0') {
        return '60' . substr($cleaned, 1);
    }
    
    return $cleaned;
}

echo "<h3>Phone Number Formatting Tests:</h3>";
$testPhones = [
    '012-3456789', 
    '0123456789', 
    '+60123456789', 
    '60123456789', 
    '012 345 6789',
    '(012) 345-6789'
];

foreach ($testPhones as $phone) {
    $formatted = formatPhoneForWhatsApp($phone);
    echo "<p><strong>$phone</strong> → <strong>$formatted</strong></p>";
}

// Test WhatsApp link generation
function generateWhatsAppLink($staffName, $fileCode, $daysOverdue, $status) {
    $message = '';
    if ($status === 'due_today') {
        $message = "Salam {$staffName}, hari ini adalah tarikh akhir pemulangan fail {$fileCode}. Mohon kembalikan fail hari ini. Terima kasih.";
    } else {
        $message = "Salam {$staffName}, pemulangan fail {$fileCode} anda telah lewat {$daysOverdue} hari. Mohon kembalikan fail secepat mungkin. Terima kasih.";
    }
    
    return 'https://wa.me/60123456789?text=' . urlencode($message);
}

echo "<h3>WhatsApp Message Tests:</h3>";

$testCases = [
    ['Ahmad', 'ABC-123', 1, 'overdue'],
    ['Siti', 'DEF-456', 0, 'due_today']
];

foreach ($testCases as $case) {
    list($name, $file, $days, $status) = $case;
    $link = generateWhatsAppLink($name, $file, $days, $status);
    
    echo "<p><strong>$status</strong> case for <strong>$name</strong>:</p>";
    echo "<p><a href='$link' target='_blank'>$link</a></p>";
    echo "<hr>";
}

echo "<p style='color: green;'><strong>Test completed! Click the links above to test WhatsApp integration.</strong></p>";
?>
