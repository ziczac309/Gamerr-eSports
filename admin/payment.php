<?php
// admin/payments.php
require '../config.php';

// Strict Admin Access Check
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

$message = "";

// Handle Action (Approve / Reject)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $payment_id = intval($_POST['payment_id']);
    $action = $_POST['action'];

    // Fetch payment details first
    $stmt = $pdo->prepare("SELECT * FROM payments WHERE id = ? AND status = 'Pending'");
    $stmt->execute([$payment_id]);
    $payment = $stmt->fetch();

    if ($payment) {
        if ($action === 'Approve') {
            try {
                $pdo->beginTransaction();

                // 1. Update Payment Status
                $updatePay = $pdo->prepare("UPDATE payments SET status = 'Approved' WHERE id = ?");
                $updatePay->execute([$payment_id]);

                // 2. Add Balance to User Wallet
                $updateWallet = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
                $updateWallet->execute([$payment['amount'], $payment['user_id']]);

                $pdo->commit();
                $message = "<div class='bg-green-500 text-white p-3 rounded-xl mb-4 text-center'>Payment approved successfully! Wallet updated.</div>";
            } catch (Exception $e) {
                $pdo->rollBack();
                $message = "<div class='bg-red-500 text-white p-3 rounded-xl mb-4 text-center'>Transaction failed.</div>";
            }
        } elseif ($action === 'Reject') {
            $updatePay = $pdo->prepare("UPDATE payments SET status = 'Rejected' WHERE id = ?");
            $updatePay->execute([$payment_id]);
            $message = "<div class='bg-orange-500 text-white p-3 rounded-xl mb-4 text-center'>Payment request rejected.</div>";
        }
    }
}

// Fetch Pending Payments with Usernames
$query = "SELECT p.*, u.username FROM payments p JOIN users u ON p.user_id = u.id WHERE p.status = 'Pending' ORDER BY p.created_at ASC";
$pendingPayments = $pdo->query($query)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Verify Payments - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-white pb-20">

    <div class="flex items-center p-5 bg-gray-800 shadow-md mb-6">
        <a href="dashboard.php" class="mr-4 text-xl text-purple-400"><i class="fa-solid fa-arrow-left"></i></a>
        <h2 class="font-bold text-xl">Verify Pending Payments</h2>
    </div>

    <div class="px-5">
        <?= $message ?>

        <?php if(count($pendingPayments) > 0): ?>
            <?php foreach($pendingPayments as $p): ?>
            <div class="bg-gray-800 rounded-[25px] p-5 mb-4 border border-gray-700 shadow-md">
                <div class="flex justify-between items-center mb-3">
                    <div>
                        <span class="text-xs text-purple-400 font-bold">User</span>
                        <h4 class="font-bold text-lg text-white"><?= htmlspecialchars($p['username']) ?></h4>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-green-400 font-bold">Amount</span>
                        <h4 class="font-bold text-xl text-green-400">₹ <?= number_format($p['amount'], 2) ?></h4>
                    </div>
                </div>

                <div class="bg-gray-900 p-3 rounded-xl text-sm mb-4 border border-gray-700">
                    <p class="text-gray-400"><span class="font-bold text-gray-300">UTR:</span> <?= htmlspecialchars($p['utr_number']) ?></p>
                    <p class="text-gray-400 text-xs mt-1">Requested: <?= date('d M, h:i A', strtotime($p['created_at'])) ?></p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <form method="POST" action="">
                        <input type="hidden" name="payment_id" value="<?= $p['id'] ?>">
                        <button type="submit" name="action" value="Approve" class="w-full bg-green-600 hover:bg-green-500 text-white font-bold py-2.5 rounded-xl transition text-sm">
                            <i class="fa-solid fa-check mr-1"></i> Approve
                        </button>
                    </form>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="payment_id" value="<?= $p['id'] ?>">
                        <button type="submit" name="action" value="Reject" class="w-full bg-red-600 hover:bg-red-500 text-white font-bold py.2.5 rounded-xl transition text-sm">
                            <i class="fa-solid fa-xmark mr-1"></i> Reject
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-10 text-gray-500">
                <i class="fa-solid fa-square-check text-5xl mb-3 text-gray-700"></i>
                <p>No pending payment verification requests found.</p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
