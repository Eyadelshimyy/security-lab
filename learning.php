<?php
require "auth.php";
require "csrf.php";
require_login();

$displayName = htmlspecialchars($_SESSION['username']);
$initial = strtoupper(substr($_SESSION['username'], 0, 1));
$user_id = $_SESSION['user_id'];

$goalsQuery = $conn->prepare("SELECT id, title, progress, created_at FROM learning_goals WHERE user_id = ? ORDER BY created_at DESC, id DESC");
$goalsQuery->bind_param("i", $user_id);
$goalsQuery->execute();
$goals = $goalsQuery->get_result()->fetch_all(MYSQLI_ASSOC);

$goalTotal = count($goals);
$goalAvg = 0;
if ($goalTotal > 0) {
    $sum = 0;
    foreach ($goals as $g) $sum += $g['progress'];
    $goalAvg = round($sum / $goalTotal);
}

$activeNav = 'learning';
$searchPlaceholder = 'Search learning goals';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Learning</title>
    <link rel="stylesheet" href="dashboard-style.css">
</head>
<body>
    <div class="app-shell">
        <?php require "nav.php"; ?>

        <div class="main-area">
            <?php require "topbar.php"; ?>

            <div class="bento-grid" style="grid-template-columns: 1fr;">
                <div class="growth-panel" style="justify-content: center; align-items: center; flex-direction: row; gap: 40px; min-height: unset; padding: 24px;">
                    <div class="gauge" data-gauge="<?php echo $goalAvg; ?>" style="width:110px; height:110px; --gauge-color:#ffffff; --track-color: rgba(255,255,255,0.15);">
                        <div class="gauge-value" style="color:white;">
                            <span class="num" style="color:white; font-size:20px;"><span data-count-to="<?php echo $goalAvg; ?>">0</span>%</span>
                            <span class="label" style="color:rgba(255,255,255,0.7);">Avg. progress</span>
                        </div>
                    </div>
                    <div>
                        <h2 style="margin-bottom: 6px;">Skill Growth</h2>
                        <p style="opacity:0.8; font-size:14px; margin-bottom:0;"><?php echo $goalTotal; ?> active learning goal<?php echo $goalTotal === 1 ? '' : 's'; ?></p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>Learning Goals</h2>
                    </div>

                    <div data-searchable-list>
                        <?php if ($goalTotal > 0): ?>
                            <?php foreach ($goals as $goal): ?>
                                <div class="habit-chip" style="align-items:center;" data-search-text="<?php echo htmlspecialchars($goal['title']); ?>">
                                    <div style="flex:1;">
                                        <div class="habit-name"><?php echo htmlspecialchars($goal['title']); ?></div>
                                        <div style="background:#1f1f1f; border-radius:20px; height:6px; margin-top:8px; overflow:hidden;">
                                            <div class="bar-fill-h" data-bar-width="<?php echo (int) $goal['progress']; ?>" style="width:0%; height:100%; background:linear-gradient(90deg, #4fae94, #2d6a5f); transition: width 0.9s cubic-bezier(.16,1,.3,1); box-shadow: 0 0 10px -2px rgba(79,174,148,0.8);"></div>
                                        </div>
                                    </div>
                                    <form action="update-learning-goal.php" method="POST" style="margin:0 0 0 16px; display:flex; align-items:center; gap:8px;">
                                        <?php csrf_field(); ?>
                                        <input type="hidden" name="goal_id" value="<?php echo (int) $goal['id']; ?>">
                                        <input type="number" name="progress" class="field" min="0" max="100" value="<?php echo (int) $goal['progress']; ?>" style="width:60px; padding:6px 8px; font-size:12px;">
                                        <button type="submit" class="details-btn" style="width:auto; padding:8px 12px;">Update</button>
                                    </form>
                                    <form action="delete-learning-goal.php" method="POST" style="margin:0 0 0 8px;" data-confirm="Delete this learning goal?">
                                        <?php csrf_field(); ?>
                                        <input type="hidden" name="goal_id" value="<?php echo (int) $goal['id']; ?>">
                                        <button type="submit" class="icon-delete-btn" title="Delete goal">&times;</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="placeholder-text">No learning goals yet &mdash; add your first one below.</div>
                        <?php endif; ?>
                        <div class="placeholder-text" data-search-empty hidden>No goals match your search.</div>
                    </div>

                    <form action="add-learning-goal.php" method="POST" style="margin-top: 14px; display: flex; gap: 8px;">
                        <?php csrf_field(); ?>
                        <input type="text" name="title" class="field" placeholder="New learning goal" required maxlength="150" style="flex:1;">
                        <button type="submit" class="btn-add">Add</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="app.js"></script>
</body>
</html>
