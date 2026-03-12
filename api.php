<?php
header('Content-Type: application/json');
include 'db_connect.php';

$method = $_SERVER['REQUEST_METHOD'];

// --- 1. GET: Fetch photos for a specific month ---
if ($method == 'GET') {
    if (isset($_GET['month'])) {
        $month = $_GET['month'];
        
        // Prepare SQL to prevent errors
        $stmt = $conn->prepare("SELECT * FROM memories WHERE month_name = ?");
        $stmt->bind_param("s", $month);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $memories = array();
        while($row = $result->fetch_assoc()) {
            $memories[] = $row;
        }
        
        echo json_encode($memories);
    } else {
        echo json_encode([]);
    }
}

// --- 2. POST: Upload a new photo ---
if ($method == 'POST') {
    // Check if a file was actually sent
    if(isset($_FILES['photo']) && isset($_POST['month']) && isset($_POST['grid_position'])) {
        
        $month = $_POST['month'];
        $gridPos = $_POST['grid_position'];
        $caption = $_POST['caption'];
        $date = $_POST['date']; // YYYY-MM-DD
        
        // Handle File Upload
        $target_dir = "uploads/";
        // Create a unique name: "February_div1_filename.jpg"
        $new_filename = $month . "_" . $gridPos . "_" . basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            // Success! Now save to Database
            $stmt = $conn->prepare("INSERT INTO memories (image_path, caption, memory_date, month_name, grid_position) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $target_file, $caption, $date, $month, $gridPos);
            
            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Memory Saved!"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Database Error: " . $conn->error]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to move uploaded file."]);
        }
    }
}

// --- 3. DELETE: Remove a photo ---
if ($method == 'DELETE') {
    // PHP doesn't handle DELETE params automatically like POST, so we read the input stream
    $data = json_decode(file_get_contents("php://input"), true);
    
    if(isset($data['id'])) {
        $id = $data['id'];
        
        // Optional: Delete the actual file from the folder too (Advanced step)
        // For now, let's just delete the database record
        $stmt = $conn->prepare("DELETE FROM memories WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error"]);
        }
    }
}

$conn->close();
?>