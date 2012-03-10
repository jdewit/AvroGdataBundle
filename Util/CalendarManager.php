<?php
namespace Avro\GdataBundle\Util;

use Symfony\Component\Security\Core\SecurityContextInterface;

/*
 * Helper class for Zend Gdata Calendar
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class CalendarManager
{
    /*
     * Get calendar list feed
     *
     * @param Calendar Service Instance $service
     *
     * @return array $listFeed 
     */
    public function getCalendarListFeed($service) {
        try {
            $listFeed= $service->getCalendarListFeed();
        } catch (\Zend_Gdata_App_Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $listFeed;
    }

    /*
     * Create an event
     *
     * @param Calendar Service Instance $service
     * @param array $options
     *
     * @return string $id the calendars id 
     */
    public function createEvent($service, array $options) {
        $event= $service->newEventEntry();
        $event = $this->writeEvent($service, $event, $options);
         
        try {
            $event = $service->insertEvent($event);
        } catch (\Zend_Gdata_App_Exception $e) {
            throw new \Exception($e->getMessage());
        }
        $url =  $event->id->text;
        $id = explode("/", $url);
        
        return array_pop($id);
    }

    /*
     * Edit an event
     *
     * @param Calendar Service Instance $service
     * @param array $options
     *
     * @return boolean true
     */
    public function editEvent($service, $options) {
        $event = $this->getEvent($service, $options['id']);
        $event = $this->writeEvent($service, $event, $options);

        try {
            $event->save();
        } catch (\Zend_Gdata_App_Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return true;
    }

    /*
     * Get an event
     *
     * @param Calendar Service Instance $service
     * @param array $options
     *
     * @return $event
     */
    public function getEvent($service, $id) {
        $query = $service->newEventQuery();
        $query->setUser('default');
        $query->setVisibility('private');
        $query->setProjection('full');
        $query->setEvent($id);
         
        try {
            $event = $service->getCalendarEventEntry($query);
        } catch (\Zend_Gdata_App_Exception $e) {
            throw new \Exception($e->getMessage());
        }        
        
        return $event;
    }
        
    /*
     * Write an event
     *
     * @param Calendar Service $service
     * @param Event $event
     * @param array $options
     *
     * @return $event
     */
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

    /*
     * Delete an event
     *
     * @param Calendar Service $service
     * @param string $id
     *
     * @return boolean true
     */
    public function deleteEvent($service, $id) {
        try {
            $this->getEvent($service, $id)->delete();
        } catch (\Zend_Gdata_App_Exception $e) {
            throw new \Exception($e->getMessage());
        }     

        return true;
    }

    /*
     * Convert time to 24hr if necessary
     *
     * @param string $time
     *
     * return string $time
     */
    public function convertTime($time) {
        if (substr($time, 7, 7) == 'm') {
            $time = DATE("H:i", STRTOTIME($time));
        } 

        return $time;
    }
}
