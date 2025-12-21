<?php
session_start();
include 'includes/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $target_dir = "uploads/original/";
    
    // Normalize files structure
    $files = $_FILES['image'];
    
    $file_count = is_array($files['name']) ? count($files['name']) : 1;
    $upload_data = []; 
    $epoch = isset($_POST['epoch']) ? (int)$_POST['epoch'] : 10;
    $batch_size = isset($_POST['batch_size']) ? (int)$_POST['batch_size'] : 4;
    $learning_rate = isset($_POST['learning_rate']) ? (float)$_POST['learning_rate'] : 0.01;

    // Process each file
    for ($i = 0; $i < $file_count; $i++) {
        $name = is_array($files['name']) ? $files['name'][$i] : $files['name'];
        $tmp_name = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
        $size = is_array($files['size']) ? $files['size'][$i] : $files['size'];
        
        $timestamp = time() . '_' . $i; 
        $imageFileType = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $new_filename = "flood_" . $timestamp . "." . $imageFileType;
        $target_file = $target_dir . $new_filename;
        $abs_path = realpath($target_dir) . DIRECTORY_SEPARATOR . $new_filename;
        
        // Validation
        if ($size > 5000000 || !in_array($imageFileType, ['jpg', 'png', 'jpeg'])) {
            continue; 
        }

        if (move_uploaded_file($tmp_name, $target_file)) {
            // Insert into DB with User ID, Epoch, Batch Size, and Learning Rate
            $sql = "INSERT INTO analysis_history (original_filename, status, user_id, epoch, batch_size, learning_rate) VALUES ('$new_filename', 'pending', $user_id, $epoch, $batch_size, $learning_rate)";
            if ($conn->query($sql) === TRUE) {
                $last_id = $conn->insert_id;
                $upload_data[] = [
                    'id' => $last_id,
                    'path' => $abs_path,
                    'epoch' => $epoch,
                    'batch_size' => $batch_size,
                    'learning_rate' => $learning_rate
                ];
            }
        }
    }

    // Call Python
    if (!empty($upload_data)) {
        $json_payload = base64_encode(json_encode($upload_data));
        $python_script = "predict.py";
        $command = "py -3.10 $python_script batch $json_payload 2>&1";
        $output = shell_exec($command);
        
        // Logging
        $log_file = 'debug_log.txt';
        $log_content = "Time: " . date('Y-m-d H:i:s') . "\n";
        $log_content .= "Batch Size: " . count($upload_data) . "\n";
        $log_content .= "Command: " . $command . "\n";
        $log_content .= "Output: " . substr($output, 0, 500) . "...\n";
        $log_content .= "-----------------------------------\n";
        file_put_contents($log_file, $log_content, FILE_APPEND);
    }

    header("Location: history.php");
    exit();
}
?>
