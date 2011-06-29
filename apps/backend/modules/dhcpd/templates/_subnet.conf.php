
#  <?php echo $subnet->getName() ?>

subnet <?php echo $subnet->getIpAddress() ?> netmask <?php echo $subnet->getNetmask() ?> {

  pool {

    failover peer "dhcp_failover";
    deny dynamic bootp clients;

    range <?php echo $subnet->getRangeBegin().' '.$subnet->getRangeEnd() ?>;

    option subnet-mask  <?php echo $subnet->getNetmask() ?>;
    option routers      <?php echo preg_replace('/\.0$/', '.1', $subnet->getIpAddress()) // on remplace le dernier ".0" par ".1" ?>;
    option domain-name  "<?php echo $subnet->getDomainName() ?>";

    <?php if ($subnet->getPxeFileId() !== null) echo 'filename = "'.$subnet->getPxeFile ()->getFilename()."\"\n"; ?>

    <?php echo str_replace("\n", "\n    ", $subnet->getCustomConf())."\n" ?>

  <?php foreach ($subnet->getHosts () as $host): ?>

    <?php if ($host->getComment ()): ?># <?php echo str_replace("\n", "\n    # ", $host->getComment())."\n" ?><?php endif; ?>
    # Created at : <?php echo $host->getCreatedAt() ?>.
    # Updated at : <?php echo $host->getUpdatedAt() ?>.
    host <?php echo $host->getHostname() ?> {
        hardware ethernet           <?php echo $host->getMacAddress() ?>;
        fixed-address               <?php echo $host->getIpAddress() ?>;
        option host-name            "<?php echo $host->getHostname() ?>";
        option domain-name-servers  <?php echo $subnet->getDnsServer() ?>;

        <?php if ($host->getPxeFileId() !== null) echo 'filename = "'.$host->getPxeFile ()->getFilename()."\"\n"; ?>
        <?php echo str_replace("\n", "\n        ", $host->getCustomConf())."\n" ?>
    }
  <?php endforeach ?>

  }

}
