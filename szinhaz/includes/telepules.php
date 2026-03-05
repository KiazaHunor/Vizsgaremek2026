<?php
var_dump($_POST);
if(isset($_POST))
{
  $postId = $_POST['id'] ?? '';
  $postNev = $_POST['name'] ?? '';
  $postSend = $_POST['save'] ?? 'default';
  $postNew = $_POST['new'] ?? 'default';
  if($postSend==""){
    csoportUpdate($postId,$postNev);
  }
  elseif($postNew==""){
    csoportInsert($postNev);
  }

}

if(isset($_GET['action']) && $_GET['action']=="delete"){
  csoportDelete($_GET['id']);
}

$tartalom="";

$tartalom = szerkezet();

  function cim($cim){
     return "<h2>$cim</h2>";
  }
  function szerkezet(){
    return 
    "<div class=\"container\">
        <div class=\"row\">".cim("Település")."</div>
        <div class=\"row\">
            <div class=\"col-6\">
                ".csoportForm()."
            </div>
            <div class=\"col-6\">
                ".csoportLista()."
            </div>
        </div>
    </div>
    ";
  }
  function csoportForm(){

    $csoportAdat=["id"=>"","nev"=>""];
    if(isset($_GET["action"]) && $_GET["action"] == "edit"){
      $csoportAdat = csoportAdat($_GET["id"]);
      
    }


    return '
    <form method="post" action="?page=telepules">
  <div class="container">
    <input type="hidden" name="id" id="id" value="'.$csoportAdat['id'].'">
    <div class="row">
      <div class="col-12">Név:</div>
    </div>
    <div class="row">
      <div class="col-12"><input type="text" name="name" id="name" class="form-control" value="'.$csoportAdat["nev"].'"></div>
    </div>
    <div class="row">
  '.($csoportAdat['id'] != "" 
      ? '<div class="col-12"><button type="submit" class="mt-4 btn btn-primary" name="save">Mentés</button></div>' 
      : ''
  ).'
  <div class="col-12"><button type="submit" class="mt-4 btn btn-primary" name="new">Mentés újként</button></div>
</div>


  </div>

</form>';
  }
  
  function csoportLista(){

  $csoportListaAdat = csoportListaAdat();
  $aktualisId=$_GET["id"] ?? "";


  $vissza="";
  foreach($csoportListaAdat as $egyCsoport){
    
    if($aktualisId==$egyCsoport['id'])
    {
      $elemClass=" active";
      $linkColor=' text-white';
    }
    else
    {
      $elemClass="";
      $linkColor='';
    }

    $vissza.="<li class=\"list-group-item$elemClass\">$egyCsoport[nev]
                <a href=\"?page=telepules&action=edit&id=$egyCsoport[id]\" class=\"$linkColor\"><i class=\"bi bi-pencil\"></i></a>
                <a href=\"?page=telepules&action=delete&id=$egyCsoport[id]\ class=\"$linkColor\"><i class=\"bi bi-trash\"></i></a>
              </li>";
  }


    return '<ul class="list-group">
    '.$vissza.' </ul>
';
  }
  function csoportListaAdat(){
    GLOBAL $conn; 


    //$query = "INSERT INTO MyGuests (firstname, lastname, email) VALUES (?, ?, ?)";
    $query="SELECT * from telepules WHERE ?";
    $vissza=[];
    if($stmt = $conn->prepare($query)) {

      $stmt ->bind_param("i",$szam);
      $szam = 1;
      $stmt->execute();
      $result= $stmt->get_result();
      
      while($row = $result->fetch_assoc()) {
       $vissza[] = $row;
      }
      //var_dump($vissza);
      
    }
    else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $stmt ->close();

    return $vissza;
  }
  function csoportAdat($id){
    GLOBAL $conn; 


    
    $query="SELECT * from telepules WHERE id=?";
    $vissza=[];
    if($stmt = $conn->prepare($query)) {

      $stmt ->bind_param("i",$id);
      $stmt->execute();
      $result= $stmt->get_result();
      
      while($row = $result->fetch_assoc()) {
       $vissza[] = $row;
      }
    
      
    }
    else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $stmt ->close();

    return $vissza[0];
  }
  function csoportInsert($nev){
    GLOBAL $conn; 

    $query="INSERT INTO telepules (nev) values (?)";
    if($stmt = $conn->prepare($query)) {

      $stmt ->bind_param("s",$nev);
      $stmt->execute();  
    }
    else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $stmt ->close();
  }
  function csoportUpdate($id,$nev){
    GLOBAL $conn; 

    $query="UPDATE telepules SET nev=? WHERE id=?";
    if($stmt = $conn->prepare($query)) {

      $stmt ->bind_param("si",$nev,$id);
      $stmt->execute();  
    }
    else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $stmt ->close();
  }

  function csoportDelete($id)
  {
    GLOBAL $conn; 

    $query="DELETE FROM telepules WHERE id=?";
    if($stmt = $conn->prepare($query)) {

      $stmt ->bind_param("i",$id);
      $stmt->execute();  
    }
    else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $stmt ->close();
  }
?>


