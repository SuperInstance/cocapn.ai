<?php
// API: submit tile to PLATO
header('Content-Type: application/json');
require_once __DIR__ . '/../lib/plato_client.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['error' => 'Method not allowed']);
  exit;
}

$room = $_POST['room'] ?? '';
$content = $_POST['content'] ?? '';
$tags = $_POST['tags'] ?? '';

if (!$room || !$content) {
  echo json_encode(['error' => 'room and content are required']);
  exit;
}

$tile = [
  'room' => $room,
  'content' => $content,
  'tags' => $tags ? array_map('trim', explode(',', $tags)) : [],
  'timestamp' => date('c'),
];

$plato = new CocapnPlatoClient();
$result = $plato->submit_tile($tile);

if (empty($result) || isset($result['error'])) {
  http_response_code(500);
  echo json_encode(['error' => 'Failed to submit tile', 'detail' => $result]);
} else {
  echo json_encode(['success' => true, 'tile' => $result]);
}
exit;