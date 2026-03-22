    <!-- Modal de mensaje -->
    <div class="modal fade" id="modalMensaje" tabindex="-1" role="dialog" aria-labelledby="modalMensajeGeneral"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-light">
                    <h5 class="modal-title" id="modalMensajeGeneral">NUEVA ACTUALIZACIÓN</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true" class="text-light">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Se ha creado la opción de agregar</p>
                    <p>EXTRAS O BONOS Y PEAJES</p>
                    <img width="100%" src="assets/img/img10012024_nuevosIconos.jpg" alt="nuevosIconos">
                    <p>Los extras (cuando hayan) se mostraran desglosados en el menú ESTADISTICAS</p>
                    <p>Los peajes son solo para llevar un control y no afectaran los montos finales</p>
                    <p>Se pueden agregar tantos peajes o bonos como quieras. Por ejemplo si ofrecen un bono por día de
                        $750 y
                        luego otro de $1000 se podrán agregar todos.</p>
                    <p>Al final un cuadro con todos los datos se vería así:</p>
                    <img width="100%" src="assets/img/img10012024_cuadroEstadisticas.jpg" alt="Cuadro estadisticas">
                    <p>Para evitar cofusiones, si no hay peajes o bonos o ambos el cuadro no mostrará estos detalles</p>
                    <p>Cualquier duda o sugerencia me avisan por correo: admin@biowork.xyz o al whatsapp</p>
                    <p class="small text-danger">SI NO SE VEN LOS CAMBIOS INTENTAR LO MISMO QUE CON
                        BEETRACK, ES
                        DECIR, BORRAR LOS DATOS DE LA APP y ENTRAR DE NUEVO</p>
                    <!-- Checkbox -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="checkNoMostrar">
                        <label class="form-check-label small" for="checkNoMostrar">
                            He leído y no deseo ver de nuevo este mensaje
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"
                        onclick="guardarDecision()">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function mostrarModalMensaje() {
            // Mostrar el modal
            //temporal 10012024
            if (localStorage.getItem('modalActualizacion10012024') !== 'true') {
                $('#modalMensaje').modal('show');
            }

        }
    </script>

    <!-- fin modal mensaje-->

    <!-- modal eliminar viaje -->
    <div class="modal fade" id="modalEliminarViaje" tabindex="-1" role="dialog"
        aria-labelledby="modalEliminarViajeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-light">
                    <h5 class="modal-title" id="modalEliminarViajeLabel">ELIMINAR VIAJE</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true" class="text-light">&times;</span>
                    </button>
                </div>
                <input type="hidden" name="idEliminaViaje" id="idEliminaViaje" value="">
                <div class="modal-body">
                    <p>Esta acción eliminará el viaje y los detalles que contenga (peajes y extras)</p>
                    <p>¿Desea continuar?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"
                        onclick="eliminaViaje()">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function mostrarModaleliminaViaje(id) {
            // Mostrar el modal
            document.getElementById("idEliminaViaje").value = id;
            $('#modalEliminarViaje').modal('show');

        }
    </script>
    <!-- fin eliminar viaje -->

    <!-- modal cambio dominio -->
    <div class="modal fade" id="cambioDominioModal" tabindex="-1" aria-labelledby="cambioDominioModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-light">
                    <h5 class="modal-title" id="cambioDominioModalLabel">¡IMPORTANTE: CAMBIO DE DOMINIO!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true" class="text-light">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-center font-weight-bold text-danger">Este sitio (boosterapp.site) dejará de funcionar pronto.</p>
                    
                    <div class="alert alert-warning mt-3">
                        <h6 class="font-weight-bold"><i class="fas fa-globe mr-2"></i>Nueva Dirección Web:</h6>
                        <p>Para entrar desde el navegador, ahora debes usar:</p>
                        <p class="text-center"><a href="https://boosterapp.de" class="h5 font-weight-bold">boosterapp.de</a></p>
                        <p class="small text-muted">Tus datos de acceso (usuario y contraseña) siguen siendo los mismos.</p>
                    </div>

                    <div class="alert alert-info mt-3">
                        <h6 class="font-weight-bold"><i class="fas fa-mobile-alt mr-2"></i>Usuarios de Android:</h6>
                        <p>1. <strong>Desinstala</strong> la aplicación que tienes actualmente.</p>
                        <p>2. Entra a <a href="https://boosterapp.de" class="font-weight-bold">boosterapp.de</a> desde tu celular.</p>
                        <p>3. Ve a <strong>"Descarga la App"</strong> e instala la nueva versión para seguir registrando tus viajes.</p>
                    </div>

                    <div class="alert alert-danger mt-3 mb-0 text-center py-2">
                        <p class="mb-0 small font-italic">Evita pérdida de acceso migrando hoy mismo al nuevo sitio.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Entendido</button>
                    <a href="https://boosterapp.de" class="btn btn-danger">Ir al nuevo sitio</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function mostrarModalCambioDominio() {
            // Mostrar siempre debido a la urgencia (boosterapp.site -> boosterapp.de)
            const modalEl = document.getElementById('cambioDominioModal');
            if(modalEl) {
                $('#cambioDominioModal').modal('show');
            }
        }
    </script>