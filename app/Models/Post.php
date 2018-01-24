<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use MDEditor ;

class Post extends Model
{
    //
    use SoftDeletes ;

    protected $fillable = ['title','body','user_id','category_id','views','global_top','category_top'];

    public function setGlobalTopAttribute($value)
    {
    	$this->attributes['global_top'] = $value == 'global' ? 1 : 0 ;
    }

    public function setCategoryTopAttribute($value)
    {
    	$this->attributes['category_top'] = $value == 'category' ? 1 : 0 ;
    }

    public function tags() {
    	return $this->belongsToMany('App\Models\Tag');
    }

	public function category() {
	    return $this->belongsTo('App\Models\Category')->withDefault(['name' => '未分类','id' => 0]);
	}

	public function comments()
	{
		return $this->hasMany('App\Models\Comment');
	}
	public function thumbs()
	{
		return $this->morphMany('App\Models\Thumb', 'thumbable');
	}

	public function getBodyAttribute($value) {
	    return MDEditor::MarkDecode($value);
	}

	public function pics()
	{
		return $this->hasMany('App\Models\Pic');
	}

    public function user()
    {
    	return $this->belongsTo('App\Models\User');
    }
	public function scopeOfCate($query, $cate) {
		return  $query->where('category_id', $cate) ;
	}

	public function scopeOfTitle($query, $title)
	{
		return  $query->where('title', 'like', '%'.$title.'%');
	}

	public function scopeOfContent($query, $str)
	{
		return $query->where('title', 'like', '%'.$str.'%')->orWhere('body','like', '%'.$str.'%');
	}

	public function order($query, $order)
	{
		if($orderby)
        {
            list($field, $type) = explode('@', $orderby);
            if(in_array($field, ['created_at', 'updated_at', 'last_replied']))
            {
                $query = $query->orderBy($field, $type) ;
            } 
            elseif($field == 'replies')
            {
                $query = $query->withCount('comments')->orderBy('comments_count',$type);
            }   
            else
            {
                $query = $query->orderBy('global_top','desc')->orderBy('created_at','desc');
            }    
        }
        else
        {
            $query = $query->orderBy('global_top','desc')->orderBy('created_at','desc');
        }  
        return $query ;  
	}



	
}