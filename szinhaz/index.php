<?php 
    include_once("includes/config.php");
    include_once("includes/database.php");
    

    $navbar = [
        0=>["url"=>"?page=eloadas","text"=>"Előadás"],
        1=>["url"=>"?page=mufaj","text"=>"Műfaj"],
        2=>["url"=>"?page=nyelv","text"=>"Nyelv"],
        3=>["url"=>"?page=szekhely","text"=>"Székhely"],
        4=>["url"=>"?page=szinhaz","text"=>"Színhház"],
        5=>["url"=>"?page=tulajdonsag","text"=>"Tulajdonsagnév"],
        6=>["url"=>"?page=tulajdonsagnev","text"=>"Tulajdonsagnév"],

    ]; 

   $page = $_GET["page"] ?? "";
   $tartalom="";
   switch($page){
    case "eloadas":
        //include("includes/csoport.php");
        $oldalData=[
                "cim" => "sablon",
                "page" => "csoport",
                "tabla" => "csoport"
            ];
            include_once("includes/egyszeruAdatbazis.php");
            break;
    case "mufaj":
            $oldalData=[
                "cim"=>"Műfaj",
                "page"=>"mufaj",
                "tabla"=>"mufaj"
            ];
            include_once("includes/egyszeruAdatbazis.php");
            break;
    case "nyelv":
       $oldalData=[
                "cim"=>"Nyelv",
                "page"=>"nyelv",
                "tabla"=>"nyelv"
            ];
            include_once("includes/egyszeruAdatbazis.php");
            break;
    case "szekhely":
        $oldalData=[
                "cim"=>"Székhely",
                "page"=>"szekhely",
                "tabla"=>"szekhely"
            ];
            include_once("includes/egyszeruAdatbazis.php");
            break;
            
    case "szinhaz":
        $oldalData=[
                "cim" => "sablon",
                "page" => "targy",
                "tabla" => "targy"
            ];
            include_once("includes/egyszeruAdatbazis.php");;
            break;
    case "tulajdonsag":
        $oldalData=[
                "cim" => "Tulajdonság",
                "page" => "tulajdonsag",
                "tabla" => "tulajdonsag"
            ];
            include_once("includes/tulajdonsag.php");;
            break;
    case "tulajdonsagnev":
        $oldalData=[
                "cim"=>"Tulajdonságnév",
                "page"=>"tulajdonsagnev",
                "tabla"=>"tulajdonsagnev"
            ];
            include_once("includes/egyszeruAdatbazis.php");
            break;
    default: break;
   }


    include_once("includes/sablon.php");
?>