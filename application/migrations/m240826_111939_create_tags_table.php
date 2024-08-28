<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tags}}`.
 */
class m240826_111939_create_tags_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tags}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string('255')->unique(),
        ]);

        $this->createTable('{{%relations}}', [
            'tag_id' => $this->integer()->notNull(),
            'note_id' => $this->integer()->notNull(),
        ]);

        $this->createIndex('index-tags-title', '{{%tags}}', 'title');

        $this->addForeignKey(
            'fk-relations-tag_id-tags-id',
            '{{%relations}}',
            'tag_id',
            '{{%tags}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-relations-note_id-notes-id',
            '{{%relations}}',
            'note_id',
            '{{%notes}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-relations-tag_id-tags-id', '{{%relations}}');
        $this->dropForeignKey('fk-relations-note_id-notes-id', '{{%relations}}');
        $this->dropTable('{{%relations}}');
        $this->dropTable('{{%tags}}');
    }
}
