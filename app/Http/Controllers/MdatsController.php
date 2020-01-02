<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Mdat;

use Carbon\Carbon;
//
class MdatsController extends Controller
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
    public function index()
    {
//var_dump("#index");
        $user_id = Auth::id();
        $dt = new Carbon(self::getYm_firstday());
        $now_month= $dt->format('Y-m');
        $startDt = $dt->format('Y-m-d');
        $endDt = $dt->endOfMonth()->format('Y-m-d');
//dd($startDt);
//exit                
        $mdats = Mdat::orderBy('date', 'asc')
        ->where("user_id", $user_id )
        ->whereBetween("date", [$startDt, $endDt ])
        ->paginate(10 );
        $prev = $this->getPrev();
        $next = $this->getNext();        
        return view('mdats/index')->with(
            compact('mdats', 'prev', 'next','now_month') 
        );
    }
    /**************************************
     *
     **************************************/
    public function create()
    {
        $date = "";
        if(isset($_GET['ymd'])){
            $date = $_GET['ymd'];
        }else{
            $date = Carbon::now()->format('Y-m-d');
        }
        $mdat = new Mdat();
        $mdat["date"] = $date;

        return view('mdats/create')->with('mdat', $mdat );
    }
    /**************************************
     *
     **************************************/    
    public function store(Request $request)
    {
        $data = $request->all();
        $user_id = Auth::id();
        if(empty($user_id)){
            session()->flash('flash_message', 'ユーザー情報の取得に失敗しました。');
            return redirect()->route('mdats.index');
        }     
        $data["user_id"] = $user_id;   
//dd($data );
//exit();
        $mdat = new Mdat();
        $mdat->fill($data );
        $mdat->save();
        session()->flash('flash_message', '保存が完了しました');
        return redirect()->route('mdats.index');
    }  
    /**************************************
     *
     **************************************/
    public function edit($id)
    {
        $mdat = Mdat::find($id);
        return view('mdats/edit')->with('mdat', $mdat );
    }    
    /**************************************
     *
     **************************************/
    public function update(Request $request, $id)
    {
        $mdat = Mdat::find($id);
        $mdat->fill($request->all());
        $mdat->save();
        session()->flash('flash_message', '保存が完了しました');
        return redirect()->route('mdats.index');
    }   
    /**************************************
     *
     **************************************/
    public function chart()
    {
        $user_id = Auth::id();
        $dt = new Carbon(self::getYm_firstday());
        $now_month= $dt->format('Y-m');
        $startDt = $dt->format('Y-m-d');
        $endDt = $dt->endOfMonth()->format('Y-m-d');

        $mdats = Mdat::orderBy('date', 'asc')
        ->where("user_id", $user_id )
        ->whereBetween("date", [$startDt, $endDt ])
        ->get();
//dd($mdats->toArray() );
//exit();
        return view('mdats/chart')->with('mdats', $mdats );        
    }
    /**************************************
     *
     **************************************/
    public function destroy($id)
    {
        $mdat = Mdat::find($id);
        $mdat->delete();
        return redirect()->route('mdats.index');
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
