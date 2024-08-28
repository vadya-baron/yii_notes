<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%notes}}`.
 */
class m240826_102628_create_notes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%notes}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'title' => $this->string('255')->notNull(),
            'description' => $this->text()->notNull(),
            'create_at' => $this->dateTime()->notNull(),
            'update_at' => $this->dateTime()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-notes-user_id-users-id',
            '{{%notes}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex('index-notes-create_at', '{{%notes}}', '`create_at` DESC');
        $this->createIndex('index-notes-title', '{{%notes}}', 'title');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-notes-user_id-users-id', '{{%notes}}');
        $this->dropTable('{{%notes}}');
    }
}
