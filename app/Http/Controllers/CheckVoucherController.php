<?php

namespace App\Http\Controllers;

use App\ChartOfAccount;
use App\CheckVoucher;
use App\CheckVoucherDetail;
use App\Customer;
use App\Library\NumToWords;
use App\Loan;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;

class CheckVoucherController extends Controller
{
    public $arr_rules;
    public $route;

    public function __construct(){
        $route = "check-vouchers";
        $this->route = $route;

        $this->arr_rules  = [

        ];

        $chart_of_accounts = ChartOfAccount::all()->pluck('account_desc','id');
        $bank_accounts = ChartOfAccount::bankAccounts()->pluck('account_desc','id');
        $expense_accounts = ChartOfAccount::type('E')->pluck('account_desc','id');
        $customers = Customer::all()->pluck('name','id');

        $search_data = [ 'search_url' => $this->route ];

        return view()->share(compact([
            'search_data','customers','collateral_classes','trans_types','collaterals','route',
            'chart_of_accounts',
            'bank_accounts',
            'expense_accounts'
        ]));
    }

    public function index(Request $request){
        $keyword = $request->input('keyword');

        if ( $keyword ) {
            $check_vouchers = CheckVoucher::where('id','like',"%$keyword%")
                ->paginate();
        } else {
            $check_vouchers = CheckVoucher::paginate();
        }

        return view(str_replace("-","_",$this->route) . ".search_" . str_replace("-","_",$this->route),compact([
            'check_vouchers'
        ]));
    }

    public function create(){
        return view(str_replace("-","_",$this->route).".".str_replace("-","_",$this->route),compact([
        ]));
    }

    public function store(Request $request){
        //dd($request->all());
        $this->validate($request,$this->arr_rules);
        $CheckVoucher = CheckVoucher::create($request->all());

        if ( $request->has('check_voucher_details') ) {
            foreach ( $request->input('check_voucher_details')['chart_of_account_id']  as $i => $chart_of_account_id) {
                $CheckVoucher->details()->save(new CheckVoucherDetail([
                    'chart_of_account_id' => $chart_of_account_id,
                    'debit' => $request->input('check_voucher_details')['debit'][$i],
                    'credit' => $request->input('check_voucher_details')['credit'][$i]
                ]));
            }
        }

        return Redirect::to("/{$this->route}/{$CheckVoucher->id}/edit")->with('flash_message','Information Saved');
    }

    public function edit(CheckVoucher $check_voucher){
        return view(str_replace("-","_",$this->route).".".str_replace("-","_",$this->route),compact([
            'check_voucher'
        ]));

    }

    public function update(Request $request, CheckVoucher $check_voucher){
        $this->validate($request,$this->arr_rules);
        $check_voucher->update($request->all());

        if ( $request->has('check_voucher_details') ) {
            foreach ( $request->input('check_voucher_details')['chart_of_account_id']  as $i => $chart_of_account_id) {

                if ( empty($request->input('check_voucher_details')['id'][$i]) ) {
                    $check_voucher->details()->save(new CheckVoucherDetail([
                        'chart_of_account_id' => $chart_of_account_id,
                        'debit' => $request->input('check_voucher_details')['debit'][$i],
                        'credit' => $request->input('check_voucher_details')['credit'][$i]
                    ]));
                } else {
                    CheckVoucherDetail::find($request->input('check_voucher_details')['id'][$i])
                        ->update([
                            'chart_of_account_id' => $chart_of_account_id,
                            'debit' => $request->input('check_voucher_details')['debit'][$i],
                            'credit' => $request->input('check_voucher_details')['credit'][$i]
                        ]);
                }

            }
        }

        return Redirect::to("/{$this->route}/{$check_voucher->id}/edit")->with('flash_message','Information Saved');
    }

    public function destroy(Loan $loan){
        $loan->delete();
        return Redirect::to("/{$this->route}")->with('flash_message','Loan Class Deleted');
    }


    public function printCashVoucher(CheckVoucher $check_voucher){

        $formatter = new NumToWords($check_voucher->amount);
        $words = $formatter->getWord();


        return view('reports.print_cash_voucher_2', compact([
            'check_voucher',
            'words'
        ]));
    }

    public function checkVoucherDetails(CheckVoucher $check_voucher){
        return $check_voucher->details;
    }

    public function deleteDetail(CheckVoucherDetail $check_voucher_detail) {
        $CheckVoucher = $check_voucher_detail->checkVoucher;
        $check_voucher_detail->delete();

        /**
         * update amount when deleting
         */
        $total_amount = $CheckVoucher->details->sum(function($Detail){
            return $Detail->debit - $Detail->credit;
        });

        $CheckVoucher->amount = $total_amount;
        $CheckVoucher->save();
    }
}
