
<h1>Conf DNS</h1>

<!-- Fichiers avec des erreurs -->
<?php foreach ($files as $filename): ?>
  <?php if ($dnsErrors->offsetExists ($filename)): ?>
  <h2><?php echo $filename ?> - <?php echo link_to ('Afficher', '@dns_show?filename='.urlencode($filename)) ?></h2>
    <pre class="dns_errors"><?php echo implode ("\n", $dnsErrors->getRaw($filename)) ?></pre>
  <?php endif ?>
<?php endforeach ?>


<!-- Fichiers sans erreurs -->
<?php foreach ($files as $filename): ?>
  <?php if (! $dnsErrors->offsetExists ($filename)): ?>
    <h2><?php echo $filename ?> - <?php echo link_to ('Afficher', '@dns_show?filename='.$filename) ?></h2>
  <?php endif ?>
<?php endforeach ?>
