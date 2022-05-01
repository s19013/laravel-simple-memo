<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;

class HomeController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // このタイミングでメモを取得
        $memos = Memo::select('memos.*')
        ->Where('user_id','=',\Auth::id())
        ->WhereNull('deleted_at')
        ->orderBy('updated_at','DESC')
        ->get();

        return view('create',compact('memos'));
    }

    public function store(Request $request)
    {
        // $request変数の中にあるものをすべて$postsの中に入れる
        $posts=$request->all();

        Memo::insert(['content' => $posts['content'], 'user_id' => \Auth::id()]);
        return redirect(route('home'));
    }
}
