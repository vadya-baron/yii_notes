<?php

declare(strict_types=1);


namespace components\notes\interfaces;

use components\notes\filters\NoteFilter;
use components\notes\filters\TagFilter;
use components\notes\models\Note;
use components\notes\models\NoteForm;
use components\notes\models\Tag;
use components\notes\models\TagForm;
use components\notes\models\Tags;
use components\notes\models\Notes;

interface INotes
{
    public function getNotes(NoteFilter $filter): Notes;

    public function getNoteById(NoteFilter $filter): ?Note;
    public function deleteNote(Note $note, array &$errors = []): bool;

    public function getTags(TagFilter $filter): Tags;

    public function getTagById(TagFilter $filter): ?Tag;

    public function storeForm(
        int $userId,
        array $formData,
        array &$messages = [],
        array &$errors = [],
    ): ?Note;

    public function storeTagForm(
        array $formData,
        array &$messages = [],
        array &$errors = [],
    ): ?Tag;

    public function getStoreForm(
        int $userId,
        ?Note $note = null,
        array $formData = [],
        array &$messages = [],
        array &$errors = [],
    ): NoteForm;

    public function getStoreTagForm(
        ?Tag $tag = null,
        array $formData = [],
        array &$messages = [],
        array &$errors = [],
    ): TagForm;
}