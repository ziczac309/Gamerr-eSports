<?php
// staff/dashboard.php
require '../config.php';

// Strict Staff Access Check
if (!isset($_SESSION['staff_id'])) {
    header("Location: ../login.php");
    exit;
}

// Fetch Tournaments Grouped by Status for the Staff
$upcomingStmt =$pdo->query("SELECT * FROM tournaments WHERE status = 'Upcoming' ORDER BY match_time ASC");
$upcoming =$upcomingStmt->fetchAll();

$ongoingStmt =$pdo->query("SELECT * FROM tournaments WHERE status = 'Ongoing' ORDER BY match_time ASC");
$ongoing =$ongoingStmt->fetchAll();

$completedStmt =$pdo->query("SELECT * FROM tournaments WHERE status = 'Completed' ORDER BY match_time DESC LIMIT 10");
$completed =$completedStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Staff Dashboard - GAMER ESPORTS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-white pb-20">

    <div class="flex justify-between items-center p-5 bg-gray-800 shadow-lg mb-6">
        <div>
            <h2 class="font-bold text-xl text-purple-400">Staff Panel</h2>
            <p class="text-gray-400 text-sm">Match Management Only</p>
        </div>
        <a href="../logout.php" class="text-red-400 font-bold"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>

    <div class="px-5">
        <h3 class="text-lg font-bold mb-3 text-orange-400 border-l-4 border-orange-500 pl-3">Live / Ongoing Matches</h3>
        <?php if(count($ongoing) > 0): ?>
            <?php foreach($ongoing as$match): ?>
            <div class="bg-gray-800 rounded-[20px] p-4 mb-4 border border-orange-500/30">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="font-bold"><?= htmlspecialchars($match['title']) ?></h4>
                    <span class="bg-orange-600 text-xs px-2 py-0.5 rounded-full font-bold">LIVE</span>
                </div>
                <p class="text-xs text-gray-400 mb-3">Game: <?= htmlspecialchars($match['game_name']) ?></p>
                <a href="update_match.php?id=<?= $match['id'] ?>" class="block text-center bg-orange-500 text-white font-bold py-2 rounded-xl text-sm">Update Room / Result</a>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-gray-500 text-sm mb-6">No live matches currently.</p>
        <?php endif; ?>

        <h3 class="text-lg font-bold mb-3 text-purple-400 border-l-4 border-purple-500 pl-3">Upcoming Matches</h3>
        <?php if(count($upcoming) > 0): ?>
            <?php foreach($upcoming as$match): ?>
            <div class="bg-gray-800 rounded-[20px] p-4 mb-4 border border-gray-700">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="font-bold"><?= htmlspecialchars($match['title']) ?></h4>
                    <span class="text-xs text-purple-300 bg-purple-900/50 px-2 py-0.5 rounded-full"><?= date('d M, h:i A', strtotime($match['match_time'])) ?></span>
                </div>
                <p class="text-xs text-gray-400 mb-3">Game: <?= htmlspecialchars($match['game_name']) ?></p>
                <a href="update_match.php?id=<?= $match['id'] ?>" class="block text-center bg-purple-600 text-white font-bold py-2 rounded-xl text-sm">Manage Status & Room</a>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-gray-500 text-sm mb-6">No upcoming matches.</p>
        <?php endif; ?>

        <h3 class="text-lg font-bold mb-3 text-gray-400 border-l-4 border-gray-500 pl-3">Recently Completed</h3>
        <?php foreach($completed as$match): ?>
        <div class="bg-gray-800/60 rounded-[20px] p-4 mb-3 border border-gray-800 flex justify-between items-center">
            <div>
                <h4 class="font-bold text-gray-300 text-sm"><?= htmlspecialchars($match['title']) ?></h4>
                <p class="text-xs text-green-400 font-bold">Winner ID: <?= $match['winner_id'] ?$match['winner_id'] : 'Declared' ?></p>
            </div>
            <span class="bg-gray-700 text-gray-400 text-xs px-2 py-1 rounded-md">Finished</span>
        </div>
        <?php endforeach; ?>
    </div>

</body>
</html>
