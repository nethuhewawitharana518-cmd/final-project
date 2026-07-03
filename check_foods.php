<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
foreach (App\Models\Food::all() as $f) {
    echo "ID: {$f->id} | Name: {$f->name} | Biz: {$f->business->business_name} | Biz Type: {$f->business->business_type} | Status: {$f->status} | Expiry: {$f->expiry_datetime} | Qty: {$f->available_quantity}" . PHP_EOL;
}
