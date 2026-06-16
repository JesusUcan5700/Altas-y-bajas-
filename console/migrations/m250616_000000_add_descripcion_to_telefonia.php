<?php

use yii\db\Migration;

class m250616_000000_add_descripcion_to_telefonia extends Migration
{
    public function safeUp()
    {
        $this->addColumn('telefonia', 'DESCRIPCION', $this->string(255)->defaultValue(null));
    }

    public function safeDown()
    {
        $this->dropColumn('telefonia', 'DESCRIPCION');
    }
}
