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

$monthlyBudget = 1000; // fixed budget for demo purposes

$balanceQuery = $conn->prepare("SELECT
    SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
    SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense
    FROM transactions WHERE user_id = ?");
$balanceQuery->bind_param("i", $user_id);
$balanceQuery->execute();
$balanceRow = $balanceQuery->get_result()->fetch_assoc();

$totalIncome = $balanceRow['total_income'] ?? 0;
$totalExpense = $balanceRow['total_expense'] ?? 0;
$percentUsed = $monthlyBudget > 0 ? min(100, round(($totalExpense / $monthlyBudget) * 100)) : 0;
$gaugeDeg = round(($percentUsed / 100) * 360);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard-style.css">
</head>
<body>
    <div class="app-shell">
        <div class="sidebar">
            <div class="logo">L</div>
            <div class="nav-icon active" title="Home">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/></svg>
            </div>
            <div class="nav-icon" title="Habits">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="nav-icon" title="Finance">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div class="nav-icon" title="Learning">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            </div>
            <div class="nav-icon bottom-icon" title="Settings">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            </div>
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
                <!-- Habit tracker weekly card -->
                <div class="card">
                    <div class="card-header">
                        <h2>Habit Tracker</h2>
                        <div class="card-nav">
                            <span>‹ Last Week</span>
                            <span>Next Week ›</span>
                        </div>
                    </div>
                    <div class="week-row">
                        <div class="day-col"><div>Mon</div><div class="day-num">22</div></div>
                        <div class="day-col today"><div>Tue</div><div class="day-num">23</div></div>
                        <div class="day-col"><div>Wed</div><div class="day-num">24</div></div>
                        <div class="day-col"><div>Thu</div><div class="day-num">25</div></div>
                        <div class="day-col"><div>Fri</div><div class="day-num">26</div></div>
                        <div class="day-col"><div>Sat</div><div class="day-num">27</div></div>
                        <div class="day-col"><div>Sun</div><div class="day-num">28</div></div>
                    </div>

                    <?php
                    $habitQuery = $conn->prepare("SELECT h.id, h.name, h.time_of_day,
                        (SELECT COUNT(*) FROM habit_logs WHERE habit_id = h.id AND log_date = ?) as done_today
                        FROM habits h WHERE h.user_id = ?");
                    $habitQuery->bind_param("si", $today, $user_id);
                    $habitQuery->execute();
                    $habitResult = $habitQuery->get_result();

                    if ($habitResult->num_rows > 0) {
                        while ($habit = $habitResult->fetch_assoc()) {
                            $checkedClass = $habit['done_today'] > 0 ? 'done' : '';
                            echo '<div class="habit-chip">';
                            echo '<div><div class="habit-name">' . htmlspecialchars($habit['name']) . '</div>';
                            echo '<div class="habit-time">' . htmlspecialchars($habit['time_of_day']) . '</div></div>';
                            echo '<form action="check-habit.php" method="POST" style="margin:0;">';
                            echo '<input type="hidden" name="habit_id" value="' . $habit['id'] . '">';
                            echo '<button type="submit" class="habit-check ' . $checkedClass . '" style="border:none;cursor:pointer;">' . ($habit['done_today'] > 0 ? '&check;' : '') . '</button>';
                            echo '</form>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="placeholder-text">No habits yet</div>';
                    }
                    ?>

                    <form action="add-habit.php" method="POST" style="margin-top: 14px; display: flex; gap: 8px;">
                        <input type="text" name="habit_name" placeholder="New habit" required style="flex:1; padding:8px 12px; background:#1c1c1c; border:1px solid #333; border-radius:8px; color:white; font-size:13px;">
                        <input type="text" name="habit_time" placeholder="Time" style="width:80px; padding:8px 12px; background:#1c1c1c; border:1px solid #333; border-radius:8px; color:white; font-size:13px;">
                        <button type="submit" style="padding:8px 16px; background:#2d6a5f; color:white; border:none; border-radius:8px; font-size:13px; cursor:pointer;">Add</button>
                    </form>
                </div>

                <!-- Learning growth gradient panel -->
                <div class="growth-panel">
                    <h2>Skill Growth</h2>
                    <div>
                        <span class="skill-pill">Cybersecurity</span><br>
                        <span class="skill-pill">Networking</span><br>
                        <span class="skill-pill">PHP &amp; MySQL</span>
                    </div>
                </div>

                <!-- Bottom row -->
                <div class="bottom-row">
                    <!-- Learning resources card -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Learning Resources</h2>
                        </div>
                        <div class="resource-row">
                            <div class="resource-thumb">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            </div>
                            <div class="resource-thumb">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                            </div>
                        </div>
                        <div class="goal-text">
                            I am building a <b>full-stack security lab</b> covering authentication, injection attacks, and remediation — with the goal of a portfolio-ready project by month end.
                        </div>
                    </div>

                    <!-- Finance gauge card -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Monthly Budget</h2>
                        </div>
                        <div class="gauge-wrap">
                            <div class="gauge" style="--gauge-deg: <?php echo $gaugeDeg; ?>deg; background: conic-gradient(#4fae94 0deg, #4fae94 <?php echo $gaugeDeg; ?>deg, #1f1f1f <?php echo $gaugeDeg; ?>deg 360deg);">
                                <div class="gauge-value">
                                    <span class="num"><?php echo $percentUsed; ?>%</span>
                                    <span class="label">Used</span>
                                </div>
                            </div>
                        </div>
                        <div style="font-size:12px; color:#999; text-align:center; margin-bottom:14px;">
                            $<?php echo number_format($totalExpense, 2); ?> of $<?php echo number_format($monthlyBudget, 2); ?>
                        </div>
                    
                        <form action="add-transaction.php" method="POST" style="display:flex; flex-direction:column; gap:8px;">
                            <input type="text" name="description" placeholder="Description" required style="padding:8px 12px; background:#1c1c1c; border:1px solid #333; border-radius:8px; color:white; font-size:13px;">
                            <div style="display:flex; gap:8px;">
                                <input type="number" step="0.01" name="amount" placeholder="Amount" required style="flex:1; padding:8px 12px; background:#1c1c1c; border:1px solid #333; border-radius:8px; color:white; font-size:13px;">
                                <select name="type" style="padding:8px 12px; background:#1c1c1c; border:1px solid #333; border-radius:8px; color:white; font-size:13px;">
                                    <option value="expense">Expense</option>
                                    <option value="income">Income</option>
                                </select>
                            </div>
                            <button type="submit" class="details-btn">Add Transaction</button>
                        </form>
                    </div>

                    <!-- Insights / goals card -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Insights</h2>
                        </div>
                        <div class="insight-desc">A quick look at your active goals across all trackers this week.</div>
                        <div class="active-goals-label">Active goals 3 &#8599;</div>
                        <div class="active-goals-count">84%</div>

                        <div class="goal-row">
                            <span class="goal-name">Save $200 this month</span>
                            <span class="status-badge done">done</span>
                        </div>
                        <div class="goal-row">
                            <span class="goal-name">Finish PHP security course</span>
                            <span class="status-badge progress">in progress</span>
                        </div>
                        <div class="goal-row">
                            <span class="goal-name">Skip gym 3 days</span>
                            <span class="status-badge closed">closed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
