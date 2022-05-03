<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;
use App\Models\MemoTag;
use App\Models\Tag;
use DB;

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

        $tags = Tag::where('user_id','=',\Auth::id())
        ->where('deleted_at')
        ->orderBy('id','DESC')
        ->get();



        return view('create',compact('memos','tags'));
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

        // トランザクション
        DB::transaction(function() use($posts){
            $memo_id = Memo::insertGetId(['content' => $posts['content'], 'user_id' => \Auth::id()]);
            // タグがだぶらないようにする
            $tag_exists = Tag::where('user_id','=',\Auth::id())
            ->where('name','=',$posts['new_tag'])
            ->exists();//existsでダブっていればtrue

            if (!empty($posts['new_tag']) && !$tag_exists) {
                 // 新規タグが既に存在しなければ、tagsテーブルにインサート→IDを取得
                $tag_id = Tag::insertGetId(['user_id' => \Auth::id(),'name' => $posts['new_tag']]);
                // memo_tagsにインサートして、メモとタグを紐付ける
                MemoTag::insert(['memo_id' => $memo_id,'tag_id' => $tag_id]);
            }

            // 複数タグが紐付けられた場合 memo_tagsにインサート

            //postsの中のtagsが空ではなかった場合 memoTagを入れる
            if (!empty($posts['tags'][0])) {
                foreach($posts['tags'] as $tag){
                    MemoTag::insert(['memo_id' => $memo_id,'tag_id' => $tag]);
                }
            }
        });


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
