<?php
require __DIR__ . '/auth_admin.php';
$indice = "maestro";
include __DIR__ . "/../partials/header.php";
?>

<div class="mx-3 mt-4">
    <h4 class="font-weight-bold mb-4 text-danger"><i class="fas fa-crown mr-2"></i>Panel Maestro de Administración</h4>

    <!-- STATS CARDS -->
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-2">
            <div class="card border-0 shadow-sm text-center py-2" style="background:#fef2f2">
                <h6 class="text-muted small mb-1">USUARIOS TOTALES</h6>
                <p id="stat-usuarios" class="h4 mb-0 font-weight-bold">-</p>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="card border-0 shadow-sm text-center py-2" style="background:#f0fdf4">
                <h6 class="text-muted small mb-1">ACTIVOS (6 MESES)</h6>
                <p id="stat-activos" class="h4 mb-0 font-weight-bold text-success">-</p>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="card border-0 shadow-sm text-center py-2" style="background:#eff6ff">
                <h6 class="text-muted small mb-1">VIAJES TOTALES</h6>
                <p id="stat-viajes" class="h4 mb-0 font-weight-bold text-primary">-</p>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="card border-0 shadow-sm text-center py-2" style="background:#fff7ed">
                <h6 class="text-muted small mb-1">COLABORADORES</h6>
                <p id="stat-colab" class="h4 mb-0 font-weight-bold text-warning">-</p>
            </div>
        </div>
    </div>

    <!-- FILTROS Y TABLA -->
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap">
            <h6 class="mb-0 font-weight-bold text-muted my-1">Gestión de Usuarios</h6>
            <div class="d-flex align-items-center my-1">
                <button id="btnEliminarMasivo" class="btn btn-sm btn-danger mr-2 d-none" onclick="eliminarMasivo()">
                    <i class="fas fa-trash-alt mr-1"></i> Eliminar Seleccionados (<span id="countSeleccionados">0</span>)
                </button>
                <select id="filtroUsuario" class="form-control form-control-sm" style="width: 150px;" onchange="cargarListado()">
                    <option value="todos">Todos</option>
                    <option value="activos" selected>Activos (6 meses)</option>
                    <option value="inactivos">Inactivos</option>
                </select>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                <table class="table table-hover mb-0" style="font-size: 0.85rem;">
                    <thead class="bg-light sticky-top">
                        <tr>
                            <th class="text-center" style="width: 40px;">
                                <input type="checkbox" id="checkAll" onchange="toggleSelectAll(this)" title="Seleccionar/Deseleccionar todos">
                            </th>
                            <th>Nombre / Correo</th>
                            <th class="text-center">Viajes</th>
                            <th>Última Actividad</th>
                            <th class="text-center">Admin</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="tablaUsuarios">
                        <tr><td colspan="6" class="text-center py-4 text-muted">Cargando datos...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .sticky-top { position: sticky; top: -1px; z-index: 10; border-bottom: 2px solid #dee2e6; }
    #tablaUsuarios tr:hover { background-color: #fffaf0; }
</style>

<?php include __DIR__ . "/../partials/boostrap_script.php"; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        cargarStats();
        cargarListado();
    });

    function cargarStats() {
        $.post('conexiones_admin.php', { ingresar: 'stats' }).done(function(data) {
            const res = JSON.parse(data);
            document.getElementById('stat-usuarios').textContent = res.usuarios;
            document.getElementById('stat-viajes').textContent = res.viajes;
            document.getElementById('stat-activos').textContent = res.activos6m;
            document.getElementById('stat-colab').textContent = res.colab;
        });
    }

    function cargarListado() {
        const filtro = document.getElementById('filtroUsuario').value;
        const tbody = document.getElementById('tablaUsuarios');
        const checkAll = document.getElementById('checkAll');
        if (checkAll) checkAll.checked = false;
        
        $.post('conexiones_admin.php', { ingresar: 'listado', filtro: filtro }).done(function(data) {
            const usuarios = JSON.parse(data);
            let html = "";
            
            if (usuarios.length === 0) {
                html = '<tr><td colspan="6" class="text-center py-4">No se encontraron usuarios.</td></tr>';
            } else {
                usuarios.forEach(u => {
                    const ultimaAct = u.ultimo_viaje ? u.ultimo_viaje : '<span class="text-muted italic">Sin viajes</span>';
                    const isAdmin = u.admin == 1 ? '<i class="fas fa-check-circle text-success"></i>' : '-';
                    const checkbox = u.admin == 1 ? '' : `<input type="checkbox" class="check-usuario" value="${u.idusuario}" onchange="actualizarSeleccion()">`;
                    const btnEliminar = u.admin == 1 ? '' : `<button class="btn btn-sm btn-outline-danger" title="Eliminar usuario" onclick="borrarUsuario(${u.idusuario}, '${u.nombre}')"><i class="fas fa-trash-alt"></i></button>`;
                    
                    html += `
                        <tr>
                            <td class="text-center align-middle">${checkbox}</td>
                            <td>
                                <div class="font-weight-bold">${u.nombre}</div>
                                <div class="text-muted small">${u.correo}</div>
                            </td>
                            <td class="text-center font-weight-bold align-middle">${u.total_viajes}</td>
                            <td class="align-middle">${ultimaAct}</td>
                            <td class="text-center align-middle">${isAdmin}</td>
                            <td class="text-center align-middle">${btnEliminar}</td>
                        </tr>
                    `;
                });
            }
            tbody.innerHTML = html;
            actualizarSeleccion();
        });
    }

    function toggleSelectAll(master) {
        const checkboxes = document.querySelectorAll('.check-usuario');
        checkboxes.forEach(cb => cb.checked = master.checked);
        actualizarSeleccion();
    }

    function actualizarSeleccion() {
        const seleccionados = document.querySelectorAll('.check-usuario:checked');
        const total = seleccionados.length;
        const btn = document.getElementById('btnEliminarMasivo');
        const countSpan = document.getElementById('countSeleccionados');
        const checkAll = document.getElementById('checkAll');
        const totalCheckboxes = document.querySelectorAll('.check-usuario');

        if (total > 0) {
            btn.classList.remove('d-none');
            countSpan.textContent = total;
        } else {
            btn.classList.add('d-none');
        }

        if (checkAll) {
            checkAll.checked = totalCheckboxes.length > 0 && total === totalCheckboxes.length;
        }
    }

    function eliminarMasivo() {
        const seleccionados = Array.from(document.querySelectorAll('.check-usuario:checked')).map(cb => cb.value);
        if (seleccionados.length === 0) return;

        if (confirm(`¿Estás SEGURO de eliminar permanentemente a los ${seleccionados.length} usuarios seleccionados? Esta acción no se puede deshacer y borrará todos sus viajes y rutas.`)) {
            $.post('conexiones_admin.php', { ingresar: 'eliminar_masivo', ids: seleccionados }).done(function(data) {
                if (data.trim() === 'ok') {
                    const checkAll = document.getElementById('checkAll');
                    if (checkAll) checkAll.checked = false;
                    cargarListado();
                    cargarStats();
                } else {
                    alert('Error al eliminar usuarios: ' + data);
                }
            });
        }
    }

    function borrarUsuario(id, nombre) {
        if(confirm(`¿Estás SEGURO de eliminar al usuario "${nombre}"? Esta acción borrará permanentemente todos sus viajes y rutas.`)) {
            $.post('conexiones_admin.php', { ingresar: 'eliminar_usuario', id: id }).done(function(data) {
                if(data.trim() === 'ok') {
                    cargarListado();
                    cargarStats();
                } else {
                    alert('Error al eliminar: ' + data);
                }
            });
        }
    }
</script>


</body>
</html>
