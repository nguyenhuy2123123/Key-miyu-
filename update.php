<?php
// ====== Cấu hình ======
$github_username = "nguyenhuy2123123"; // tên tài khoản GitHub
$repo_name = "key-miyu-";                  // repo
$file_path = "active_keys.txt";            // file trong repo
$branch = "main";                          // nhánh
$token = "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";              // token cá nhân (PRIVATE)

// ====== Lấy dữ liệu từ client ======
if (!isset($_POST["content"])) {
    http_response_code(400);
    echo "Thiếu dữ liệu.";
    exit;
}
$content = $_POST["content"];

// ====== API URL ======
$url = "https://api.github.com/repos/$github_username/$repo_name/contents/$file_path";

// ====== Lấy SHA hiện tại ======
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  "Authorization: token $token",
  "User-Agent: Miyu-Key-Updater"
]);
$response = curl_exec($ch);
$data = json_decode($response, true);
curl_close($ch);

$sha = $data["sha"] ?? null;

// ====== Cập nhật file ======
$update = [
  "message" => "Update active_keys.txt",
  "content" => base64_encode($content),
  "branch" => $branch
];
if ($sha) $update["sha"] = $sha;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  "Authorization: token $token",
  "User-Agent: Miyu-Key-Updater",
  "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($update));
$response = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($code >= 200 && $code < 300) {
  echo "✅ Cập nhật GitHub thành công!";
} else {
  echo "❌ Lỗi cập nhật: $response";
}
?>
