<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;
use App\Models\GameAvailability;
use App\Models\Reservation;
use App\Models\Game;
use App\Notifications\GeneralNotification;
use App\Helpers\Fcm;
use App\Mail\GeneralMail;
use Mail;
use Validator;
use Notification;
use DB;

class AjaxController extends FrontController {

    public function __construct() {
        parent::__construct();
        $this->middleware('auth', ['only' => ['reserve_submit']]);
    }

    private $reserve_rules = array(
        'name' => 'required',
        'email' => 'required|email',
        'phone' => 'required',
        'reservation_date' => 'required',
        'reservation_time' => 'required',
    );

    public function search(Request $request) {
        //dd($request->all());
        $city_id = $request->input('city');
        $area_id = $request->input('region');
        $long = 7 * 60 * 24;
        return response()->json([
                    'type' => 'success',
                    'message' => _url('resturantes')
                ])->cookie('city_id', $city_id, $long)->cookie('area_id', $area_id, $long);
    }

    public function checkAvailability(Request $request) {
        $date = $request->input('date');
        $game_id = $request->input('game_id');
        //dd($game_id);
        $game = Game::find(base64_decode($request->input('game_id')));
        if (!$game) {
            $message = _lang('app.error_is_occured');
            if ($request->ajax()) {
                return _json('error', $message);
            } else {
                return redirect()->back()->withInput($request->all())->with(['errorMessage' => $message]);
            }
        }
        $times = $this->getAvailableTimes($date, $game->id);
        //dd($times);
        if ($times->count() > 0) {
            return _json('success', $times);
        } else {
            return _json('error', _lang('app.no_available_times'));
        }
    }

    public function reserve_submit(Request $request) {
        //dd($request->all());
        $validator = Validator::make($request->all(), $this->reserve_rules);
        if ($validator->fails()) {
            $this->errors = $validator->errors()->toArray();
            if ($request->ajax()) {
                return _json('error', $this->errors);
            } else {
                return redirect()->back()->withInput($request->all())->withErrors($this->errors);
            }
        }
        DB::beginTransaction();
        try {
            $game = Game::find(base64_decode($request->input('game_id')));
            if (!$game) {
                $message = _lang('app.error_is_occured');
                if ($request->ajax()) {
                    return _json('error', $message);
                } else {
                    return redirect()->back()->withInput($request->all())->with(['errorMessage' => $message]);
                }
            }
            $reservation_date = $request->input('reservation_date');
            $reservation_time_from = $request->input('reservation_time');

            $times = $this->getAvailableTimes($reservation_date, $game->id);
            $reservation_time_index = $this->getAvailableTime($times, $reservation_time_from);

            if ($reservation_time_index || $reservation_time_index == 0) {
                $overtime = (bool) $request->input('has_over');
                //dd($overtime);
                $times = $this->handleReservationInTimes($times, $reservation_time_index, $overtime);
                $times = $times->toArray();
                $Reservation = new Reservation;
                $Reservation->name = $request->input('name');
                $Reservation->email = $request->input('email');
                $Reservation->phone = $request->input('phone');
                if ($request->input('lat') && $request->input('lng')) {
                    $Reservation->lat = $request->input('lat');
                    $Reservation->lng = $request->input('lng');
                }

                $Reservation->reservation_date = $request->input('reservation_date');
                $Reservation->price = $game->price;
                $Reservation->overtime_price = $overtime ? $game->over_price : 0;

                $Reservation->reservation_time_from = $times[$reservation_time_index]->from;
                $Reservation->reservation_time_to = $times[$reservation_time_index]->to;
                $Reservation->user_id = $this->User->id;
                $Reservation->game_id = $game->id;
                $Reservation->date = date('Y-m-d');
                $Reservation->save();
                unset($times[$reservation_time_index]);

                GameAvailability::updateOrCreate(
                        ['game_id' => $game->id, 'date' => $request->input('reservation_date')], ['game_id' => $game->id, 'date' => $request->input('reservation_date'), 'times' => json_encode($times)]);

                DB::commit();
                $message = _lang('app.request_sent_successfully');
                if ($request->ajax()) {
                    return _json('success', $message);
                } else {
                    return redirect()->back()->withInput($request->all())->with(['succeessMessage' => $message]);
                }
            } else {
                $message = _lang('app.no_available_time');
                if ($request->ajax()) {
                    return _json('error', $message);
                } else {
                    return redirect()->back()->withInput($request->all())->with(['errorMessage' => $message]);
                }
            }
        } catch (\Exception $ex) {
            DB::rollback();
            dd($ex->getMessage() . $ex->getLine());
            $message = _lang('app.error_is_occured');
            if ($request->ajax()) {
                return _json('error', $message);
            } else {
                return redirect()->back()->withInput($request->all())->with(['errorMessage' => $message]);
            }
        }
    }

    private function getAvailableTimes($date, $game_id) {
        $date_found = GameAvailability::where('date', $date)->where('game_id', 3)->first();
        $times = array();
        $work_from=$this->_settings["work_from"]->value;
        $work_to=$this->_settings["work_to"]->value;
        if (!$date_found) {
            $start = "$date $work_from";
            $end = "$date $work_to";
            $count = 0;
            while ($start < $end) {
                $obj = new \stdClass();
                $obj->from = date('H:i a', strtotime($start));
                $obj->to = date('H:i a', strtotime('+3 hour', strtotime($start)));
                //dd($obj->to);
                $times[$count] = $obj;
//                $times[$count]['from'] = date('H:i a', strtotime($start));
//                $times[$count]['to'] = date('H:i a', strtotime('+3 hour', strtotime($start)));
                $start = date('Y-m-d H:i', strtotime('+3 hour', strtotime($start)));

                $count++;
            }
        } else {
            $times = json_decode($date_found->times);
        }

        return collect($times);
    }

    private function handleReservationInTimes($times, $reservation_time_index, $overtime = false) {

        if ($overtime) {
            $count = $times->count() - 1;
            foreach ($times as $key => $time) {

                if ($key >= $reservation_time_index) {
                    if ($key > $reservation_time_index) {
                        $time->from = date('H:i a', strtotime('+1 hour', strtotime($time->from)));
                    }

                    $time->to = date('H:i a', strtotime('+1 hour', strtotime($time->to)));
                }
            }
        }

        return $times;
    }

    private function getAvailableTime($times, $reservation_time_from) {
        //dd($times);
        if ($times->count() > 0) {
            foreach ($times as $index => $time) {
                if ($time->from == $reservation_time_from) {
                    return $index;
                }
            }
        }

        return FALSE;
    }

}
