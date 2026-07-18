<?php
// staff/update_match.php
require '../config.php';

if (!isset($_SESSION['staff_id'])) {
    header("Location: ../login.php");
    exit;
}

$message = "";
$tournament_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle Form Submission for Room Details and Status Updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_match'])) {
    $status = $_POST['status'];
    $room_id = htmlspecialchars(trim($_POST['room_id']));
    $room_password = htmlspecialchars(trim($_POST['room_password']));
    $winner_id = !empty($_POST['winner_id']) ? intval($_POST['winner_id']) : null;

    $stmt = $pdo->prepare("UPDATE tournaments SET status = ?, room_id = ?, room_password = ?, winner_id = ? WHERE id = ?");
    if ($stmt->execute([$status, $room_id, $room_password, $winner_id, $tournament_id])) {
        $message = "<div class='bg-green-500 text-white p-3 rounded-xl mb-4 text-center'>Match updated successfully!</div>";
    } else {
        $message = "<div class='bg-red-500 text-white p-3 rounded-xl mb-4 text-center'>Failed to update match.</div>";
    }
}

// Fetch current tournament data
$stmt = $pdo->prepare("SELECT * FROM tournaments WHERE id = ?");
$stmt->execute([$tournament_id]);
$match = $stmt->fetch();

if (!$match) {
    die("Match not found!");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Update Match - Staff</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-white pb-20">

    <div class="flex items-center p-5 bg-gray-800 shadow-md mb-6">
        <a href="dashboard.php" class="mr-4 text-xl text-purple-400"><i class="fa-solid fa-arrow-left"></i></a>
        <h2 class="font-bold text-xl">Update Match Details</h2>
    </div>

    <div class="px-5">
        <?= $message ?>

        <div class="bg-gray-800 rounded-[25px] p-6 border border-gray-700 shadow-md">
            <h3 class="text-lg font-bold text-purple-400 mb-1"><?= htmlspecialchars($match['title']) ?></h3>
            <p class="text-xs text-gray-400 mb-6">Game: <?= htmlspecialchars($match['game_name']) ?></p>

            <form method="POST" action="">
                <div class="mb-4">
                    <label class="block text-gray-400 text-sm mb-1">Match Status</label>
                    <select name="status" class="w-full px-4 py-2.5 rounded-xl bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="Upcoming" <?= $match['status'] == 'Upcoming' ? 'selected' : '' ?>>Upcoming</option>
                        <option value="Ongoing" <?= $match['status'] == 'Ongoing' ? 'selected' : '' ?>>Ongoing (Live)</option>
                        <option value="Completed" <?= $match['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Room ID</label>
                        <input type="text" name="room_id" value="<?= htmlspecialchars($match['room_id'] ?? '') ?>" class="w-full px-4 py-2.5 rounded-xl bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="Room ID">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Room Password</label>
                        <input type="text" name="room_password" value="<?= htmlspecialchars($match['room_password'] ?? '') ?>" class="w-full px-4 py-2.5 rounded-xl bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="Password">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-400 text-sm mb-1">Winner User ID (If Completed)</label>
                    <input type="number" name="winner_id" value="<?= htmlspecialchars($match['winner_id'] ?? '') ?>" class="w-full px-4 py-2.5 rounded-xl bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="e.g. 5">
                </div>

                <button type="submit" name="update_match" class="w-full bg-purple-600 py-3 rounded-xl font-bold shadow-lg hover:bg-purple-500 transition">Save Changes</button>
            </form>
        </div>
    </div>
</body>
</html>
