<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;
use App\Models\User;
use App\Models\Item;
use Symfony\Component\HttpKernel\Profiler\Profile;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function setProfile(Request $request)
    {
        $user = Auth::user();
        return view('set-profile', compact('user'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return
     * \Illuminate\Http\Response
     */
    public function mypage(Request $request)
    {
        $page = $request->query('page', 'sell');

        $user = User::with(['sellItems', 'purchases.item'])->find(Auth::id());
        $sellItems = $user->sellItems ?? collect();
        $purchases = $user->purchases ?? collect();

        return view('mypage', compact('user', 'page', 'sellItems', 'purchases'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProfileRequest $request)
    {

        $user = Auth::user();
        $userData = $request->only(['name', 'postal_code', 'address', 'building']);

        $userData['postal_code'] = mb_convert_kana($userData['postal_code'], 'a');

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                \Storage::disk('public')->delete($user->profile_image);
            }
            $userData['profile_image'] = $request->file('profile_image')->store('profile_images', 'public');
        } else {
            $userData['profile_image'] = $user->profile_image;
        }

        $user->update($userData);

        return redirect('/');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }
}
