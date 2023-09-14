<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cache;
use Kreait\Firebase\Auth\UserRecord;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Factory;


class TestController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param Request $request
     * @return UserRecord
     * @throws AuthException
     * @throws FirebaseException
     */
    public function test(Request $request)
    {
        if(file_exists('../firebase_credentials.json')){
            return "treu";
        }
        return "treuss";
        return;
        return BusinessSetting::all();
        return Cache::get('business_settings');
        $factory = (new Factory)->withServiceAccount('../firebase_credentials.json');
        return $factory->createAuth()->getUser('eMJ8H28byQUAIm0e8yMr39EPB32');


    }
}
/*
public function index()
{

}

public function create()
{

}


public function store(Request $request)
{

}

public function show($id)
{
}


public function edit($id)
{

}


public function update(Request $request)
{

}


public function destroy($id){

}
*/
