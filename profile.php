<?php
   session_start();
      if(!file_exists("profile.json")){
          //Set Array with the profile data
          $profile =  array();
          $profile[0]["id"] = "1";
          $profile[0]["firstName"] = "Ahmed";
          $profile[0]["lastName"] = "Sayed Bedair";
          $profile[0]["skills"][] = "PHP";
          $profile[0]["skills"][] = "LINUX";
          $profile[0]["Age"] = 36;
      
          $profile[1]["id"] = "2";
          $profile[1]["firstName"] = "Mariam";
          $profile[1]["lastName"] = "Ahmed Saeed";
          $profile[1]["skills"][] = "Photoshop";
          $profile[1]["skills"][] = "Writing";
          $profile[1]["Age"] = 30;
      
      
          //Create JSON form array
          $profilejson = json_encode($profile);
          
          //Create file and append the JSON 
          $myfile = fopen("profile.json", "w") or die("Unable to open file!");
          fwrite($myfile, $profilejson);
          fclose($myfile);
      
      }
      function encrypt_decrypt($action, $string)
      {
       //Function source from
       //https://jonlabelle.com/snippets/view/php/php-encrypt-and-decrypt
   
        $output = false;
       
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'This is my secret key';
        $secret_iv = 'This is my secret iv';
       
        // hash
        $key = hash('sha256', $secret_key);
       
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a
        // warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
       
        if ($action == 'encrypt')
        {
          $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
          $output = base64_encode($output);
        }
        else
        {
          if ($action == 'decrypt')
          {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
          }
        }
        return $output;
      }
       function searchData($profiles){
            if(isset($_POST["search"]) && $_POST["search"]!=""){
                if(strpos($profiles["firstName"],$_POST["search"])!==false){
                    return true;
            }
                if(strpos($profiles["lastName"],$_POST["search"])!==false){
                    return true;
            }
            if(strpos($profiles["firstName"] ." " .$profiles["lastName"],$_POST["search"])!==false){
                return true;
            }
            if($profiles["Age"]==$_POST["search"]){
                return true;
            }
            foreach($profiles["skills"] as $skill){
                if(strpos($skill,$_POST["search"])!==false){
                return true;
            }
            }
            return false;
            }
            
       }
   
      ?>
<html>
   <head>
      <link rel="stylesheet" href="style.css">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
      <script>
         function checklastelm(){
           if($('.skillsadd').last().val().trim()!=""){
                $(".skills").append('<input type="text" name="skills[]" oninput="checklastelm()" class="form-control skillsadd" id="validationDefault05" placeholder="Skill">');
         }
         }
                
      </script>
   </head>
   <body>
      <div class="container">
         <?php if(isset($_GET["action"]) && $_GET["action"]=="add"){
            ?>
         <div class="bd-example">
            <?php
               //Check if post exists
               if($_POST){
                   //Validate primary inputs and captcha
                   if($_POST["captcha"]!=encrypt_decrypt('decrypt',$_SESSION["captcha"])){
                    ?>
                    <div class="alert alert-danger" role="alert">
                               Wrong Captcha
                             </div>
                    <?php
                                    }elseif(isset($_POST["firsname"]) && isset($_POST["lastname"]) && isset($_POST["age"])){
                              $profile = json_decode(file_get_contents("profile.json"),true);
                              $lastinput = end($profile);
                              $insertid = $lastinput["id"]+1;
                              $profile[$insertid-1]["id"] = $insertid;
                               $profile[$insertid-1]["firstName"] = $_POST["firsname"];
               $profile[$insertid-1]["lastName"] = $_POST["lastname"];
                              foreach($_POST["skills"] as $skill){
                                  if($skill!=""){
               $profile[$insertid-1]["skills"][] = $skill;
                                  }
                              }
               $profile[$insertid-1]["Age"] = $_POST["age"];
               //Create JSON form array
               $profilejson = json_encode($profile);
               
               //Create file and append the JSON 
               $myfile = fopen("profile.json", "w") or die("Unable to open file!");
               if(fwrite($myfile, $profilejson)){
               ?>
            <div class="alert alert-success" role="alert">
               New profile has been saved
            </div>
            <?php
               }
               fclose($myfile);
                                }else{
                                    ?>
            <div class="alert alert-danger" role="alert">
               Misssing Primary data
            </div>
            <?php
               }
               }
               else{
               $_SESSION["captcha"] = encrypt_decrypt('encrypt', rand(10,6809809));
               }
               ?>
            <form method="POST">
               <h5><b>Add Profile Data</b></h5>
               <div class="form-row">
                  <div class="col-md-4 mb-3">
                     <label for="validationDefault01">First name</label>
                     <input type="text" class="form-control" name="firsname" id="validationDefault01" placeholder="First name" required>
                  </div>
                  <div class="col-md-4 mb-3">
                     <label for="validationDefault02">Last name</label>
                     <input type="text" class="form-control" name="lastname" id="validationDefault02" placeholder="Last name" required>
                  </div>
                  <div class="col-md-2 mb-2">
                     <label for="validationDefaultUsername">Age</label>
                     <div class="input-group">
                        <input type="number" name="age" class="form-control" id="validationDefaultUsername" placeholder="Age" aria-describedby="inputGroupPrepend2" required>
                     </div>
                  </div>
               </div>
               <div class="form-row">
                  <div class="col-md-5 mb-5">
                     <label for="validationDefaultUsername">Simple Captcha :<br> Please write this number in the filed bellow (<?=encrypt_decrypt('decrypt',$_SESSION["captcha"])?>)</label>
                     <div class="input-group">
                        <input type="text" name="captcha" class="form-control" id="validationDefaultUsername" aria-describedby="inputGroupPrepend2" required>
                     </div>
                  </div>
               </div>
               <div class="form-row">
                  <div class="col-md-3 mb-3">
                     <label for="validationDefault05">Skills</label>
                     <div class="skills">
                        <input type="text" name="skills[]" oninput="checklastelm();" class="form-control skillsadd" id="validationDefault05" placeholder="Skill">
                     </div>
                  </div>
               </div>
         </div>
         <button class="btn btn-primary" type="submit">Submit form</button>
         </form>
      </div>
      <div style="text-align:right;">
         <a href="profile.php" type="button" class="btn btn-primary">Back</a>
      </div>
      <?php
         }elseif(isset($_GET["action"]) && $_GET["action"]=="edit"){
             ?>
      <div class="bd-example">
         <?php
            //Check if post exists
            $_GET["id"] = encrypt_decrypt('decrypt',$_GET["id"]);
            if($_POST){
                //Validate primary inputs
                if($_POST["captcha"]!=encrypt_decrypt('decrypt',$_SESSION["captcha"])){
?>
<div class="alert alert-danger" role="alert">
           Wrong Captcha
         </div>
<?php
                }elseif(isset($_POST["firsname"]) && isset($_POST["lastname"]) && isset($_POST["age"])){
                           $profile = json_decode(file_get_contents("profile.json"),true);

                           $profile[$_GET["id"]]["firstName"] = $_POST["firsname"];
            $profile[$_GET["id"]]["lastName"] = $_POST["lastname"];
            $profile[$_GET["id"]]["skills"] = array();
                           foreach($_POST["skills"] as $skill){
                               if($skill!=""){
            $profile[$_GET["id"]]["skills"][] = $skill;
                               }
                           }
            $profile[$_GET["id"]]["Age"] = $_POST["age"];
            //Create JSON form array
            $profilejson = json_encode($profile);
            
            //Create file and append the JSON 
            $myfile = fopen("profile.json", "w") or die("Unable to open file!");
            if(fwrite($myfile, $profilejson)){
            ?>
         <div class="alert alert-success" role="alert">
            profile has been Edited
         </div>
         <?php
            }
            fclose($myfile);
                             }else{
                                 ?>
         <div class="alert alert-danger" role="alert">
            Misssing Primary data
         </div>
         <?php
            }
            }else{
            $_SESSION["captcha"] = encrypt_decrypt('encrypt', rand(10,6809809));
            }
                             //Get JSON data from the file
         $getcontent = json_decode(file_get_contents("profile.json"),true);
         $getprofiledata = $getcontent[$_GET["id"]];
            ?>
         <form method="POST">
            <h5><b>Edit Profile Data</b></h5>
            <div class="form-row">
               <div class="col-md-4 mb-3">
                  <label for="validationDefault01">First name</label>
                  <input type="text" class="form-control" name="firsname" value="<?=$getprofiledata["firstName"]?>" id="validationDefault01" placeholder="First name" required>
               </div>
               <div class="col-md-4 mb-3">
                  <label for="validationDefault02">Last name</label>
                  <input type="text" class="form-control" name="lastname" value="<?=$getprofiledata["lastName"]?>" id="validationDefault02" placeholder="Last name" required>
               </div>
               <div class="col-md-2 mb-2">
                  <label for="validationDefaultUsername">Age</label>
                  <div class="input-group">
                     <input type="number" value="<?=$getprofiledata["Age"]?>" name="age" class="form-control" id="validationDefaultUsername" placeholder="Age" aria-describedby="inputGroupPrepend2" required>
                  </div>
               </div>
            </div>
            <div class="form-row">
               <div class="col-md-5 mb-5">
                  <label for="validationDefaultUsername">Simple Captcha :<br> Please write this number in the filed bellow (<?=encrypt_decrypt('decrypt',$_SESSION["captcha"])?>)</label>
                  <div class="input-group">
                     <input type="text" name="captcha" class="form-control" id="validationDefaultUsername" aria-describedby="inputGroupPrepend2" required>
                  </div>
               </div>
            </div>
            <div class="form-row">
               <div class="col-md-3 mb-3">
                  <label for="validationDefault05">Skills</label>
                  <div class="skills">
                     <?php
                        foreach($getprofiledata["skills"] as $skill){
                                    ?>
                     <input type="text" value="<?=$skill?>" name="skills[]" oninput="checklastelm();" class="form-control skillsadd" id="validationDefault05" placeholder="Skill">
                     <?php
                        }
                                
                                ?>
                     <input type="text" name="skills[]" oninput="checklastelm();" class="form-control skillsadd" id="validationDefault05" placeholder="Skill">
                  </div>
               </div>
            </div>
            <button class="btn btn-primary" type="submit">Submit form</button>
         </form>
      </div>
      <div style="text-align:right;">
         <a href="profile.php" type="button" class="btn btn-primary">Back</a>
         <?php
            ?>
         <?php
            }
            else{
                //Get JSON data from the file
            $getcontent = json_decode(file_get_contents("profile.json"),true);

            ?>
            <div class="row">
                <form method="POST">
            <table>
                 <tr><td><b>Search the profiles Names: </b></td><td><input type="text" name="search" required></td><td><input type="submit" value="Search"></td></tr>
            </table>
            </form>
            <br>
            <br>
            </div>
         <div class="row">
            <h5><b>Profiles Data</b></h5>
            <table class="table">
               <thead class="thead-dark">
                  <tr>
                     <th>FirstName</th>
                     <th>LastName</th>
                     <th>Skills</th>
                     <th>Age</th>
                     <th></th>
                  </tr>
               </thead>
               <tbody>
                  <?php
                     //Extracting data
                     $existrec = false;
                     foreach($getcontent as $getcontentext){
                        if(isset($_POST["search"])){
                        if(!searchData($getcontentext)){
                            continue;
                        }
                    }
                    $existrec = true;
                     ?>
                  <tr>
                     <td><b><?=$getcontentext["firstName"] ;?></b></td>
                     <td><b><?=$getcontentext["lastName"] ;?></b></td>
                     <td>
                        <table>
                           <?php
                              foreach($getcontentext["skills"] as $skill){
                              ?>
                           <tr>
                              <td>
                                 <?=$skill?>
                              </td>
                           </tr>
                           <?php
                              }
                              ?>
                        </table>
                     </td>
                     <td><?=$getcontentext["Age"] ;?></td>
                     <td><a href="profile.php?action=edit&id=<?=encrypt_decrypt('encrypt', $getcontentext["id"]-1)?>" type="button" class="btn btn-info">Edit</a></td>
                  </tr>
                  <?php
                     }
                     if(!$existrec){
                     ?>
                        <tr><td colspan="5" style="text-align:center;">Sorry no records found!</td></tr>
                     <?php
                     }
                     ?>
                  <tr>
                     <td colspan="5" style="text-align:right;">
                        <a href="profile.php?action=add" type="button" class="btn btn-primary">Add</a>
                     </td>
                  </tr>
               </tbody>
            </table>
         </div>
         <?php
            }
            ?>
      </div>
   </body>
</html>