<?php
// Expects $initial to be set. Optional $searchPlaceholder overrides the default text.
$searchPlaceholder = $searchPlaceholder ?? 'Enter your search request';
?>
<div class="topbar">
    <input class="search-bar" type="text" id="pageSearch" placeholder="<?php echo htmlspecialchars($searchPlaceholder); ?>" autocomplete="off">
    <div class="topbar-right">
        <div class="icon-btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        </div>
        <div class="user-avatar"><?php echo $initial; ?></div>
        <a href="logout.php" class="logout-link">Logout</a>
    </div>
</div>
<div id="flashToast" class="flash-toast" hidden></div>
