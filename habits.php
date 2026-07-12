<?php
require "auth.php";
require "csrf.php";
require_login();

$displayName = htmlspecialchars($_SESSION['username']);
$initial = strtoupper(substr($_SESSION['username'], 0, 1));
$user_id = $_SESSION['user_id'];
$today = date("Y-m-d");

$habitQuery = $conn->prepare("SELECT h.id, h.name, h.time_of_day,
    (SELECT COUNT(*) FROM habit_logs WHERE habit_id = h.id AND log_date = ?) as done_today,
    (SELECT COUNT(*) FROM habit_logs WHERE habit_id = h.id AND log_date >= DATE_SUB(?, INTERVAL 6 DAY)) as done_this_week
    FROM habits h WHERE h.user_id = ? ORDER BY h.created_at DESC, h.id DESC");
$habitQuery->bind_param("ssi", $today, $today, $user_id);
$habitQuery->execute();
$habits = $habitQuery->get_result()->fetch_all(MYSQLI_ASSOC);

$habitTotal = count($habits);
$habitDone = 0;
foreach ($habits as $h) {
    if ($h['done_today'] > 0) $habitDone++;
}
$habitPercent = $habitTotal > 0 ? round(($habitDone / $habitTotal) * 100) : 0;

// Real week strip (Mon-Sun of the current week), not hardcoded dates.
$weekDays = [];
$weekStart = strtotime("monday this week");
for ($i = 0; $i < 7; $i++) {
    $ts = strtotime("+$i days", $weekStart);
    $weekDays[] = [
        'label' => date("D", $ts),
        'num' => date("j", $ts),
        'isToday' => date("Y-m-d", $ts) === $today,
    ];
}

$activeNav = 'habits';
$searchPlaceholder = 'Search your habits';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Habits</title>
    <link rel="stylesheet" href="dashboard-style.css">
</head>
<body>
    <div class="app-shell">
        <?php require "nav.php"; ?>

        <div class="main-area">
            <?php require "topbar.php"; ?>

            <div class="bento-grid" style="grid-template-columns: 1fr;">
                <div class="card">
                    <div class="card-header">
                        <h2>Habit Tracker</h2>
                        <span class="status-badge done"><?php echo $habitDone; ?>/<?php echo $habitTotal; ?> today (<?php echo $habitPercent; ?>%)</span>
                    </div>

                    <div class="week-row">
                        <?php foreach ($weekDays as $day): ?>
                            <div class="day-col<?php echo $day['isToday'] ? ' today' : ''; ?>">
                                <div><?php echo $day['label']; ?></div>
                                <div class="day-num"><?php echo $day['num']; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div data-searchable-list>
                        <?php if ($habitTotal > 0): ?>
                            <?php foreach ($habits as $habit): ?>
                                <?php
                                $checkedClass = $habit['done_today'] > 0 ? 'done' : '';
                                $weekPercent = round(($habit['done_this_week'] / 7) * 100);
                                ?>
                                <div class="habit-chip" data-search-text="<?php echo htmlspecialchars($habit['name'] . ' ' . $habit['time_of_day']); ?>">
                                    <div>
                                        <div class="habit-name"><?php echo htmlspecialchars($habit['name']); ?></div>
                                        <div class="habit-time"><?php echo htmlspecialchars($habit['time_of_day']); ?> &middot; <?php echo $weekPercent; ?>% this week</div>
                                    </div>
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <form action="check-habit.php" method="POST" style="margin:0;">
                                            <?php csrf_field(); ?>
                                            <input type="hidden" name="habit_id" value="<?php echo (int) $habit['id']; ?>">
                                            <button type="submit" class="habit-check <?php echo $checkedClass; ?>" style="border:none;cursor:pointer;"><?php echo $habit['done_today'] > 0 ? '&check;' : ''; ?></button>
                                        </form>
                                        <form action="delete-habit.php" method="POST" style="margin:0;" data-confirm="Delete this habit? This also removes its history.">
                                            <?php csrf_field(); ?>
                                            <input type="hidden" name="habit_id" value="<?php echo (int) $habit['id']; ?>">
                                            <button type="submit" class="icon-delete-btn" title="Delete habit">&times;</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="placeholder-text">No habits yet &mdash; add your first one below.</div>
                        <?php endif; ?>
                        <div class="placeholder-text" data-search-empty hidden>No habits match your search.</div>
                    </div>

                    <form action="add-habit.php" method="POST" style="margin-top: 14px; display: flex; gap: 8px;">
                        <?php csrf_field(); ?>
                        <input type="text" name="habit_name" placeholder="New habit" required maxlength="100" style="flex:1; padding:8px 12px; background:#1c1c1c; border:1px solid #333; border-radius:8px; color:white; font-size:13px;">
                        <input type="text" name="habit_time" placeholder="Time" maxlength="50" style="width:80px; padding:8px 12px; background:#1c1c1c; border:1px solid #333; border-radius:8px; color:white; font-size:13px;">
                        <button type="submit" style="padding:8px 16px; background:#2d6a5f; color:white; border:none; border-radius:8px; font-size:13px; cursor:pointer;">Add</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="app.js"></script>
</body>
</html>
