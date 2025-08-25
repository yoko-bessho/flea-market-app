<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Item;
use App\Enums\ItemCondition;
use App\Http\Requests\CommentRequest;
use App\Models\Like;
use Facade\Ignition\QueryRecorder\Query;
use PhpParser\Builder\Function_;
use Symfony\Component\CssSelector\Node\FunctionNode;

class ItemController extends Controller
{

    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommended');
        $user = Auth::user();
        $filters = $request->only(['keyword']);

        if ($user) {
            $mylistItems = $user->mylistItems()->keywordSearch($filters)->get();
            $recommendedItems = Item::where('user_id', '!=', $user->id)
                ->keywordSearch($filters)
                ->get();
        } else {
            $mylistItems = collect();
            $recommendedItems = Item::keywordSearch($filters)->get();
        }

        return view('index', compact( 'user', 'tab', 'mylistItems', 'recommendedItems', 'filters'));
    }





    public function create()
    {
        $user = Auth::user();
        $categories = Category::all();
        $conditionlabels = ItemCondition::alllabels();

        return view('sell', compact('user', 'categories', 'conditionlabels'));
    }


    public function store(Request $request)
    {
        $imagePath = $request->file('image')->store('item_images', 'public');

        $itemData = $request->only(['title', 'description', 'brand', 'condition', 'price']);
        $itemData['user_id'] = Auth::id();
        $itemData['image_path'] = $imagePath;
        $itemData['is_sold'] = false;

        $item = Item::create($itemData);
        $item->categories()->sync($request->input('category_id'));

        return redirect()->route('mypage', ['page' => 'sell']);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show ($id)
    {
        $item = Item::with('categories', 'likes', 'commentingUsers')->find($id);
        $commenters = $item->commentingUsers;
        $conditionlabels = \App\Enums\ItemCondition::allLabels();

        return view('item-detail', compact('item', 'commenters', 'conditionlabels'));
    }

    public Function like(Item $item)
    {
        $user = auth()->user();
        $isLiked = $user->mylistItems()->where('item_id', $item->id)->exists();
        if ($isLiked) {
            $user->mylistItems()->detach($item->id);
        }else {
            $user->mylistItems()->attach($item->id);
        }
        return back();
    }

    public function comment(CommentRequest $request, Item $item)
    {
        $user = auth::user();
        $user->commentedItems()->attach($item->id, $request->only('text'));
        return back();
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
