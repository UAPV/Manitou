<h1>Restauration</h1>

<?php echo form_tag ('@image_restore', array ('method' => 'post')) ?>

<!--
  <h2>Machines sélectionnées</h2>
  <ul>
    <?php foreach ($hosts as $host): ?>
      <li><?php echo $host ?></li>
    <?php endforeach ?>
  </ul>
-->

  <?php echo $form ?>

<p>
  <input type="submit" value="GO !" />
</p>

</form>
