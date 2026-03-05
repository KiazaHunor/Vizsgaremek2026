<?php

var_dump($_POST);
if(isset($_POST))
{
  $postAdat=[
    "id"=>$_POST['id'] ?? '',
    "datum"=>$_POST['datum'] ?? '',
    "orasorszam"=>$_POST['orasorszam'] ?? '',
    "targy_id"=>$_POST['targy_id'] ?? '',
    "terem_id"=>$_POST['terem_id'] ?? '',
    "ferohely"=>$_POST['ferohely'] ?? '',
    "tanar_id"=>$_POST['tanar_id'] ?? '',
    "csoport_id"=>$_POST['csoport_id'] ?? '',

    "save"=>$_POST['save'] ?? '',
    "new"=>$_POST['new'] ?? '',



  ];
  if($postAdat['save']==""){
    oraUpdate($postAdat);
  }
  elseif($postAdat['new']==""){
    oraInsert($postAdat);
  }

}

if(isset($_GET['action']) && $_GET['action']=="delete"){
  odaDelete($_GET['id']);
}

$tartalom="";

$tartalom = szerkezet();

  function cim($cim){
     return "<h2>$cim</h2>";
  }
  function szerkezet(){
    return 
    "<div class=\"container\">
        <div class=\"row\">".cim("Órák")."</div>
        <div class=\"row\">
            <div class=\"col-6\">
                ".oraForm()."
            </div>
            <div class=\"col-6\">
                ".oraLista()."
            </div>
        </div>
    </div>
    ";
  }
  function oraForm(){

    $oraAdat=["id"=>"","targy_id"=>"","terem_id"=>"","tanar_id"=>"","datum"=>"","orasorszam"=>"","csoport_id"=>"","ferohely"=>""];

    $diakLista=[["diak_nev"=>"","telepules_nev"=>""]];
    if(isset($_GET["action"]) && $_GET["action"] == "edit"){
      $oraAdat = oraAdat($_GET["id"]);
      $diakLista = diakListaAdat($_GET["id"]);
    }

    $diakListaSzöveg = "";
    if($diakLista[0]["diak_nev"] != ""){
      foreach($diakLista as $egyDiak){
        $diakListaSzöveg.='<div class="col-6 p-2">' . $egyDiak['diak_nev'] . '(' . $egyDiak['telepules_nev'] . ')</div>';
      }
    }

    return '
    <form method="post" action="?page=orak">
  <div class="container">
    <input type="hidden" name="id" id="id" value="'.$oraAdat['id'].'">
    <div class="row">
      <div class="col-12">Tárgy</div>
        <div class="row">
          <div class="col-12">' . formSelect($oraAdat['targy_id'],"targy") . '</div>
        </div>
    </div>
    <div class="row">
      <div class="col-12">Terem:</div>
    </div>
    <div class="row">
      <div class="col-12">' . formSelect($oraAdat['terem_id'],"terem") .'</div>
    </div>
    <div class="row">
      <div class="col-12">Tanár:</div>
    </div>
    <div class="row">
      <div class="col-12">' . formSelect($oraAdat['tanar_id'],"tanar") .'</div>
    </div>
    <div class="row">
      <div class="col-12">Dátum:</div>
    </div>
    <div class="row">
      <div class="col-12"><input type="date" name="datum" id="datum" class="form-control" value="' . $oraAdat['datum'] .'"></div>
    </div>
    <div class="row">
      <div class="col-12">Órasorszám:</div>
    </div>
    <div class="row">
      <div class="col-12"><input type="number" name="orasorszam" id="orasorszam" class="form-control" value="' . $oraAdat['orasorszam'] .'"></div>
    </div>
     <div class="row">
      <div class="col-12">Csoport:</div>
    </div>
    <div class="row">
      <div class="col-12">' . formSelect($oraAdat['csoport_id'],"csoport") .'</div>
    </div>
     <div class="row">
      <div class="col-12">Férőhely:</div>
    </div>
    <div class="row">
      <div class="col-12"><input type="number" name="ferohely" id="ferohely" class="form-control" value="' . $oraAdat['ferohely'] .'"></div>
    </div>
    <div class="row">
      ' . $diakListaSzöveg . '
    </div>
  
    <div class="row">
  '.($oraAdat['id'] != "" 
      ? '<div class="col-12"><button type="submit" class="mt-4 btn btn-primary" name="save">Mentés</button></div>' 
      : ''
  ).'
  <div class="col-12"><button type="submit" class="mt-4 btn btn-primary" name="new">Mentés újként</button></div>
</div>


  </div>

</form>';
  }
  function formSelect($id, $tabla){
    $adatok = formListaAdat($tabla);
    $vissza='<select name="'.$tabla.'_id" class="form-select">';
    
    foreach($adatok as $egyAdat)
      {

      $vissza .= '<option value="'.$egyAdat['id'].'" '.($egyAdat['id']==$id?'selected':'').'>'.$egyAdat['nev'].'</option>';

      }
     
    $vissza.='</select>';

    return $vissza;
  }
  function formListaAdat($tabla){
    GLOBAL $conn; 

    $vissza = [];

    $query = "SELECT * FROM $tabla";

    if($result = $conn->query($query)) {
        while($row = $result->fetch_assoc()) {
            $vissza[] = $row;
        }
    } else {
        echo $conn->error;
    }

    return $vissza;
}
function diakListaAdat($id){
    
    GLOBAL $conn; 

    $query = "SELECT diakok.nev as diak_nev, telepules.nev as telepules_nev FROM kapcsolo join diakok on kapcsolo.diakid=diakok.id
                                    join telepules on diakok.telepules_id= telepules.id
                                    where kapcsolo.oraid=?
                                    order by diak_nev";
    $vissza = [];

   

    if($stmt = $conn->prepare($query)) {
        $stmt -> bind_param("i",$id);
        $stmt -> execute();
        $result = $stmt-> get_result();
        while($row = $result->fetch_assoc()) {
            $vissza[] = $row;
        }
    } else {
        echo $conn->error;
    }

    $stmt -> close();
    return $vissza;
}
  
  function oraLista(){

  $oraListaAdat = oraListaAdat();
  $aktualisId=$_GET["id"] ?? "";


  $vissza="";
  foreach($oraListaAdat as $egyOra){
    
    if($aktualisId==$egyOra['id'])
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
                    <div class=\"col-12 container\">
                        <div class=\"row\">
                            <div class=\"col-4\">Tárgy: $egyOra[targy_nev]</div>
                            <div class=\"col-3\">Terem: $egyOra[terem_nev]</div>
                            <div class=\"col-5\">Tanár: $egyOra[tanar_nev]</div>
                            <div class=\"col-4\">Dátum: $egyOra[datum]</div>
                            <div class=\"col-3\">Órasorszám: $egyOra[orasorszam]</div>
                            <div class=\"col-3\">Csoport: $egyOra[csoport_nev]</div>
                            <div class=\"col-3\">Férőhely: $egyOra[ferohely]</div>
                            <div class=\"col-3\">Jelentkező: $egyOra[diak_darab] fő</div>
                            <div class=\"col-4\"></div>
                            <div class=\"col-2\">
                                <a href=\"?page=orak&action=edit&id=$egyOra[id]\" class=\"$linkColor\"><i class=\"bi bi-pencil\"></i></a>
                                <a href=\"?page=orak&action=delete&id=$egyOra[id]\ class=\"$linkColor\"><i class=\"bi bi-trash\"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
              </li>";
  }


    return '<ul class="list-group">
    '.$vissza.'
</ul>
';
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
        ORDER BY datum";
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
  function oraAdat($id){
    GLOBAL $conn; 


    $query="SELECT orak.*, tanar.nev as tanar_nev,
            terem.nev as terem_nev ,
            csoport.nev as csoport_nev ,
            targy.nev as targy_nev 

    from orak 
            join tanar on orak.tanar_id=tanar.id
            join terem on orak.terem_id=terem.id
            join csoport on orak.csoport_id=csoport.id
            join targy on orak.targy_id=targy.id
        WHERE orak.id=?";
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
  function oraInsert($adatok){
    GLOBAL $conn; 

    $query="INSERT INTO orak (datum,tanar_id,terem_id,csoport_id,targy_id,ferohely,orasorszam) values (?,?,?,?,?,?,?)";
    if($stmt = $conn->prepare($query)) {

      $stmt ->bind_param("siiiiii",$adatok['datum'],
                                  $adatok['tanar_id'],
                                  $adatok['terem_id'],
                                  $adatok['csoport_id'],
                                  $adatok['targy_id'],
                                  $adatok['ferohely'],
                                  $adatok['orasorszam']
      );
      $stmt->execute();  
    }
    else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $stmt ->close();
  }
  function oraUpdate($adatok){
    GLOBAL $conn; 

    $query="UPDATE orak SET datum=?, tanar_id=?, terem_id=?, csoport_id=?, targy_id=?, ferohely=?, orasorszam=? WHERE id=?";
    if($stmt = $conn->prepare($query)) {

      $stmt ->bind_param("siiiiiii",$adatok['datum'],
                                  $adatok['tanar_id'],
                                  $adatok['terem_id'],
                                  $adatok['csoport_id'],
                                  $adatok['targy_id'],
                                  $adatok['ferohely'],
                                  $adatok['orasorszam'],
                                  $adatok['id']
                                  );
                                  
      $stmt->execute();  
    }
    else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $stmt ->close();
  }

  function odaDelete($adatok)
  {
    GLOBAL $conn; 

    $query="DELETE FROM orak WHERE id=?";
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


