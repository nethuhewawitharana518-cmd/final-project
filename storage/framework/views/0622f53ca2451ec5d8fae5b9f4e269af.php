<?php $__env->startSection('title', 'Admin Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<style>
    /* CSS variables tailored for premium dark dashboard theme */
    :root {
        --dash-orange: #FF6B00;
        --dash-green: #10B981;
        --dash-blue: #3B82F6;
        --dash-purple: #8B5CF6;
        --dash-yellow: #F59E0B;
        --dash-bg-card: #1E1E1E;
        --dash-border: #2D2D2D;
    }

    /* Core grid structure from dashboard-ui.jpg */
    .grid-container {
        display: grid;
        grid-template-cols: repeat(12, 1fr);
        gap: 20px;
        padding: 25px;
        background-color: #121212;
    }

    .widget-card {
        background-color: var(--dash-bg-card);
        border: 1px solid var(--dash-border);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .widget-title {
        font-size: 14px;
        font-weight: 700;
        color: #FFFFFF;
        margin-bottom: 5px;
    }

    .widget-subtitle {
        font-size: 11px;
        color: #888888;
        margin-bottom: 15px;
    }

    /* Spans */
    .span-3 { grid-column: span 3; }
    .span-4 { grid-column: span 4; }
    .span-5 { grid-column: span 5; }
    .span-6 { grid-column: span 6; }
    .span-7 { grid-column: span 7; }
    .span-8 { grid-column: span 8; }
    .span-12 { grid-column: span 12; }

    /* WIDGET 1: Top-left Single Line Chart with Tooltip */
    .tooltip-marker {
        background-color: #FFFFFF;
        color: #121212;
        font-size: 10px;
        font-weight: 800;
        padding: 2px 6px;
        border-radius: 4px;
        position: absolute;
        top: 25px;
        right: 40px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.5);
    }

    /* WIDGET 2: Two-column Stats Card */
    .stat-split {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-right: 1px solid var(--dash-border);
        padding-right: 15px;
    }
    .stat-split:last-child {
        border-right: none;
        padding-right: 0;
        padding-left: 15px;
    }

    /* WIDGET 5: Conic-gradient Progress Ring */
    .ring-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 10px 0;
    }
    .conic-ring {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        background: conic-gradient(var(--dash-orange) 75%, #2d2d2d 0);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        margin-bottom: 15px;
    }
    .conic-ring::after {
        content: '';
        position: absolute;
        width: 106px;
        height: 106px;
        background-color: var(--dash-bg-card);
        border-radius: 50%;
    }
    .conic-value {
        position: relative;
        z-index: 10;
        font-size: 26px;
        font-weight: 800;
        color: #FFFFFF;
    }

    /* WIDGET 6: Horizontal Timeline */
    .timeline-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        padding: 20px 10px;
        margin-top: 10px;
    }
    .timeline-line {
        position: absolute;
        height: 4px;
        background-color: #333333;
        width: 90%;
        left: 5%;
        top: 50%;
        transform: translateY(-50%);
        z-index: 1;
    }
    .timeline-node {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background-color: var(--dash-orange);
        border: 3px solid #1E1E1E;
        box-shadow: 0 0 0 2px var(--dash-orange);
        position: relative;
        z-index: 2;
        cursor: pointer;
    }
    .timeline-node.active {
        background-color: var(--dash-green);
        box-shadow: 0 0 0 2px var(--dash-green);
    }
    .node-label {
        position: absolute;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 10px;
        white-space: nowrap;
        color: #888888;
        font-weight: 600;
    }

    /* WIDGET 7: Calendar Widget */
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
        text-align: center;
        font-size: 12px;
        padding-top: 10px;
    }
    .cal-header {
        font-weight: 700;
        color: #666;
        padding-bottom: 4px;
    }
    .cal-day {
        color: #CCC;
        padding: 6px 0;
        border-radius: 6px;
        cursor: pointer;
    }
    .cal-day:hover {
        background-color: rgba(255, 107, 0, 0.1);
        color: var(--dash-orange);
    }
    .cal-day.active {
        background-color: var(--dash-orange);
        color: #FFFFFF;
        font-weight: 700;
    }

    /* WIDGET 8: Double Side-by-side Progress Rings */
    .double-rings {
        display: flex;
        justify-content: space-around;
        align-items: center;
        padding: 10px 0;
    }
    .mini-ring {
        width: 75px;
        height: 75px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .mini-ring::after {
        content: '';
        position: absolute;
        width: 61px;
        height: 61px;
        background-color: var(--dash-bg-card);
        border-radius: 50%;
    }
    .mini-value {
        position: relative;
        z-index: 10;
        font-size: 14px;
        font-weight: 800;
    }

    /* WIDGET 11: Segmented Progress Bar */
    .segmented-bar {
        display: flex;
        height: 10px;
        border-radius: 5px;
        overflow: hidden;
        margin: 15px 0;
    }
    .seg-part {
        height: 100%;
    }

    /* Animations */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .widget-card {
        animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
    }
    .grid-container > .widget-card:nth-child(1) { animation-delay: 0.05s; }
    .grid-container > .widget-card:nth-child(2) { animation-delay: 0.15s; }
    .grid-container > .widget-card:nth-child(3) { animation-delay: 0.25s; }
    .grid-container > .widget-card:nth-child(4) { animation-delay: 0.35s; }
    .grid-container > .widget-card:nth-child(5) { animation-delay: 0.45s; }
    .grid-container > .widget-card:nth-child(6) { animation-delay: 0.55s; }
    .grid-container > .widget-card:nth-child(7) { animation-delay: 0.65s; }
    .grid-container > .widget-card:nth-child(8) { animation-delay: 0.75s; }
    .grid-container > .widget-card:nth-child(9) { animation-delay: 0.85s; }
    .grid-container > .widget-card:nth-child(10) { animation-delay: 0.95s; }
    
    /* Responsive grid formatting */
    @media (max-width: 1024px) {
        .span-3, .span-4, .span-5, .span-6, .span-7, .span-8 {
            grid-column: span 12 !important;
        }
    }
</style>

<div class="d-flex">
    <?php echo $__env->make('admin.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-content flex-grow-1">
        <!-- Top Title Bar -->
        <div class="page-header border-0 pb-0">
            <div>
                <h1 class="page-title">Admin Dashboard</h1>
                <span style="color: #888; font-size: 14px;">Platform Intelligence & Operations HUD</span>
            </div>
            <span class="admin-badge">SYSTEM ADMINISTRATOR</span>
        </div>

        <div class="content-area pt-2">
            <div class="grid-container">
                
                <!-- ROW 1: WIDGET 1 (Revenue Curve) & WIDGET 4 (Order Dots Chart) -->
                <!-- WIDGET 1: Top-Left Revenue Card -->
                <div class="widget-card span-4" style="position: relative; min-height: 250px;">
                    <div>
                        <div class="widget-title">Sales Revenue</div>
                        <div class="widget-subtitle">Real-time daily transaction curves</div>
                    </div>
                    <div class="tooltip-marker">Rs. <?php echo e(number_format($totalRevenue)); ?></div>
                    
                    <div style="height: 130px; margin-top: 10px;">
                        <svg viewBox="0 0 320 120" width="100%" height="100%">
                            <!-- Grid lines -->
                            <line x1="45" y1="20" x2="310" y2="20" stroke="#252525" stroke-dasharray="4" />
                            <line x1="45" y1="55" x2="310" y2="55" stroke="#252525" stroke-dasharray="4" />
                            <line x1="45" y1="90" x2="310" y2="90" stroke="#252525" stroke-dasharray="4" />
                            
                            <!-- Y-Axis Labels -->
                            <text x="5" y="24" fill="#666" font-size="9" font-family="sans-serif">Rs. 50K</text>
                            <text x="5" y="59" fill="#666" font-size="9" font-family="sans-serif">Rs. 25K</text>
                            <text x="5" y="94" fill="#666" font-size="9" font-family="sans-serif">Rs. 0</text>
                            
                            <!-- Chart Line -->
                            <path d="M 45 90 C 90 30, 150 70, 210 35 T 310 15" fill="none" stroke="var(--dash-orange)" stroke-width="3" />
                            <circle cx="210" cy="35" r="4" fill="#FFFFFF" stroke="var(--dash-orange)" stroke-width="2" />
                            
                            <!-- X-Axis Labels -->
                            <text x="45" y="110" fill="#666" font-size="9" font-family="sans-serif" text-anchor="middle">Mon</text>
                            <text x="90" y="110" fill="#666" font-size="9" font-family="sans-serif" text-anchor="middle">Tue</text>
                            <text x="135" y="110" fill="#666" font-size="9" font-family="sans-serif" text-anchor="middle">Wed</text>
                            <text x="180" y="110" fill="#666" font-size="9" font-family="sans-serif" text-anchor="middle">Thu</text>
                            <text x="225" y="110" fill="#666" font-size="9" font-family="sans-serif" text-anchor="middle">Fri</text>
                            <text x="270" y="110" fill="#666" font-size="9" font-family="sans-serif" text-anchor="middle">Sat</text>
                            <text x="310" y="110" fill="#666" font-size="9" font-family="sans-serif" text-anchor="middle">Sun</text>
                        </svg>
                    </div>
                    <!-- Legend -->
                    <div class="d-flex align-items-center gap-2 mt-2" style="font-size: 10px; color: #888; font-weight: 700;">
                        <span style="display: inline-block; width: 8px; height: 8px; background-color: var(--dash-orange); border-radius: 50%;"></span>
                        <span>Daily Sales Revenue</span>
                    </div>
                </div>

                <!-- WIDGET 4: Top-Middle Line Point Chart -->
                <div class="widget-card span-4" style="min-height: 250px;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="widget-title">Order Activity</div>
                            <div class="widget-subtitle">Order volume trends this week</div>
                        </div>
                        <span class="badge bg-dark text-muted font-monospace" style="font-size: 9px; border: 1px solid var(--dash-border);">WEEK</span>
                    </div>

                    <div style="height: 130px; margin-top: 10px;">
                        <svg viewBox="0 0 320 120" width="100%" height="100%">
                            <!-- Grid lines -->
                            <line x1="30" y1="20" x2="310" y2="20" stroke="#252525" stroke-dasharray="4" />
                            <line x1="30" y1="55" x2="310" y2="55" stroke="#252525" stroke-dasharray="4" />
                            <line x1="30" y1="90" x2="310" y2="90" stroke="#252525" stroke-dasharray="4" />
                            
                            <!-- Y-Axis Labels -->
                            <text x="5" y="24" fill="#666" font-size="9" font-family="sans-serif">100</text>
                            <text x="5" y="59" fill="#666" font-size="9" font-family="sans-serif">50</text>
                            <text x="5" y="94" fill="#666" font-size="9" font-family="sans-serif">0</text>
                            
                            <!-- Connected dots path -->
                            <polyline points="40,80 85,50 130,90 175,40 220,60 265,30 310,45" fill="none" stroke="var(--dash-green)" stroke-width="2.5" />
                            
                            <!-- Dots -->
                            <circle cx="40" cy="80" r="3.5" fill="var(--dash-green)" />
                            <circle cx="85" cy="50" r="3.5" fill="var(--dash-green)" />
                            <circle cx="130" cy="90" r="3.5" fill="var(--dash-green)" />
                            <circle cx="175" cy="40" r="3.5" fill="var(--dash-green)" />
                            <circle cx="220" cy="60" r="3.5" fill="var(--dash-green)" />
                            <circle cx="265" cy="30" r="3.5" fill="var(--dash-green)" />
                            <circle cx="310" cy="45" r="3.5" fill="var(--dash-green)" />
                            
                            <!-- X-Axis Labels -->
                            <text x="40" y="110" fill="#666" font-size="9" font-family="sans-serif" text-anchor="middle">M</text>
                            <text x="85" y="110" fill="#666" font-size="9" font-family="sans-serif" text-anchor="middle">T</text>
                            <text x="130" y="110" fill="#666" font-size="9" font-family="sans-serif" text-anchor="middle">W</text>
                            <text x="175" y="110" fill="#666" font-size="9" font-family="sans-serif" text-anchor="middle">T</text>
                            <text x="220" y="110" fill="#666" font-size="9" font-family="sans-serif" text-anchor="middle">F</text>
                            <text x="265" y="110" fill="#666" font-size="9" font-family="sans-serif" text-anchor="middle">S</text>
                            <text x="310" y="110" fill="#666" font-size="9" font-family="sans-serif" text-anchor="middle">S</text>
                        </svg>
                    </div>
                    <!-- Legend -->
                    <div class="d-flex align-items-center gap-2 mt-2" style="font-size: 10px; color: #888; font-weight: 700;">
                        <span style="display: inline-block; width: 8px; height: 8px; background-color: var(--dash-green); border-radius: 50%;"></span>
                        <span>Weekly Placed Orders</span>
                    </div>
                </div>

                <!-- WIDGET 7: Calendar Card -->
                <div class="widget-card span-4" style="min-height: 250px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="widget-title" style="margin-bottom: 0;">📅 System Logs</div>
                        <span class="text-white small fw-bold font-monospace" style="font-size: 11px; color: var(--dash-orange) !important;"><?php echo e(date('F Y')); ?></span>
                    </div>

                    <div class="calendar-grid">
                        <!-- Days of week -->
                        <div class="cal-header">S</div>
                        <div class="cal-header">M</div>
                        <div class="cal-header">T</div>
                        <div class="cal-header">W</div>
                        <div class="cal-header">T</div>
                        <div class="cal-header">F</div>
                        <div class="cal-header">S</div>

                        <!-- Mock calendar cells -->
                        <div class="cal-day text-muted">28</div>
                        <div class="cal-day text-muted">29</div>
                        <div class="cal-day text-muted">30</div>
                        <div class="cal-day">1</div>
                        <div class="cal-day active">2</div>
                        <div class="cal-day">3</div>
                        <div class="cal-day">4</div>
                        <div class="cal-day">5</div>
                        <div class="cal-day">6</div>
                        <div class="cal-day">7</div>
                        <div class="cal-day">8</div>
                        <div class="cal-day">9</div>
                        <div class="cal-day">10</div>
                        <div class="cal-day">11</div>
                    </div>
                </div>

                <!-- ROW 2: WIDGET 2 (Stats Split), WIDGET 5 (Progress Ring) & WIDGET 8 (Double Rings) -->
                <!-- WIDGET 2: Column Stats Split -->
                <div class="widget-card span-4" style="min-height: 240px; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div class="widget-title">Core Entities</div>
                        <div class="widget-subtitle">Live database summary links</div>
                    </div>
                    <div class="d-flex w-100 justify-content-between">
                        <div class="stat-split w-50">
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <div>
                                    <div style="font-size: 26px; font-weight: 800; color: #FFF; line-height: 1.2;"><?php echo e($activeBusinessesCount ?? 0); ?></div>
                                    <div style="font-size: 10px; color: #888; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Merchants</div>
                                </div>
                                <div style="background: rgba(255, 107, 0, 0.1); padding: 10px; border-radius: 10px; border: 1px solid rgba(255, 107, 0, 0.2);">
                                    <i class="fa fa-store" style="color: var(--dash-orange); font-size: 18px;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="stat-split w-50">
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <div>
                                    <div style="font-size: 26px; font-weight: 800; color: #FFF; line-height: 1.2;"><?php echo e(number_format($foodSavedKg ?? 0)); ?></div>
                                    <div style="font-size: 10px; color: #888; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Food Saved (kg)</div>
                                </div>
                                <div style="background: rgba(16, 185, 129, 0.1); padding: 10px; border-radius: 10px; border: 1px solid rgba(16, 185, 129, 0.2);">
                                    <i class="fa fa-leaf" style="color: var(--dash-green); font-size: 18px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- WIDGET 5: Conic Circular Goal Card -->
                <div class="widget-card span-4 ring-box" style="min-height: 240px;">
                    <div class="widget-title">Carbon Offset Target</div>
                    <div class="conic-ring mt-2">
                        <div class="conic-value">75%</div>
                    </div>
                    <div style="font-size: 11px; color: #AAAAAA;">Monthly Carbon Cap Goal Reached</div>
                </div>

                <!-- WIDGET 8: Double side-by-side Progress Rings -->
                <div class="widget-card span-4" style="min-height: 240px; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div class="widget-title">User Split Intensity</div>
                        <div class="widget-subtitle">Proportion of customer & admin records</div>
                    </div>
                    <div class="double-rings">
                        <div class="text-center">
                            <div class="mini-ring" style="background: conic-gradient(var(--dash-blue) 80%, #2d2d2d 0);">
                                <span class="mini-value text-white">80%</span>
                            </div>
                            <div style="font-size: 10px; color: #888; font-weight: 700; margin-top: 8px;">Customers</div>
                        </div>
                        <div class="text-center">
                            <div class="mini-ring" style="background: conic-gradient(var(--dash-purple) 20%, #2d2d2d 0);">
                                <span class="mini-value text-white">20%</span>
                            </div>
                            <div style="font-size: 10px; color: #888; font-weight: 700; margin-top: 8px;">Admins</div>
                        </div>
                    </div>
                </div>

                <!-- ROW 3: WIDGET 3 (Up-Down Bars), WIDGET 6 (Timeline) & WIDGET 11 (Segmented Bar) -->
                <!-- WIDGET 3: Vertical Up-Down Bar Chart -->
                <div class="widget-card span-4" style="min-height: 250px;">
                    <div>
                        <div class="widget-title">System Fluctuations</div>
                        <div class="widget-subtitle">Peak upload and processing changes</div>
                    </div>
                    <div style="height: 130px; margin-top: 10px;">
                        <svg viewBox="0 0 320 120" width="100%" height="100%">
                            <!-- Center Line -->
                            <line x1="35" y1="55" x2="310" y2="55" stroke="#444" stroke-width="2" />
                            
                            <!-- Grid lines -->
                            <line x1="35" y1="20" x2="310" y2="20" stroke="#252525" stroke-dasharray="4" />
                            <line x1="35" y1="90" x2="310" y2="90" stroke="#252525" stroke-dasharray="4" />
                            
                            <!-- Y-Axis Labels -->
                            <text x="5" y="24" fill="#666" font-size="9" font-family="sans-serif">+50%</text>
                            <text x="5" y="59" fill="#666" font-size="9" font-family="sans-serif">0%</text>
                            <text x="5" y="94" fill="#666" font-size="9" font-family="sans-serif">-50%</text>
                            
                            <!-- Bars (Up/Down) -->
                            <rect x="60" y="20" width="12" height="35" rx="3" fill="var(--dash-orange)" />
                            <rect x="110" y="55" width="12" height="25" rx="3" fill="var(--dash-blue)" />
                            <rect x="160" y="10" width="12" height="45" rx="3" fill="var(--dash-orange)" />
                            <rect x="210" y="40" width="12" height="15" rx="3" fill="var(--dash-orange)" />
                            <rect x="260" y="55" width="12" height="35" rx="3" fill="var(--dash-blue)" />
                            
                            <!-- X-Axis Labels -->
                            <text x="66" y="112" fill="#666" font-size="9" font-family="sans-serif" text-anchor="middle">Mon</text>
                            <text x="116" y="112" fill="#666" font-size="9" font-family="sans-serif" text-anchor="middle">Tue</text>
                            <text x="166" y="112" fill="#666" font-size="9" font-family="sans-serif" text-anchor="middle">Wed</text>
                            <text x="216" y="112" fill="#666" font-size="9" font-family="sans-serif" text-anchor="middle">Thu</text>
                            <text x="266" y="112" fill="#666" font-size="9" font-family="sans-serif" text-anchor="middle">Fri</text>
                        </svg>
                    </div>
                    <!-- Legend -->
                    <div class="d-flex align-items-center gap-3 mt-2" style="font-size: 10px; color: #888; font-weight: 700;">
                        <span class="d-flex align-items-center gap-1">
                            <span style="display: inline-block; width: 8px; height: 8px; background-color: var(--dash-orange); border-radius: 2px;"></span> Upward
                        </span>
                        <span class="d-flex align-items-center gap-1">
                            <span style="display: inline-block; width: 8px; height: 8px; background-color: var(--dash-blue); border-radius: 2px;"></span> Downward
                        </span>
                    </div>
                </div>

                <!-- WIDGET 6: Horizontal Nodes Timeline -->
                <div class="widget-card span-4" style="min-height: 250px;">
                    <div>
                        <div class="widget-title">System Status Lifecycle</div>
                        <div class="widget-subtitle">Health check node responses</div>
                    </div>
                    <div class="timeline-container">
                        <div class="timeline-line"></div>
                        <div class="timeline-node active"><span class="node-label">DB</span></div>
                        <div class="timeline-node active"><span class="node-label">Mail</span></div>
                        <div class="timeline-node active"><span class="node-label">Stripe</span></div>
                        <div class="timeline-node"><span class="node-label">AI</span></div>
                    </div>
                </div>

                <!-- WIDGET 11: Segmented Progress Bar -->
                <div class="widget-card span-4" style="min-height: 250px; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div class="widget-title">User Allocation Breakdown</div>
                        <div class="widget-subtitle">Distribution of total database logins</div>
                    </div>
                    
                    <div>
                        <div style="font-size: 28px; font-weight: 800; color: #FFF;"><?php echo e($usersCount ?? 0); ?></div>
                        <div style="font-size: 11px; color: #888; font-weight: 600;">Total registered system accounts</div>
                    </div>

                    <div class="segmented-bar">
                        <div class="seg-part" style="width: 60%; background-color: var(--dash-orange);"></div>
                        <div class="seg-part" style="width: 25%; background-color: var(--dash-green);"></div>
                        <div class="seg-part" style="width: 15%; background-color: var(--dash-blue);"></div>
                    </div>

                    <div class="d-flex justify-content-between" style="font-size: 10px; color: #888; font-weight: 700;">
                        <span>● Cust</span>
                        <span>● Vendor</span>
                        <span>● Adm</span>
                    </div>
                </div>

                <!-- ROW 4: Dynamic Detailed Curve Widget (Spans 12 columns) -->
                <!-- WIDGET 9: Curved profit curve & approvals detail card -->
                <div class="widget-card span-12" style="min-height: 320px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="widget-title">Monthly Platform Growth Ledger</div>
                            <div class="widget-subtitle">Revenue accumulation and active transaction splits</div>
                        </div>
                        <span class="badge bg-dark text-muted font-monospace" style="border: 1px solid var(--dash-border);">ALL TIME</span>
                    </div>

                    <div style="height: 200px; width: 100%; margin-top: 10px;">
                        <svg viewBox="0 0 800 200" width="100%" height="100%">
                            <!-- Grid lines -->
                            <line x1="60" y1="20" x2="780" y2="20" stroke="#222" stroke-width="1" />
                            <line x1="60" y1="70" x2="780" y2="70" stroke="#222" stroke-width="1" />
                            <line x1="60" y1="120" x2="780" y2="120" stroke="#222" stroke-width="1" />
                            <line x1="60" y1="170" x2="780" y2="170" stroke="#222" stroke-width="1" />
                            
                            <!-- Y-Axis Labels -->
                            <text x="5" y="24" fill="#666" font-size="10" font-family="sans-serif">Rs. 150K</text>
                            <text x="5" y="74" fill="#666" font-size="10" font-family="sans-serif">Rs. 100K</text>
                            <text x="5" y="124" fill="#666" font-size="10" font-family="sans-serif">Rs. 50K</text>
                            <text x="5" y="174" fill="#666" font-size="10" font-family="sans-serif">Rs. 0</text>
                            
                            <!-- Orange Curve -->
                            <path d="M 60 170 C 200 60, 400 130, 600 80 C 700 50, 750 40, 780 20" fill="none" stroke="var(--dash-orange)" stroke-width="4" />
                            <circle cx="600" cy="80" r="5" fill="#FFF" stroke="var(--dash-orange)" stroke-width="3" />
                            
                            <!-- Green Curve -->
                            <path d="M 60 170 C 200 130, 400 100, 600 140 C 700 90, 750 100, 780 60" fill="none" stroke="var(--dash-green)" stroke-width="3" />
                            <circle cx="600" cy="140" r="5" fill="#FFF" stroke="var(--dash-green)" stroke-width="3" />
                            
                            <!-- X-Axis Labels -->
                            <text x="60" y="192" fill="#666" font-size="10" font-family="sans-serif" text-anchor="middle">Jan</text>
                            <text x="180" y="192" fill="#666" font-size="10" font-family="sans-serif" text-anchor="middle">Mar</text>
                            <text x="300" y="192" fill="#666" font-size="10" font-family="sans-serif" text-anchor="middle">May</text>
                            <text x="420" y="192" fill="#666" font-size="10" font-family="sans-serif" text-anchor="middle">Jul</text>
                            <text x="540" y="192" fill="#666" font-size="10" font-family="sans-serif" text-anchor="middle">Sep</text>
                            <text x="660" y="192" fill="#666" font-size="10" font-family="sans-serif" text-anchor="middle">Nov</text>
                            <text x="780" y="192" fill="#666" font-size="10" font-family="sans-serif" text-anchor="middle">Dec</text>
                        </svg>
                    </div>
                    <!-- Legends -->
                    <div class="d-flex align-items-center gap-4 mt-2" style="font-size: 11px; color: #888; font-weight: 700;">
                        <span class="d-flex align-items-center gap-2">
                            <span style="display: inline-block; width: 10px; height: 10px; background-color: var(--dash-orange); border-radius: 50%;"></span>
                            <span>Accumulated Platform Sales</span>
                        </span>
                        <span class="d-flex align-items-center gap-2">
                            <span style="display: inline-block; width: 10px; height: 10px; background-color: var(--dash-green); border-radius: 50%;"></span>
                            <span>Active Deals Redeemed</span>
                        </span>
                    </div>
                </div>

                <!-- ROW 5: Pending Merchant Approvals Table (Spans 12 columns) -->
                <div class="widget-card span-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold mb-0 text-white" style="font-size: 16px;">Pending Business Approvals</h5>
                            <span style="font-size: 11px; color: #888;">Review and verify new merchant registrations</span>
                        </div>
                        <a href="<?php echo e(route('admin.businesses')); ?>" class="btn btn-outline-success btn-sm px-4">Manage Approvals</a>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table fr-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Business Name</th>
                                    <th>Owner</th>
                                    <th>Type</th>
                                    <th>Reg Number</th>
                                    <th>Contact</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $pendingApprovals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="fw-bold text-white"><?php echo e($approval->business_name); ?></td>
                                    <td><?php echo e($approval->user->name ?? 'N/A'); ?></td>
                                    <td><span class="badge bg-secondary" style="border-radius: var(--radius-sm);"><?php echo e(ucfirst($approval->business_type)); ?></span></td>
                                    <td><code><?php echo e($approval->reg_number); ?></code></td>
                                    <td><?php echo e($approval->email); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('admin.businesses.show', $approval->id)); ?>" class="btn btn-sm btn-primary px-3">Review</a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fa fa-check-circle fa-2x mb-3 text-success"></i>
                                        <p class="mb-0">All registered businesses have been reviewed. No pending approvals!</p>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Animate Conic Ring (Carbon Target - 75%)
    const conicRing = document.querySelector('.conic-ring');
    if (conicRing) {
        animateRing(conicRing, 75, 'var(--dash-orange)');
    }

    // 2. Animate Mini Rings (User Split - 80% & 20%)
    const miniRings = document.querySelectorAll('.mini-ring');
    if (miniRings.length >= 2) {
        animateRing(miniRings[0], 80, 'var(--dash-blue)');
        animateRing(miniRings[1], 20, 'var(--dash-purple)');
    }

    function animateRing(el, target, color) {
        let current = 0;
        // Set initial state to 0% to override inline styles
        el.style.background = `conic-gradient(${color} 0%, #2d2d2d 0)`;
        
        // Wait for card fade-in animation to finish before starting the fill
        setTimeout(() => {
            const interval = setInterval(() => {
                current += Math.max(1, target / 40); // Increment step
                if (current >= target) {
                    current = target;
                    clearInterval(interval);
                }
                el.style.background = `conic-gradient(${color} ${current}%, #2d2d2d 0)`;
            }, 30); // 30ms per tick for smooth fill
        }, 700);
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projectn_dark\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>