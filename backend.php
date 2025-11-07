<?php
// backend.php
header('Content-Type: application/json; charset=utf-8');
include 'db_connect.php';

$upload_dir = __DIR__ . '/uploads';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

$action = $_GET['action'] ?? '';

function json($arr){ echo json_encode($arr); exit; }

if ($action === 'getAll') {
    $res = $mysqli->query("SELECT * FROM users ORDER BY id DESC");
    $out = [];
    while($r = $res->fetch_assoc()) $out[] = $r;
    json($out);
}

if ($action === 'getOne') {
    $id = intval($_GET['id'] ?? 0);
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    json($res ?: []);
}

if ($action === 'delete') {
    $id = intval($_GET['id'] ?? 0);
    // delete file if exists
    $old = $mysqli->query("SELECT photo FROM users WHERE id = $id")->fetch_assoc();
    if ($old && !empty($old['photo']) && file_exists($upload_dir . '/' . $old['photo'])) unlink($upload_dir . '/' . $old['photo']);
    $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    json(["status"=>true]);
}

// SAVE: handle both insert and update via POST multipart/form-data
if ($action === 'save') {
    // fields list
    $fields = [
      'first_name','last_name','email','password','dob','gender','phone',
      'address1','address2','city','state','country','zipcode','education',
      'occupation','company','industry','experience','skills','hobbies',
      'marital_status','religion','blood_group','emergency_contact','website','linkedin'
    ];
    // collect POST values
    $vals = [];
    foreach($fields as $f) $vals[$f] = isset($_POST[$f]) ? $_POST[$f] : '';

    $id = isset($_POST['id']) && is_numeric($_POST['id']) ? intval($_POST['id']) : 0;

    // handle photo upload
    $photo_filename = '';
    if (isset($_FILES['photo']) && !empty($_FILES['photo']['name'])) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo_filename = uniqid('p_') . '.' . $ext;
        $target = $upload_dir . '/' . $photo_filename;
        move_uploaded_file($_FILES['photo']['tmp_name'], $target);
        // remove old if updating
        if ($id > 0) {
            $old = $mysqli->query("SELECT photo FROM users WHERE id = $id")->fetch_assoc();
            if ($old && !empty($old['photo']) && file_exists($upload_dir . '/' . $old['photo'])) unlink($upload_dir . '/' . $old['photo']);
        }
    } else {
        if ($id > 0) {
            $old = $mysqli->query("SELECT photo FROM users WHERE id = $id")->fetch_assoc();
            $photo_filename = $old['photo'] ?? '';
        }
    }

    if ($id > 0) {
        // UPDATE
        $set_clause = implode(", ", array_map(function($c){ return "$c = ?"; }, $fields));
        $set_clause .= ", photo = ?";
        $sql = "UPDATE users SET $set_clause WHERE id = ?";
        $types = str_repeat('s', count($fields)) . 'si';
        $params = array_merge(array_values($vals), [$photo_filename, $id]);
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $ok = $stmt->execute();
        json(["status"=>$ok ? true : false]);
    } else {
        // INSERT
        $cols = implode(",", array_merge($fields, ['photo']));
        $placeholders = implode(",", array_fill(0, count($fields)+1, '?'));
        $sql = "INSERT INTO users ($cols) VALUES ($placeholders)";
        $types = str_repeat('s', count($fields)+1);
        $params = array_merge(array_values($vals), [$photo_filename]);
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $ok = $stmt->execute();
        json(["status"=>$ok ? true : false]);
    }
}

// default
json(["status"=>false,"error"=>"invalid action"]);
