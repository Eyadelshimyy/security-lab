<?php
require "auth.php";
require "csrf.php";
require_login();

$displayName = htmlspecialchars($_SESSION['username']);
$initial = strtoupper(substr($_SESSION['username'], 0, 1));
$user_id = $_SESSION['user_id'];

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
$balance = $totalIncome - $totalExpense;
$percentUsed = $monthlyBudget > 0 ? min(100, round(($totalExpense / $monthlyBudget) * 100)) : 0;

$txQuery = $conn->prepare("SELECT id, description, amount, type, created_at FROM transactions WHERE user_id = ? ORDER BY created_at DESC, id DESC");
$txQuery->bind_param("i", $user_id);
$txQuery->execute();
$transactions = $txQuery->get_result()->fetch_all(MYSQLI_ASSOC);

$activeNav = 'finance';
$searchPlaceholder = 'Search transactions';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Finance</title>
    <link rel="stylesheet" href="dashboard-style.css">
</head>
<body>
    <div class="app-shell">
        <?php require "nav.php"; ?>

        <div class="main-area">
            <?php require "topbar.php"; ?>

            <div class="bento-grid" style="grid-template-columns: 1fr 1fr;">
                <div class="card">
                    <div class="card-header">
                        <h2>Monthly Budget</h2>
                    </div>
                    <div class="gauge-wrap">
                        <div class="gauge" data-gauge="<?php echo $percentUsed; ?>">
                            <div class="gauge-value">
                                <span class="num"><span data-count-to="<?php echo $percentUsed; ?>">0</span>%</span>
                                <span class="label">Used</span>
                            </div>
                        </div>
                    </div>
                    <div style="font-size:12px; color:#999; text-align:center; margin-bottom:14px;">
                        $<?php echo number_format($totalExpense, 2); ?> of $<?php echo number_format($monthlyBudget, 2); ?> spent
                    </div>
                    <div class="active-goals-count" style="text-align:center; color: <?php echo $balance >= 0 ? '#7fbfae' : '#d97b7b'; ?>;">
                        $<?php echo number_format($balance, 2); ?>
                    </div>
                    <div class="insight-desc" style="text-align:center;">current balance ($<?php echo number_format($totalIncome, 2); ?> income &minus; $<?php echo number_format($totalExpense, 2); ?> expenses)</div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>Add Transaction</h2>
                    </div>
                    <form action="add-transaction.php" method="POST" style="display:flex; flex-direction:column; gap:10px;">
                        <?php csrf_field(); ?>
                        <input type="text" name="description" class="field" placeholder="Description" required maxlength="255">
                        <div style="display:flex; gap:8px;">
                            <input type="number" step="0.01" min="0.01" name="amount" class="field" placeholder="Amount" required style="flex:1;">
                            <select name="type" class="field">
                                <option value="expense">Expense</option>
                                <option value="income">Income</option>
                            </select>
                        </div>
                        <button type="submit" class="details-btn">Add Transaction</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>Transaction History</h2>
                    <span class="card-tag" style="font-size:11px; color:#7fbfae; background:rgba(45,106,95,0.2); padding:4px 10px; border-radius:20px;"><?php echo count($transactions); ?> total</span>
                </div>

                <div data-searchable-list>
                    <?php if (count($transactions) > 0): ?>
                        <?php foreach ($transactions as $tx): ?>
                            <div class="goal-row" data-search-text="<?php echo htmlspecialchars($tx['description']); ?>">
                                <span class="goal-name">
                                    <?php echo htmlspecialchars($tx['description']); ?>
                                    <span style="color:#666;">&middot; <?php echo date("M j, Y", strtotime($tx['created_at'])); ?></span>
                                </span>
                                <span style="display:flex; align-items:center; gap:12px;">
                                    <span class="status-badge <?php echo $tx['type'] === 'income' ? 'done' : 'closed'; ?>">
                                        <?php echo $tx['type'] === 'income' ? '+' : '-'; ?>$<?php echo number_format($tx['amount'], 2); ?>
                                    </span>
                                    <form action="delete-transaction.php" method="POST" style="margin:0;" data-confirm="Delete this transaction?">
                                        <?php csrf_field(); ?>
                                        <input type="hidden" name="transaction_id" value="<?php echo (int) $tx['id']; ?>">
                                        <button type="submit" class="icon-delete-btn" title="Delete transaction">&times;</button>
                                    </form>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="placeholder-text">No transactions yet &mdash; add your first one above.</div>
                    <?php endif; ?>
                    <div class="placeholder-text" data-search-empty hidden>No transactions match your search.</div>
                </div>
            </div>
        </div>
    </div>
    <script src="app.js"></script>
</body>
</html>
