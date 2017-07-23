<?php

namespace App\Http\Controllers;

use App\ChartOfAccount;
use App\Customer;
use App\GeneralLedger;
use App\Journal;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;

class GeneralLedgersController extends Controller
{
    public $arr_rules;
    public $route;

    public function __construct(){
        $route = "general-ledgers";
        $this->route = $route;

        $this->arr_rules  = [
            'journal_id' => 'required',
            'date' => 'required'
        ];

        $journals = Journal::all()->pluck('journal_desc','id');
        $chart_of_accounts = ChartOfAccount::orderBy('account_code')->get()->pluck('label','id');

        $search_data = [ 'search_url' => $this->route ];

        return view()->share(compact([
            'search_data',
            'route',

            /**
             * add additonal variables below
             */

            'chart_of_accounts', 'journals'
        ]));
    }

    public function index(Request $request){
        $keyword = $request->input('keyword');

        if ( $keyword ) {
            $general_ledgers = GeneralLedger::where('id','like',"%$keyword%")
                ->paginate();
        } else {
            $general_ledgers = GeneralLedger::paginate();
        }

        return view(str_replace("-","_",$this->route) . ".search_" . str_replace("-","_",$this->route),compact([
            'general_ledgers'
        ]));
    }

    public function create(){

        return view(str_replace("-","_",$this->route).".".str_replace("-","_",$this->route),compact([
        ]));
    }

    public function store(Request $request){
        //dd($request->all());
        $this->validate($request,$this->arr_rules);
        $GeneralLedger = GeneralLedger::create($request->all());

        if ( $request->has('general_ledger_details') ) {
            $arr_accounts = [];
            foreach ( $request->input('general_ledger_details')['chart_of_account_id']  as $i => $chart_of_account_id) {

                $arr_accounts[$chart_of_account_id] = [
                    'debit' => empty($request->input('general_ledger_details')['debit'][$i]) ? 0 : $request->input('general_ledger_details')['debit'][$i],
                    'credit' => empty($request->input('general_ledger_details')['credit'][$i]) ? 0 : $request->input('general_ledger_details')['credit'][$i],
                    'chart_of_account_id' => $request->input('general_ledger_details')['chart_of_account_id'][$i],
                    'description' => $request->input('general_ledger_details')['description'][$i]
                ];

            }

            $GeneralLedger->chartOfAccounts()->sync($arr_accounts);


        }

        return Redirect::to("/{$this->route}/{$GeneralLedger->id}/edit")->with('flash_message','Information Saved');
    }

    public function edit(GeneralLedger $general_ledger){
        return view(str_replace("-","_",$this->route).".".str_replace("-","_",$this->route),compact([
            'general_ledger'
        ]));

    }

    public function update(Request $request, GeneralLedger $general_ledger){
        $this->validate($request,$this->arr_rules);
        $general_ledger->update($request->all());

        if ( $request->has('general_ledger_details') ) {

            $arr_accounts = [];
            foreach ( $request->input('general_ledger_details')['chart_of_account_id']  as $i => $chart_of_account_id) {
                if ( empty($request->input('general_ledger_details')['id'][$i]) ) {
                    $arr_accounts[$chart_of_account_id] = [
                        'debit' => empty($request->input('general_ledger_details')['debit'][$i]) ? 0 : $request->input('general_ledger_details')['debit'][$i],
                        'credit' => empty($request->input('general_ledger_details')['credit'][$i]) ? 0 : $request->input('general_ledger_details')['credit'][$i],
                        'chart_of_account_id' => $request->input('general_ledger_details')['chart_of_account_id'][$i],
                        'description' => $request->input('general_ledger_details')['description'][$i]
                    ];
                }
            }

            $general_ledger->chartOfAccounts()->sync($arr_accounts);
        }

        return Redirect::to("/{$this->route}/{$general_ledger->id}/edit")->with('flash_message','Information Saved');
    }

    public function destroy(GeneralLedger $general_ledger){
        $general_ledger->delete();


        return Redirect::to("/{$this->route}")->with('flash_message','GL has been Deleted');
    }



    public function generalLedgerDetails(GeneralLedger $general_ledger){
        return $general_ledger->chartOfAccounts;
    }

    public function deleteDetail(GeneralLedger $general_ledger, $account) {
        $general_ledger->chartOfAccounts()->detach($account);
    }
}
