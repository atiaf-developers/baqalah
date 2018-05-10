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
use App\Models\Container;
use App\Models\ContainerAssignedHistory;
use PDF;
use App\Helpers\Fcm;

class DelegatesReportController extends BackendController {

    private $limit = 10;
    private $assign_rules = array(
        'delegate' => 'required'
    );

    public function __construct() {


        parent::__construct();
        $this->middleware('CheckPermission:delegates_report,open', ['only' => ['index']]);
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
            //dd($this->data['from']);
        }
        //dd($this->getLog($request));
        $this->data['log'] = $this->getLog($request);
        //dd( $this->data['delegates_report'] );
        $this->data['delegates'] = $this->getDelagates();
        return $this->_view('delegates_report.index', 'backend');
    }

    public function show(Request $request, $id) {
        $donation_request = $this->getDelegatesReport($request, $id);
        if (!$donation_request) {
            return $this->err404();
        }
        $donation_request->images = preg_filter('/^/', url('public/uploads/delegates_report') . '/', json_decode($donation_request->images));
        //dd($donation_request->images);
        $this->data['delegates'] = $this->getDelagates();
        $this->data['donation_request'] = $donation_request;
        $this->data['status_arr'] = DonationRequest::$status_text;
        return $this->_view('delegates_report.view', 'backend');
    }

    private function getDelagates() {
        $Users = User::select('id', "username")->where('type', 2)->get();
        return $Users;
    }

    private function handleWhere($delegates_report, $request) {

        if ($request->all()) {
            if ($from = $request->input('from')) {
                $delegates_report->where("delegates_report.date", ">=", "$from");
            }
            if ($to = $request->input('to')) {
                $delegates_report->where("delegates_report.date", "<=", "$to");
            }
            if ($delegate = $request->input('delegate')) {
                $delegates_report->where("users.id", $delegate);
            }
            if ($order = $request->input('order')) {
                $delegates_report->where("delegates_report.id", $order);
            }
            if ($status = $request->input('status')) {
                $status = array_search($status, DonationRequest::$status_text);
                //dd($status);
                $delegates_report->where("delegates_report.status", $status);
            }
        }
        return $delegates_report;
    }

    private function getLog($request) {
        $start = $request->input('from');
        $end = $request->input('to');
        $delegate = $request->input('delegate');
        if ($request->input('page')) {
            $current_page = $request->input('page');
        } else {
            $current_page = 1;
        }
        $offset = ($current_page - 1) * $this->limit;
        $allDays = GetDays($start, $end);

        $start = $start > 0 ? date('Y-m-d', strtotime("+$offset day", strtotime($start))) : $start;
        $count = 0;
        $days = [];
        //dd($this->getDelagateContainer(10, '2018-04-3'));
        while ($start <= $end) {
            if ($count > 9) {
                break;
            }

            $days[$start] = $this->getDelagateContainer($delegate, $start);
            $start = date('Y-m-d', strtotime('+1 day', strtotime($start)));

            $count++;
        }
        //dd($allDays);
        if ($request->all()) {
            $paginator = new LengthAwarePaginator(
                    $days, count($allDays), $this->limit, $current_page, ['path' => $request->url()]);

            return $paginator->appends($request->all());
        } else {
            return collect([]);
        }
    }

    private function getDelagateContainer($delegate_id, $date) {
        $delegates_report = ContainerAssignedHistory::join('containers', function ($join) use($date, $delegate_id) {
                    $join->on('containers.id', '=', 'container_assigned_history.container_id')
                            ->where('container_assigned_history.delegate_id', $delegate_id)
                            ->whereRaw("CASE WHEN container_assigned_history.end IS NULL THEN container_assigned_history.start <='$date' ELSE container_assigned_history.start <= '$date' and container_assigned_history.end >= '$date' END ");
                });
        $delegates_report->join('containers_translations as trans', function ($join) {
            $join->on('containers.id', '=', 'trans.container_id')
                    ->where('trans.locale', $this->lang_code);
        });
        $delegates_report->leftJoin('unloaded_containers', function ($join) use($date, $delegate_id) {
            $join->on('containers.id', '=', 'unloaded_containers.container_id')
                    ->where('unloaded_containers.date_of_unloading', $date)
                    ->where('unloaded_containers.delegate_id', $delegate_id);
        });
        $delegates_report->select([
            "trans.title as container_title", 'unloaded_containers.date_of_unloading'
        ]);
        $delegates_report->groupBy("containers.id");
        return $delegates_report->get();
    }

    private function getDelagateContainer2($delegate_id, $date) {
        $delegates_report = Container::join('users', 'users.id', '=', 'containers.delegate_id');
        $delegates_report->join('containers_translations as trans', 'containers.id', '=', 'trans.container_id');
        $delegates_report->leftJoin('unloaded_containers', function ($join) use($date, $delegate_id) {
            $join->on('containers.id', '=', 'unloaded_containers.container_id')
                    ->where('unloaded_containers.date_of_unloading', $date)
                    ->where('unloaded_containers.delegate_id', $delegate_id);
        });
        $delegates_report->select([
            "trans.title as container_title", 'unloaded_containers.date_of_unloading'
        ]);
        $delegates_report->where('trans.locale', $this->lang_code);
        $delegates_report->where("users.id", $delegate_id);
        return $delegates_report->get();
    }

}
