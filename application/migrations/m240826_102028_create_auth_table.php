<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%auth}}`.
 */
class m240826_102028_create_auth_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string()->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->notNull(),
            'email' => $this->string()->notNull()->unique(),
            'name' => $this->string()->null(),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'create_at' => $this->dateTime()->notNull(),
            'update_at' => $this->dateTime()->notNull(),
        ]);

        $this->createTable('{{%auth}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'social_id' => $this->string()->notNull(),
            'client' => $this->string()->notNull(),
            'access_token' => $this->text()->null(),
            'refresh_token' => $this->text()->null(),
            'device_id' => $this->text()->null(),
            'expired_date_time' => $this->dateTime()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-auth-user_id-users-id',
            '{{%auth}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex('index-users-create_at', '{{%users}}', '`create_at` DESC');
        $this->createIndex('index-users-username', '{{%users}}', 'username');
        $this->createIndex('index-users-email', '{{%users}}', 'email');
        $this->createIndex('index-auth-social_id', '{{%auth}}', 'social_id');
        $this->createIndex('index-auth-client', '{{%auth}}', 'client');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%auth}}');
        $this->dropTable('{{%users}}');
    }
}
