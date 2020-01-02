<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Plan;
use App\User;

use Carbon\Carbon;
//
class PlansController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }    
    /**************************************
     *
     **************************************/
    public function index(Request $request)
    {   
        //$user = Auth::user();
        $user_id = Auth::id();

        $startDt = new Carbon(self::getYm_firstday());
        $endDt = $startDt->addMonthNoOverflow()->format('Y-m-d');
        $plans = Plan::where('user_id', $user_id)->get(); 
//debug_dump($plans->toArray() );
        $month = $this->getMonth();
        $weeks = $this->getWeekItems();

//debug_dump($weeks[0] );
//exit();
        $prev = $this->getPrev();
        $next = $this->getNext();
        return view('plans/index')->with(compact('weeks','prev','next','month' ));
    }
    /**************************************
     *
     **************************************/
    public function create()
    {
        $plan = new Plan();
        $plan["date"] = Carbon::now()->format('Y-m-d');
//debug_dump( $plan );
//exit();
        return view('plans/create')->with('plan', $plan );
    }    
    /**************************************
     *
     **************************************/    
    public function store(Request $request)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        if(empty($user_id)){
            session()->flash('flash_message', 'ユーザー情報の取得に失敗しました。');
            return redirect()->route('plans.index');
        }
        $inputs = $request->all();
        $inputs["user_id"] = $user_id;
//debug_dump( $inputs );
        $todo = new Plan();
        $todo->fill($inputs);
        $todo->save();
        session()->flash('flash_message', '保存が完了しました');
        return redirect()->route('plans.index');
    }    
    /**************************************
     *
     **************************************/
    private function getWeekItems(){
        $weeks = [];
        $weekItem = [];

        $dt = new Carbon(self::getYm_firstday());
        $day_of_week = $dt->dayOfWeek;
        $days_in_month = $dt->daysInMonth;
        $dayArray = array(
            "day" => null, 
            "today" => false,
            "content" => "",
        );
        $dayItem = $dayArray;
        for($i =0; $i < $day_of_week ;$i++ ){ $weekItem[] = $dayItem; }
//var_dump($weekItem );
        for ($day = 1; $day <= $days_in_month; $day++, $day_of_week++) {
            $dayItem = $dayArray;
            $date = self::getYm() . '-' . $day;
            $dayItem["day"] = $day;
            if (Carbon::now()->format('Y-m-j') === $date) {
                $dayItem["today"] = true;
                $weekItem[] = $dayItem;
            } else {
                $weekItem[] = $dayItem;
            }
            if (($day_of_week % 7 === 6) || ($day === $days_in_month)) {
                if ($day === $days_in_month) {
                    $dayItem["day"] ="";
                    $num =6 - ($day_of_week % 7);
                    for($i =0; $i < $num ;$i++ ){ $weekItem[] = $dayItem; }
                }
//var_dump($week);
                $weeks[] = $weekItem;
                $weekItem = [];
            }
        }
        return $weeks;
    }    
    /**************************************
     *
     **************************************/
    private function getWeeks()
    {
        $weeks = [];
        $week = '';

        $dt = new Carbon(self::getYm_firstday());
        $day_of_week = $dt->dayOfWeek;
        $days_in_month = $dt->daysInMonth;

        $week .= str_repeat('<td></td>', $day_of_week);

        for ($day = 1; $day <= $days_in_month; $day++, $day_of_week++) {
            $date = self::getYm() . '-' . $day;
            if (Carbon::now()->format('Y-m-j') === $date) {
                $week .= '<td class="today">' . $day;
            } else {
                $week .= '<td>' . $day;
            }
            $week .= '</td>';

            if (($day_of_week % 7 === 6) || ($day === $days_in_month)) {
                if ($day === $days_in_month) {
                    $week .= str_repeat('<td></td>', 6 - ($day_of_week % 7));
                }
                $weeks[] = '<tr>' . $week . '</tr>';
                $week = '';
            }
        }
        return $weeks;
    }
    /**
     * month 文字列を返却する
     *
     * @return string
     */
    public function getMonth()
    {
        return Carbon::parse(self::getYm_firstday())->format('Y年n月');
    }
    /**
     * prev 文字列を返却する
     *
     * @return string
     */
    public function getPrev()
    {
        return Carbon::parse(self::getYm_firstday())->subMonthsNoOverflow()->format('Y-m');
    }
    /**
     * next 文字列を返却する
     *
     * @return string
     */
    public function getNext()
    {
        return Carbon::parse(self::getYm_firstday())->addMonthNoOverflow()->format('Y-m');
    }
    /**
     * GET から Y-m フォーマットを返却する
     *
     * @return string
     */
    private static function getYm()
    {
        if (isset($_GET['ym'])) {
            return $_GET['ym'];
        }
        return Carbon::now()->format('Y-m');
    }
    /**
     * YYYY-MM-DD 書式の取得
     *
     * @return string
     */
    private static function getYm_firstday()
    {
        return self::getYm() . '-01';
    }    



}
