<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

/**
 * Class m180415_084429_cyrs
 */
class m180415_084429_cyrs extends Migration
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
        echo "m180415_084429_cyrs cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180415_084429_cyrs cannot be reverted.\n";

        return false;
    }
    */

    public function up()
    {
        $this->createTable('cyrs', [
            'id' => Schema::TYPE_PK,
            'timestamp' => Schema::TYPE_TIMESTAMP . ' NOT NULL',
            'btc_usd' => 'numeric(15, 8) NOT NULL',
            'eth_btc' => 'numeric(15, 8) NOT NULL',
            'eth_usd' => 'numeric(15, 8) NOT NULL',
        ]);
        $this->createTable('twit_cyrs', [
            'id' => Schema::TYPE_PK,
            'twit_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'cyr_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'type'=>'smallint not null default 0'
        ]);

        $this->addForeignKey(
            'fk-cyrstwit',
            'twit_cyrs',
            'twit_id',
            'twits',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-cyrscyrs',
            'twit_cyrs',
            'cyr_id',
            'cyrs',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'twitsts',
            'twits',
            'date'
        );

        $this->createIndex(
            'cyrsts',
            'cyrs',
            'timestamp'
        );
    }

}
