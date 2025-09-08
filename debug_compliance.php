<?php

// Simple debug script to check compliance data
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\FacultySemesterCompliance;

echo "=== Checking Faculty Semester Compliance Records ===\n";
echo "Current time: " . date('Y-m-d H:i:s') . "\n\n";

$compliances = FacultySemesterCompliance::with('documentType', 'user')
    ->orderBy('updated_at', 'desc')
    ->take(10)
    ->get();

foreach ($compliances as $compliance) {
    echo "ID: {$compliance->id}\n";
    echo "User: {$compliance->user->name}\n";
    echo "Document Type: {$compliance->documentType->name}\n";
    echo "Evidence Link: " . ($compliance->evidence_link ?: 'NULL') . "\n";
    echo "Status: {$compliance->self_evaluation_status}\n";
    echo "Updated: {$compliance->updated_at}\n";
    echo "---\n";
}
