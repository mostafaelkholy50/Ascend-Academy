<?php

namespace App\Repositories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Builder;

class BookRepository
{
    public function getBooksQuery(): Builder
    {
        return Book::query();
    }

    public function getActiveBooksQuery(): Builder
    {
        return Book::active();
    }

    public function findOrFail(int $id): Book
    {
        return Book::findOrFail($id);
    }

    public function create(array $data): Book
    {
        return Book::create($data);
    }

    public function update(Book $book, array $data): bool
    {
        return $book->update($data);
    }

    public function delete(Book $book): ?bool
    {
        return $book->delete();
    }
}
