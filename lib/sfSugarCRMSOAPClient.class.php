<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2007 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2007 Nick Winfield <enquiries@superhaggis.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfSugarCRMSOAPClient allows multiple SoapClient objects
 * to be reused.
 *
 * @package    symfony.plugins
 * @subpackage sfSugarCRMSOAPClient
 * @author     Nick Winfield <enquiries@superhaggis.com>
 * @version    SVN: $Id$
 */

class sfSugarCRMSOAPClient
{
  private static 
    /**
     * Array of singleton instances of PHP5's SoapClient class.
     * @access private
     * @var array
     */
    $instances = array();

  /**
   * Singleton method that returns single static instance
   * of PHP5's SoapClient class from an instances array.
   * This allows multiple WSDL documents to be loaded 
   * and reused.
   *
   * @access public
   * @param string $wsdl The absolute WSDL document URI.
   * @return SoapClient
   */
  public static function getInstance($wsdl)
  {
    if (!array_key_exists($wsdl, self::$instances))
    {
      try
      {
        self::$instances[$wsdl] = new SoapClient($wsdl);
      }
      catch (SoapFault $exception)
      {
        throw $exception;
      }
    }

    return self::$instances[$wsdl];
  }
}
