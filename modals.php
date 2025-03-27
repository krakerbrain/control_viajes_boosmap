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
                    <!-- <p>Durante los próximos días se harán algunas actualizaciones de la app para modificar el nuevo
                        porcentaje
                        del ISLR.</p>
                    <p> El mismo pasa de 13% a 13.75%.</p>
                    <p>Como el pago de la última quincena (en Viña del Mar) es en Enero va a haber una diferencia en el
                        pago y para que los montos sean mostrados lo más cercano posible a la realidad es necesario
                        actualizar
                        el monto de todos los viajes desde el 16/12 en adelante.</p>
                    <p>También se actualizará el monto actual de las rutas ya que, como esta app refleja el monto
                        líquido pagado por viaje,
                        al haber un cambio de % de retención de ISLR el monto líquido es menos dinero por viaje que el
                        año anterior.</p>
                    <p>Trataré de ser lo más cuidadoso posible para no alterar los datos registrados, pero si llega a
                        pasar algo recuerden que pueden
                        contactarme para ayudarlos a corregir cualquier problema. Los que tienen mi número pueden
                        hacerlo
                        por ahí y si no por correo: admin@biowork.xyz</p> -->
                    <!-- <p><b>Los cambios han sido realizados.</b></p>
                    <p>Si consideras que los montos no son los correctos, siempre
                        puedes
                        modificar los montos a tu gusto en el menú "Configura Viajes". Puedes tanto borrar todas las
                        rutas y crearlas de nuevo
                        (eso no afectará los viajes existentes) o puedes ir más abajo en "Modificar montos" y
                        cambiarlos, solo que hay
                        dos opciones a escoger y eso si puede modificar el monto de los viajes existentes. </p>
                    <p>Recuerden también que si tienen dudas me pueden contactar.</p>
                    <p>Les agradezco a todos los que usan esta aplicación. Para mí es importante porque me ayuda a
                        mantenerme activo en
                        mi trabajo como programador.</p>
                    <p>Feliz año para todos!!</p> -->
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
                    <h5 class="modal-title" id="cambioDominioModalLabel">¡Importante cambio de dominio!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true" class="text-light">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>A partir del <strong>1 de abril</strong>, nuestra plataforma migrará a un nuevo dominio.</p>

                    <div class="alert alert-info mt-3">
                        <h6 class="fw-bold">Para usuarios web:</h6>
                        <p>La nueva dirección será: <a href="https://boosterapp.site"
                                class="fw-bold">boosterapp.site</a></p>
                        <p>La dirección antigua <strong>boosterapp2024.in</strong> dejará de funcionar.</p>
                    </div>

                    <div class="alert alert-info mt-3">
                        <h6 class="fw-bold">Para usuarios de Android:</h6>
                        <p>Deben descargar la nueva versión de la aplicación para continuar usando el servicio sin
                            interrupciones.</p>
                        <p>La versión actual dejará de funcionar después del 1 de abril.</p>
                    </div>

                    <div class="alert alert-danger mt-4">
                        <p class="mb-0"><strong>Fecha límite:</strong> 1 de abril de 2025. Después de esta fecha, tanto
                            la app como la dirección antigua quedarán inoperativas.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <a href="https://boosterapp.site" class="btn btn-success">Ir al nuevo sitio</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para mostrar el modal automáticamente -->
    <script>
        function mostrarModalCambioDominio() {
            // ID del modal y clave para almacenar en localStorage
            const modalId = 'cambioDominioModal';
            const storageKey = 'lastModalShownDate';

            // Obtener la fecha de la última vez que se mostró el modal
            const lastShownDate = localStorage.getItem(storageKey);
            const today = new Date().toDateString(); // Fecha actual en formato legible (sin hora)

            // Si no se ha mostrado hoy (o nunca), mostrar el modal
            if (!lastShownDate || lastShownDate !== today) {
                const modal = new bootstrap.Modal(document.getElementById(modalId));
                modal.show();

                // Guardar la fecha actual en localStorage
                localStorage.setItem(storageKey, today);
            }
        }
    </script>