<?php

use yii\db\Migration;

class m250526_000000_expand_descripcion_columns extends Migration
{
    public function safeUp()
    {
        $tables = [
            'telefonia',
            'baterias',
            'impresora',
            'monitor',
            'memoria_ram',
            'adaptadores',
            'almacenamiento',
            'conectividad',
            'fuentes_de_poder',
            'procesador',
            'pila',
            'nobreak',
            'sonido',
            'videovigilancia',
        ];

        foreach ($tables as $table) {
            // Verificar si la tabla existe
            if ($this->db->getTableSchema($table) !== null) {
                // Buscar la columna DESCRIPCION o Descripcion
                $columns = $this->db->getTableSchema($table)->columns;
                $descColumnName = null;

                foreach ($columns as $column) {
                    if (strtolower($column->name) === 'descripcion') {
                        $descColumnName = $column->name;
                        break;
                    }
                }

                if ($descColumnName !== null) {
                    // Cambiar de VARCHAR a TEXT
                    $this->alterColumn($table, $descColumnName, 'TEXT');
                    echo "Tabla {$table}: columna {$descColumnName} modificada a TEXT\n";
                }
            }
        }
    }

    public function safeDown()
    {
        echo "m250526_000000_expand_descripcion_columns cannot be reverted safely.\n";
        return false;
    }
}
