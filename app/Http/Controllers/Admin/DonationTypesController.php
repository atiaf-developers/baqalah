<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\DonationType;
use App\Models\DonationTypeTranslation;
use Validator;
use DB;


class DonationTypesController extends BackendController {

    private $rules = array(
        'active' => 'required',
        'this_order' => 'required|unique:donation_types'
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:donation_types,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:donation_types,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:donation_types,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:donation_types,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {
        return $this->_view('donation_types/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        return $this->_view('donation_types/create', 'backend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $columns_arr = array(
            'title' => 'required|unique:donation_types_translations,title',
            
        );
        $lang_rules = $this->lang_rules($columns_arr);
        $this->rules = array_merge($this->rules, $lang_rules);

        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        DB::beginTransaction();
        try {
            $donation_type = new DonationType;
            $donation_type->active = $request->input('active');
            $donation_type->this_order = $request->input('this_order');
            
            $donation_type->save();
            
            $donation_type_translations = array();
            $donation_type_title = $request->input('title');

            foreach ($this->languages as $key => $value) {
                $donation_type_translations[] = array(
                    'locale' => $key,
                    'title'  => $donation_type_title[$key],
                    'donation_type_id' => $donation_type->id
                );
            }
            DonationTypeTranslation::insert($donation_type_translations);
            DB::commit();
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
             DB::rollback();
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $find = DonationType::find($id);

        if ($find) {
            return _json('success', $find);
        } else {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $donation_type = DonationType::find($id);

        if (!$donation_type) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }

        $this->data['translations'] = DonationTypeTranslation::where('donation_type_id',$id)->get()->keyBy('locale');
        $this->data['donation_type'] = $donation_type;

        return $this->_view('donation_types/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $donation_type = DonationType::find($id);

        if (!$donation_type) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }

       $columns_arr = array(
            'title' => 'required|unique:donation_types_translations,title,'.$id .',donation_type_id',
        );
        $lang_rules = $this->lang_rules($columns_arr);
        $this->rules['this_order'] = 'required|unique:donation_types,this_order,'.$id;
        $this->rules = array_merge($this->rules, $lang_rules);

        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }

        DB::beginTransaction();
        try {

            $donation_type->active = $request->input('active');
            $donation_type->this_order = $request->input('this_order');
            
            $donation_type->save();
            
            $donation_type_translations = array();

            DonationTypeTranslation::where('donation_type_id', $donation_type->id)->delete();

            $donation_type_title = $request->input('title');

            foreach ($this->languages as $key => $value) {
                $donation_type_translations[] = array(
                    'locale' => $key,
                    'title'  => $donation_type_title[$key],
                    'donation_type_id' => $donation_type->id
                );
            }
            DonationTypeTranslation::insert($donation_type_translations);

            DB::commit();
            return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $donation_type = DonationType::find($id);
        if (!$donation_type) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $donation_type->delete();
            DB::commit();
            return _json('success', _lang('app.deleted_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            if ($ex->getCode() == 23000) {
                return _json('error', _lang('app.this_record_can_not_be_deleted_for_linking_to_other_records'), 400);
            } else {
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }

    public function data(Request $request) {

        $donation_types = DonationType::Join('donation_types_translations', 'donation_types.id', '=', 'donation_types_translations.donation_type_id')
                ->where('donation_types_translations.locale', $this->lang_code)
                ->select([
            'donation_types.id', "donation_types_translations.title", "donation_types.this_order", 'donation_types.active',
        ]);

        return \Datatables::eloquent($donation_types)
        ->addColumn('options', function ($item) {

            $back = "";
            if (\Permissions::check('donation_types', 'edit') || \Permissions::check('donation_types', 'delete')) {
                $back .= '<div class="btn-group">';
                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                $back .= '<i class="fa fa-angle-down"></i>';
                $back .= '</button>';
                $back .= '<ul class = "dropdown-menu" role = "menu">';
                if (\Permissions::check('donation_types', 'edit')) {
                    $back .= '<li>';
                    $back .= '<a href="' . route('donation_types.edit', $item->id) . '">';
                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                    $back .= '</a>';
                    $back .= '</li>';
                }

                if (\Permissions::check('donation_types', 'delete')) {
                    $back .= '<li>';
                    $back .= '<a href="" data-toggle="confirmation" onclick = "DonationTypes.delete(this);return false;" data-id = "' . $item->id . '">';
                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                    $back .= '</a>';
                    $back .= '</li>';
                }

                $back .= '</ul>';
                $back .= ' </div>';
            }
            return $back;
        })
        ->editColumn('active', function ($item) {
                          if ($item->active == 1) {
                          $message = _lang('app.active');
                          $class = 'label-success';
                          } else {
                          $message = _lang('app.not_active');
                          $class = 'label-danger';
                          }
                          $back = '<span class="label label-sm ' . $class . '">' . $message . '</span>';
                          return $back;
                      }) 
                      ->escapeColumns([])
                      ->make(true);
    }

              
}
