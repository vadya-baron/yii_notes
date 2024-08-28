<?php

declare(strict_types=1);


namespace components\notes;

use components\notes\entities\TagRelation;
use components\notes\filters\NoteFilter;
use components\notes\filters\TagFilter;
use components\notes\filters\TagRelationFilter;
use components\notes\interfaces\INoteValidator;
use components\notes\interfaces\IModelBuilder;
use components\notes\interfaces\INotes;
use components\notes\interfaces\INoteRepository;
use components\notes\interfaces\ITagRepository;
use components\notes\interfaces\ITagValidator;
use components\notes\models\Note;
use components\notes\models\NoteForm;
use components\notes\models\Tag;
use components\notes\models\TagForm;
use components\notes\models\Tags;
use components\notes\models\Notes as NotesModel;
use components\notes\entities\Note as NoteEntity;
use components\notes\entities\Tag as TagEntity;
use Throwable;

/** Tags добавлены в Notes сознательно, так как являются субъектом объекта Notes */
class Notes implements INotes
{
    public function __construct(
        protected INoteRepository $noteRepository,
        protected ITagRepository  $tagRepository,
        protected IModelBuilder   $modelBuilder,
        protected INoteValidator  $noteValidator,
        protected ITagValidator  $tagValidator,
    ) {
    }

    public function getNotes(NoteFilter $filter): NotesModel
    {
        $ids = $this->getNoteIdsByTagTitle(tagTitle: $filter->getTagTitle());
        if ($ids) {
            $filter->addIds($ids);
        }
        $notesData = $this->noteRepository->getNotesData(filter: $filter);

        return $this->modelBuilder->buildNotesModel(
            notesData: $notesData,
            tagsData: $this->getNotesTagsData(data: $notesData),
        );
    }

    public function getNoteById(NoteFilter $filter): ?Note
    {
        $noteData = $this->noteRepository->getNoteData(filter: $filter);
        return $this->modelBuilder->buildNoteModel(
            noteData: $noteData,
            tagsData: $this->getNotesTagsData(data: [$noteData]),
        );
    }

    public function deleteNote(Note $note, array &$errors = []): bool
    {
        try {
            $this->noteRepository->deleteNote(id: $note->getId());
        } catch (Throwable $e) {
            $errors[] = 'Ошибка удаления заметки';
            /** TODO логирование ошибки @param $e */
            return false;
        }
        return true;
    }

    public function getTags(TagFilter $filter): Tags
    {
        $tagsData = $this->tagRepository->getTagsData(filter: $filter);

        return $this->modelBuilder->buildTagsModel(tagsData: $tagsData);
    }

    public function getTagById(TagFilter $filter): ?Tag
    {
        return $this->modelBuilder->buildTagModel(
            tagData: $this->tagRepository->getTagData(filter: $filter)
        );
    }

    public function storeForm(
        int $userId,
        array $formData,
        array &$messages = [],
        array &$errors = [],
    ): ?Note {
        $noteInput = $formData['Note'] ?? [];
        if (!$this->validateNoteData(
            data: $noteInput,
            errors: $errors
        )) {
            return null;
        }

        $tagsIds = $noteInput['tags'] ?? null;
        unset($noteInput['tags']);

        try {
            $noteInput['user_id'] = $userId;
            if ($noteInput['id'] ?? null) {
                $noteInput['id'] = (int)$noteInput['id'];
            }
            $entity = NoteEntity::make(...$noteInput);

            $this->noteRepository->saveNote(entity: $entity);
        } catch (Throwable $e) {
            $errors[] = 'Ошибка сохранения заметки';
            /** TODO логирование ошибки @param $e */
            return null;
        }

        $this->addNoteRelations(
            noteId: $entity->getId(),
            tagsIds: $tagsIds,
            errors: $errors,
        );

        return $this->modelBuilder->buildNoteModel(
            noteData: $entity,
            tagsData: $this->getNotesTagsData(data: [$entity->toArray()]),
            messages: $messages,
            errors: $errors
        );
    }

    public function storeTagForm(
        array $formData,
        array &$messages = [],
        array &$errors = [],
    ): ?Tag {
        $tagInput = $formData['Tag'] ?? [];
        if (!$this->validateTagData(
            data: $tagInput,
            errors: $errors
        )) {
            return null;
        }

        $title = $tagInput['title'] ?? null;

        if ($this->tagRepository->countTags(
            filter: TagFilter::make(title: $title)
        )) {
            $errors[] = 'Тег (' . $title . ') уже существует!';
            return null;
        }

        try {
            if ($tagInput['id'] ?? null) {
                $tagInput['id'] = (int)$tagInput['id'];
            }
            $entity = TagEntity::make(...$tagInput);

            $this->tagRepository->saveTag(entity: $entity);
        } catch (Throwable $e) {
            $errors[] = 'Ошибка сохранения тега';
            /** TODO логирование ошибки @param $e */
            return null;
        }

        return $this->modelBuilder->buildTagModel(
            tagData: $entity,
            messages: $messages,
            errors: $errors
        );
    }

    public function getStoreForm(
        int $userId,
        ?Note $note = null,
        array $formData = [],
        array &$messages = [],
        array &$errors = [],
    ): NoteForm {
        $allTagsData = $this->tagRepository->getTagsData(filter: TagFilter::make());
        return $this->modelBuilder->buildForm(
            userId: $userId,
            note: $note,
            formData: $formData,
            allTagsData: $allTagsData,
            messages: $messages,
            errors: $errors
        );
    }

    public function getStoreTagForm(
        ?Tag $tag = null,
        array $formData = [],
        array &$messages = [],
        array &$errors = [],
    ): TagForm {
        return $this->modelBuilder->buildTagForm(
            tag: $tag,
            formData: $formData,
            messages: $messages,
            errors: $errors
        );
    }

    private function validateNoteData(array $data, array &$errors = []): bool
    {
        return $this->noteValidator->validate(input: $data, errors: $errors);
    }

    private function validateTagData(array $data, array &$errors = []): bool
    {
        return $this->tagValidator->validate(input: $data, errors: $errors);
    }

    private function getNotesTagsData(?array $data): ?array
    {
        if (!$data) {
            return null;
        }

        $noteIds = [];
        foreach ($data as $note) {
            $id = $note['id'] ?? null;
            if (!$id) {
                continue;
            }
            $noteIds[] = $id;
        }

        if (!$noteIds) {
            return null;
        }

        $relations = $this->tagRepository->getRelationsData(
            filter: TagRelationFilter::make(noteIds: $noteIds)
        );

        if (!$relations) {
            return null;
        }

        $ids = [];
        foreach ($relations as $relation) {
            $id = $relation['tag_id'] ?? null;
            if (!$id) {
                continue;
            }
            $ids[] = $id;
        }

        $tagsData = $this->tagRepository->getTagsData(
            filter: TagFilter::make(ids: $ids)
        );

        if (!$tagsData) {
            return null;
        }

        $result = [];
        foreach ($relations as $relation) {
            $tagId = $relation['tag_id'] ?? null;
            $noteId = $relation['note_id'] ?? null;
            if (!$tagId || !$noteId) {
                continue;
            }

            $key = array_key_first(array_filter(
                $tagsData,
                fn(array $current) => $tagId == $current['id'] ?? null,
                ARRAY_FILTER_USE_BOTH,
            ));


            $result[$noteId][] = $tagsData[$key];
        }

        return $result;
    }

    private function addNoteRelations(int $noteId, ?array $tagsIds, array &$errors = []): void
    {
        $relationsData = $this->tagRepository->getRelationsData(
            filter: TagRelationFilter::make(
                noteIds: [$noteId],
            ),
        );

        if (!$relationsData && !$tagsIds) {
            return;
        }

        if ($relationsData) {
            $relationsEntities = [];
            foreach ($relationsData as $relationData) {
                $relationsEntities[] = TagRelation::make(
                    tag_id: (int)$relationData['tag_id'],
                    note_id: $noteId,
                );
            }

            try {
                $this->tagRepository->deleteTagRelations(entities: $relationsEntities);
            } catch (Throwable $e) {
                $errors[] = 'Ошибка удаления связей';
                /** TODO логирование ошибки @param $e */
                return;
            }
        }

        if ($tagsIds) {
            $relationsEntities = [];
            foreach ($tagsIds as $id) {
                $relationsEntities[] = TagRelation::make(
                    tag_id: (int)$id,
                    note_id: $noteId,
                );
            }

            try {
                $this->tagRepository->addTagRelations(entities: $relationsEntities);
            } catch (Throwable $e) {
                $errors[] = 'Ошибка сохранения связей';
                /** TODO логирование ошибки @param $e */
                return;
            }
        }
    }

    private function getNoteIdsByTagTitle(?string $tagTitle = null): ?array
    {
        if (!$tagTitle) {
            return null;
        }

        $tagsData = $this->tagRepository->getTagsData(
            filter: TagFilter::make(title: $tagTitle)
        );
        if (!$tagsData) {
            return null;
        }
        $tagIds = [];
        foreach ($tagsData as $tag) {
            $id = $tag['id'] ?? null;
            if (!$id) {
                continue;
            }
            $tagIds[] = $id;
        }
        if (!$tagIds) {
            return null;
        }
        $relations = $this->tagRepository->getRelationsData(
            filter: TagRelationFilter::make(tagIds: $tagIds)
        );

        if (!$relations) {
            return null;
        }
        $notesIds = [];
        foreach ($relations as $relation) {
            $noteId = $relation['note_id'] ?? null;
            if (!$noteId) {
                continue;
            }
            $notesIds[] = $noteId;
        }

        return $notesIds;
    }
}