<div id="sf_admin_container">

<h1>Configuration générée</h1>

<?php echo link_to ('Forcer la regénération', 'dhcpd/reload') ?>

<?php foreach ($dhcpdConf as $file => $conf): ?>
  <h2><?php echo $file ?></h2>
  <pre><?php echo $conf ?></pre>
<?php endforeach ?>

</div>
