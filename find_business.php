<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$b = App\Models\Business::all();
foreach ($b as $biz) {
    echo "ID: {$biz->id} | Name: {$biz->business_name} | Owner: {$biz->user->name} ({$biz->user->email}) | Status: {$biz->status}" . PHP_EOL;
}



