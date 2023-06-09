<?php

    include_once '../configuraciones/bd.php';

    //Creamos la instancia de la bd
    $conexionBD=BD::crearInstancia();

    $id = isset($_POST['id']) ? $_POST['id']:'';
    $nombres = isset($_POST['nombres']) ? $_POST['nombres']:'';
    $apellidos = isset($_POST['apellidos']) ? $_POST['apellidos']:'';
    $cursos = isset($_POST['cursos']) ? $_POST['cursos']:'';
    $accion = isset($_POST['accion']) ? $_POST['accion']:'';
    //print_r("$_POST['accion']");
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
            case 'Seleccionar':
                //echo "Presionaste seleccionar";
                $sql = "SELECT *
                          FROM alumno
                         WHERE id = :id";
                $consulta = $conexionBD->prepare($sql);
                $consulta->bindParam(':id', $id);
                $consulta->execute();
                $alumno = $consulta->fetch(PDO::FETCH_ASSOC);

                $nombres = $alumno['nombres'];
                $apellidos = $alumno['apellidos'];

                $sql = "SELECT cursos.id
                          FROM alumnos_cursos
                    INNER JOIN cursos
                            ON cursos.id = alumnos_cursos.idcurso
                         WHERE alumnos_cursos.idalumno = :idalumno";
                $consulta = $conexionBD->prepare($sql);
                $consulta->bindParam(':idalumno', $id);
                $consulta->execute();
                $cursosAlumno = $consulta->fetchAll(PDO::FETCH_ASSOC);
                //print_r($cursosAlumno);
                foreach($cursosAlumno as $curso){
                  $arregloCursos[] = $curso['id'];   
                }
            break;
            case 'borrar':
                $sql = "DELETE
                          FROM alumno
                         WHERE id = :id";
                $consulta = $conexionBD->prepare($sql);
                $consulta->bindParam(':id', $id);
                $consulta->execute();
            break;
            case 'editar':
                $sql = "UPDATE alumno 
                           SET nombres   = :nombres,
                               apellidos = :apellidos
                         WHERE id        = :id";
                $consulta = $conexionBD->prepare($sql);
                $consulta->bindParam(':id', $id);
                $consulta->bindParam(':nombres', $nombres);
                $consulta->bindParam(':apellidos', $apellidos);
                $consulta->execute();

                if(isset($cursos)){
                    $sql = "DELETE
                              FROM alumnos_cursos
                             WHERE idalumno = :idalumno";
                    $consulta = $conexionBD->prepare($sql);
                    $consulta->bindParam(':idalumno', $id);
                    $consulta->execute();

                    foreach($cursos as $curso){
                        $sql = "INSERT INTO alumnos_cursos (id, idalumno, idcurso) VALUES (NULL, :idalumno, :idcurso)";
                        $consulta = $conexionBD->prepare($sql);
                        $consulta->bindParam(':idalumno', $id);
                        $consulta->bindParam(':idcurso', $curso);
                        $consulta->execute();
                    }
                    $arregloCursos = $cursos;
                }
            break;
        }
    }

    //print_r($_POST);

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
    $sql = "SELECT *
             FROM cursos";
    $listaCursos = $conexionBD -> query($sql);
    $cursos = $listaCursos -> fetchAll();

    //print_r($cursos);
?>