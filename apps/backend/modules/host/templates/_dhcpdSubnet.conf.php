
#  <?php echo $subnet->getName() ?>

subnet <?php echo $subnet->getIpAddress() ?> netmask <?php echo $subnet->getNetmask() ?> {
  
    option subnet-mask  <?php echo $subnet->getNetmask() ?>;
    option routers      <?php echo preg_replace('/\.0$/', '.1', $subnet->getIpAddress()) // on remplace le dernier ".0" par ".1" ?>;
    next-server         10.4.0.10; <?php // TODO rendre ça configurable ! ?>

  <?php foreach ($subnet->getHosts () as $host): ?>
      
    <?php if ($host->getCustomConf ()): ?>

    # <?php echo str_replace("\n", "\n    # ", $host->getComment()) ?>
    <?php endif; ?>

    # Created at : <?php print $host->getCreatedAt() ?>

    # Updated at : <?php print $host->getUpdatedAt() ?>

    host <?php echo $host->getHostname() ?> {
        hardware ethernet <?php echo $host->getMacAddress() ?>;
        fixed-address     <?php echo $host->getIpAddress() ?>;
        option host-name  <?php echo $host->getHostname() ?>;
        <?php echo $host->getCustomConf() ?> 
    }
  <?php endforeach ?>

}
