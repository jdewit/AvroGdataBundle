<?php
namespace Avro\GdataBundle\Service;

use Symfony\Component\Security\Core\SecurityContextInterface;

class CalendarService
{
    protected $context;
    protected $service;

    public function __construct(SecurityContextInterface $context)
    {
        $this->context = $context;    

        $service = \Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
        
        $user = "jorisdewitblackberry@gmail.com";
        $pass = "sf2champ*";
         
        // Create an authenticated HTTP client
        $client = \Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
         
        // Create an instance of the Calendar service
        $this->service = new \Zend_Gdata_Calendar($client);

    }

    public function getCalendarListFeed() {
        try {
            $listFeed= $this->service->getCalendarListFeed();
        } catch (Zend_Gdata_App_Exception $e) {
            echo "Error: " . $e->getMessage();
        }

        return $listFeed;
    }

    public function createEvent(array $options) {
        $event= $this->service->newEventEntry();
         
        // Populate the event with the desired information
        // Note that each attribute is crated as an instance of a matching class
        $event->title = $this->service->newTitle("My Event");
        $event->where = array($this->service->newWhere("Mountain View, California"));
        $event->content =
            $this->service->newContent(" This is my awesome event. RSVP required.");
         
        // Set the date using RFC 3339 format.
        $startDate = "2008-01-20";
        $startTime = "14:00";
        $endDate = "2008-01-20";
        $endTime = "16:00";
        $tzOffset = "-08";
         
        $when = $this->service->newWhen();
        $when->startTime = "{$startDate}T{$startTime}:00.000{$tzOffset}:00";
        $when->endTime = "{$endDate}T{$endTime}:00.000{$tzOffset}:00";
        $event->when = array($when);
         
        // Upload the event to the calendar server
        // A copy of the event as it is recorded on the server is returned
        $newEvent = $this->service->insertEvent($event);
    }

    public function quickAdd(array $options) {
        // Create a new entry using the calendar service's magic factory method
        $event= $this->service->newEventEntry();
         
        // Populate the event with the desired information
        $event->content= $this->service->newContent("Dinner at Joe's Diner on Thursday");
        $event->quickAdd = $this->service->newQuickAdd("true");
         
        // Upload the event to the calendar server
        // A copy of the event as it is recorded on the server is returned
        $newEvent = $this->service->insertEvent($event);
    }

    public function editEvent($event) {
        // Get the first event in the user's event list
        $event = $eventFeed[0];
         
        // Change the title to a new value
        $event->title = $service->newTitle("Woof!");
         
        // Upload the changes to the server
        try {
            $event->save();
        } catch (Zend_Gdata_App_Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function deleteEvent($event) {
        $event->delete();
    }
}
