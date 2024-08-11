<?php

    include "../config/database.php";

    $database = new Database();
    $database->connectDatabase();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="POST" id="form" enctype="multipart/form-data">
        <input type="text" name="product_name">
        <input type="text" name="product_category">
        <input type="text" name="product_details">
        <input type="file" name="product_image">
        
        <input type="submit" name="submit">
    </form>

        <script>
            // console.log(document.querySelector("#form"));
            const baseUrl = "../api/v1/products.php?id=5"
            document.querySelector("#form").addEventListener('submit', e=>{

                e.preventDefault()

                const formData = new FormData(e.target)
                console.log(formData);
                const data = formData
                console.log(Object.fromEntries(formData.entries()));

                fetch(baseUrl, {
                    method: 'POST',
                    body: formData
                })
                    .then(res => res.json())
                    .then(data => {
                        console.log(data);
                        e.target.reset()
                    })
                    .catch(err => {
                        console.log(err.message);
                        console.log(err.code);
                    })
            })
        
        </script>
</body>
</html>