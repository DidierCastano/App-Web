<?php
    require('../librerias/fpdf/fpdf.php');
    include_once('../configuraciones/bd.php');
    $conexionBD = BD::crearInstancia();

    function agregarTexto($pdf, $texto, $x, $y, $align = 'L', $fuente, $size = 10, $r = 0, $g = 0, $b = 0){
        $pdf->SetFont($fuente, "", $size);
        $pdf->SetXY($x, $y);
        $pdf->SetTextColor($r, $g, $b);
        $pdf->Cell(0, 10, $texto, 0, 0, $align);
    }

    function agregarImagen($pdf, $imagen, $x, $y){
        $pdf->Image($imagen, $x, $y, 0);
    }

    $idcurso = isset($_GET['idcurso'])?$_GET['idcurso']:'';
    $idalumno = isset($_GET['idalumno'])?$_GET['idalumno']:'';

    $sql = "SELECT alumno.nombres  , 
                   alumno.apellidos,
                   cursos.nombre_curso
              FROM alumno,
                   cursos
             WHERE alumno.id = :idalumno
               AND cursos.id = :idcurso";
    
    $consulta = $conexionBD->prepare($sql);
    $consulta->bindParam(':idalumno', $idalumno);
    $consulta->bindParam(':idcurso' , $idcurso );
    $consulta->execute();
    $alumno = $consulta->fetch(PDO::FETCH_ASSOC);

    $pdf = new FPDF("L", "mm", array(254,194));
    $pdf->AddPage();
    $pdf->setFont("Arial", "B", 16);
    agregarImagen($pdf, "../src/certificado_.jpg", 0, 0);
    agregarTexto($pdf, "Didier Castano",95,95,'L',"Helvetica",40,0,84,115);
    agregarTexto($pdf, "Curso Wep PHP",40,135,'C',"Helvetica",30,0,84,115);
    agregarTexto($pdf, date("d/m/Y"), 100,158,'C',"Helvetica",20,0,84,115);
    $pdf->Output();
    //Si el cursor o la consulta trae el nombre y el curso por qué no lo muestra?
    /* print_r($alumno['nombres']." ".$alumno['apellidos']);
    print_r($alumno['nombre_curso']); */

?>