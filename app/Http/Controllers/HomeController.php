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

    public function edit($id)
    {
        // このタイミングでメモを取得
        $memos = Memo::select('memos.*')
        ->Where('user_id','=',\Auth::id())
        ->WhereNull('deleted_at')
        ->orderBy('updated_at','DESC')
        ->get();

        // phpの変数はスネーク
        $edit_memo = Memo::find($id);

        return view('edit',compact('memos','edit_memo'));
    }

    public function store(Request $request)
    {
        // $request変数の中にあるものをすべて$postsの中に入れる
        $posts=$request->all();

        Memo::insert(['content' => $posts['content'], 'user_id' => \Auth::id()]);
        return redirect(route('home'));
    }

    public function update(Request $request)
    {
        // $request変数の中にあるものをすべて$postsの中に入れる
        $posts=$request->all();

        Memo::where('id',$posts['memo_id'])->update(['content' => $posts['content'], 'user_id' => \Auth::id()]);
        return redirect(route('home'));
    }

    public function destroy(Request $request)
    {
        // $request変数の中にあるものをすべて$postsの中に入れる
        $posts=$request->all();


        // 論理削除
        Memo::where('id',$posts['memo_id'])->update(['deleted_at' => date("Y-m-d H:i:s",time())]);
        return redirect(route('home'));
    }


}
