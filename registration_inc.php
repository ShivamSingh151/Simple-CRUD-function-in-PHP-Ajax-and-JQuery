<?php

function pr($arr = array(), $is_die = 0){
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
    echo "<br>";

    if($is_die == 1){
        die;
    }
}

function _conenct_db(){
    // Database configuration 
    $dbHost     = "localhost"; 
    $dbUsername = "root"; 
    $dbPassword = ""; 
    $dbName     = "test"; 
    
    // Create database connection 
    $db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName); 
    
    // Check connection 
    if ($db->connect_error) { 
        die("Connection failed: " . $db->connect_error); 
    }

    return $db;
}

function get_data(){
    $offset = 0;
    $limit = 10;
    
    $arr_response = array( 
        'status' => 0, 
        'message' => 'Something went wrong!' 
    );

    if(isset($_REQUEST["pgno"]) && intval($_REQUEST["pgno"]) > 1){
        $pg_no = intval($_REQUEST["pgno"]);
        $offset = ($pg_no - 1) * $limit;
    }

    $conn = _conenct_db();

    $sql = "SELECT * FROM users WHERE id=?"; // SQL with parameters
    $stmt = $conn->prepare($sql); 
    $stmt->bind_param("i", 1);
    $stmt->execute();
    $result = $stmt->get_result();
    pr($result, 1);
    $user = $result->fetch_assoc();

    return $arr_response;
}

function save_data(){
    $uploadDir = 'uploads/'; 
    
    $arr_response = array( 
        'status' => 0, 
        'message' => 'Form submission failed, please try again.' 
    );

    if(isset($_POST['name']) || isset($_POST['email']) || isset($_FILES['image'])){ 
        $id = intval($_POST['id']);
        $name = $_POST['name']; 
        $email = $_POST['email'];
        $photo = ""; 
        
        if(!empty($name) && !empty($email)){ 
            // Validate email 
            if(filter_var($email, FILTER_VALIDATE_EMAIL) === false){ 
                $arr_response['message'] = 'Please enter a valid email.'; 
            }else{ 
                $uploadStatus = 1; 
                
                // Upload file 
                $photo = '';
                
                if(!empty($_FILES["image"]["name"])){
                    $fileName = basename($_FILES["image"]["name"]); 
                    
                    $targetFilePath = $uploadDir . $fileName; 
                    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION); 
                    
                    // Allow certain file formats 
                    $allowTypes = array('pdf', 'doc', 'docx', 'jpg', 'png', 'jpeg'); 
                    
                    if(in_array($fileType, $allowTypes)){ 
                        // Upload file to the server 
                        if(move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)){ 
                            $photo = $fileName; 
                        }else{ 
                            $uploadStatus = 0; 
                            $arr_response['message'] = 'Sorry, there was an error uploading your file.'; 
                        } 
                    }else{ 
                        $uploadStatus = 0; 
                        $arr_response['message'] = 'Sorry, only PDF, DOC, JPG, JPEG, & PNG files are allowed to upload.'; 
                    } 
                }
                
                if($uploadStatus == 1){ 
                    $db = _conenct_db();
                    
                    // Insert form data in the database 
                    if($id == 0){
                        $sql = $db->query("INSERT INTO emp (name, email, photo) VALUES ('".$name."','".$email."','".$photo."')");
                    }else{
                        $sql = $db->query("UPDATE emp SET name = '".$name."', email = '".$email."', photo = '".$photo."' WHERE id= '$id'");
                    }

                    if($sql){ 
                        $arr_response['status'] = 1; 
                        $arr_response['message'] = 'Form data submitted successfully!'; 
                    }
                } 
            } 
        }else{ 
            $arr_response['message'] = 'Please fill all the mandatory fields (name and email).'; 
        } 
    }

    return $arr_response;
}

if(isset($_REQUEST["action"]) && trim($_REQUEST["action"]) == "ajax_call"){
    $arr_res = array("status"=>0, "message"=>"Something went wrong!");
    // pr($_SERVER, 1);

    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $_section = trim($_REQUEST["section"]);

        if($_section != ""){
            switch($_section){
                case "save_data":
                    $arr_res = save_data();
                break;

                case "get_data":
                    $arr_res = get_data();
                break;

                default:
                break;
            }
        }
    }

    echo json_encode($arr_res); die;
}
?>