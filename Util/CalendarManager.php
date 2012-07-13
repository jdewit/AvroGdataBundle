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
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $listFeed;
    }

    /*
     * Create an event
     *
     * @param Calendar Service Instance $service
     * @param array $options
     * @param string $uri
     *
     * @return string $id the calendars id
     */
    public function createEvent($service, array $options, $id = null) {
        $event= $service->newEventEntry();
        $event = $this->writeEvent($service, $event, $options);

        if ($id != null) {
            $uri = 'https://www.google.com/calendar/feeds/'.$id.'%40group.calendar.google.com/private/full';
        } else {
            $uri = null;
        }

        try {
            $event = $service->insertEvent($event, $uri);
        } catch (\Exception $e) {
            throw new \Exception('Error creating event. Verify your google credentials or calendar id');
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
        try {
            $event = $this->getEvent($service, $options['id']);
            $event = $this->writeEvent($service, $event, $options);
            $event->save();
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
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
