<?php

declare(strict_types=1);


namespace components\notes\modelBuilders;

use commonComponents\interfaces\ICsrfHelper;
use commonComponents\models\buttons\Button;
use commonComponents\models\fields\Field;
use commonComponents\models\forms\Form;
use components\notes\entities\Note as NoteEntity;
use components\notes\entities\Tag as TagEntity;
use components\notes\interfaces\IModelBuilder;
use components\notes\models\Note;
use components\notes\models\NoteForm;
use components\notes\models\Notes;
use components\notes\models\Tag;
use components\notes\models\TagForm;
use components\notes\models\Tags;
use Throwable;


/** TODO Можно подключить локализацию, но не стал тратить время */
class ModelBuilder implements IModelBuilder
{
    public function __construct(protected ICsrfHelper $csrfHelper)
    {
    }

    public function buildNotesModel(
        ?array $notesData = null,
        ?array $tagsData = null,
    ): Notes {
        if (!$notesData) {
            return Notes::make(title: 'Заметки', description: 'Создайте свою первую заметку');
        }

        $items = [];
        foreach ($notesData as $noteData) {
            $note = Note::make(
                id: $noteData['id'] ?? null,
                title: $noteData['title'] ?? null,
                description: $noteData['description'] ?? null,
                userId: $noteData['user_id'] ?? null,
                tags: $this->buildTagsModel(),

            );

            $tags = $this->getTagsByNoteId(
                tagsData: $tagsData[$note->getId()] ?? null,
            );

            if ($tags) {
                $note->addTags(tags: $tags);
            }

            $items[] = $note;
        }

        return Notes::make(
            title: 'Заметки',
            description: 'Создайте новую или обновите заметку',
            items: $items,
        );
    }

    public function buildNoteModel(
        null|array|NoteEntity $noteData = null,
        ?array $tagsData = null,
        array &$messages = [],
        array &$errors = [],
    ): ?Note {
        if (!$noteData) {
            return null;
        }
        if (is_array($noteData)) {
            try {
                $note = Note::make(
                    id: $noteData['id'] ?? null,
                    title: $noteData['title'] ?? null,
                    description: $noteData['description'] ?? null,
                    userId: $noteData['user_id'] ?? null,
                    createAt: $noteData['create_at'] ?? null,
                    updateAt: $noteData['update_at'] ?? null,
                );
            } catch (Throwable $e) {
                $errors[] = $e->getMessage();
                return null;
            }
        } else {
            $note = Note::make(
                id: $noteData->getId(),
                title: $noteData->getTitle(),
                description: $noteData->getDescription(),
                userId: $noteData->getUserId(),
                createAt: $noteData->getCreateAt(),
                updateAt: $noteData->getUpdateAt(),
            );
        }

        $tags = $this->getTagsByNoteId(
            tagsData: $tagsData[$note->getId()] ?? null,
        );
        if ($tags) {
            $note->addTags(tags: $tags);
        }

        return $note;
    }

    public function buildForm(
        int $userId,
        ?Note $note = null,
        ?array $formData = null,
        ?array $allTagsData = null,
        array &$messages = [],
        array &$errors = [],
    ): NoteForm {
        $fields = [
            Field::make(
                type: 'hidden',
                name: $this->csrfHelper->getCsrfFieldName(),
                required: true,
                value: $this->csrfHelper->getCsrfFieldValue(),
                class: 'hidden',
            ),
            Field::make(
                type: 'text',
                name: 'Note[title]',
                required: true,
                value: $note?->getTitle() ?? $formData['Note']['title'] ?? null,
                label: 'Заголовок заметки',
                placeholder: 'Заголовок заметки',
                error: $errors['title'] ?? null,
            ),
            Field::make(
                type: 'textarea',
                name: 'Note[description]',
                required: true,
                value: $note?->getDescription() ?? $formData['Note']['description'] ?? null,
                label: 'Описание заметки',
                placeholder: 'Описание заметки',
                error: $errors['description'] ?? null,
            ),
            Field::make(
                type: 'hidden',
                name: 'Note[create_at]',
                required: false,
                value: $note?->getCreateAt() ?? null,
                class: 'hidden',
            ),
        ];

        if ($allTagsData) {
            $this->addCheckboxGroupFields(
                allTagsData: $allTagsData,
                fields: $fields,
                note: $note,
            );
        }

        $noteId = $note?->getId() ?? null;
        if ($noteId) {
            $fields[] = Field::make(
                type: 'hidden',
                name: 'Note[id]',
                required: true,
                value: (string)$noteId,
                class: 'hidden',
            );
        }

        $form = Form::make(
            method: 'POST',
            action: '/notes/store',
            fields: $fields,
            buttons: [Button::make(
                type: 'submit',
                name: 'store-note-submit',
                label: 'Добавить',
                class: 'btn btn-primary',
            )],
            errors: $errors,
            messages: $messages,
        );

        return NoteForm::make(
            title: $noteId ? 'Редактировать запись' : 'Создать запись',
            description: 'Все поля обязательны для заполнения',
            form: $form,
        );
    }

    public function buildTagsModel(?array $tagsData = null): Tags
    {
        if (!$tagsData) {
            return Tags::make(title: 'Теги', description: 'Создайте свой первый тег');
        }

        $items = [];
        foreach ($tagsData as $tagData) {
            $items[] = Tag::make(
                id: $tagData['id'] ?? null,
                title: $tagData['title'] ?? null,
            );
        }
        return Tags::make(
            title: 'Теги',
            description: 'Создайте новый или обновите тег',
            items: $items,
        );
    }

    public function buildTagModel(
        null|array|TagEntity $tagData = null,
        array &$messages = [],
        array &$errors = [],
    ): ?Tag {
        if (!$tagData) {
            return null;
        }
        if (is_array($tagData)) {
            try {
                $tag = Tag::make(
                    id: $tagData['id'] ?? null,
                    title: $tagData['title'] ?? null,
                );
            } catch (Throwable $e) {
                $errors[] = $e->getMessage();
                return null;
            }
        } else {
            $tag = Tag::make(
                id: $tagData->getId(),
                title: $tagData->getTitle(),
            );
        }

        return $tag;
    }

    public function buildTagForm(
        ?Tag $tag = null,
        ?array $formData = null,
        array &$messages = [],
        array &$errors = [],
    ): TagForm {
        $fields = [
            Field::make(
                type: 'hidden',
                name: $this->csrfHelper->getCsrfFieldName(),
                required: true,
                value: $this->csrfHelper->getCsrfFieldValue(),
                class: 'hidden',
            ),
            Field::make(
                type: 'text',
                name: 'Tag[title]',
                required: true,
                value: $tag?->getTitle() ?? $formData['Tag']['title'] ?? null,
                label: 'Заголовок тега',
                placeholder: 'Заголовок тега',
                error: $errors['title'] ?? null,
            ),
        ];

        $tagId = $tag?->getId() ?? null;
        if ($tagId) {
            $fields[] = Field::make(
                type: 'hidden',
                name: 'Tag[id]',
                required: true,
                value: (string)$tagId,
                class: 'hidden',
            );
        }

        $form = Form::make(
            method: 'POST',
            action: '/notes/store-tag',
            fields: $fields,
            buttons: [Button::make(
                type: 'submit',
                name: 'store-note-submit',
                label: 'Добавить',
                class: 'btn btn-primary',
            )],
            errors: $errors,
            messages: $messages,
        );

        return TagForm::make(
            title: $tagId ? 'Редактировать тег' : 'Создать тег',
            description: 'Все поля обязательны для заполнения',
            form: $form,
        );
    }

    private function getTagsByNoteId(?array $tagsData): ?Tags
    {
        if (!$tagsData) {
            return null;
        }

        $tags = Tags::make();

        foreach ($tagsData as $tagData) {
            $tags->addItem(Tag::make(
                id: $tagData['id'] ?? null,
                title: $tagData['title'] ?? null,
            ));
        }

        if ($tags->getCount() > 0) {
            return $tags;
        }

        return null;
    }

    private function addCheckboxGroupFields(
        array $allTagsData,
        array &$fields,
        ?Note $note = null,
    ): void {
        /** @var Tags|null $noteTags */
        $noteTags = $note?->getTags();

        foreach ($allTagsData as $tagData) {
            $id = $tagData['id'] ?? null;
            $title = $tagData['title'] ?? null;
            $check = (bool)$noteTags?->getItem($id) ?? false;

            $fields[] = Field::make(
                type: 'checkbox',
                name: 'Note[tags][]',
                required: false,
                value: (string)$id,
                label: $title,
                check: $check,
            );
        }
    }
}