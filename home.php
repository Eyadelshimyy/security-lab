<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$displayName = htmlspecialchars($_SESSION['username']);
$initial = strtoupper(substr($_SESSION['username'], 0, 1));
$user_id = $_SESSION['user_id'];
$today = date("Y-m-d");

$habitStats = $conn->prepare("SELECT
    COUNT(*) as total,
    SUM(CASE WHEN EXISTS (SELECT 1 FROM habit_logs WHERE habit_id = h.id AND log_date = ?) THEN 1 ELSE 0 END) as done
    FROM habits h WHERE h.user_id = ?");
$habitStats->bind_param("si", $today, $user_id);
$habitStats->execute();
$habitRow = $habitStats->get_result()->fetch_assoc();
$habitTotal = $habitRow['total'] ?? 0;
$habitDone = $habitRow['done'] ?? 0;
$habitPercent = $habitTotal > 0 ? round(($habitDone / $habitTotal) * 100) : 0;

$financeStats = $conn->prepare("SELECT
    SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
    SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense
    FROM transactions WHERE user_id = ?");
$financeStats->bind_param("i", $user_id);
$financeStats->execute();
$financeRow = $financeStats->get_result()->fetch_assoc();
$totalIncome = $financeRow['total_income'] ?? 0;
$totalExpense = $financeRow['total_expense'] ?? 0;
$balance = $totalIncome - $totalExpense;

$learningStats = $conn->prepare("SELECT AVG(progress) as avg_progress, COUNT(*) as total FROM learning_goals WHERE user_id = ?");
$learningStats->bind_param("i", $user_id);
$learningStats->execute();
$learningRow = $learningStats->get_result()->fetch_assoc();
$learningAvg = round($learningRow['avg_progress'] ?? 0);
$learningTotal = $learningRow['total'] ?? 0;

$overallScore = round(($habitPercent + $learningAvg + (min(100, 100 - (($totalExpense / max($totalIncome, 1)) * 100)))) / 3);
$overallScore = max(0, min(100, $overallScore));
$overallDeg = round(($overallScore / 100) * 360);

$totalHabitsCount = $habitTotal;
$weeklyTrend = [];
for ($i = 6; $i >= 0; $i--) {
    $day = date("Y-m-d", strtotime("-$i days"));
    $dayLabel = date("D", strtotime("-$i days"));
    $dayQuery = $conn->prepare("SELECT COUNT(*) as done FROM habit_logs hl
        JOIN habits h ON hl.habit_id = h.id
        WHERE h.user_id = ? AND hl.log_date = ?");
    $dayQuery->bind_param("is", $user_id, $day);
    $dayQuery->execute();
    $dayRow = $dayQuery->get_result()->fetch_assoc();
    $dayDone = $dayRow['done'] ?? 0;
    $dayPercent = $totalHabitsCount > 0 ? round(($dayDone / $totalHabitsCount) * 100) : 0;
    $weeklyTrend[] = ['label' => $dayLabel, 'percent' => min(100, $dayPercent)];
}

$totalTransactionsQuery = $conn->prepare("SELECT COUNT(*) as total FROM transactions WHERE user_id = ?");
$totalTransactionsQuery->bind_param("i", $user_id);
$totalTransactionsQuery->execute();
$totalTransactions = $totalTransactionsQuery->get_result()->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <link rel="stylesheet" href="dashboard-style.css">
</head>
<body>
    <div class="app-shell">
        <div class="sidebar">
            <div class="logo">L</div>
            <a href="home.php" class="nav-icon active" title="Home">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/></svg>
            </a>
            <a href="habits.php" class="nav-icon" title="Habits">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </a>
            <a href="finance.php" class="nav-icon" title="Finance">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </a>
            <a href="learning.php" class="nav-icon" title="Learning">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            </a>
            <a href="#" class="nav-icon bottom-icon" title="Settings">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            </a>
        </div>

        <div class="main-area">
            <div class="topbar">
                <input class="search-bar" type="text" placeholder="Enter your search request" disabled>
                <div class="topbar-right">
                    <div class="icon-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    </div>
                    <div class="user-avatar"><?php echo $initial; ?></div>
                    <a href="logout.php" class="logout-link">Logout</a>
                </div>
            </div>

            <div class="bento-grid">
                <div class="growth-panel" style="justify-content: center; align-items: center; flex-direction: row; gap: 40px;">
                    <div class="gauge" style="width:160px; height:160px; background: conic-gradient(white 0deg, white <?php echo $overallDeg; ?>deg, rgba(255,255,255,0.15) <?php echo $overallDeg; ?>deg 360deg);">
                        <div class="gauge-value" style="color:white;">
                            <span class="num" style="color:white;"><?php echo $overallScore; ?></span>
                            <span class="label" style="color:rgba(255,255,255,0.7);">Performance</span>
                        </div>
                    </div>
                    <div>
                        <h2 style="margin-bottom: 6px;">Welcome back, <?php echo $displayName; ?></h2>
                        <p style="opacity:0.8; font-size:14px; margin-bottom:0;">Here's a quick glimpse of how things are going across your habits, finances, and learning goals.</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>Habits Today</h2>
                        <span class="status-badge done"><?php echo $habitPercent; ?>%</span>
                    </div>
                    <div class="active-goals-count"><?php echo $habitDone; ?>/<?php echo $habitTotal; ?></div>
                    <div class="insight-desc">habits completed today</div>
                    <a href="habits.php" class="details-btn" style="display:block; text-align:center; text-decoration:none;">Open Habit Tracker</a>
                </div>

                <div class="bottom-row" style="grid-template-columns: 1fr 1fr;">
                    <div class="card">
                        <div class="card-header">
                            <h2>Finance Balance</h2>
                        </div>
                        <div class="active-goals-count" style="color: <?php echo $balance >= 0 ? '#7fbfae' : '#d97b7b'; ?>;">
                            $<?php echo number_format($balance, 2); ?>
                        </div>
                        <div class="insight-desc">current balance</div>
                        <a href="finance.php" class="details-btn" style="display:block; text-align:center; text-decoration:none;">Open Finance Tracker</a>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h2>Learning Progress</h2>
                        </div>
                        <div class="active-goals-count"><?php echo $learningAvg; ?>%</div>
                        <div class="insight-desc"><?php echo $learningTotal; ?> active goals</div>
                        <a href="learning.php" class="details-btn" style="display:block; text-align:center; text-decoration:none;">Open Learning Tracker</a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>Performance Overview</h2>
                    <span class="card-tag" style="font-size:11px; color:#7fbfae; background:rgba(45,106,95,0.2); padding:4px 10px; border-radius:20px;">Last 7 days</span>
                </div>

                <div style="display:flex; gap:16px; margin-bottom:24px;">
                    <div style="flex:1; background:#1a1a1a; border-radius:12px; padding:16px; text-align:center;">
                        <div style="font-size:22px; font-weight:700;"><?php echo $totalHabitsCount; ?></div>
                        <div style="font-size:11px; color:#999;">Total Habits</div>
                    </div>
                    <div style="flex:1; background:#1a1a1a; border-radius:12px; padding:16px; text-align:center;">
                        <div style="font-size:22px; font-weight:700;"><?php echo $totalTransactions; ?></div>
                        <div style="font-size:11px; color:#999;">Transactions Logged</div>
                    </div>
                    <div style="flex:1; background:#1a1a1a; border-radius:12px; padding:16px; text-align:center;">
                        <div style="font-size:22px; font-weight:700;"><?php echo $learningTotal; ?></div>
                        <div style="font-size:11px; color:#999;">Learning Goals</div>
                    </div>
                    <div style="flex:1; background:#1a1a1a; border-radius:12px; padding:16px; text-align:center;">
                        <div style="font-size:22px; font-weight:700; color:#7fbfae;"><?php echo $overallScore; ?>%</div>
                        <div style="font-size:11px; color:#999;">Overall Score</div>
                    </div>
                </div>

                <div style="display:flex; align-items:flex-end; gap:14px; height:140px; padding:0 10px;">
                    <?php foreach ($weeklyTrend as $day): ?>
                        <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:8px; height:100%; justify-content:flex-end;">
                            <div style="width:100%; max-width:36px; height:<?php echo max(4, $day['percent']); ?>%; background:linear-gradient(180deg, #4fae94, #2d6a5f); border-radius:8px 8px 0 0; transition:height 0.3s ease;"></div>
                            <div style="font-size:11px; color:#777;"><?php echo $day['label']; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
