<?php
// admin/manage_tournament.php
require '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

$message = "";

// Handle Tournament Creation Form
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_tournament'])) {
    $title = htmlspecialchars(trim($_POST['title']));
    $game_name = htmlspecialchars(trim($_POST['game_name']));
    $entry_fee = floatval($_POST['entry_fee']);
    $prize_pool = floatval($_POST['prize_pool']);
    $match_time = $_POST['match_time'];
    $total_slots = intval($_POST['total_slots']);

    if (!empty($title) && !empty($game_name) && $entry_fee >= 0 && $prize_pool > 0 && !empty($match_time) && $total_slots > 0) {
        $stmt = $pdo->prepare("INSERT INTO tournaments (title, game_name, entry_fee, prize_pool, match_time, total_slots, status) VALUES (?, ?, ?, ?, ?, ?, 'Upcoming')");
        if ($stmt->execute([$title, $game_name, $entry_fee, $prize_pool, $match_time, $total_slots])) {
            $message = "<div class='bg-green-500 text-white p-3 rounded-xl mb-4 text-center'>Tournament Created Successfully!</div>";
        } else {
            $message = "<div class='bg-red-500 text-white p-3 rounded-xl mb-4 text-center'>Failed to create tournament.</div>";
        }
    } else {
        $message = "<div class='bg-red-500 text-white p-3 rounded-xl mb-4 text-center'>Please fill all fields correctly.</div>";
    }
}

// Fetch All Tournaments
$tournaments = $pdo->query("SELECT * FROM tournaments ORDER BY match_time DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Manage Tournaments - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-white pb-20">

    <div class="flex items-center p-5 bg-gray-800 shadow-md mb-6">
        <a href="dashboard.php" class="mr-4 text-xl text-purple-400"><i class="fa-solid fa-arrow-left"></i></a>
        <h2 class="font-bold text-xl">Manage Tournaments</h2>
    </div>

    <div class="px-5">
        <?= $message ?>

        <div class="bg-gray-800 rounded-[25px] p-6 mb-8 border border-gray-700 shadow-md">
            <h3 class="text-lg font-bold mb-4 text-purple-400"><i class="fa-solid fa-square-plus mr-2"></i>Create New Tournament</h3>
            <form method="POST" action="">
                <div class="mb-4">
                    <label class="block text-gray-400 text-sm mb-1">Tournament Title</label>
                    <input type="text" name="title" required class="w-full px-4 py-2.5 rounded-xl bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="e.g. Sunday Mega Cup">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-400 text-sm mb-1">Game Name</label>
                    <input type="text" name="game_name" required class="w-full px-4 py-2.5 rounded-xl bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="e.g. BGMI / Free Fire">
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Entry Fee (₹)</label>
                        <input type="number" name="entry_fee" required min="0" class="w-full px-4 py-2.5 rounded-xl bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Prize Pool (₹)</label>
                        <input type="number" name="prize_pool" required min="1" class="w-full px-4 py-2.5 rounded-xl bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Total Slots</label>
                        <input type="number" name="total_slots" required min="1" class="w-full px-4 py-2.5 rounded-xl bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Match Date & Time</label>
                        <input type="datetime-local" name="match_time" required class="w-full px-4 py-2.5 rounded-xl bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
                <button type="submit" name="create_tournament" class="w-full bg-purple-600 py-3 rounded-xl font-bold shadow-lg hover:bg-purple-500 transition">Create Tournament</button>
            </form>
        </div>

        <h3 class="text-lg font-bold mb-4 border-l-4 border-purple-500 pl-3">Existing Tournaments</h3>
        <div class="flex flex-col gap-4">
            <?php foreach($tournaments as $t): ?>
            <div class="bg-gray-800 rounded-[20px] p-4 border border-gray-700">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h4 class="font-bold"><?= htmlspecialchars($t['title']) ?></h4>
                        <p class="text-xs text-gray-400"><?= htmlspecialchars($t['game_name']) ?></p>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-full font-bold <?= $t['status'] == 'Upcoming' ? 'bg-purple-900/50 text-purple-300' : ($t['status'] == 'Ongoing' ? 'bg-orange-600 text-white' : 'bg-gray-700 text-gray-400') ?>">
                        <?= $t['status'] ?>
                    </span>
                </div>
                <div class="text-xs text-gray-400 flex justify-between mt-2 border-t border-gray-700 pt-2">
                    <span>Fee: <strong class="text-green-400">₹<?= $t['entry_fee'] ?></strong></span>
                    <span>Prize: <strong class="text-yellow-400">₹<?= $t['prize_pool'] ?></strong></span>
                    <span>Slots: <strong><?= $t['total_slots'] ?></strong></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
