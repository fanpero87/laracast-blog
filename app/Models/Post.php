<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    // Option 1 - For Mass Assign
    // protected $fillable = ['title'];

    // $fillabe means that the field can be store when you do something like this
    // Post::create([
    //     'title' => 'some title',
    //     'body' => 'some body',
    //     'excerpt' => 'some excerpt'
    // ]);

    // In this case, onlt the field Title will get created, if you don't have default values
    // for the others, the whole thing will fail.

    // Option 2 - For Mass Assign
    // protected $guarded = [];

    // This is the opposite. This means, everything is fillable execept what is inside the
    // array. Everything can be "Mass Assign"

    protected $with = ['category', 'author'];

    public function scopeFilter($query, array $filters) // Post::newQuery()->filter()->get();
    {
        // Two different ways of doing the same

        $query->when($filters['search'] ?? false, fn($query, $search) =>
            $query->where(fn($query) =>
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('title', 'like', '%' . $search . '%')
            )
        );

        $query->when($filters['category'] ?? false, fn($query, $category)=>
            $query->whereHas('category', fn($query) =>
                $query->where('slug', $category)));

        $query->when($filters['author'] ?? false, fn($query, $author)=>
        $query->whereHas('author', fn($query) =>
            $query->where('userna,e', $author)));
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
