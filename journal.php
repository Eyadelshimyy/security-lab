<?php
require "auth.php";
require "csrf.php";
require_login();

$displayName = htmlspecialchars($_SESSION['username']);
$initial = strtoupper(substr($_SESSION['username'], 0, 1));
$user_id = $_SESSION['user_id'];

$entriesQuery = $conn->prepare("SELECT id, title, body, created_at FROM journal_entries WHERE user_id = ? ORDER BY created_at DESC, id DESC");
$entriesQuery->bind_param("i", $user_id);
$entriesQuery->execute();
$entries = $entriesQuery->get_result()->fetch_all(MYSQLI_ASSOC);
$entryTotal = count($entries);

$activeNav = 'journal';
$searchPlaceholder = 'Search journal entries';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Journal</title>
    <link rel="stylesheet" href="dashboard-style.css">
    <script>(function(){try{if(localStorage.getItem('theme')==='red'){document.documentElement.setAttribute('data-theme','red');}}catch(e){}})();</script>
</head>
<body>
    <div class="app-shell">
        <?php require "nav.php"; ?>

        <div class="main-area">
            <?php require "topbar.php"; ?>

            <div class="bento-grid" style="grid-template-columns: 1fr;">
                <div class="card">
                    <div class="card-header">
                        <h2>New Entry</h2>
                    </div>
                    <form action="add-journal-entry.php" method="POST" style="display:flex; flex-direction:column; gap:10px;">
                        <?php csrf_field(); ?>
                        <input type="text" name="title" class="field" placeholder="Title" required maxlength="150">
                        <textarea name="body" class="field" placeholder="What's on your mind?" required maxlength="10000" rows="4" style="resize:vertical; font-family:inherit;"></textarea>
                        <button type="submit" class="details-btn" style="width:auto; align-self:flex-end; padding:10px 24px;">Save Entry</button>
                    </form>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>Entries</h2>
                        <span class="tag"><?php echo $entryTotal; ?> total</span>
                    </div>

                    <div data-searchable-list>
                        <?php if ($entryTotal > 0): ?>
                            <?php foreach ($entries as $entry): ?>
                                <?php
                                $body = $entry['body'];
                                $preview = strlen($body) > 220 ? substr($body, 0, 220) . '…' : $body;
                                ?>
                                <div class="habit-chip" style="align-items:flex-start; flex-direction:column; gap:10px;" data-search-text="<?php echo htmlspecialchars($entry['title'] . ' ' . $entry['body']); ?>">
                                    <div style="display:flex; align-items:center; justify-content:space-between; width:100%;">
                                        <div class="habit-name"><?php echo htmlspecialchars($entry['title']); ?></div>
                                        <div style="display:flex; align-items:center; gap:12px;">
                                            <span class="habit-time"><?php echo date("M j, Y g:ia", strtotime($entry['created_at'])); ?></span>
                                            <form action="delete-journal-entry.php" method="POST" style="margin:0;" data-confirm="Delete this journal entry?">
                                                <?php csrf_field(); ?>
                                                <input type="hidden" name="entry_id" value="<?php echo (int) $entry['id']; ?>">
                                                <button type="submit" class="icon-delete-btn" title="Delete entry">&times;</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div style="font-size:13px; color:var(--text-dim); line-height:1.6; white-space:pre-wrap;"><?php echo nl2br(htmlspecialchars($preview)); ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="placeholder-text">No journal entries yet &mdash; write your first one above.</div>
                        <?php endif; ?>
                        <div class="placeholder-text" data-search-empty hidden>No entries match your search.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="app.js"></script>
</body>
</html>
