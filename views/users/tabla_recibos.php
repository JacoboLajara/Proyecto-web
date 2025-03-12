<?php
// views/users/tabla_recibos.php
if (!isset($recibos) || count($recibos) === 0) {
    echo "<p class='text-center'>No se encontraron recibos.</p>";
    return;
}
?>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido 1</th>
                <th>Apellido 2</th>
                <th>Curso</th>
                <th>Importe</th>
                <th>Estado</th>
                <th>Fecha Emisión</th>
                <th>Fecha Pago</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recibos as $recibo): ?>
                <tr>
                    <td><?= htmlspecialchars($recibo['Nombre'] ?? '') ?></td>
                    <td><?= htmlspecialchars($recibo['Apellido1'] ?? '') ?></td>
                    <td><?= htmlspecialchars($recibo['Apellido2'] ?? '') ?></td>
                    <td><?= htmlspecialchars($recibo['Curso'] ?? '') ?></td>
                    <td><?= htmlspecialchars($recibo['Importe'] ?? '') ?></td>
                    <td><?= htmlspecialchars($recibo['Estado'] ?? '') ?></td>
                    <td><?= htmlspecialchars($recibo['Fecha_Emision'] ?? '') ?></td>
                    <td><?= htmlspecialchars($recibo['Fecha_Pago'] ?? '') ?></td>
                    <td class="accion-cell">
                        <div>
                            <?php if ($recibo['Estado'] === 'Pendiente'): ?>
                                <input type="checkbox" class="chkPagado" data-id="<?= $recibo['ID_Recibo'] ?>">
                            <?php else: ?>
                                <span class="text-success">Pagado</span>
                            <?php endif; ?>
                        </div>
                        <div style="margin-top: 5px;">
                            <button class="btn btn-sm btn-info editarReciboBtn" 
                                    data-id="<?= $recibo['ID_Recibo'] ?>" 
                                    data-estado="<?= $recibo['Estado'] ?>" 
                                    data-fecha_pago="<?= $recibo['Fecha_Pago'] ?>">
                                Editar
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
