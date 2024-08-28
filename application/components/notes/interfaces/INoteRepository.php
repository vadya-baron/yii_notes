<?php

declare(strict_types=1);


namespace components\notes\interfaces;

use components\notes\Entities\Note;
use components\notes\exceptions\NoteQueryException;
use components\notes\filters\NoteFilter;

interface INoteRepository
{
    public function getNoteData(NoteFilter $filter): ?array;

    public function getNotesData(NoteFilter $filter): ?array;

    public function countNotes(NoteFilter $filter): int;

    /**
     * @throws NoteQueryException
     */
    public function saveNote(Note &$entity): void;

    /**
     * @throws NoteQueryException
     */
    public function deleteNote(int $id): void;
}