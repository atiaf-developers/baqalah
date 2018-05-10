<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AUTHORIZATION;
use App\Models\User;
use App\Models\Device;
use App\Models\DonationRequest;
use App\Events\Noti;
use DB;
use App\Helpers\Fcm;

class DonationRequestsController extends ApiController {

    private $step_one_rules = array(
        'images' => 'required',
        'description' => 'required',
        'appropriate_time' => 'required',
        'lat' => 'required',
        'lng' => 'required',
        'donation_type' => 'required',
        'device_id' => 'required',
        'device_token' => 'required',
        'device_type' => 'required',
    );
    private $step_two_rules = array(
        'name' => 'required',
        'mobile' => 'required',
    );
    private $step_three_rules = array(
        'name' => 'required',
        'mobile' => 'required|unique:users',
        'images' => 'required',
        'description' => 'required',
        'appropriate_time' => 'required',
        'lat' => 'required',
        'lng' => 'required',
        'donation_type' => 'required',
        'device_id' => 'required',
        'device_token' => 'required',
        'device_type' => 'required',
    );
    private $donation_requests_rules = array(
        'type' => 'required',
        'lat' => 'required',
        'lng' => 'required'
    );
    private $status_rules = array(
        'request_id' => 'required',
        'status' => 'required'
    );

    public function __construct() {
        parent::__construct();
    }

    public function index(Request $request) {
        try {

            $validator = Validator::make($request->all(), $this->donation_requests_rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json(new \stdClass(), ['errors' => $errors], 400);
            }

            $user = $this->auth_user();
            $lat = $request->input('lat');
            $lng = $request->input('lng');
            $type = $request->input('type');

            $donation_requests = DonationRequest::join('donation_types', 'donation_requests.donation_type_id', '=', 'donation_types.id');
            $donation_requests->join('donation_types_translations', 'donation_types_translations.donation_type_id', '=', 'donation_types.id');
            $donation_requests->where('donation_types_translations.locale', $this->lang_code);
            $donation_requests->where('donation_requests.delegate_id', $user->id);
            if ($type == 1) {

                $donation_requests->whereIn('status', [1, 2, 3]);
                $donation_requests->select('donation_requests.id', 'donation_requests.status', 'donation_requests.appropriate_time', 'donation_requests.description', 'donation_requests.images', 'donation_requests.lat', 'donation_requests.lng', 'donation_requests.name', 'donation_requests.mobile', 'donation_types_translations.title as donation_type', DB::raw($this->iniDiffLocations('donation_requests', $lat, $lng))
                );
                //$donation_requests->having('distance','<=',$distance);
                $donation_requests->orderBy('distance');
            } else if ($type == 2) {
                $donation_requests->where('status', 4);
                $donation_requests->select('donation_requests.id', 'donation_requests.status', 'donation_requests.appropriate_time', 'donation_requests.description', 'donation_requests.images', 'donation_requests.name', 'donation_types_translations.title as donation_type');
            }
            $donation_requests = $donation_requests->paginate($this->limit);

            return _api_json(DonationRequest::transformCollection($donation_requests));
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message], 400);
        }
    }

    //for the client
    public function store(Request $request) {

        if ($request->step == 1) {
            $rules = $this->step_one_rules;
        } else if ($request->step == 2) {
            $rules = $this->step_two_rules;
        } else if ($request->step == 3) {
            $rules = $this->step_three_rules;
        } else {
            unset($this->step_one_rules['device_id'], $this->step_one_rules['device_token'], $this->step_one_rules['device_type']);
            $rules = $this->step_one_rules;
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json(new \stdClass(), ['errors' => $errors], 400);
        } else {
            if ($request->step == 1) {
                return _api_json(new \stdClass());
            } else if ($request->step == 2) {
                $verification_code = Random(4);
                return _api_json(new \stdClass(), ['code' => $verification_code]);
            } else {
                DB::beginTransaction();
                try {
                    $this->create_donation_request($request);
                    DB::commit();
                    $message = _lang('app.request_has_been_sent_successfully');
                    return _api_json('', ['message' => $message], 201);
                } catch (\Exception $e) {
                    DB::rollback();
                    $message = _lang('app.error_is_occured');
                    return _api_json('', ['message' => $message], 400);
                }
            }
        }
    }

    public function store2(Request $request) {
        if ($request->step) {
            if ($request->step == 1) {
                $validator = Validator::make($request->all(), $this->step_one_rules);
                if ($validator->fails()) {
                    $errors = $validator->errors()->toArray();
                    return _api_json(new \stdClass(), ['errors' => $errors], 400);
                }
                return _api_json('');
            } else if ($request->step == 2) {

                $validator = Validator::make($request->all(), $this->step_two_rules);
                if ($validator->fails()) {
                    $errors = $validator->errors()->toArray();
                    return _api_json(new \stdClass(), ['errors' => $errors], 400);
                }
                $verification_code = Random(4);
                return _api_json('', ['code' => $verification_code]);
            } else if ($request->step == 3) {
                $validator = Validator::make($request->all(), $this->step_three_rules);
                if ($validator->fails()) {
                    $errors = $validator->errors()->toArray();
                    return _api_json('', ['errors' => $errors], 400);
                }
                DB::beginTransaction();
                try {

                    $this->create_donation_request($request);
                    DB::commit();
                    $message = _lang('app.request_has_been_sent_successfully');
                    return _api_json('', ['message' => $message], 201);
                } catch (\Exception $e) {
                    DB::rollback();
                    $message = _lang('app.error_is_occured');
                    return _api_json('', ['message' => $message], 400);
                }
            } else {
                return _api_json('', ['message' => _lang('app.error_is_occured')], 400);
            }
        } else {
            unset($this->step_one_rules['device_id'], $this->step_one_rules['device_token'], $this->step_one_rules['device_type']);
            $validator = Validator::make($request->all(), $this->step_one_rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json(new \stdClass(), ['errors' => $errors], 400);
            }

            DB::beginTransaction();
            try {
                $this->create_donation_request($request);
                DB::commit();
                $message = _lang('app.request_has_been_sent_successfully');
                return _api_json('', ['message' => $message], 201);
            } catch (\Exception $e) {
                DB::rollback();
                $message = _lang('app.error_is_occured');
                return _api_json('', ['message' => $message], 400);
            }
        }
    }

    public function status(Request $request) {
        try {

            $validator = Validator::make($request->all(), $this->status_rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json(new \stdClass(), ['errors' => $errors], 400);
            }

            $donation_request = DonationRequest::join('devices', 'devices.id', '=', 'donation_requests.device_id')
                    ->leftJoin('users', 'users.device_id', '=', 'devices.id')
                    ->where('donation_requests.id', $request->input('request_id'))
                    ->select('donation_requests.*', 'devices.device_token', 'devices.device_type', 'users.id as user_id')
                    ->first();

            if (!$donation_request) {
                $message = _lang('app.not_found');
                return _api_json('', ['message' => $message], 404);
            }
            if (in_array($request->status, [2, 3, 4])) {
                $message = DonationRequest::$status_text[$request->status]['client'];
            } else {
                $message = _lang('app.not_found');
                return _api_json('', ['message' => $message], 404);
            }
            $donation_request->status = $request->input('status');
            $donation_request->save();

            if ($donation_request->user_id) {
                $notifier_id = $donation_request->user_id;
                $notifible_type = 1;
                $this->create_noti($request->input('request_id'), $notifier_id, $request->input('status'), $notifible_type);
                //dd('here');
                event(new Noti(['user_id' => $notifier_id, 'type' => 1, 'body' => $message['message_' . $this->lang_code], 'url' => null]));
            } else {
                $notifier_id = $donation_request->device_id;
                $notifible_type = 3;
            }

            $fcm = new Fcm();
            $notification = ['title' => 'Keswa', 'body' => $message, 'type' => 1];
            if ($donation_request->device_type == 1) {
                $fcm->send($donation_request->device_token, $notification, 'and');
            } else {
                $fcm->send($donation_request->device_token, $notification, 'ios');
            }



            return _api_json('', ['message' => _lang('app.updated_successfully')]);
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message], 400);
        }
    }

    private function create_donation_request($request) {

        $donation_request = new DonationRequest;

        $donation_request->description = $request->input('description');
        $donation_request->appropriate_time = $request->input('appropriate_time');
        $donation_request->lat = $request->input('lat');
        $donation_request->lng = $request->input('lng');
        $donation_request->donation_type_id = $request->input('donation_type');
        $donation_images = json_decode($request->images);
        $images = [];
        foreach ($donation_images as $image) {
            $image = preg_replace("/\r|\n/", "", $image);
            if (!isBase64image($image)) {
                continue;
            }
            $images[] = DonationRequest::upload($image, 'donation_requests', true, false, true);
        }
        $donation_request->images = json_encode($images);
        if ($this->auth_user()) {
            $donation_request->name = $this->auth_user()->name;
            $donation_request->mobile = $this->auth_user()->mobile;
            $donation_request->device_id = $this->auth_user()->device_id;
        } else {
            $donation_request->name = $request->input('name');
            $donation_request->mobile = $request->input('mobile');
            $device = Device::updateOrCreate(
                            ['device_id' => $request->input('device_id')], ['device_token' => $request->input('device_token'), 'device_type' => $request->input('device_type')]
            );
            $donation_request->device_id = $device->id;
        }
        $donation_request->save();
    }

}
