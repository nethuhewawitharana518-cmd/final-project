<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$query = App\Models\Food::with('business', 'category')
    ->where('status', 'active')
    ->where('available_quantity', '>', 0);
    // Ignore expiry for testing to see all active listings

echo "Total active items: " . $query->count() . PHP_EOL;
foreach ($query->get() as $f) {
    echo "Food: {$f->name} | Biz: {$f->business->business_name} | Type: {$f->business->business_type}" . PHP_EOL;
}
