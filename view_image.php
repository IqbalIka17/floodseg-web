<?php
if (!isset($_GET['src'])) {
    die("Gambar tidak ditemukan.");
}
$src = $_GET['src'];
$title = isset($_GET['title']) ? $_GET['title'] : 'Preview Gambar';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?> - FloodSeg Viewer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0f172a;
            height: 100vh;
            margin: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .viewer-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            background-image: 
                linear-gradient(45deg, #1e293b 25%, transparent 25%), 
                linear-gradient(-45deg, #1e293b 25%, transparent 25%), 
                linear-gradient(45deg, transparent 75%, #1e293b 75%), 
                linear-gradient(-45deg, transparent 75%, #1e293b 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
        }
        .viewer-image {
            max-width: 90%;
            max-height: 90vh;
            object-fit: contain;
            image-rendering: pixelated; /* Penting agar tidak blur saat di-upscale */
            box-shadow: 0 0 50px rgba(0,0,0,0.5);
            transition: transform 0.2s;
        }
        .toolbar {
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(10px);
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 100;
        }
        .zoom-controls {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 10px 20px;
            border-radius: 50px;
            display: flex;
            gap: 15px;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .zoom-btn {
            color: white;
            background: none;
            border: none;
            cursor: pointer;
            transition: 0.2s;
        }
        .zoom-btn:hover {
            color: #3b82f6;
            transform: scale(1.2);
        }
    </style>
</head>
<body>

<div class="toolbar">
    <div class="d-flex align-items-center">
        <a href="javascript:history.back()" class="btn btn-outline-light btn-sm rounded-circle me-3" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-arrow-left"></i></a>
        <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($title); ?></h6>
    </div>
    <a href="<?php echo htmlspecialchars($src); ?>" download class="btn btn-primary btn-sm rounded-pill px-3 fw-bold">
        <i class="fas fa-download me-2"></i>Download
    </a>
</div>

<div class="viewer-container" id="container">
    <img src="<?php echo htmlspecialchars($src); ?>" class="viewer-image" id="image">
</div>

<div class="zoom-controls">
    <button class="zoom-btn" onclick="zoomOut()"><i class="fas fa-minus"></i></button>
    <button class="zoom-btn" onclick="resetZoom()"><i class="fas fa-expand"></i></button>
    <button class="zoom-btn" onclick="zoomIn()"><i class="fas fa-plus"></i></button>
</div>

<script>
    let scale = 1;
    const img = document.getElementById('image');
    
    // Auto scale up small images (like 128x128)
    window.onload = function() {
        if (img.naturalWidth < 500) {
            scale = 3; // Zoom 3x by default for small AI outputs
            updateTransform();
        }
    }

    function zoomIn() {
        scale += 0.5;
        updateTransform();
    }

    function zoomOut() {
        if (scale > 0.5) scale -= 0.5;
        updateTransform();
    }

    function resetZoom() {
        scale = 1;
        updateTransform();
    }

    function updateTransform() {
        img.style.transform = `scale(${scale})`;
    }

    // Mouse Wheel Zoom
    document.getElementById('container').addEventListener('wheel', function(e) {
        e.preventDefault();
        if (e.deltaY < 0) zoomIn();
        else zoomOut();
    });
</script>

</body>
</html>