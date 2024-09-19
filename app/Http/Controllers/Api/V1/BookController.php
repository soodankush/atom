<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\BookRentalRequest;
use App\Http\Requests\Book\ReturnBookRequest;
use App\Mail\BookOverdueNotificationMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Book;
use App\Models\BookRental;
use Illuminate\Support\Facades\Mail;

class BookController extends Controller
{
    public function __construct()
    {

    }

    /**
     * Function to find the search results.
     * @param Request $request
     * @return JsonResponse
     */
    public function searchBookOrGenre(Request $request): JsonResponse
    {
        $inputSearchTerm = $request->get('searchTerm');
        if(!$inputSearchTerm)
        {
            return response()->json([
                'message'   => 'Bad request. Error response has to be included.',
                'success'    => false,
                'data'       => null
            ], Response::HTTP_BAD_REQUEST);
        }

//        $searchResults = Book::getSearchResultsUsingTerm($inputSearchTerm);
        $searchResults = Book::join('genres','books.genre_id', '=', 'genres.id')
            ->join('authors', 'books.author_id', '=', 'authors.id')
            ->where('books.title','LIKE' ,'%'. $inputSearchTerm .'%')
            ->orWhere('genres.name', 'LIKE', '%' . $inputSearchTerm .'%')
            ->select('books.title', 'books.isbn','authors.name as author','genres.name as genre')
            ->get();;

        if(!count($searchResults)) {
            return response()->json([
                'message'   => 'No results found.',
                'success'    => true,
                'data'       => null
            ]);
        }

        return response()->json([
            'message'   => 'Search results retrieved successfully',
            'success'    => true,
            'data'       => $searchResults
        ], Response::HTTP_OK);
    }

    /**
     * Function to rent a book
     * @param BookRentalRequest $request
     * @return JsonResponse
     */
    public function rentBook(BookRentalRequest $request): JsonResponse
    {
        $validatedBookRequest = $request->validated();

        try{
            $tillDate = Carbon::createFromFormat('Y-m-d H:i:s', $validatedBookRequest['from_date'])->copy()->addWeeks(2)->endOfDay();

            //check and verify if the same user has Booked the same book.

            $bookingData = BookRental::create([
                'book_id'   => $validatedBookRequest["book_id"],
                'user_id'   => $validatedBookRequest["user_id"],
                'from_date' => $validatedBookRequest["from_date"],
                'till_date' => $tillDate,
                'status'    => 1,
                'is_overdue'=> false,
            ]);

            $response = [
                'book_name' => $bookingData->book->title,
                'user_name' => $bookingData->user->name,
                'from_date' => $bookingData->from_date,
                'till_date' => $bookingData->till_date,
                'status'    => $bookingData->status,
            ];

            return response()->json([
                'success'   => true,
                'message'   => 'Booking done successfully',
                'data'      => $response
            ]);
        } catch(\Exception $e) {
            \Log::error(' Error encountered while booking a book in ' . __METHOD__ );
            \Log::error($e);
            return response()->json([
                'error'     => true,
                'message'   => 'Error encountered while booking a book. Please try again later',
                'data'      => null
            ]);
        }
    }

    /**
     * Function to return the book
     * @param ReturnBookRequest $request
     * @return JsonResponse
     */
    public function returnBook(ReturnBookRequest $request): JsonResponse
    {
        $validatedReturnData = $request->validated();

        $fetchBookRentData = BookRental::where('book_id', $validatedReturnData['book_id'])
                                        ->where('user_id', $validatedReturnData['user_id'])
                                        ->where('status', 1)
                                        ->first();

        if(!$fetchBookRentData) {
            return response()->json([
                'success'   => true,
                'message'   => 'No book found with the user',
                'data'      => null
            ]);
        }

        $returnDate = Carbon::now();

        $fetchBookRentData->update([
            'status'        => 2,
            'return_date'   => $returnDate
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'Book has been returned',
            'data'      => [
                'book_name' => $fetchBookRentData->book->title,
                'user_name' => $fetchBookRentData->user->name,
                'return_date'   => $fetchBookRentData->return_date,
                'is_overdue'    => $fetchBookRentData->is_overdue,

            ]
        ]);
    }

    /**
     * Function to view rental history
     * @param Request $request
     * @return JsonResponse
     */

    public function viewRentalHistory(Request $request): JsonResponse
    {
        $inputBookId = $request->get('book_id');
        $perPageCount = $request->get('per_page') ?? 5;

        if(!$inputBookId) {
            return response()->json([
                'message'   => 'Bad request. PLease mention book for history to be viewed.',
                'success'    => false,
                'data'       => null
            ], Response::HTTP_BAD_REQUEST);
        }

        $getBookRentalHistory = BookRental::where('book_id', $inputBookId)
                                    ->with(['user','book'])
                                    ->paginate($perPageCount);

        $totalRecords = $getBookRentalHistory->total();
        if ($totalRecords > 0) {
            $message = "Showing " . $getBookRentalHistory->count() . " records out of " . $totalRecords . " total records.";
        } else {
            $message = "No records found.";
        }

        return response()->json([
            'success'   => true,
            'message'   => $message,
            'data'      => $getBookRentalHistory
        ]);

    }

    /**
     * Function to get the book stats
     * @return JsonResponse
     */
    public function getBookRentalStats(): JsonResponse
    {
        $mostOverdueBook = DB::table('book_rentals')
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

        $mostPopularBook = DB::table('book_rentals')
            ->join('books', 'book_rentals.book_id', '=', 'books.id')
            ->select(
                'books.title as book_name',
                'books.id as book_id',
                DB::raw('count(book_id) as book_count')
            )
            ->groupBy('book_rentals.book_id')
            ->orderBy('book_count','desc')
            ->first();

        $leastPopularBook = DB::table('book_rentals')
            ->join('books', 'book_rentals.book_id', '=', 'books.id')
            ->select(
                'books.title as book_name',
                'books.id as book_id',
                DB::raw('SUM(DATEDIFF(return_date, from_date)) as booking_days')
            )
            ->where('status', 2) // Book has been returned
            ->groupBy('book_rentals.book_id')
            ->orderBy('booking_days','asc')
            ->first();

        $bookStatData['most_overdue_book'] = $mostOverdueBook;
        $bookStatData['most_popular_book'] = $mostPopularBook;
        $bookStatData['least_popular_book'] = $leastPopularBook;

        return response()->json([
            'success'   => true,
            'data'      => $bookStatData,
            'message'   => 'Data fetched successfully.'
        ]);
    }

}
