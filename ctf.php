<?php
require "auth.php";
require "csrf.php";
require_login();

$displayName = htmlspecialchars($_SESSION['username']);
$initial = strtoupper(substr($_SESSION['username'], 0, 1));
$user_id = $_SESSION['user_id'];

$platforms = ['HackTheBox', 'TryHackMe', 'CTFtime', 'PicoCTF', 'Other'];
$categories = ['Web', 'Pwn', 'Reversing', 'Crypto', 'Forensics', 'Network', 'OSINT', 'Misc'];
$statusLabels = ['not_started' => 'Not Started', 'in_progress' => 'In Progress', 'solved' => 'Solved'];
$statusBadgeClass = ['not_started' => 'pending', 'in_progress' => 'progress', 'solved' => 'done'];

$notesQuery = $conn->prepare("SELECT id, room_name, platform, category, status, notes FROM ctf_notes WHERE user_id = ? ORDER BY created_at DESC, id DESC");
$notesQuery->bind_param("i", $user_id);
$notesQuery->execute();
$notes = $notesQuery->get_result()->fetch_all(MYSQLI_ASSOC);

$noteTotal = count($notes);
$solvedTotal = 0;
foreach ($notes as $n) {
    if ($n['status'] === 'solved') $solvedTotal++;
}
$solvedPercent = $noteTotal > 0 ? round(($solvedTotal / $noteTotal) * 100) : 0;

$activeNav = 'ctf';
$searchPlaceholder = 'Search CTF rooms';
?>
<!DOCTYPE html>
<html>
<head>
    <title>CTF Notes</title>
    <link rel="stylesheet" href="dashboard-style.css">
    <script>(function(){try{if(localStorage.getItem('theme')==='red'){document.documentElement.setAttribute('data-theme','red');}}catch(e){}})();</script>
</head>
<body>
    <div class="app-shell">
        <?php require "nav.php"; ?>

        <div class="main-area">
            <?php require "topbar.php"; ?>

            <div class="bento-grid" style="grid-template-columns: 1fr;">
                <div class="growth-panel" style="justify-content: center; align-items: center; flex-direction: row; gap: 40px; min-height: unset; padding: 24px;">
                    <div class="gauge" data-gauge="<?php echo $solvedPercent; ?>" style="width:110px; height:110px; --gauge-color:#ffffff; --track-color: rgba(255,255,255,0.15);">
                        <div class="gauge-value" style="color:white;">
                            <span class="num" style="color:white; font-size:20px;"><span data-count-to="<?php echo $solvedPercent; ?>">0</span>%</span>
                            <span class="label" style="color:rgba(255,255,255,0.7);">Solved</span>
                        </div>
                    </div>
                    <div>
                        <h2 style="margin-bottom: 6px;">CTF Notes</h2>
                        <p style="opacity:0.8; font-size:14px; margin-bottom:0;"><?php echo $solvedTotal; ?> of <?php echo $noteTotal; ?> room<?php echo $noteTotal === 1 ? '' : 's'; ?> solved &mdash; keep a running log of what you tried and what worked.</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>Add Room</h2>
                    </div>
                    <form action="add-ctf-note.php" method="POST" style="display:flex; gap:8px; flex-wrap:wrap;">
                        <?php csrf_field(); ?>
                        <input type="text" name="room_name" class="field" placeholder="Room / challenge name" required maxlength="150" style="flex:2; min-width:180px;">
                        <select name="platform" class="field" style="flex:1; min-width:140px;">
                            <?php foreach ($platforms as $p): ?>
                                <option value="<?php echo htmlspecialchars($p); ?>"><?php echo htmlspecialchars($p); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="category" class="field" style="flex:1; min-width:140px;">
                            <?php foreach ($categories as $c): ?>
                                <option value="<?php echo htmlspecialchars($c); ?>"><?php echo htmlspecialchars($c); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn-add">Add Room</button>
                    </form>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>Rooms</h2>
                        <span class="tag"><?php echo $noteTotal; ?> total</span>
                    </div>

                    <div data-searchable-list>
                        <?php if ($noteTotal > 0): ?>
                            <?php foreach ($notes as $note): ?>
                                <div class="habit-chip" style="flex-direction:column; align-items:stretch; gap:12px;" data-search-text="<?php echo htmlspecialchars($note['room_name'] . ' ' . $note['platform'] . ' ' . $note['category'] . ' ' . $note['notes']); ?>">
                                    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px;">
                                        <div>
                                            <div class="habit-name"><?php echo htmlspecialchars($note['room_name']); ?></div>
                                            <div class="habit-time"><?php echo htmlspecialchars($note['platform']); ?> &middot; <?php echo htmlspecialchars($note['category']); ?></div>
                                        </div>
                                        <div style="display:flex; align-items:center; gap:10px;">
                                            <span class="status-badge <?php echo $statusBadgeClass[$note['status']]; ?>"><?php echo $statusLabels[$note['status']]; ?></span>
                                            <form action="delete-ctf-note.php" method="POST" style="margin:0;" data-confirm="Delete this CTF room and its notes?">
                                                <?php csrf_field(); ?>
                                                <input type="hidden" name="note_id" value="<?php echo (int) $note['id']; ?>">
                                                <button type="submit" class="icon-delete-btn" title="Delete room">&times;</button>
                                            </form>
                                        </div>
                                    </div>
                                    <form action="update-ctf-note.php" method="POST" style="display:flex; flex-direction:column; gap:8px;">
                                        <?php csrf_field(); ?>
                                        <input type="hidden" name="note_id" value="<?php echo (int) $note['id']; ?>">
                                        <textarea name="notes" class="field" placeholder="What did you try? What worked?" rows="2" maxlength="10000" style="resize:vertical; font-family:inherit;"><?php echo htmlspecialchars($note['notes']); ?></textarea>
                                        <div style="display:flex; gap:8px;">
                                            <select name="status" class="field" style="flex:1;">
                                                <?php foreach ($statusLabels as $value => $label): ?>
                                                    <option value="<?php echo $value; ?>" <?php echo $note['status'] === $value ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" class="btn-add">Save</button>
                                        </div>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="placeholder-text">No CTF rooms logged yet &mdash; add your first one above.</div>
                        <?php endif; ?>
                        <div class="placeholder-text" data-search-empty hidden>No rooms match your search.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="app.js"></script>
</body>
</html>
