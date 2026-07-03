<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Food Deal Alert</title>
    <style>
        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 40px 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #1e1e1e;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #2d2d2d;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .header {
            background: linear-gradient(135deg, #ff6b00, #ff8800);
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }
        .content {
            padding: 30px;
            text-align: center;
        }
        .restaurant-tag {
            display: inline-block;
            background-color: rgba(255, 107, 0, 0.15);
            border: 1px solid rgba(255, 107, 0, 0.3);
            color: #ff6b00;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .food-title {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 10px 0;
        }
        .description {
            color: #a0a0a0;
            font-size: 15px;
            line-height: 1.6;
            margin: 0 auto 25px auto;
            max-width: 480px;
        }
        .btn-container {
            margin-top: 10px;
        }
        .btn {
            display: inline-block;
            background-color: #ff6b00;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 35px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 16px;
            box-shadow: 0 5px 15px rgba(255, 107, 0, 0.4);
            transition: transform 0.2s;
        }
        .footer {
            background-color: #161616;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #2d2d2d;
            font-size: 12px;
            color: #666666;
        }
    </style>
</head>
<body>
    <?php
        $embeddedImage = null;
        if ($food->image) {
            $cleanPath = str_replace('\\', '/', $food->image);
            $fullPath = str_starts_with($cleanPath, 'assets/') 
                ? public_path($cleanPath) 
                : storage_path('app/public/' . $cleanPath);

            if (file_exists($fullPath)) {
                $embeddedImage = $message->embed($fullPath);
            }
        }
    ?>
    <div class="container">
        <div class="header">
            <h1>FOOD RESCUE DEALS</h1>
        </div>
        <div class="content">
            <span class="restaurant-tag">🏪 <?php echo e($food->business->business_name); ?></span>
            
            <?php if($embeddedImage): ?>
                <div style="margin-bottom: 25px; text-align: center;">
                    <img src="<?php echo e($embeddedImage); ?>" alt="<?php echo e($food->name); ?>" style="max-width: 100%; max-height: 240px; border-radius: 8px; object-fit: cover; display: inline-block; border: 1px solid #2d2d2d;">
                </div>
            <?php endif; ?>

            <h2 class="food-title"><?php echo e($food->name); ?></h2>
            <p class="description"><?php echo e($food->description ?: 'No description provided.'); ?></p>
            
            <div style="text-align: center; margin-bottom: 30px; width: 100%;">
                <span style="color: #777777; text-decoration: line-through; font-size: 16px; margin-right: 15px; display: inline-block;">
                    Rs. <?php echo e(number_format($food->original_price, 2)); ?>

                </span>
                <span style="color: #10b981; font-size: 24px; font-weight: 800; display: inline-block;">
                    Rs. <?php echo e(number_format($food->discount_price, 2)); ?>

                </span>
            </div>

            <div class="btn-container">
                <a href="<?php echo e(route('food.browse')); ?>" class="btn">View Deal Now</a>
            </div>
        </div>
        <div class="footer">
            <p>You received this email because you registered on FoodRescue Marketplace.</p>
            <p>&copy; <?php echo e(date('Y')); ?> FoodRescue. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
<?php /**PATH D:\projectn_dark\resources\views/emails/new-food-alert.blade.php ENDPATH**/ ?>