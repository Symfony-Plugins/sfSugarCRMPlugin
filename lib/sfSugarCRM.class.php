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
 * Wrapper class for SugarCRM's WSDL implementation.
 * 
 * @package    symfony.plugins
 * @subpackage sfSugarCRM
 * @author     Nick Winfield <enquiries@superhaggis.com>
 * @version    SVN: $Id$
 */

class sfSugarCRM
{
  protected 
    /**
     * The remote WSDL document URI.
     * @access private
     * @var string
     */
    $wsdl = '',

    /**
     * The remote user's username.
     * @access private
     * @var string
     */
    $username = '',

    /**
     * The remote user's password.
     * @access private
     * @var string
     */
    $password = '',

    /**
     * The output from the last SOAP request.
     * @access private
     * @var string
     */
    $lastResponse = null;

  /**
   * Default constructor.  Sets up the remote WSDL
   * document and turns off WSDL document caching.
   *
   * Also checks to make sure that the SOAP extension
   * has been loaded successfully.
   *
   * Example usage:
   *
   *   $sugar = new sfSugarCRM('http://foo/crm/soap.php?wsdl');
   *   $sugar->setUsername('jbloggs');
   *   $sugar->setPassword('mypass123');
   *   $sugar->getUserlist();
   *   $response = $sugar->getResponse();
   *   ...
   *
   * @access public
   * @param string $wsdl The absolute URI of the WSDL document.
   * @throws sfException, if SOAP extension is not enabled.
   * @return void
   */
  public function __construct($wsdl)
  {
    if (!in_array('soap', get_loaded_extensions()))
    {
      throw new sfException("PHP's native SOAP extension is not enabled; make sure PHP has been compiled correctly.");
    }
  
    ini_set('soap.wsdl_cache_enabled', 0);
    $this->wsdl = $wsdl;
  }

  /**
   * Mutator for remote user's username.
   *
     @access public
   * @param string $username The user's username.
   * @return void
   */
  public function setUsername($username)
  {
    $this->username = $username;
  }

  /**
   * Mutator for remote user's password.  An MD5 hash
   * of the password is stored.
   *
   * @access public
   * @param string $password The user's plaintext password.
   * @return void
   */
  public function setPassword($password)
  {
    $this->password = md5($password);
  }

  /**
   * Accessor for last SOAP response.
   *
   * @access public
   * @return string The output from the SOAP request.
   */
  public function getResponse()
  {
    return $this->lastResponse;
  }

  /**
   * Flushes the SOAP response.
   *
   * @access public
   * @return void
   */
  public function flushResponse()
  {
    $this->lastResponse = null;
  }

  /**
   * Authenticates the remote user and sets up a session.
   * It seems that validate_user() is called on an adhoc basis
   * by other remote methods, so this method may be deprecated.
   *
   * @access public
   * @return void
   */
  public function createSession()
  {
    $client = sfSugarCRMSOAPClient::getInstance($this->wsdl);

    try
    {
      $response = $client->create_session(
        $this->username,
        $this->password
      );
    }
    catch (SoapFault $exception)
    {
      throw $exception;
    }

    $this->lastResponse = $response;
  }

  /**
   * Closes a remote user session.
   * This WSDL method hasn't been implemented properly,
   * as it return 'Success' regardless of state.
   *
   * @access public
   * @return void
   */
  public function endSession()
  {
    $client = sfSugarCRMSOAPClient::getInstance($this->wsdl);

    try
    {
      $response = $client->end_session(
        $this->username
      );
    }
    catch (SoapFault $exception)
    {
      throw $exception;
    }

    $this->lastResponse = $response;
  }

  /**
   * Search for contact(s) by name (first or last).
   *
   * @access public
   * @param string $name A firstname or lastname of a contact.
   * @return void
   */  
  public function searchContactsByName($name)
  {
    $client = sfSugarCRMSOAPClient::getInstance($this->wsdl);

    try
    {
      $response = $client->search(
        $this->username,
        $this->password,
        $name
      );
    }
    catch (SoapFault $exception)
    {
      throw $exception;
    }

    $this->lastResponse = $response;    
  }

  /**
   * Search for contact(s) by email address.
   *
   * @access public
   * @param string $email An email address.
   * @return void
   */
  public function searchContactsByEmail($email_address)
  {
    $client = sfSugarCRMSOAPClient::getInstance($this->wsdl);

    try
    {
      $response = $client->contact_by_email(
        $this->username,
        $this->password,
        $email_address
      );
    }
    catch (SoapFault $exception)
    {
      throw $exception;
    }

    $this->lastResponse = $response;
  }

  /**
   * Fetches a array of users.
   *
   * @access public
   * @return void
   */
  public function getUserlist()
  {
    $client = sfSugarCRMSOAPClient::getInstance($this->wsdl);

    try
    {
      $response = $client->user_list(
        $this->username,
        $this->password
      );
    }
    catch (SoapFault $exception)
    {
      throw $exception;
    }

    $this->lastResponse = $response;
  }

  /**
   * Creates a new lead.
   *
   * @access public
   * @param array $parameters Minimum credentials for a new lead.
   * @return void
   */
  public function createLead($parameters)
  {
    $client = sfSugarCRMSOAPClient::getInstance($this->wsdl);

    try
    {
      $response = $client->create_lead(
        $this->username,
        $this->password,
        $parameters['first_name'],
        $parameters['last_name'],
        $parameters['email_address']
      );
    }
    catch (SoapFault $exception)
    {
      throw $exception;
    }

    $this->lastResponse = $response;
  }

  /**
   * Creates a new contact.
   * This may or may not be an alias for create_lead().
   *
   * @access public
   * @param array $parameters Minimum credentials for a new contact.
   * @return void
   */
  public function createContact($parameters)
  {
    $client = sfSugarCRMSOAPClient::getInstance($this->wsdl);

    try
    {
      $response = $client->create_contact(
        $this->username,
        $this->password,
        $parameters['first_name'],
        $parameters['last_name'],
        $parameters['email_address']
      );
    }
    catch (SoapFault $exception)
    {
      throw $exception;
    }

    $this->lastResponse = $response;
  }

  /**
   * Creates a new account.
   *
   * @access public
   * @param array $parameters Minimum credentials for a new account.
   * @return void
   */
  public function createAccount($parameters)
  {
    $client = sfSugarCRMSOAPClient::getInstance($this->wsdl);

    try
    {
      $response = $client->create_account(
        $this->username,
        $this->password,
        $parameters['name'],
        $parameters['telephone'],
        $parameters['website']
      );
    }
    catch (SoapFault $exception)
    {
      throw $exception;
    }
     
    $this->lastResponse = $response;
  }

  /**
   * Creates a new opportunity.
   *
   * @access public
   * @param array $parameters Minimum credentials for a new opportunity.
   * @return void
   */
  public function createOpportunity($parameters)
  {
    $client = sfSugarCRMSOAPClient::getInstance($this->wsdl);

    try
    {
      $response = $client->create_opportunity(
        $this->username,
        $this->password,
        $parameters['name'],
        $parameters['amount']
      );
    }
    catch (SoapFault $exception)
    {
      throw $exception;
    }

    $this->lastResponse = $response;
  }

  /**
   * Creates a new case.
   *
   * @access public
   * @param array $parameters Minimum credentials for a new case.
   * @return void
   */
  public function createCase($parameters)
  {
    $client = sfSugarCRMSOAPClient::getInstance($this->wsdl);

    try
    {
      $response = $client->create_case(
        $this->username,
        $this->password,
        $parameters['name']
      );
    }
    catch (SoapFault $exception)
    {
      throw $exception;
    }   
  
    $this->lastResponse = $response;
  }
}
