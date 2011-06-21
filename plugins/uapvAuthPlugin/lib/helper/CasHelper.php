<?php


/**
 * return a link to the CAS login page
 *
 * @param string $label         content of the link
 * @param array  $attributes    HTML attributes of the link
 * @return string               HTML code of the link
 */
function link_to_cas_login ($label, $attributes = array())
{
  return link_to ($label, url_for_cas_login (), $attributes);
}

/**
 * Return the absolute URL of the configured CAS server
 *
 * @param $url      Return url after successful login
 * @return string   absolute url
 */
function url_for_cas_login ($url = null)
{
  if ($url === null)
  {
    $url = sfContext::getInstance()->getController()
      ->genUrl('authentication/casLogin', true).'?redirect='.urlencode (sfContext::getInstance()->getRequest()->getUri());
  }

  $casUrl[] = 'https://'.sfConfig::get ('app_cas_server_host', 'localhost').':'.sfConfig::get ('app_cas_server_port', 443);

  if (strlen (sfConfig::get ('app_cas_server_path', '')) > 0)
    $casUrl[] = trim (sfConfig::get ('app_cas_server_path', ''), '/');

  $casUrl[] = 'login?service='.urlencode ($url);

  return implode ('/', $casUrl);
}

/**
 * Detect CAS session, and force CAS login if possible.
 * Require activation of the uapvAuthCas module
 */
function detect_cas_session ()
{
  $iframeUrl = url_for ('uapvAuthCAS/detect');
  $casUrl = json_encode (url_for_cas_login ());

  return <<<HTML
<script type="text/javascript">function uapvAuthCASSessionDetected () { document.location.href = $casUrl ;}</script>
<iframe src="$iframeUrl" width="1px" height="1px" style="visibility: hidden;"></iframe>
HTML
    ;
}