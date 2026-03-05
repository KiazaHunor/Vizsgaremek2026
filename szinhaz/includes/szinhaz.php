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
                ".adatForm()."
            </div>
            <div class=\"col-6\">
                ".adatLista()."
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
 
  function adatForm()
  {
    global $oldalData;
    $formAdat=["id"=>"","ertek"=>"","eloadasid"=>""];

    
    if(isset($_GET["action"]) && $_GET["action"] == "edit")
    {
      $formAdat = formAdat($_GET["id"]);
    }


    return '
    <form method="post" action="?page='.$oldalData['page'].'">
  <div class="container">
    <input type="hidden" name="id" id="id" value="'.$formAdat['id'].'">
    <div class="row">
      <div class="col-12">Előadás:</div>
      <div class"col-12">
      előadás select
      </div>
    </div>
    <div class="row">
      <div class="col-12"><input type="text" name="name" id="name" class="form-control" value="'.$formAdat["nev"].'"></div>
    </div>
    <div class="row">
      <div class="col-12">Érték:</div>
    </div>
    <div class="row">
      <div class="col-12"><input type="text" name="ertek" id="ertek" class="form-control" value="'.$formAdat["ertek"].'"></div>
    </div>
    <div class="row">
      <div class="col-12">Név:</div>
    </div>
    <div class="row">
      '. adatSelect("eloadas",$formAdat['eloadasid'], "cim").'
    </div>    
    <div class="row">
    </div>
    <div class="row">
  '.($formAdat['id'] != "" 
      ? '<div class="col-12"><button type="submit" class="mt-4 btn btn-primary" name="save">Mentés</button></div>' 
      : ''
  ).'
  <div class="col-12"><button type="submit" class="mt-4 btn btn-primary" name="new">Mentés újként</button></div>
</div>


  </div>

</form>';
  }
  
  
  function adatSelect($tabla,$id,$mezo="nev")
  {
    $adatok=ListaAdat($tabla,$mezo);
    $vissza='<select name="'. $tabla .'_id">';
    
    foreach($adatok as $egyAdat)
      {

      $vissza .= '<option value="'.$egyAdat['id'].'" '.($egyAdat['id']==$id?'selected':'').'>'.$egyAdat[$mezo].'</option>';

      }
     
    $vissza.='</select>';

    return $vissza;
  }
  function ListaAdat($tabla,$mezo)
  {
    GLOBAL $conn; 

    if($tabla == "eloadas")
      {
        $query="SELECT eloadas.id,Concat(eloadas.cim," - "szinhaz.nev) as $mezo
                    from eloadas
                      join szinhaz ON eloadas.szinhazid=szinhaz.id";
      }

    //$query = "INSERT INTO MyGuests (firstname, lastname, email) VALUES (?, ?, ?)";
    $query="SELECT * from $tabla";
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
  
  function adatLista(){

  GLOBAL $oldalData;
  $adatListaAdat = adatListaAdat();
  $aktualisId=$_GET["id"] ?? "";


  $vissza="";
  foreach($adatListaAdat as $egyAdat){
    
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
                  <div class=\"col-7\">$egyAdat[cim]</div>
                  <div class=\"col-3\">$egyAdat[ertek]</div>
                  <div class=\"col-4\">$egyAdat[nev]</div>                  
                  <div class=\"col-2\">
                    <a href=\"?page=".$oldalData["page"]."&action=edit&id=$egyAdat[id]\" class=\"$linkColor\"><i class=\"bi bi-pencil\"></i></a>
                    <a href=\"?page=".$oldalData["page"]."&action=delete&id=$egyAdat[id]\ class=\"$linkColor\"><i class=\"bi bi-trash\"></i></a>
                  </div>
                </div>
              </li>";
  }


    return '<ul class="list-group">
    '.$vissza.'
</ul>
';
  }
   
  function adatListaAdat(){
    GLOBAL $conn; 


    //$query = "INSERT INTO MyGuests (firstname, lastname, email) VALUES (?, ?, ?)";
    $query="SELECT tulajdonsag.*,tulajdonsagnev.nev,eloadas.cim
                          from tulajdonsag
                          JOIN tulajdonsagnev on tulajdonsagnev.id=tulajdonsag.tulajdonsag_id
                          JOIN eloadas on tulajdonsag.eloadasid=eloadas.id
                        ORDER BY eloadas.cim";
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
  function formAdat($id){
    GLOBAL $conn; 


    $query="SELECT *
            FROM tulajdonsag            
            WHERE id=?";

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


