<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

/**
 * Class m180409_101810_twits
 */
class m180409_101810_twits extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180409_101810_twits cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('twits', [
            'id' => Schema::TYPE_PK,
            'url' => Schema::TYPE_STRING . ' NOT NULL',
            'channel_id' => Schema::TYPE_INTEGER,
            'retweet' => Schema::TYPE_BOOLEAN,
            'date' => Schema::TYPE_TIMESTAMP,
        ]);

        $this->createTable('twit_text', [
            'id' => Schema::TYPE_PK,
            'twit_id' => Schema::TYPE_INTEGER,
            'txt' => Schema::TYPE_TEXT,
        ]);

        $this->createTable('channels', [
            'id' => Schema::TYPE_PK,
            'url' => Schema::TYPE_STRING . ' NOT NULL',
            'active' => Schema::TYPE_BOOLEAN,
            'name' => Schema::TYPE_STRING
        ]);

        $this->createTable('breaks', [
            'id' => Schema::TYPE_PK,
            'word' => Schema::TYPE_STRING . ' NOT NULL',
            'group_id' => Schema::TYPE_INTEGER,
        ]);

        $this->createTable('break_groups', [
            'id' => Schema::TYPE_PK,
            'channel_id' => Schema::TYPE_INTEGER,
            'color' => Schema::TYPE_STRING,
        ]);

        $this->createTable('checked', [
            'id' => Schema::TYPE_PK,
            'twit_id' => Schema::TYPE_INTEGER,
            'break_id' => Schema::TYPE_INTEGER,
        ]);

        $this->addForeignKey(
            'fk-twitstxt',
            'twit_text',
            'twit_id',
            'twits',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-twits_channels',
            'twits',
            'channel_id',
            'channels',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-break_channels',
            'break_groups',
            'channel_id',
            'channels',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-break_groups',
            'breaks',
            'group_id',
            'break_groups',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-check_break',
            'checked',
            'break_id',
            'breaks',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-check_twits',
            'checked',
            'twit_id',
            'twits',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        echo "m180409_101810_twits cannot be reverted.\n";

        return false;
    }
}
