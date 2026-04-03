<?php
session_start();
include_once("../public/includes/db_connect.php");
header('Content-Type: application/json');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$customerId     = intval($_GET['customer'] ?? 0);
$receptionistId = intval($_GET['receptionist'] ?? 0);

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("Invalid session.");
    }

    $sql = "SELECT m.id, m.sender_id, m.receiver_id,
                   u.username AS sender_name,
                   u2.username AS receiver_name,
                   m.message, m.status, m.created_at
            FROM messages m
            JOIN users u ON m.sender_id=u.id
            JOIN users u2 ON m.receiver_id=u2.id
            WHERE (m.sender_id=? AND m.receiver_id=?)
            ORDER BY m.created_at ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $receptionistId, $customerId);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    $stmt->close();

    // Mark unread as read
    $updateSql = "UPDATE messages SET status='read'
                  WHERE receiver_id=? AND sender_id=? AND status='unread'";
    $stmtUpdate = $conn->prepare($updateSql);
    $stmtUpdate->bind_param("ii", $customerId, $receptionistId);
    $stmtUpdate->execute();
    $stmtUpdate->close();

    echo json_encode(["messages"=>$messages]);

} catch (Exception $e) {
    echo json_encode(["error"=>$e->getMessage()]);
}