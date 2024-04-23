<?php
/**
 * Plantilla para mostrar campos personalizados de profesor y horario
 *
 * @author Your Name
 * @version 1.0.0
 */

// Obtener datos globales
global $profesor_id, $horario_id;

?>

<div class="campos-personalizados">
  <h3>Seleccionar profesor y horario</h3>

  <div class="campo-profesor">
    <label for="profesor_id">Profesor:</label>
    <select id="profesor_id" name="profesor_id">
      <?php foreach ( $profesor_id as $profesor ): ?>
        <option value="<?php echo $profesor['id']; ?>"><?php echo $profesor['nombre']; ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="campo-horario">
    <label for="horario_id">Horario:</label>
    <select id="horario_id" name="horario_id">
      <?php foreach ( $horario_id as $horario ): ?>
        <option value="<?php echo $horario['id']; ?>"><?php echo $horario['horario']; ?></option>
      <?php endforeach; ?>
    </select>
  </div>
</div>