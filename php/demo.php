<?php
include 'db.php';
function getBrands() {
    global $conn;
    $brands = [];
    
    $sql = "SELECT id, name, logo FROM brands ORDER BY name";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $brands[] = $row;
        }
    }
    return $brands;
}

 // <-- You need this!
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<select class="form-select" id="brand_id" name="brand_id" required>
    <option value="" selected disabled>Select Brand</option>
    <?php foreach ($brands as $brand): ?>
    <option value="<?php echo $brand['id']; ?>" data-logo="">
        <?php echo $brand['name']; ?>
    </option>
    <?php endforeach; ?>
    <option value="other">Other</option>
</select>

</body>
</html>
