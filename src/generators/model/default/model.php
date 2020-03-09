<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $properties array list of properties (property => [type, name. comment]) */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use Yii;
/**
* @OA\Schema(
*      schema="<?= $className ?>",
<?php if (isset($oasSchemas['required'])): ?>
*      required={<?= rtrim(ltrim(json_encode($oasSchemas['required']),'['),']') ?>},
<?php endif; ?>
<?php foreach ($oasSchemas['properties'] as $property): ?>
<?php if (in_array($property['property'],['created_at','created_by','updated_at','updated_by','deleted_at'])):
continue;
endif; ?>
*     @OA\Property(
*        property="<?= $property['property'] ?>",
*        description="<?= ($property['description'] ? strtr($property['description'], ["\n" => ' ']) : '')?>",
*        type="<?= $property['type'] ?>",
<?php if (isset($property['format'])): ?>
*        format="<?= $property['format']?>",
<?php endif; ?>
<?php if (isset($property['maxLength'])): ?>
*        maxLength=<?= $property['maxLength']?>,
<?php endif; ?>
<?php if ($property['default'] !==null): ?>
*        default=<?= '"'. $property['default'].'"'?>,
<?php endif; ?>
<?php if (isset($property['enum'])): ?>
*        enum={<?= rtrim(ltrim(json_encode($property['enum']),'['),']') ?>}
<?php endif; ?>
*    ),
<?php endforeach; ?>
* )
*/

/**
 * This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($properties as $property => $data): ?>
 * @property <?= "{$data['type']} \${$property}"  . ($data['comment'] ? ' ' . strtr($data['comment'], ["\n" => ' ']) : '') . "\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)): ?>
 *
<?php foreach ($relations as $name => $relation): ?>
 * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php endforeach; ?>
<?php endif; ?>
 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->baseClass, '\\') . "\n" ?>
{
    public function behaviors()
    {
        return [
            [
                'class' => \yii2tech\ar\softdelete\SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'deleted_at' =>  time(),
                ],
                'restoreAttributeValues' => [
                    'deleted_at' => 0
                ]
//                'replaceRegularDelete' => true
            ],
            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' =>  time(),
            ],
            [
                'class' => \yii\behaviors\BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }
<?php if ($generator->db !== 'db'): ?>

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [<?= empty($rules) ? '' : ("\n            " . implode(",\n            ", $rules) . ",\n        ") ?>];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
<?php foreach ($labels as $name => $label): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endforeach; ?>
        ];
    }

<?php if ($queryClassName): ?>
<?php
    $queryClassFullName = ($generator->ns === $generator->queryNs) ? $queryClassName : '\\' . $generator->queryNs . '\\' . $queryClassName;
    echo "\n";
?>
    /**
     * {@inheritdoc}
     * @return <?= $queryClassFullName ?> the active query used by this AR class.
     */
    public static function find()
    {
        return new <?= $queryClassFullName ?>(get_called_class());
    }
<?php endif; ?>

    public static function find()
    {
    $query = parent::find();

    $query->attachBehavior('softDelete', \yii2tech\ar\softdelete\SoftDeleteQueryBehavior::className());

    return $query->notDeleted();
    }

    public function fields()
    {
        $fields = parent::fields();
        $customFields = [
            'created_at' => function ($model) {
                return \Yii::$app->formatter->asDatetime($model->created_at,'php:c');
            },
            'updated_at' => function ($model) {
                return \Yii::$app->formatter->asDatetime($model->updated_at,'php:c');
            },
        ];
        unset($fields['deleted_at']);

        return \yii\helpers\ArrayHelper::merge($fields, $customFields);
    }

    public function extraFields()
    {
        return [
            'creator',
            'updater'
        ];
    }
<?php foreach ($relations as $name => $relation): ?>

    /**
    * @return \yii\db\ActiveQuery
    */
    public function get<?= $name ?>()
    {
    <?= $relation[0] . "\n" ?>
    }
<?php endforeach; ?>

    public function getCreator()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    public function getUpdater()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

}
