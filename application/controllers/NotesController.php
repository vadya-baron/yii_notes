<?php

namespace app\controllers;

use components\notes\filters\NoteFilter;
use components\notes\filters\TagFilter;
use components\notes\interfaces\INotes;
use yii\web\Response;

class NotesController extends BaseController
{
    protected INotes $notes;
    public function __construct(
        $id,
        $module,
        INotes $notes,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->notes = $notes;
    }

    public function beforeAction($action)
    {
        if (!$this->userId) {
            return $this->redirect('/auth/login');
        }
        return parent::beforeAction($action);
    }

    public function actionIndex(): Response|string
    {
        $this->setTitle('Заметки');

        $model = $this->notes->getNotes(
            filter: $this->getNoteFilter()
        );

        return $this->render(
            'index',
            ['model' => $model, 'title' => 'Заметки']
        );
    }

    public function actionEdit(int $id): Response|string
    {
        if (!$id) {
            $this->throwBadRequest('Не передан ID заметки');
        }

        $note = $this->notes->getNoteById(
            filter: $this->getNoteFilter()
        );

        if (!$note) {
            $this->throwNotFound('Заметка не найдена');
        }
        $form = $this->notes->getStoreForm(
            userId: $this->userId,
            note: $note,
        );
        $this->setTitle(
            $form->getTitle()
        );

        return $this->render(
            'store',
            [
                'model' => $form,
                'title' => $form->getTitle(),
            ]
        );
    }

    public function actionDelete(int $id): Response|string
    {
        if (!$id) {
            $this->throwBadRequest('Не передан ID заметки');
        }

        $note = $this->notes->getNoteById(
            filter: $this->getNoteFilter()
        );

        if (!$note) {
            $this->throwNotFound('Заметка не найдена');
        }

        $this->notes->deleteNote(note: $note);

        return $this->redirect('/notes');
    }

    public function actionStore(): Response|string
    {
        $this->setTitle('Добавление заметки');
        $formData = $this->getPost();

        $note = null;
        $messages = [];
        $errors = [];
        if ($formData) {
            $note = $this->notes->storeForm(
                userId: $this->userId,
                formData: $formData,
                messages: $messages,
                errors: $errors,
            );
        }

        $form = $this->notes->getStoreForm(
            userId: $this->userId,
            note: $note,
            formData: $formData,
            messages: $messages,
            errors: $errors,
        );

        if ($note && $note->getId()) {
            return $this->redirect('/notes/edit?id=' . $note->getId());
        }
        return $this->render(
            'store',
            ['model' => $form, 'title' => 'Добавление заметки']
        );
    }

    public function actionEditTag(int $id): Response|string
    {
        if (!$id) {
            $this->throwBadRequest('Не передан ID тега');
        }

        $tag = $this->notes->getTagById(
            filter: $this->getTagFilter()
        );
        if (!$tag) {
            $this->throwNotFound('Тег не найден');
        }
        $form = $this->notes->getStoreTagForm(
            tag: $tag,
        );
        $this->setTitle(
            $form->getTitle()
        );

        return $this->render(
            'store-tag',
            [
                'model' => $form,
                'title' => $form->getTitle(),
            ]
        );
    }

    public function actionStoreTag(): Response|string
    {
        $this->setTitle('Добавление тега');
        $formData = $this->getPost();

        $tag = null;
        $messages = [];
        $errors = [];
        if ($formData) {
            $tag = $this->notes->storeTagForm(
                formData: $formData,
                messages: $messages,
                errors: $errors,
            );
        }

        $form = $this->notes->getStoreTagForm(
            tag: $tag,
            formData: $formData,
            messages: $messages,
            errors: $errors,
        );

        if ($tag && $tag->getId()) {
            return $this->redirect('/notes/edit-tag?id=' . $tag->getId());
        }
        return $this->render(
            'store-tag',
            ['model' => $form, 'title' => 'Добавление тега']
        );
    }

    private function getNoteFilter(): NoteFilter
    {
        $query = $this->getQuery();

        $filter = NoteFilter::make(
            userId: $this->userId,
        );

        $id = $query['id'] ?? null;
        if ($id) {
            $filter->addId((int)$id);
        }

        $ids = $query['ids'] ?? null;
        if ($ids && is_array($ids)) {
            $filter->addIds($ids);
        }

        $sort = $query['sort'] ?? null;
        if ($sort) {
            $filter->addSort($sort);
        }

        $search = $query['search'] ?? null;
        if ($search) {
            $filter->addSearch($search);
        }

        $tag = $query['tag'] ?? null;
        if ($tag) {
            $filter->addTagTitle($tag);
        }

        $limit = $query['limit'] ?? null;
        if ($limit) {
            $filter->addLimit((int)$limit);
        }

        $offset = $query['offset'] ?? null;
        if ($offset) {
            $filter->addOffset((int)$offset);
        }

        return $filter;
    }

    private function getTagFilter(): TagFilter
    {
        $query = $this->getQuery();

        $filter = TagFilter::make();

        $id = $query['id'] ?? null;
        if ($id) {
            $filter->addId((int)$id);
        }

        $ids = $query['ids'] ?? null;
        if ($ids && is_array($ids)) {
            $filter->addIds($ids);
        }

        $noteIds = $query['note_ids'] ?? null;
        if ($noteIds && is_array($noteIds)) {
            $filter->addNoteIds($noteIds);
        }

        $title = $query['title'] ?? null;
        if ($title) {
            $filter->addTitle($title);
        }

        return $filter;
    }
}
