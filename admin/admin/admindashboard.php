<?php
// admin/dashboard.php
require '../config.php';

// Strict Admin Access Check
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

// Fetch Statistics
$usersCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$staffCount = $pdo->query("SELECT COUNT(*) FROM staff")->fetchColumn();
$tournamentsCount = $pdo->query("SELECT COUNT(*) FROM tournaments")->fetchColumn();
$activeMatches = $pdo->query("SELECT COUNT(*) FROM tournaments WHERE status = 'Ongoing'")->fetchColumn();

// Fetch Total Revenue (Sum of approved payments)
$totalRevenue = $pdo->query("SELECT SUM(amount) FROM payments WHERE status = 'Approved'")->fetchColumn();
$totalRevenue = $totalRevenue ? $totalRevenue : 0.00;

// Fetch Total Prize Distributed (Sum of prize pools from completed matches)
$prizeDistributed = $pdo->query("SELECT SUM(prize_pool) FROM tournaments WHERE status = 'Completed'")->fetchColumn();
$prizeDistributed = $prizeDistributed ? $prizeDistributed : 0.00;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Admin Dashboard - GAMER ESPORTS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-white pb-20">

    <div class="flex justify-between items-center p-5 bg-gray-800 shadow-lg mb-6">
        <div>
            <h2 class="font-bold text-xl text-purple-400">Admin Panel</h2>
            <p class="text-gray-400 text-sm">Overview & Management</p>
        </div>
        <a href="../logout.php" class="text-red-400 font-bold"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>

    <div class="px-5 grid grid-cols-2 gap-4 mb-8">
        
        <div class="col-span-2 bg-gradient-to-r from-green-600 to-green-800 p-5 rounded-[25px] shadow-lg">
            <p class="text-green-200 text-sm mb-1">Total Revenue</p>
            <h1 class="text-3xl font-bold">₹ <?= number_format($totalRevenue, 2) ?></h1>
        </div>

        <div class="col-span-2 bg-gradient-to-r from-yellow-600 to-yellow-800 p-5 rounded-[25px] shadow-lg">
            <p class="text-yellow-200 text-sm mb-1">Prizes Distributed</p>
            <h1 class="text-3xl font-bold">₹ <?= number_format($prizeDistributed, 2) ?></h1>
        </div>

        <div class="bg-gray-800 p-5 rounded-[20px] text-center border border-gray-700">
            <i class="fa-solid fa-users text-2xl text-purple-400 mb-2"></i>
            <p class="text-gray-400 text-xs">Total Users</p>
            <p class="text-xl font-bold"><?= $usersCount ?></p>
        </div>
        
        <div class="bg-gray-800 p-5 rounded-[20px] text-center border border-gray-700">
            <i class="fa-solid fa-gamepad text-2xl text-purple-400 mb-2"></i>
            <p class="text-gray-400 text-xs">Tournaments</p>
            <p class="text-xl font-bold"><?= $tournamentsCount ?></p>
        </div>

        <div class="bg-gray-800 p-5 rounded-[20px] text-center border border-gray-700">
            <i class="fa-solid fa-fire text-2xl text-orange-400 mb-2"></i>
            <p class="text-gray-400 text-xs">Active Matches</p>
            <p class="text-xl font-bold"><?= $activeMatches ?></p>
        </div>

        <div class="bg-gray-800 p-5 rounded-[20px] text-center border border-gray-700">
            <i class="fa-solid fa-user-tie text-2xl text-blue-400 mb-2"></i>
            <p class="text-gray-400 text-xs">Total Staff</p>
            <p class="text-xl font-bold"><?= $staffCount ?></p>
        </div>
    </div>

    <div class="px-5">
        <h3 class="text-lg font-bold mb-4 border-l-4 border-purple-500 pl-3">Management</h3>
        <div class="flex flex-col gap-3">
            <a href="manage_tournament.php" class="bg-gray-800 p-4 rounded-xl flex justify-between items-center">
                <span><i class="fa-solid fa-trophy w-6 text-purple-400"></i> Tournaments</span>
                <i class="fa-solid fa-chevron-right text-gray-500"></i>
            </a>
            <a href="payments.php" class="bg-gray-800 p-4 rounded-xl flex justify-between items-center">
                <span><i class="fa-solid fa-money-check w-6 text-green-400"></i> Verify Payments</span>
                <i class="fa-solid fa-chevron-right text-gray-500"></i>
            </a>
            <a href="users.php" class="bg-gray-800 p-4 rounded-xl flex justify-between items-center">
                <span><i class="fa-solid fa-users-gear w-6 text-blue-400"></i> Users & Staff</span>
                <i class="fa-solid fa-chevron-right text-gray-500"></i>
            </a>
            <a href="banners.php" class="bg-gray-800 p-4 rounded-xl flex justify-between items-center">
                <span><i class="fa-solid fa-images w-6 text-pink-400"></i> Banners</span>
                <i class="fa-solid fa-chevron-right text-gray-500"></i>
            </a>
        </div>
    </div>
</body>
</html>
