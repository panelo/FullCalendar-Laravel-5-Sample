<?php namespace App\Http\Controllers;

use Admin;
use AdminAuth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client; 
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;

class CalendarController extends Controller {

    public function index() { //code should be inside a method
    	
    	//if using guzzle - Restful API
    	$client = new Client(); 
		$response = $client->get('http://api.enviefitness.com/v1/events');
		$body = json_decode($response->getBody(), TRUE);
		
		foreach ($body['events'] as $e) {
			$events[] = \Calendar::event(
	            $e['activityType'].': '.$e['title'] , //event title
	            false, //full day event?
	            $e['start'], //start time (you can also use Carbon instead of DateTime)
	            $e['end'], //end time (you can also use Carbon instead of DateTime)
	            $e['id'] //optionally, you can specify an event ID
	        );
		}
		
		//if using model
        $events[] = \Calendar::event(
            'Test', //event title
            false, //full day event?
            '2015-07-07T1030', //start time (you can also use Carbon instead of DateTime)
            '2015-07-07T1200', //end time (you can also use Carbon instead of DateTime)
            0, //optionally, you can specify an event ID
            'This is a test Event' //description
        );
		
		$events[] = \Calendar::event(
            'Sample', //event title
            false, //full day event?
            '2015-07-07T1200', //start time (you can also use Carbon instead of DateTime)
            '2015-07-07T1230', //end time (you can also use Carbon instead of DateTime)
            0, //optionally, you can specify an event ID
            'This is a sample event' //description
        );
		
        $calendar = \Calendar::addEvents($events) //add an array with addEvents
            ->setOptions([ //set fullcalendar options
                'firstDay' => 7,
                'editable' => true,
                'eventLimit' => true,
                'header' => array('left' => 'prev,next today', 'center' => 'title', 'right' => 'month,basicWeek,basicDay')
            ])->setCallbacks([ //set fullcalendar callback options (will not be JSON encoded)
		        'eventClick' => 'function(calEvent, jsEvent, view) {
					$("#modalTitle").html(calEvent.title);
		            $("#modalBody").html(calEvent.description);
		            $("#eventUrl").attr("href",calEvent.linkurl);
		            $("#fullCalModal").modal();
				}'
		    ]);
		$content = view('calendar', compact('calendar'));
		return View::make($content);
    }
}