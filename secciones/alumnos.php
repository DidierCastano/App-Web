<?php

    include_once '../configuraciones/bd.php';

    //Creamos la instancia de la bd
    $conexionBD=BD::crearInstancia();

    $id = isset($_POST['id']) ? $_POST['id']:'';
    $nombres = isset($_POST['nombres']) ? $_POST['nombres']:'';
    $apellidos = isset($_POST['apellidos']) ? $_POST['apellidos']:'';

    $cursos = isset($_POST['cursos']) ? $_POST['cursos']:'';

    $accion = isset($_POST['accion']) ? $_POST['accion']:'';

    if($accion != ''){
        switch($accion){
            case 'agregar':
                $sql = "INSERT INTO alumno (id, nombres, apellidos) VALUES (NULL, :nombres, :apellidos)";
                $consulta = $conexionBD->prepare($sql);
                $consulta->bindParam(':nombres', $nombres);
                $consulta->bindParam(':apellidos', $apellidos);
                $consulta->execute();
                $idAlumno = $conexionBD->lastInsertId();

                foreach($cursos as $curso){
                    $sql = "INSERT INTO alumnos_cursos (id, idalumno, idcurso) VALUES (NULL, :idalumno, :idcurso)";
                    $consulta = $conexionBD->prepare($sql);
                    $consulta->bindParam(':idalumno', $idAlumno);
                    $consulta->bindParam(':idcurso', $curso);
                    $consulta->execute();
                }
            break;
        }
    }

    print_r($_POST);

    //Consultamos la bd para que traiga todos los registros
    $sql = "SELECT *
              FROM alumno";
    $listaAlumnos = $conexionBD->query($sql);
    $alumnos = $listaAlumnos->fetchAll();

    //Consultamos la bd para que traiga todos los registros de los alumnos cursos
    foreach($alumnos as $clave => $alumno){
        $sql = "SELECT *
                  FROM cursos 
                 WHERE id IN (SELECT idcurso 
                                FROM alumnos_cursos 
                               WHERE idalumno = :idalumno)";
        $consulta = $conexionBD->prepare($sql);
        $consulta->bindParam(':idalumno', $alumno['id']);
        $consulta->execute();
        $cursosAlumno = $consulta->fetchAll();
        $alumnos[$clave]['cursos'] = $cursosAlumno;
    }
?>