<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage ;
use Intervention\Image\Facades\Image;
use App\Models\Config ;

class ConfigController extends Controller
{
    //
    function __construct() {
        $this->middleware('checkmanager');
    }

    public function base()
    {
    	$fields = [
    		'title',
	    	'description', 
	    	'keywords', 
	    	'logpic', 
	    	'my_name', 
            'sub_name',
	    	'about_me',
    	];
    	foreach($fields as $field)
    	{
    		Config::firstOrCreate(['name'=>$field],['value'=>'', 'groups' => 'base']);
    	}	
        $data = Config::where('groups','base')->pluck('value','name');
    	return view('admin.config.base', ['data' => $data]);
    }

    public function other()
    { 
        $data = Config::where('groups','other')->pluck('value','name');
        return view('admin.config.other', ['data' => $data]);
    }

    public function store(Request $request)
    {
    	if($file = $request->file('logpic'))
    	{
		    Image::make($request->logpic)->resize(200,200)->save('storage/logo/logo.jpeg') ;
		    Config::updateOrCreate(['name'=>'logpic', 'value'=>'logo/logo.jpeg'], ['groups'=>'base']);
    	}
    		
    	$configs = $request->name ;
    	$group = $request->group ;
    	foreach($configs as $name => $value)
    	{
    		if(strlen($value)>0)
    		{
    			Config::updateOrCreate(['name'=>$name, 'groups'=>$group], ['value' => $value]);
    		}	
    	}
        return back() ;
    }
}
