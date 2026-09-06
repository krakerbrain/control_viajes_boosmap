<?php
require __DIR__ . '/auth_admin.php';

if (isset($_POST['ingresar'])) {
    $accion = $_POST['ingresar'];

    switch ($accion) {
        case 'stats':
            $res = [];
            // Total usuarios
            $q1 = $con->query("SELECT COUNT(*) as total FROM usuarios");
            $res['usuarios'] = $q1->fetch(PDO::FETCH_ASSOC)['total'];

            // Total viajes
            $q2 = $con->query("SELECT COUNT(*) as total FROM viajes");
            $res['viajes'] = $q2->fetch(PDO::FETCH_ASSOC)['total'];

            // Activos 6 meses
            $q3 = $con->query("SELECT COUNT(DISTINCT idusuario) as total FROM viajes WHERE fecha >= DATE_SUB(NOW(), INTERVAL 6 MONTH)");
            $res['activos6m'] = $q3->fetch(PDO::FETCH_ASSOC)['total'];

            // Colaboradores
            $q4 = $con->query("SELECT COUNT(*) as total FROM colaboraciones WHERE verificado = 1");
            $res['colab'] = $q4->fetch(PDO::FETCH_ASSOC)['total'];

            echo json_encode($res);
            break;

        case 'listado':
            $filtro = $_POST['filtro'] ?? 'todos';
            $sql = "SELECT 
                        u.idusuario, u.nombre, u.correo, u.fecha_registro, u.admin,
                        MAX(v.fecha) as ultimo_viaje,
                        COUNT(v.idviaje) as total_viajes
                    FROM usuarios u
                    LEFT JOIN viajes v ON u.idusuario = v.idusuario";

            if ($filtro === 'activos') {
                $sql .= " GROUP BY u.idusuario HAVING ultimo_viaje >= DATE_SUB(NOW(), INTERVAL 6 MONTH)";
            } elseif ($filtro === 'inactivos') {
                $sql .= " GROUP BY u.idusuario HAVING (ultimo_viaje < DATE_SUB(NOW(), INTERVAL 6 MONTH) OR ultimo_viaje IS NULL)";
            } else {
                $sql .= " GROUP BY u.idusuario";
            }

            $sql .= " ORDER BY ultimo_viaje DESC";

            $q = $con->query($sql);
            $usuarios = $q->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($usuarios);
            break;

        case 'detalles_usuario':
            $id = $_POST['id'];
            $q = $con->prepare("SELECT * FROM usuarios WHERE idusuario = ?");
            $q->execute([$id]);
            echo json_encode($q->fetch(PDO::FETCH_ASSOC));
            break;
            
        case 'eliminar_usuario':
             // Solo si no es su propio usuario
             $id = $_POST['id'];
             if($id != $datosUsuario['idusuario']) {
                 // Borrar viajes primero (si no hay FK cascade)
                 $con->prepare("DELETE FROM viajes WHERE idusuario = ?")->execute([$id]);
                 $con->prepare("DELETE FROM rutas WHERE idusuario = ?")->execute([$id]);
                 $con->prepare("DELETE FROM colaboraciones WHERE idusuario = ?")->execute([$id]);
                 $con->prepare("DELETE FROM usuarios WHERE idusuario = ?")->execute([$id]);
                 echo "ok";
             }
             break;

        case 'eliminar_masivo':
            $ids = $_POST['ids'] ?? [];
            if (!is_array($ids) || empty($ids)) {
                echo "No hay usuarios seleccionados";
                break;
            }

            $ids = array_map('intval', $ids);
            $currentAdminId = (int)$datosUsuario['idusuario'];

            // Excluir usuario actual
            $ids = array_filter($ids, function($id) use ($currentAdminId) {
                return $id > 0 && $id !== $currentAdminId;
            });

            if (empty($ids)) {
                echo "No hay usuarios válidos para eliminar";
                break;
            }

            // Excluir administradores
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmtAdmins = $con->prepare("SELECT idusuario FROM usuarios WHERE idusuario IN ($placeholders) AND admin = 1");
            $stmtAdmins->execute(array_values($ids));
            $adminIds = $stmtAdmins->fetchAll(PDO::FETCH_COLUMN);

            $idsToDelete = array_values(array_diff($ids, $adminIds));

            if (empty($idsToDelete)) {
                echo "No se pueden eliminar usuarios administradores";
                break;
            }

            $inQuery = implode(',', array_fill(0, count($idsToDelete), '?'));

            try {
                $con->beginTransaction();

                $con->prepare("DELETE FROM viajes WHERE idusuario IN ($inQuery)")->execute($idsToDelete);
                $con->prepare("DELETE FROM rutas WHERE idusuario IN ($inQuery)")->execute($idsToDelete);
                $con->prepare("DELETE FROM colaboraciones WHERE idusuario IN ($inQuery)")->execute($idsToDelete);
                $con->prepare("DELETE FROM usuarios WHERE idusuario IN ($inQuery)")->execute($idsToDelete);

                $con->commit();
                echo "ok";
            } catch (PDOException $e) {
                $con->rollBack();
                echo "Error: " . $e->getMessage();
            }
            break;
    }
}

