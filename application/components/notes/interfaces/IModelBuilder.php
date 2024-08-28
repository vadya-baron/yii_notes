<?php

declare(strict_types=1);


namespace components\notes\interfaces;

use components\notes\models\Note;
use components\notes\models\NoteForm;
use components\notes\models\Notes;
use components\notes\entities\Note as NoteEntity;
use components\notes\entities\Tag as TagEntity;
use components\notes\models\Tag;
use components\notes\models\TagForm;
use components\notes\models\Tags;

interface IModelBuilder
{
    public function buildNotesModel(
        ?array $notesData = null,
        ?array $tagsData = null,
    ): Notes;

    public function buildNoteModel(
        null|array|NoteEntity $noteData = null,
        ?array $tagsData = null,
        array &$messages = [],
        array &$errors = [],
    ): ?Note;

    public function buildForm(
        int $userId,
        ?Note $note = null,
        ?array $formData = null,
        ?array $allTagsData = null,
        array &$messages = [],
        array &$errors = [],
    ): NoteForm;

    public function buildTagsModel(?array $tagsData = null): Tags;

    public function buildTagModel(
        null|array|TagEntity $tagData = null,
        array &$messages = [],
        array &$errors = [],
    ): ?Tag;

    public function buildTagForm(
        ?Tag $tag = null,
        ?array $formData = null,
        array &$messages = [],
        array &$errors = [],
    ): TagForm;
}