<?php
namespace Avro\GdataBundle\Util;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Pierrre\EncrypterBundle\Util\EncrypterManager;

/*
 * Zend Gdata authentication helper class
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class Authenticator
{
    protected $encrypter;

    public function __construct(EncrypterManager $encrypter)
    {
        $this->encrypter = $encrypter->get('avro_encrypter');
    }

    /*
     * Get an authenticated HTTP client
     *
     * @param $username
     * @param $password
     * @param $decrypt
     *
     * @return Zend_Gdata_HttpClient
     */
    public function getClient($username, $password, $decrypt = true) {
        if (!$username || !$password) { 
            throw new \Exception('Invalid username\password');
        }

        if ($decrypt) {
            $password = $this->encrypter->decrypt($password);
        }
    
        try {
            $client = \Zend_Gdata_ClientLogin::getHttpClient($username, $password, \Zend_Gdata_Calendar::AUTH_SERVICE_NAME);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $client; 
    }

    /*
     * Get Calendar Service 
     *
     * @param $username
     * @param $password
     *
     * @return Calendar Service
     */
    public function getCalendarService($username, $password) {
        $client = $this->getClient($username, $password);

        return new \Zend_Gdata_Calendar($client);
    }
}
