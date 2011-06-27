<?php



/**
 * Skeleton subclass for representing a row from the 'command' table.
 *
 * 
 *
 * This class was autogenerated by Propel 1.6.1-dev on:
 *
 * Wed May 25 23:31:12 2011
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.lib.model
 */
class Command extends BaseCommand {

  protected $pid = null;

  /**
   * Exécute la commande en redirigeant la sortie standart et d'errreur vers
   * des fichiers temporaires afin de pouvoir surveiller leur contenu en temps réel.
   *
   * Cette commande n'est pas bloquante, l'exécution se passe en arrière plan
   *
   * @param boolean     $background   Si false exec ne rentrant la main qu'un fois la commande terminée
   * @return Command    self
   */
  public function exec ($background = true)
  {
    $this->setStdErrFile  (tempnam ('/tmp','manitou_cmd_'));
    $this->setStdOutFile  (tempnam ('/tmp','manitou_cmd_'));
    $this->setExitFile    (tempnam ('/tmp','manitou_cmd_'));
    $this->setStartedAt   (time());
    $this->save();

    $command = 'nohup bash -c '.escapeshellarg($this->getCommand()
      .' 2> '.$this->getStdErrFile()
      .'  > '.$this->getStdOutFile()
      .' ; echo $? "`date --rfc-3339=seconds`" > '.$this->getExitFile() // On place le code d'erreur et la date de fin dans ce fichier
    ).' > /dev/null ';
    
    if ($background)
      $command .= ' &' ;

    file_put_contents('/tmp/manitou_cmd_debug', $command."\n", FILE_APPEND);

    exec($command);

    if (! $background)
      $this->syncStatus();

    return $this;
  }

  public function isStarted ()
  {
    return ($this->getStartedAt() !== null);
  }

  public function isRunning ()
  {
    return ! $this->isFinished();
  }

  public function isStopped ()
  {
    return ($this->getFinishedAt() !== null && $this->getReturnCode() === null);
  }

  public function isFinished ()
  {
    return ($this->getFinishedAt() !== null);
  }

  public function hasErrors ()
  {
    $code = $this->getReturnCode() ;
    
    return (($code !== null && $code !== 0) || $this->getStdErr() != '');
  }

  public function syncStatus ()
  {
    if ($this->isFinished())
      return;

    $this->setStdErr (file_get_contents ($this->getStdErrFile()));
    $this->setStdOut (file_get_contents ($this->getStdOutFile()));

    // Si le programme n'a pas d'heure de fin et qu'on ne trouve pas son PID
    // on en conclu qu'il s'est terminé.
    if ($this->getFinishedAt() === null && $this->getPid() === false)
    {
      $exitStatus = explode (' ', file_get_contents ($this->getExitFile()), 2);

      $this->setReturnCode ($exitStatus[0]);
      $this->setFinishedAt ($exitStatus[1]);
      $this->deleteOutputFiles();
    }
    $this->save();
  }

  public function deleteOutputFiles ()
  {
    unlink($this->getStdErrFile());
    unlink($this->getStdOutFile());
    unlink($this->getExitFile());
  }

  /**
   * Return the pid number of the command
   *
   * @return integer    The pid number or null if the process is not found
   */
  public function getPid ()
  {
    if ($this->pid === null)
    {
      if ($this->isFinished() || $this->isStopped())
        return false;

      $cmd =  'ps axo pid,cmd | grep '.escapeshellarg($this->getStdErrFile()).' | grep -v "grep"';
      exec ($cmd, $output);

      $this->pid = (count ($output) ? (int) $output[0] : false);
    }

    return $this->pid;
  }

  public function stop ()
  {
    exec ('kill -9 '.$this->getPid());
    $this->setStdErr (file_get_contents ($this->getStdErrFile()));
    $this->setStdOut (file_get_contents ($this->getStdOutFile()));
    $this->setFinishedAt (time());
    $this->deleteOutputFiles();
    $this->save();
  }

  /**
   * @return integer Durée en secondes
   */
  public function getDuration ()
  {
    $startedAt = strtotime ($this->getStartedAt());
    $finishedAt = ($this->getFinishedAt() === null ? time() : strtotime($this->getFinishedAt()));

    return ($finishedAt - $startedAt);
  }

  public function setArgument ($name, $value)
  {
    $this->setCommand(str_replace('%'.$name.'%', escapeshellarg ($value), $this->getCommand()));
  }


	/**
	 * Code to be run before inserting to database
	 * @param PropelPDO $con
	 * @return boolean
	 */
	public function preInsert(PropelPDO $con = null)
	{
    if (! $this->getUserId())
    {
      if (sfContext::hasInstance())
        $this->setUserId(sfContext::getInstance()->getUser()->getProfileVar('uid'));
      else
        $this->setUserId('UNKNOWN');
    }
    
		return parent::preInsert($con);
	}

} // Command
