<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookRental;
use Carbon\Carbon;
use \Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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

    /**
     * @param $bookData
     * @return null
     */
    public function rentBook($bookData): ?BookRental
    {
        $tillDate = Carbon::createFromFormat('Y-m-d H:i:s', $bookData['from_date'])->copy()->addWeeks(2)->endOfDay();

        $findIfUserHasAlreadyRentedSameBook = BookRental::where('book_id', $bookData['book_id'])
                                                        ->where('user_id', $bookData['user_id'])
                                                        ->where('status', 1)
                                                        ->first();

        if($findIfUserHasAlreadyRentedSameBook){
            return null;
        }

        $createBookingRentalEntry = BookRental::create([
            'book_id'   => $bookData["book_id"],
            'user_id'   => $bookData["user_id"],
            'from_date' => $bookData["from_date"],
            'till_date' => $tillDate,
            'status'    => 1,
            'is_overdue'=> false,
        ]);

        return $createBookingRentalEntry;
    }

    /**
     * Function which return the book's data
     * @param array $bookData
     * @return mixed
     */
    public function returnBook(array $bookData): mixed
    {
        return BookRental::where('book_id', $bookData['book_id'])
                ->where('user_id', $bookData['user_id'])
                ->where('status', 1)
                ->first();
    }

    /**
     * Function to find out most overdue book
     * @return object|null
     */
    public function mostOverdueBook()
    {
        return DB::table('book_rentals')
            ->join('books', 'book_rentals.book_id', '=', 'books.id')
            ->select(
                'books.title as book_name',
                'books.id as book_id',
                DB::raw('MAX(DATEDIFF(NOW(), till_date)) as maximum_overdue_days')
            )
            ->where('status', 1)
            ->where('is_overdue', 1)
            ->groupBy('book_rentals.book_id')
            ->orderBy('maximum_overdue_days', 'desc')
            ->first();
    }

    /**
     * Function to find out most popular book
     * @return object|null
     */
    public function mostPopularBook()
    {
        return DB::table('book_rentals')
            ->join('books', 'book_rentals.book_id', '=', 'books.id')
            ->select(
                'books.title as book_name',
                'books.id as book_id',
                DB::raw('count(book_id) as book_count')
            )
            ->groupBy('book_rentals.book_id')
            ->orderBy('book_count','desc')
            ->first();
    }

    /**
     * Function to find out least popular book
     * @return object|null
     */
    public function leastPopularBook()
    {
        return DB::table('book_rentals')
            ->join('books', 'book_rentals.book_id', '=', 'books.id')
            ->select(
                'books.title as book_name',
                'books.id as book_id',
                DB::raw('SUM(DATEDIFF(return_date, from_date)) as booking_days')
            )
            ->where('status', 2)
            ->groupBy('book_rentals.book_id')
            ->orderBy('booking_days','asc')
            ->first();
    }

}
