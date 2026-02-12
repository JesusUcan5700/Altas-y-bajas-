<?php

use yii\db\Migration;

/**
 * Crea la tabla para solicitudes de autorización de acceso
 */
class m250212_000000_create_auth_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%auth_request}}', [
            'id' => $this->primaryKey(),
            'email' => $this->string(255)->notNull(),
            'nombre_completo' => $this->string(255)->notNull(),
            'departamento' => $this->string(255)->null(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('0=Pendiente, 1=Aprobado, 2=Rechazado'),
            'approval_token' => $this->string(255)->notNull()->unique(),
            'magic_link_token' => $this->string(255)->null()->unique(),
            'token_expiry' => $this->integer()->null(),
            'approved_by' => $this->string(255)->null()->comment('Email del aprobador'),
            'approved_at' => $this->integer()->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'last_login' => $this->integer()->null(),
            'login_count' => $this->integer()->defaultValue(0),
        ]);

        // Índices para mejorar búsquedas
        $this->createIndex(
            'idx-auth_request-email',
            '{{%auth_request}}',
            'email'
        );

        $this->createIndex(
            'idx-auth_request-status',
            '{{%auth_request}}',
            'status'
        );

        $this->createIndex(
            'idx-auth_request-approval_token',
            '{{%auth_request}}',
            'approval_token'
        );

        $this->createIndex(
            'idx-auth_request-magic_link_token',
            '{{%auth_request}}',
            'magic_link_token'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%auth_request}}');
    }
}
