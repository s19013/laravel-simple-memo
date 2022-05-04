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
        $tags = Tag::where('user_id','=',\Auth::id())
        ->where('deleted_at')
        ->orderBy('id','DESC')
        ->get();



        return view('create',compact('tags'));
    }

    public function edit($id)
    {
        //編集するメモ
        //列名の衝突を避けるため別名を使う
        $edit_memo = Memo::select('memos.*','tags.id AS tag_id')
        ->leftJoin('memo_tags','memo_tags.memo_id','=','memos.id')
        ->leftJoin('tags','memo_tags.tag_id','=','tags.id')
        ->Where('memos.user_id','=',\Auth::id())
        ->Where('memo_id','=',$id)
        ->WhereNull('memos.deleted_at')
        ->get();

        // edit_memoの中身
        // {
        //  [id => ,
        //  content => ,
        //  user_id => ,
        //  deleted_at => ,
        //  updated_at => ,
        //  created_at=> ,
        //  tag_id=>]
        //  ,
        //  [id => ,
        //  content => ,
        //  user_id => ,
        //  deleted_at => ,
        //  updated_at => ,
        //  created_at=> ,
        //  tag_id=>]
        // }
        //:
        //:


        //データベースに保存されているタグの一覧
        $tags = Tag::where('user_id','=',\Auth::id())
        ->where('deleted_at')
        ->orderBy('id','DESC')
        ->get();

        // memoについているタグを別の変数に保存して置く
        $include_tags = [];
        foreach ($edit_memo as $memo) {
            array_push($include_tags,$memo['tag_id']);
        }

        dd($edit_memo);
        return view('edit',compact('edit_memo','include_tags','tags'));
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

            //postsの中のtagsが空だった場合memoTagのtag_idにnullを
            //それ以外は選択したtagを紐づける
            if (!empty($posts['tags'][0])) {
                foreach($posts['tags'] as $tag){
                    MemoTag::insert(['memo_id' => $memo_id,'tag_id' => $tag]);
                }
            }
            // else{
            //     MemoTag::insert(['memo_id' => $memo_id,'tag_id' => 0]);
            // }
        });


        return redirect(route('home'));
    }

    public function update(Request $request)
    {
        // $request変数の中にあるものをすべて$postsの中に入れる
        $posts=$request->all();

        DB::transaction(function() use($posts){
            Memo::where('id',$posts['memo_id'])->update(['content' => $posts['content'], 'user_id' => \Auth::id()]);
            //一度メモとタグの紐づけを解除する
            // ここは物理削除
            // 個人の予想ではあるが多分論理削除だと､データがいっぱいになってしまうから?
            MemoTag::where('memo_id','=',$posts['memo_id'])
            ->delete();

            // そして､新規にまた紐づけする
            foreach ($posts['tags'] as $tag){
                MemoTag::insert(['memo_id' => $posts['memo_id'],'tag_id' => $tag]);
            }

            // タグがだぶらないようにする
            $tag_exists = Tag::where('user_id','=',\Auth::id())
            ->where('name','=',$posts['new_tag'])
            ->exists();//existsでダブっていればtrue

            if (!empty($posts['new_tag']) && !$tag_exists) {
                // 新規タグが既に存在しなければ、tagsテーブルにインサート→IDを取得
               $tag_id = Tag::insertGetId(['user_id' => \Auth::id(),'name' => $posts['new_tag']]);
               // memo_tagsにインサートして、メモとタグを紐付ける
               MemoTag::insert(['memo_id' => $posts['memo_id'],'tag_id' => $tag_id]);
           }
           //postsの中のtagsが空ではなかった場合memoTagを入れる
           if (!empty($posts['tags'][0])) {
            foreach($posts['tags'] as $tag){
                MemoTag::insert(['memo_id' => $post['memo_id'],'tag_id' => $tag]);
                }
            }
        });


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
