<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BackendController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Pagination\LengthAwarePaginator;
use App;
use Auth;
use DB;
use Redirect;
use Validator;
use App\Models\User;
use App\Models\Device;
use App\Models\DonationRequest;
use PDF;
use App\Helpers\Fcm;

class DonationRequestsController extends BackendController {

    private $limit = 10;
    private $assign_rules = array(
        'delegate' => 'required'
    );

    public function __construct() {


        parent::__construct();
        $this->middleware('CheckPermission:donation_requests,open', ['only' => ['index']]);
    }

    public function index(Request $request) {
        //$pdf = PDF::loadView('pdf.document', []);
        // dd($pdf);
        if ($request->all()) {
            foreach ($request->all() as $key => $value) {

                if ($value) {
                    $this->data[$key] = $value;
                }
            }
        }
        $this->data['donation_requests'] = $this->getDonationRequests($request);
        $this->data['info'] = $this->getInfo($request);
        $this->data['delegates'] = $this->getDelagates();
        $this->data['status_filter'] = DonationRequest::$status_filter;
        $this->data['status_arr'] = DonationRequest::$status_text;
        //dd($this->data['status_arr'][0]['admin']['message_'.$this->lang_code]);
        return $this->_view('donation_requests.index', 'backend');
    }

    public function show(Request $request, $id) {
        $donation_request = $this->getDonationRequests($request, $id);
        if (!$donation_request) {
            return $this->err404();
        }
        $donation_request->images = preg_filter('/^/', url('public/uploads/donation_requests') . '/', json_decode($donation_request->images));
        //dd($donation_request->images);
        $this->data['delegates'] = $this->getDelagates();
        $this->data['donation_request'] = $donation_request;
        $this->data['status_arr'] = DonationRequest::$status_text;
        return $this->_view('donation_requests.view', 'backend');
    }

    public function assigned(Request $request) {
        $validator = Validator::make($request->all(), $this->assign_rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        } else {
            $order_id = $request->input('order_id');
            $delegate = $request->input('delegate');
            $DonationRequest = DonationRequest::find($order_id);
            if ($DonationRequest == null) {
                return _json('error', _lang('app.error_is_occured'));
            }
            DB::beginTransaction();
            try {
                $DonationRequest->delegate_id = $delegate;
                $DonationRequest->status = 1;
                $DonationRequest->save();
                $device = User::join('devices','devices.id','=','users.device_id')
                               ->where('users.id',$DonationRequest->delegate_id)
                               ->select('devices.device_type','devices.device_token')
                               ->first();
                if ($device) {
                    $message['message_ar'] = 'تم اسناد طلب تبرع جديد اليك';
                    $message['message_en'] = 'new request for a donation has been assigned to you';
                    $notification = array('title' => _lang('app.keswa'), 'body' => $message, 'type' => 1);
                    $device_type = $device->device_type == 1 ? 'and' : 'ios';
                    $Fcm = new Fcm;
                    //dd($device_type);
                    $Fcm->send($device->device_token, $notification, $device_type);
                    
                }

                DB::commit();
                return _json('success', url('admin/donation_requests'));
            } catch (\Exception $ex) {
                DB::rollback();
                return _json('error', $ex->getMessage());
            }
        }
    }

    private function getDonationRequests($request, $id = false) {


        $donation_requests = DonationRequest::join('donation_types', 'donation_types.id', '=', 'donation_requests.donation_type_id');
        $donation_requests->join('donation_types_translations as trans', 'donation_types.id', '=', 'trans.donation_type_id');
        $donation_requests->leftJoin('users', 'users.id', '=', 'donation_requests.delegate_id');
        $donation_requests->select([
            'donation_requests.id', "users.username", "trans.title as donation_title", "donation_requests.appropriate_time",
            "donation_requests.status", "donation_requests.created_at", "donation_requests.date", "donation_requests.lat", "donation_requests.lng",
            "donation_requests.name", "donation_requests.mobile", "donation_requests.images", "donation_requests.description"
        ]);
        $donation_requests->where('trans.locale', $this->lang_code);
        if (!$id) {
            $donation_requests = $this->handleWhere($donation_requests, $request);
            $donation_requests->orderBy('donation_requests.created_at', 'DESC');
            return $donation_requests->paginate($this->limit)->appends($request->all());
        } else {
            $donation_requests->where("donation_requests.id", $id);
            return $donation_requests->first();
        }

        //$bills->orderBy('bills.creatsed_at','DESC');
    }

    private function getInfo($request) {
        $donation_requests = DonationRequest::join('donation_types', 'donation_types.id', '=', 'donation_requests.donation_type_id');
        $donation_requests->join('donation_types_translations as trans', 'donation_types.id', '=', 'trans.donation_type_id');
        $donation_requests->leftJoin('users', 'users.id', '=', 'donation_requests.delegate_id');

        $donation_requests = $this->handleWhere($donation_requests, $request);
        $donation_requests->select(DB::RAW("COUNT(IF( status = 5, status, NULL)) AS completed"), DB::RAW("COUNT(IF( status != 5, status, NULL)) AS not_completed"));

        return $donation_requests->first();
    }

    private function getDelagates() {
        $Users = User::select('id', "username")->where('type', 2)->get();
        return $Users;
    }

    private function handleWhere($donation_requests, $request) {

        if ($request->all()) {
            if ($from = $request->input('from')) {
                $donation_requests->where("donation_requests.date", ">=", "$from");
            }
            if ($to = $request->input('to')) {
                $donation_requests->where("donation_requests.date", "<=", "$to");
            }
            if ($delegate = $request->input('delegate')) {
                $donation_requests->where("users.id", $delegate);
            }
            if ($order = $request->input('order')) {
                $donation_requests->where("donation_requests.id", $order);
            }
            if ($status = $request->input('status')) {
                $status = array_search($status, DonationRequest::$status_text);
                //dd($status);
                $donation_requests->where("donation_requests.status", $status);
            }
        }
        return $donation_requests;
    }

}
