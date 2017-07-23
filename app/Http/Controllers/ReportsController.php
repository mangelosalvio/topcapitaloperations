<?php

namespace App\Http\Controllers;

use App\ChartOfAccount;
use App\GeneralLedger;
use App\Journal;
use App\Library\LoanComputation;
use App\Loan;
use App\Setting;
use Carbon\Carbon;
use Faker\Provider\DateTime;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    /**
     * TRIAL BALANCE
     */
    public function trialBalance(){
        return view('reports.trial_balance', compact([
        ]));
    }

    public function generateTrialBalance(Request $request){

        $url = url("/print-trial-balance?from_date={$request->input('from_date')}
        &to_date={$request->input('to_date')}");

        return view('reports.trial_balance', compact([
            'url'
        ]));
    }

    public function printTrialBalance(Request $request){
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        $Accounts = ChartOfAccount::orderBy('account_code')->get();

        $Accounts->each(function($Account) use ($from_date, $to_date){
            $Account->debit = $this->total($Account->id,$from_date,$to_date,'debit');
            $Account->credit = $this->total($Account->id,$from_date,$to_date,'credit');
        });

        $Accounts = $Accounts->filter(function($Account){
            return $Account->debit > 0 || $Account->credit > 0;
        });

        $total_debit = $Accounts->sum(function($Account){
            return $Account->debit;
        });

        $total_credit = $Accounts->sum(function($Account){
            return $Account->credit;
        });

        return view('reports.print_trial_balance',compact([
            'from_date',
            'to_date',
            'total_debit',
            'total_credit',
            'Accounts'
        ]));
    }

    /**
     * JOURNAL LISTINGS
     */
    public function journalListings(){

        $journals = Journal::all()->pluck('journal_desc','id');

        return view('reports.journal_listings', compact([
            'journals'
        ]));
    }

    public function generateJournalListings(Request $request){

        $journals = Journal::all()->pluck('journal_desc','id');

        $url = url("/print-journal-listings?from_date={$request->input('from_date')}&to_date={$request->input('to_date')}&journal_id={$request->input('journal_id')}");

        return view('reports.journal_listings', compact([
            'url',
            'journals'
        ]));
    }

    public function printJournalListings(Request $request){
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $journal_id = $request->input('journal_id');

        $Ledgers = GeneralLedger::with('chartOfAccounts')
            ->whereBetween('date',[$from_date, $to_date]);

        if ( $journal_id ) {
            $Ledgers->whereJournalId($journal_id);
        }

        $Ledgers = $Ledgers->orderBy('date')->get();
        //dd($Ledgers->toArray());
        return view('reports.print_journal_listings',compact([
            'from_date',
            'to_date',
            'Ledgers'
        ]));
    }

    /**
     * INCOME STATEMENT
     */
    public function incomeStatement(){
        return view('reports.acctg.income_statement', compact([
        ]));
    }

    public function generateIncomeStatement(Request $request){

        $url = url("/print-income-statement?from_date={$request->input('from_date')}
        &to_date={$request->input('to_date')}");

        return view('reports.acctg.income_statement', compact([
            'url'
        ]));
    }

    public function printIncomeStatement(Request $request){
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        $RevenueAccounts = ChartOfAccount::type("I")->orderBy('account_code')->get();

        $RevenueAccounts->each(function($Account) use ($from_date, $to_date){
            $Account->debit = $this->total($Account->id,$from_date,$to_date,'debit');
            $Account->credit = $this->total($Account->id,$from_date,$to_date,'credit');
            $Account->amount = $Account->credit - $Account->debit;
        });

        $RevenueAccounts = $RevenueAccounts->filter(function($Account){
            return $Account->amount != 0;
        });

        $total_revenue = $RevenueAccounts->sum(function($Account){
            return $Account->amount;
        });

        $ExpenseAccounts = ChartOfAccount::type("E")->orderBy('account_code')->get();

        $ExpenseAccounts->each(function($Account) use ($from_date, $to_date){
            $Account->debit = $this->total($Account->id,$from_date,$to_date,'debit');
            $Account->credit = $this->total($Account->id,$from_date,$to_date,'credit');

            $Account->amount = $Account->debit - $Account->credit;
        });

        $ExpenseAccounts = $ExpenseAccounts->filter(function($Account){
            return $Account->amount != 0;
        });

        $total_expenses = $ExpenseAccounts->sum(function($Account){
            return $Account->amount;
        });


        return view('reports.acctg.print_income_statement',compact([
            'from_date',
            'to_date',
            'total_revenue',
            'RevenueAccounts',
            'ExpenseAccounts',
            'total_expenses'
        ]));
    }

    private function total($chart_of_account_id,$from_date,$to_date,$column = 'debit'){
        $GL = DB::table('general_ledgers as g')
            ->join('chart_of_account_general_ledger as c','g.id','=','c.general_ledger_id')
            ->whereNull('deleted_at')
            ->where('c.chart_of_account_id','=',$chart_of_account_id)
            ->whereBetween('date',[
                $from_date, $to_date
            ])
            ->select(DB::raw("ifnull(sum(debit),0) as debit"), DB::raw("ifnull(sum(credit),0) as credit"))->first();

        if ( $GL ) {
            return $GL->{$column};
        }

        return 0;
    }


    /**
     * BALANCE SHEET
     */
    public function balanceSheet(){
        return view('reports.acctg.balance_sheet', compact([
        ]));
    }

    public function generateBalanceSheet(Request $request){

        $url = url("/print-balance-sheet?month={$request->input('month')}&year={$request->input('year')}");

        return view('reports.acctg.balance_sheet', compact([
            'url'
        ]));
    }

    public function printBalanceSheet(Request $request){

        $year = $request->input('year');
        $month = $request->input('month');

        $from_date = Carbon::create($year,$month,1)->toDateString();
        $to_date = Carbon::create($year,$month,1)->endOfMonth()->toDateString();

        /**
         * ASSETS
         */


        $AssetAccounts = ChartOfAccount::type("A")->orderBy('account_code')->get();

        $AssetAccounts->each(function($Account) use ($to_date){
            $Account->amount = $this->balance($Account->id,$to_date,'debit');
        });

        $AssetAccounts = $AssetAccounts->filter(function($Account){
            return $Account->amount != 0;
        });

        $total_assets = $AssetAccounts->sum(function($Account){
            return $Account->amount;
        });

        /**
         * LIABILITIES
         */

        $LiabilityAccounts = ChartOfAccount::type("L")->orderBy('account_code')->get();

        $LiabilityAccounts->each(function($Account) use ($to_date){
            $Account->amount = $this->balance($Account->id,$to_date,'credit');
        });

        $LiabilityAccounts = $LiabilityAccounts->filter(function($Account){
            return $Account->amount != 0;
        });

        $total_liabilities = $LiabilityAccounts->sum(function($Account){
            return $Account->amount;
        });

        /**
         * EQUITY
         */

        $EquityAccounts = ChartOfAccount::type("R")->orderBy('account_code')->get();

        $EquityAccounts->each(function($Account) use ($to_date){
            $Account->amount = $this->balance($Account->id,$to_date,'credit');

            if ( $Account->id == Setting::account("RETAINED_EARNINGS")->id ) {
                $Account->amount += $this->getPreviousNetIncome(Carbon::parse($to_date)->year);
            }
        });

        $EquityAccounts = $EquityAccounts->filter(function($Account){
            return $Account->amount != 0;
        });

        $total_equity = $EquityAccounts->sum(function($Account){
            return $Account->amount;
        });

        $net_income = $this->getIncome(Carbon::create($year,1,1)->toDateString(),$to_date);

        return view('reports.acctg.print_balance_sheet',compact([
            'from_date',
            'to_date',
            'month',
            'year',
            'AssetAccounts',
            'LiabilityAccounts',
            'EquityAccounts',
            'total_assets',
            'total_liabilities',
            'total_equity',
            'net_income'
        ]));
    }

    private function balance($chart_of_account_id,$date,$column = 'debit'){
        $GL = DB::table('general_ledgers as g')
            ->join('chart_of_account_general_ledger as c','g.id','=','c.general_ledger_id')
            ->whereNull('deleted_at')
            ->where('c.chart_of_account_id','=',$chart_of_account_id)
            ->where('date','<=',$date)
            ->select(DB::raw("ifnull(sum(debit),0) as debit"), DB::raw("ifnull(sum(credit),0) as credit"))->first();

        if ( $column == "debit" ) {
            return $GL->debit - $GL->credit;
        } else {
            return $GL->credit - $GL->debit;
        }
    }

    private function getPreviousNetIncome($year)
    {
        $income = DB::table('general_ledgers as g')
            ->join('chart_of_account_general_ledger as c','g.id','=','c.general_ledger_id')
            ->join('chart_of_accounts as ca', 'ca.id','=','c.chart_of_account_id')
            ->join('account_types as a','a.id','=','ca.account_type_id')
            ->whereAccountTypeCode('I')
            ->whereNull('g.deleted_at')
            ->whereNull('ca.deleted_at')
            ->whereYear('date','<',$year)
            ->select(DB::raw("ifnull(sum(credit-debit),0) as amount"))->first()->amount;

        $expenses = DB::table('general_ledgers as g')
            ->join('chart_of_account_general_ledger as c','g.id','=','c.general_ledger_id')
            ->join('chart_of_accounts as ca', 'ca.id','=','c.chart_of_account_id')
            ->join('account_types as a','a.id','=','ca.account_type_id')
            ->whereAccountTypeCode('E')
            ->whereNull('g.deleted_at')
            ->whereNull('ca.deleted_at')
            ->whereYear('date','<',$year)
            ->select(DB::raw("ifnull(sum(debit-credit),0) as amount"))->first()->amount;

        return $income - $expenses;
    }

    private function getIncome($from_date, $to_date)
    {

        $income = DB::table('general_ledgers as g')
            ->join('chart_of_account_general_ledger as c','g.id','=','c.general_ledger_id')
            ->join('chart_of_accounts as ca', 'ca.id','=','c.chart_of_account_id')
            ->join('account_types as a','a.id','=','ca.account_type_id')
            ->whereAccountTypeCode('I')
            ->whereNull('g.deleted_at')
            ->whereNull('ca.deleted_at')
            ->whereBetween('date',[$from_date, $to_date])
            ->select(DB::raw("ifnull(sum(credit-debit),0) as amount"))->first()->amount;

        $expenses = DB::table('general_ledgers as g')
            ->join('chart_of_account_general_ledger as c','g.id','=','c.general_ledger_id')
            ->join('chart_of_accounts as ca', 'ca.id','=','c.chart_of_account_id')
            ->join('account_types as a','a.id','=','ca.account_type_id')
            ->whereAccountTypeCode('E')
            ->whereNull('g.deleted_at')
            ->whereNull('ca.deleted_at')
            ->whereBetween('date',[$from_date, $to_date])
            ->select(DB::raw("ifnull(sum(debit-credit),0) as amount"))->first()->amount;

        return $income - $expenses;
    }

    /**
     * AGING OF ACCOUNTS RECEIVALBES
     */

    public function agingOfAccountsReceivables(){
        return view('reports.acctg.aging_of_accounts_receivables', compact([
        ]));
    }

    public function generateAgingOfAccountsReceivables(Request $request){

        $url = url("/print-aging-of-accounts-receivables?month={$request->input('month')}&year={$request->input('year')}");

        return view('reports.acctg.aging_of_accounts_receivables', compact([
            'url'
        ]));
    }

    public function printAgingOfAccountsReceivables(Request $request){

        $year = $request->input('year');
        $month = $request->input('month');

        $from_date = Carbon::create($year,$month,1)->toDateString();
        $to_date = Carbon::create($year,$month,1)->endOfMonth()->toDateString();

        $Loans = Loan::all();

        $Loans->each(function($Loan) use ($to_date){
            $LoanComputation  = new LoanComputation($Loan->id);
            $Loan->outstanding_balance = $LoanComputation->getCurrentBalance();
            $Loan->outstanding_interest = $LoanComputation->getUIIBalance();
            $Loan->outstanding_rebate = $LoanComputation->getRFFBalance();
            $Loan->billing_for_the_month = $LoanComputation->getBillingForTheMonth($to_date);
            $Loan->balance_if_updated = $LoanComputation->getBalanceIfUpdated($to_date);
            $Loan->total_overdue = $Loan->outstanding_balance - $Loan->balance_if_updated - $Loan->billing_for_the_month;
            $Loan->months_overdue = $Loan->billing_for_the_month > 0 ?  round( $Loan->total_overdue / $Loan->billing_for_the_month ,2) : 0;
            $Loan->receivables = LoanComputation::getReceivables($Loan->billing_for_the_month, $Loan->total_overdue);
            $Loan->total_receivables = 0;
            foreach ($Loan->receivables as $receivable){
                $Loan->total_receivables += $receivable;
            }
        });

        $total_receivables = [];
        for ( $i = 0 ; $i < 5 ; $i++ ) {
            $total_receivables[] = $Loans->sum(function($Loan) use ($i){
                return $Loan->receivables[$i];
            });
        }

        $Loans = $Loans->filter(function($Loan){
            return $Loan->outstanding_balance > 0;
        });



        return view('reports.acctg.print_aging_of_accounts_receivables',compact([
            'from_date',
            'to_date',
            'month',
            'year',
            'Loans',
            'total_receivables'
        ]));
    }

    /**
     * AVAILMENT REPORT
     */
    public function availmentReport(){
        return view('reports.availment_report', compact([
        ]));
    }

    public function generateAvailmentReport(Request $request){

        $url = url("/print-availment-report?from_date={$request->input('from_date')}
        &to_date={$request->input('to_date')}");

        return view('reports.availment_report', compact([
            'url'
        ]));
    }

    public function printAvailmentReport(Request $request){
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        $Accounts = Loan::join('customers','loans.customer_id','=','customers.id')
            ->whereBetween('date_purchased',[$from_date,$to_date])
            ->orderBy('customers.last_name')
            ->orderBy('customers.first_name')
            ->with('customer','LrAccount')
            ->get();

        return view('reports.print_availment_report',compact([
            'from_date',
            'to_date',
            'Accounts'
        ]));
    }

    /**
     * COLLECTION REPORT
     */

    public function collectionReport(){
        return view('reports.collection_report', compact([
        ]));
    }

    public function generateCollectionReport(Request $request){

        $url = url("/print-collection-report?from_date={$request->input('from_date')}
        &to_date={$request->input('to_date')}");

        return view('reports.collection_report', compact([
            'url'
        ]));
    }

    public function printCollectionReport(Request $request){
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        $Accounts = Loan::join('collections','loans.id','=','collections.loan_id')
            ->whereBetween('or_date',[$from_date,$to_date])
            ->select('collections.*', 'lr_account_id','customer_id')
            ->with('customer','LrAccount')
            ->get();

        return view('reports.print_collection_report',compact([
            'from_date',
            'to_date',
            'Accounts'
        ]));
    }


}
