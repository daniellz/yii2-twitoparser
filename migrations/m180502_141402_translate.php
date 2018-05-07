<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

/**
 * Class m180502_141402_translate
 */
class m180502_141402_translate extends Migration
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
        echo "m180502_141402_translate cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('twit_translates', [
            'id' => Schema::TYPE_PK,
            'twit_id' => Schema::TYPE_INTEGER,
            'txt' => Schema::TYPE_TEXT,
        ]);

        $this->addForeignKey(
            'fk-twitstrans',
            'twit_translates',
            'twit_id',
            'twits',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        echo "m180502_141402_translate cannot be reverted.\n";

        return false;
    }
}
