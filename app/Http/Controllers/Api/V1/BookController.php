<?php

namespace App\Http\Controllers\Api\V1;

use App\Facades\BookFacade;
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
    public function __construct(){}

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

        $searchResults = BookFacade::getSearchResultsUsingTerm($inputSearchTerm);

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
            $bookingData = BookFacade::rentBook($validatedBookRequest);

            if(!$bookingData) {
                return response()->json([
                    'success'   => true,
                    'message'   => 'User has already rented this book',
                    'data'      => null
                ]);
            }
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

        $fetchBookRentData = BookFacade::returnBook($validatedReturnData);

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
        $bookStatData['most_overdue_book'] = BookFacade::mostOverdueBook();

        $bookStatData['most_popular_book'] = BookFacade::mostPopularBook();

        $bookStatData['least_popular_book'] = BookFacade::leastPopularBook();

        return response()->json([
            'success'   => true,
            'data'      => $bookStatData,
            'message'   => 'Data fetched successfully.'
        ]);
    }

}
