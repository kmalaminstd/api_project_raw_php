<?php

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json ; charset=UTF-8");
    header("Access-Control-Allow-METHODS: GET, POST, PUT, DELETE");
    header("Acceess-Control-Allow-Headers: access, Content-Type, Authorization, X-Requested-With");

    include "../../config/database.php";

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $datebase = new Database();
    $db = $datebase->connectDatabase();

    $product_name = null;
    $product_category = null;
    $product_details = null;
    $product_image_link = null;
    $id = null;


    // handle the post method of api
    /* 
        API post method will work in two types. FormData and Json. 
    */
    if($_SERVER["REQUEST_METHOD"] == "POST"){

        $data = json_decode(file_get_contents("php://input"));
        
        if(json_last_error() == JSON_ERROR_NONE){
            $product_name = filter_var($data->product_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $product_details = filter_var($data->product_details, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $product_category = filter_var($data->product_category, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if(isset($data->product_image)){
                $product_image_link = filter_var($data->product_image , FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }
            

        }else{

            $product_name = filter_var($_POST['product_name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $product_details = filter_var($_POST['product_details'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $product_category = filter_var($_POST['product_category'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
            if(isset($_FILES["product_image"])){
                $product_image_name = $_FILES["product_image"]["name"];
                $product_image_temp = $_FILES["product_image"]["tmp_name"];
                $target_folder = "../../upload/product_image/";
    
                if(!file_exists($target_folder)){
                    mkdir($target_folder, 0777, true);
                }else{
                    $product_image_link = "failed to image";
                }
    
                if(move_uploaded_file($product_image_temp, $target_folder . $product_image_name)){
                    $product_image_link = $target_folder . $product_image_name;
                }else{
                    $product_image_link = null;
                }
            }
        }

        
        // handle post the response
    
        if($product_name && $product_details && $product_category){
    
            $sql = "INSERT INTO prouducts SET product_name=:product_name, product_category=:product_category, product_details=:product_details, product_image=:product_image";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":product_name", $product_name);
            $stmt->bindParam(":product_category", $product_category);
            $stmt->bindParam(":product_details", $product_details);
            $stmt->bindParam(":product_image", $product_image_link);
    
            if($stmt->execute()){
    
                $response = [
                    "success"=>true,
                    "data"=>[
                        "name"=>$product_name,
                        "cate"=>$product_category,
                        "details"=>$product_details,
                        "image"=>$product_image_link
                    ]
                ];
            }else{
                $response = [
                    "success"=>false,
                    "message"=> "failed to save information in database"
                ];
            }
    
    
            echo json_encode($response);
    
        }else{  
            echo json_encode(["success"=>false, "message"=>"field error"]);
        }
    }



    // handle the get method of api
    if($_SERVER["REQUEST_METHOD"] == "GET"){

        if(isset($_GET["id"])){

            $id = filter_var($_GET["id"], FILTER_SANITIZE_NUMBER_INT);
            $sql = "SELECT * FROM prouducts WHERE product_id=:id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if($product){
                $response = [
                    "success"=> true,
                    "data"=> $product
                ];
            }else{
                $response = [
                    "success"=> false,
                    "message"=> "product not found"
                ];
            }

        }else{
            $sql = "SELECT * FROM prouducts";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if($products){
                $response = [
                    "success"=> true,
                    "data"=> $products
                ];
            }else{
                $response = [
                    "success"=> false,
                    "message"=> "product not found"
                ];
            }
        }

        echo json_encode($response);
    }


    // handle UPDATE method of api
    if($_SERVER["REQUEST_METHOD"] == "PUT"){

        $data = json_decode(file_get_contents("php://input"));

        if(json_last_error() == JSON_ERROR_NONE){

            $id = filter_var($_GET["id"], FILTER_VALIDATE_INT);

            $product_name = filter_var($data->product_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $product_category = filter_var($data->product_category, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $product_details = filter_var($data->product_details, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $product_image_link = filter_var($data->product_image, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            
        }else{
            $id = filter_var($_GET["id"], FILTER_VALIDATE_INT);

            $product_name = filter_var($_POST["product_name"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $product_category = filter_var($_POST["product_category"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $product_details = filter_var($_POST["product_details"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if(isset($_FILES["product_image"])){
                $product_image = $_FILES["product_image"]["name"];
                $product_image_temp = $_FILES["product_image"]["tmp_name"];
                $target_folder = "../../upload/product_image";

                if(move_uploaded_file($product_image_temp, $target_folder . $product_image)){
                    move_uploaded_file($product_image_temp, $target_folder . $product_image);
                }
            }


        }

        if(isset($id) && isset($product_name) && isset($product_category) && isset($product_details)){
            $sql = "UPDATE prouducts SET product_name=:name, product_details=:details, product_category=:category";


            if(isset($product_image_link)){ 
                $sql .= ", product_image=:image";
            }

            $sql .= " WHERE product_id=:id";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->bindParam(":name", $product_name);
            $stmt->bindParam(":details", $product_details);
            $stmt->bindParam(":category", $product_category);

            if(isset($product_image_link)){
                $stmt->bindParam(":image", $product_image_link);
            }

            if($stmt->execute()){
                $response = [
                    "success"=>true,
                    "messaage"=>"data updated successfully"
                ];
            }else{
                $response = [
                    "success"=>false,
                    "messaage"=>"Failed data update"
                ];
            }
            
            echo json_encode($response);
            
        }



    }


    // handle delete method of api
    if($_SERVER["REQUEST_METHOD"] == "DELETE"){
        if(isset($_GET["id"])){
            $id = filter_var($_GET["id"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $sql = "DELETE FROM prouducts WHERE product_id=:id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":id", $id);

            if($stmt->execute()){
                $response = [
                    "success"=>true,
                    "message"=>"product deleted successfully"
                ];
            }else{
                $response = [
                    "success"=>false,
                    "message"=>"product delete failed"
                ];
            }
            
        }else{
            $response = [
                "success"=>false,
                "message"=>"there is no id for delete data"
            ];
        }
        echo json_encode($response);
    }
    

?>