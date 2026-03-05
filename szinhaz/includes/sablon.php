<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <header class="col-12">
                <h1>Színházi bemutató</h1>
            </header>
        </div>
        <div class="row">

        
            <?php include_once("navbar.php");  
            ?>
        </div>
        <div class="row">

        
            <?php echo $tartalom;;  
            ?>
        </div>
    </div>
    
</body>
</html>