
<h1>Conf DNS</h1>

<p>
  Dernière configuration générée par Manitou (peut être différent de la dernière version du dépôt)
  <?php echo link_to ('Forcer la regénération', 'dns/reload') ?>
</p>

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
