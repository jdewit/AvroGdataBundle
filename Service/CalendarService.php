<?php
namespace Avro\GdataBundle\Service;

use Symfony\Component\Security\Core\SecurityContextInterface;

class CalendarService
{
    protected $encrypter;

    public function __construct($encrypter)
    {
        $this->encrypter = $encrypter->get('avro_encrypter');
    }

    public function getService($user) {
        $username = $user->getGmailUser();
        $password = $user->getGmailPassword();

        if (!$username || !$password) { 
            return false;
        }
        $password = $this->encrypter->decrypt($user->getGmailPassword());
    
        if (!$username || !$password) {
            throw new \Exception('Unable to add event. Gmail settings have not been set.');
        }

        $service = \Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
         
        // Create an authenticated HTTP client
        $client = \Zend_Gdata_ClientLogin::getHttpClient($username, $password, $service);
         
        // Create an instance of the Calendar service
        return new \Zend_Gdata_Calendar($client);
    }

    public function testCredentials($user) {

    }

    public function getCalendarListFeed() {
        try {
            $listFeed= $service->getCalendarListFeed();
        } catch (Zend_Gdata_App_Exception $e) {
            echo "Error: " . $e->getMessage();
        }

        return $listFeed;
    }

    public function createEvent($user, array $options) {
        $service = $this->getService($user);

        if (!$service) {
            return false;
        }

        $event= $service->newEventEntry();
         
        $options['timezoneOffset'] = $user->getOwner()->getTimezoneOffset();
        $event = $this->writeEvent($service, $event, $options);
         
        try {
            $event = $service->insertEvent($event);
        } catch (Zend_Gdata_App_Exception $e) {
            throw new \Exception("Error: " . $e->getMessage());
        }
        $url =  $event->id->text;
        $id = explode("/", $url);
        
        return array_pop($id);
    }

    public function editEvent($user, $options) {
        $service = $this->getService($user);

        if (!$service) {
            return false;
        }

        $event = $this->getEvent($service, $options['id']);

        $options['timezoneOffset'] = $user->getOwner()->getTimezoneOffset();

        $event = $this->writeEvent($service, $event, $options);

        try {
            $event->save();
        } catch (Zend_Gdata_App_Exception $e) {
            throw new \Exception("Error: " . $e->getMessage());
        }
    }

    public function getEvent($service, $id) {
        $query = $service->newEventQuery();
        $query->setUser('default');
        $query->setVisibility('private');
        $query->setProjection('full');
        $query->setEvent($id);
         
        try {
            $event = $service->getCalendarEventEntry($query);
        } catch (Zend_Gdata_App_Exception $e) {
            throw new \Exception("Error: " . $e->getMessage());
        }        
        
        return $event;
    }
        
    public function writeEvent($service, $event, $options) {
        $event->title = $service->newTitle($options['title']);
        $event->where = array($service->newWhere($options['where']));
        $event->content = $service->newContent($options['content']);
        // Set the date using RFC 3339 format.
        $startDate = $options['startDate'];
        $startTime = $this->convertTime($options['startTime']);
        $endDate = $options['endDate'];
        $endTime = $this->convertTime($options['endTime']);
        $tzOffset = $options['timezoneOffset'];
        $when = $service->newWhen();
        $when->startTime = "{$startDate}T{$startTime}:00.000{$tzOffset}:00";
        $when->endTime = "{$endDate}T{$endTime}:00.000{$tzOffset}:00";
        $event->when = array($when);

        return $event;
    }

    public function deleteEvent($user, $id) {
        $service = $this->getService($user);
        $this->getEvent($service, $id)->delete();
    }

    /*
     * Convert time to 24hr if necessary
     */
    public function convertTime($time) {
        if (substr($time, 7, 7) == 'm') {
            $time = DATE("H:i", STRTOTIME($time));
        } 

        return $time;
    }
}
