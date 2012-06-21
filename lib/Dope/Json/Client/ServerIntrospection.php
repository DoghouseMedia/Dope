<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Json
 * @subpackage Client
 * @author     Lars Kneschke <l.kneschke@metaways.de>
 * @copyright  Copyright (c) 2009 Metaways Infosystems GmbH (http://www.metaways.de)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Wraps the JSON-RPC SMD introspection methods
 *
 * @category   Zend
 * @package    Zend_Json
 * @subpackage Client
 * @author     Lars Kneschke <l.kneschke@metaways.de>
 * @copyright  Copyright (c) 2009 Metaways Infosystems GmbH (http://www.metaways.de)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Dope\Json\Client;

class ServerIntrospection
{
    /**
     * @var \Dope\Json\Client
     */
    private $_client = null;

    /**
     * @var \Dope\Json\Client\SMD
     */
    private $_smd = null;
    
    /**
     * @param \Dope\Json\Client $client
     */
    public function __construct(\Dope\Json\Client $client)
    {
        $this->_client = $client;
    }

    /**
     * Call system.methodSignature() for the given method
     *
     * @param  array  $method
     * @return array  array(array(return, param, param, param...))
     */
    public function getMethodSignature($method)
    {
        if($this->_smd === null) {
            $this->fetchSMD();
        }
        $signature = $this->_smd->getMethodSignature($method);
        
        return $signature;
    }

    /**
     * Call system.listMethods()
     *
     * @param  array  $method
     * @return array  array(method, method, method...)
     */
    public function fetchSMD()
    {
        $request = new \Zend_Json_Server_Request();
        $request->setVersion('2.0');
        $request->setId(1);
        
        $this->_smd = new SMD();
        $this->_client->doRequest($request, $this->_smd);
    }
}