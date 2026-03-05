<?php

var_dump($_POST);
if(isset($_POST))
{
  $postId = $_POST['id'] ?? '';
  $postNev = $_POST['name'] ?? '';
  $postEmail = $_POST['email'] ?? '';
  $postTelefon = $_POST['telefon'] ?? '';
  $postTelepules = $_POST['telepules'] ?? '';
  $postTelepulesId = $_POST['telepules_id'] ?? '';
  $postSend = $_POST['save'] ?? 'default';
  $postNew = $_POST['new'] ?? 'default';
  if($postSend==""){
    diakUpdate($postId,$postNev,$postEmail,$postTelefon,$postTelepules,$postTelepulesId);
  }
  elseif($postNew==""){
    diakInsert($postNev,$postEmail,$postTelefon,$postTelepules,$postTelepulesId);
  }

}

if(isset($_GET['action']) && $_GET['action']=="delete"){
  diakDelete($_GET['id']);
}

$tartalom="";

$tartalom = szerkezet();

  function cim($cim){
     return "<h2>$cim</h2>";
  }
  function szerkezet(){
    return 
    "<div class=\"container\">
        <div class=\"row\">".cim("Diakok")."</div>
        <div class=\"row\">
            <div class=\"col-6\">
                ".diakForm()."
            </div>
            <div class=\"col-6\">
                ".diakLista()."
            </div>
        </div>
    </div>
    ";
  }
   function oraListaAdat(){
    GLOBAL $conn; 


    //$query = "INSERT INTO MyGuests (firstname, lastname, email) VALUES (?, ?, ?)";
    $query="SELECT orak.*, tanar.nev as tanar_nev,
            terem.nev as terem_nev ,
            csoport.nev as csoport_nev ,
            targy.nev as targy_nev ,
            COUNT(kapcsolo.diakid) as diak_darab
    from orak 
            join tanar on orak.tanar_id=tanar.id
            join terem on orak.terem_id=terem.id
            join csoport on orak.csoport_id=csoport.id
            join targy on orak.targy_id=targy.id
            join kapcsolo on orak.id=kapcsolo.oraid
        GROUP BY orak.id
        ORDER BY datum,orasorszam";
    $vissza=[];
    if($stmt = $conn->prepare($query)) {

      //$stmt ->bind_param("i",$szam);
      //$szam = 1;
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
 
  function diakForm(){
 
    $diakAdat=["id"=>"","nev"=>"","email"=>"","telefon"=>"","telepules_id"=>""];
    if(isset($_GET["action"]) && $_GET["action"] == "edit"){
      $diakAdat = diakAdat($_GET["id"]);
      $oraLista=oraListaAdat();

      foreach($oraLista as $egyOra){
        $oraListaSzoveg='
        <div class="col-6 p-2 mt-3 bg-info container">
          <div class="row">
            <div class="col-4">Tárgy: ' . $egyOra['targy_nev'] . '</div>
            <div class="col-5">Tanár: ' . $egyOra['tanar_nev'] . '</div>
            <div class="col-3">Hely: ' . $egyOra['diak_darab'] . '/' . $egyOra['ferohely'] . '</div>
            </div>
        </div>
        ';
      }
      
    }


    return '
    <form method="post" action="?page=diakok">
  <div class="container">
    <input type="hidden" name="id" id="id" value="'.$diakAdat['id'].'">
    <div class="row">
      <div class="col-12">Név:</div>
    </div>
    <div class="row">
      <div class="col-12"><input type="text" name="name" id="name" class="form-control" value="'.$diakAdat["nev"].'"></div>
    </div>
    <div class="row">
      <div class="col-12">Email:</div>
    </div>
    <div class="row">
      <div class="col-12"><input type="text" name="email" id="email" class="form-control" value="'.$diakAdat["email"].'"></div>
    </div>
    <div class="row">
      <div class="col-12">Telefon:</div>
    </div>
    <div class="row">
      <div class="col-12"><input type="text" name="telefon" id="telefon" class="form-control" value="'.$diakAdat["telefon"].'"></div>
    </div>
    <div class="row">
      <div class="col-12">Településasda</div>
     <div class="row">
      <div class="col-12">' . telepulesSelect($diakAdat['telepules_id']) . '</div>
      </div>
    </div>
    <div class="row">
      ' . $oraListaSzoveg . '
    </div>
    <div class="row">
  '.($diakAdat['id'] != "" 
      ? '<div class="col-12"><button type="submit" class="mt-4 btn btn-primary" name="save">Mentés</button></div>' 
      : ''
  ).'
  <div class="col-12"><button type="submit" class="mt-4 btn btn-primary" name="new">Mentés újként</button></div>
</div>


  </div>

</form>';
  }
  
  
  function telepulesSelect($id){
    $telepulesek= telepulesListaAdat();
    $vissza='<select name="telepules_id">';
    
    foreach($telepulesek as $egyTelepules)
      {

      $vissza .= '<option value="'.$egyTelepules['id'].'" '.($egyTelepules['id']==$id?'selected':'').'>'.$egyTelepules['nev'].'</option>';

      }
     
    $vissza.='</select>';

    return $vissza;
  }
  function telepulesListaAdat(){
    GLOBAL $conn; 


    //$query = "INSERT INTO MyGuests (firstname, lastname, email) VALUES (?, ?, ?)";
    $query="SELECT * from telepules";
    $vissza=[];
    if($stmt = $conn->prepare($query)) {

      //$stmt ->bind_param("i",$szam);
      //$szam = 1;
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
  
  function diakLista(){

  $diakListaAdat = diakListaAdat();
  $aktualisId=$_GET["id"] ?? "";


  $vissza="";
  foreach($diakListaAdat as $egyDiak){
    
    if($aktualisId==$egyDiak['id'])
    {
      $elemClass=" active";
      $linkColor=' text-white';
    }
    else
    {
      $elemClass="";
      $linkColor='';
    }

    $vissza.="<li class=\"list-group-item$elemClass\">
                <div class=\"row\">
                  <div class=\"col-6\">$egyDiak[nev]</div>
                  <div class=\"col-4\">$egyDiak[telepules_nev]</div>
                  <div class=\"col-2\">
                    <a href=\"?page=diakok&action=edit&id=$egyDiak[id]\" class=\"$linkColor\"><i class=\"bi bi-pencil\"></i></a>
                    <a href=\"?page=diakok&action=delete&id=$egyDiak[id]\ class=\"$linkColor\"><i class=\"bi bi-trash\"></i></a>
                  </div>
                </div>
              </li>";
  }


    return '<ul class="list-group">
    '.$vissza.'
</ul>
';
  }
   
  function diakListaAdat(){
    GLOBAL $conn; 


    //$query = "INSERT INTO MyGuests (firstname, lastname, email) VALUES (?, ?, ?)";
    $query="SELECT diakok.*, telepules.nev as telepules_nev from diakok, telepules WHERE diakok.telepules_id=telepules.id";
    $vissza=[];
    if($stmt = $conn->prepare($query)) {

      //$stmt ->bind_param("i",$szam);
      //$szam = 1;
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
  function diakAdat($id){
    GLOBAL $conn; 


    
    $query="SELECT diakok.*, telepules.nev as telepules_nev from diakok, telepules WHERE diakok.telepules_id=telepules.id and diakok.id=?";
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
  function diakInsert($nev,$email,$telefon,$telepules_id){
    GLOBAL $conn; 

    $query="INSERT INTO diakok (nev,email,telefon,telepules_id) values (?,?,?,?)";
    if($stmt = $conn->prepare($query)) {

      $stmt ->bind_param("sssia",$nev,$email,$telefon,$telepules_id);
      $stmt->execute();  
    }
    else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $stmt ->close();
  }
  function diakUpdate($id,$nev,$email,$telefon,$telepules_id){
    GLOBAL $conn; 

    $query="UPDATE diakok SET nev=?, email=?, telefon=?, telepules_id=? WHERE id=?";
    if($stmt = $conn->prepare($query)) {

      $stmt ->bind_param("sssi",$nev,$email,$telefon,$telepules_id,$id);
      $stmt->execute();  
    }
    else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $stmt ->close();
  }

  function diakDelete($id)
  {
    GLOBAL $conn; 

    $query="DELETE FROM diakok WHERE id=?";
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


