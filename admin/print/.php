<?php
// Check if the file parameter is set
if (isset($_GET['file'])) {
    // Sanitize the file parameter to prevent path traversal
    $file = basename($_GET['file']);
    
    // Define the file path (assuming your files are in the 'uploads' folder)
    $filePath = '../uploads/' . $file;

    // Check if the file exists
    if (file_exists($filePath)) {
        // Detect the file's MIME type (for proper display in the browser)
        $mimeType = mime_content_type($filePath);
        
        // Output the file inside an HTML page
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Print Document</title>
            <style>
                body {
                    margin: 0;
                    padding: 0;
                }
                iframe {
                    width: 100%;
                    height: 100vh;
                    border: none;
                }
            </style>
        </head>
        <body  >
            <iframe src="<?php echo htmlspecialchars($filePath); ?>" type="<?php echo $mimeType; ?>"></iframe>
        </body>
        </html>
        <?php
    } else {
        echo "File not found!";
    }
} else {
    echo "No file specified!";
}
?>
