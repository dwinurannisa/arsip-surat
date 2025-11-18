<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Upload Excel</title>
</head>
<body>
    <h1>Upload Excel</h1>
    <form action="upload_excel.php" method="POST" enctype="multipart/form-data">
        <label for="file">Pilih file Excel:</label>
        <input type="file" name="file" id="file" accept=".xlsx, .xls" required>
        <br><br>
        <input type="submit" value="Upload File">
    </form>
</body>
</html>
