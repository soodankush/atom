<?php

namespace App\Services;

use App\Models\Book;
use \Illuminate\Support\Collection;
class BookService
{

    public function __construct()
    {

    }

    /**
     * @param $searchTerm
     * @return \Illuminate\Support\Collection
     */
    public function getSearchResultsUsingTerm($searchTerm): Collection
    {
        return Book::join('genres','books.genre_id', '=', 'genres.id')
            ->join('authors', 'books.author_id', '=', 'authors.id')
            ->where('books.title','LIKE' ,'%'. $searchTerm .'%')
            ->orWhere('genres.name', 'LIKE', '%' . $searchTerm .'%')
            ->select('books.title', 'books.isbn','authors.name as author','genres.name as genre')
            ->get();
    }

    public function rentBook()
    {
        // Logic to implement renting book functionality.

    }

}
