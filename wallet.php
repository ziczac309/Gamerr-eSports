<?php
// wallet.php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// Handle Add Money Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_money'])) {
    $amount = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $utr = htmlspecialchars(trim($_POST['utr_number']));

    if ($amount > 0 && !empty($utr)) {
        $stmt = $pdo->prepare("INSERT INTO payments (user_id, amount, utr_number, status) VALUES (?, ?, ?, 'Pending')");
        if ($stmt->execute([$user_id, $amount, $utr])) {
            $message = "<div class='bg-green-500 text-white p-3 rounded-xl mb-4 text-center'>Payment Request Submitted! Admin will verify soon.</div>";
        } else {
            $message = "<div class='bg-red-500 text-white p-3 rounded-xl mb-4 text-center'>Error submitting request.</div>";
        }
    } else {
        $message = "<div class='bg-red-500 text-white p-3 rounded-xl mb-4 text-center'>Invalid amount or UTR.</div>";
    }
}

// Fetch user data for balance
$stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch Transaction History
$historyStmt = $pdo->prepare("SELECT * FROM payments WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
$historyStmt->execute([$user_id]);
$transactions = $historyStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Wallet - GAMER ESPORTS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-white pb-20">

    <div class="flex items-center p-5 bg-gray-800 shadow-md">
        <a href="index.php" class="mr-4 text-xl"><i class="fa-solid fa-arrow-left"></i></a>
        <h2 class="font-bold text-xl">My Wallet</h2>
    </div>

    <div class="px-5 mt-6 mb-6">
        <div class="bg-gradient-to-r from-purple-600 to-purple-900 p-6 rounded-[25px] shadow-lg shadow-purple-900/50 text-center">
            <p class="text-purple-200 text-sm mb-1">Available Balance</p>
            <h1 class="text-4xl font-bold">₹ <?= number_format($user['wallet_balance'], 2) ?></h1>
        </div>
    </div>

    <div class="px-5">
        <?= $message ?>
        
        <div class="bg-gray-800 rounded-[25px] p-6 mb-8 shadow-md border border-gray-700">
            <h3 class="text-lg font-bold mb-4 text-purple-400">Add Money (UPI)</h3>
            
            <div class="bg-gray-900 p-4 rounded-xl text-center mb-5 border border-purple-500/30">
                <p class="text-sm text-gray-400 mb-2">Scan QR or Pay to UPI ID:</p>
                <p class="font-bold text-lg text-white">admin@upi</p>
                <div class="w-32 h-32 bg-white mx-auto my-3 flex items-center justify-center rounded">
                    <i class="fa-solid fa-qrcode text-6xl text-black"></i>
                </div>
            </div>

            <form method="POST" action="">
                <div class="mb-4">
                    <label class="block text-gray-400 text-sm mb-2">Amount (₹)</label>
                    <input type="number" name="amount" required min="10" class="w-full px-4 py-3 rounded-xl bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-400 text-sm mb-2">12-Digit UTR Number</label>
                    <input type="text" name="utr_number" required maxlength="12" class="w-full px-4 py-3 rounded-xl bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="e.g. 234567890123">
                </div>
                <button type="submit" name="add_money" class="w-full bg-purple-600 py-3 rounded-xl font-bold text-lg shadow-lg hover:bg-purple-500 transition">Submit Request</button>
            </form>
        </div>

        <h3 class="text-lg font-bold mb-4 border-l-4 border-purple-500 pl-3">Recent Transactions</h3>
        <div class="flex flex-col gap-3">
            <?php if(count($transactions) > 0): ?>
                <?php foreach($transactions as $txn): ?>
                <div class="bg-gray-800 p-4 rounded-xl flex justify-between items-center border border-gray-700">
                    <div>
                        <p class="font-bold">Deposit Request</p>
                        <p class="text-xs text-gray-400">UTR: <?= htmlspecialchars($txn['utr_number']) ?></p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-green-400">+₹ <?= $txn['amount'] ?></p>
                        <?php 
                            $color = $txn['status'] == 'Approved' ? 'text-green-500' : ($txn['status'] == 'Rejected' ? 'text-red-500' : 'text-yellow-500');
                        ?>
                        <p class="text-xs font-bold <?= $color ?>"><?= $txn['status'] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-500 text-sm text-center py-4">No transactions yet.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
