<?php
// Este archivo contiene las plantillas de todos los modales que necesitamos
// Se puede incluir en cualquier página que los requiera
?>


<!-- Modal de Confirmación -->
<div class="modal fade" id="confirmModalRutas" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="confirmModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmModalAction">Confirmar</button>
            </div>
        </div>
    </div>
</div>