<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Author;
use App\Models\Genre;
use App\Models\Book;
class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Books data provided in the assignment
        $books = [
            ["The Great Gatsby", "F. Scott Fitzgerald", "9780743273565", "Classics"],
            ["To Kill a Mockingbird", "Harper Lee", "9780060935467", "Classics"],
            ["1984", "George Orwell", "9780451524935", "Dystopian"],
            ["Pride and Prejudice", "Jane Austen", "9780141199078", "Romance"],
            ["The Catcher in the Rye", "J.D. Salinger", "9780316769488", "Classics"],
            ["The Hobbit", "J.R.R. Tolkien", "9780547928227", "Fantasy"],
            ["Fahrenheit 451", "Ray Bradbury", "9781451673319", "Science Fiction"],
            ["The Book Thief", "Markus Zusak", "9780375842207", "Historical Fiction"],
            ["Moby-Dick", "Herman Melville", "9781503280786", "Classics"],
            ["War and Peace", "Leo Tolstoy", "9781400079988", "Historical Fiction"]
        ];

        $booksData = [];
        foreach ($books as $dataOfBooks){

            $book['title'] = $dataOfBooks[0];
            $book['isbn'] = $dataOfBooks[2];

            $authorData = Author::firstOrCreate(['name' => $dataOfBooks[1]]);
            $book['author_id'] = $authorData->id;

            $genreData = Genre::firstOrCreate(['name' => $dataOfBooks[3]]);
            $book['genre_id'] = $genreData->id;

            //Creating array of books data
            $booksData[] = $book;
        }

        Book::insert($booksData);
    }
}
