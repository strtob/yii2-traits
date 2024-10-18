# Yii2 Validity Trait

A Yii2 trait for determining the validity stage of a date range based on two date properties: `valid_from` and `valid_until`. This trait categorizes the validity state into three stages and provides corresponding messages and Awesome icons.

## Installation

To include the `ValidityTrait` in your Yii2 project, you can either clone this repository or add it as a dependency in your `composer.json` file. Assuming you have Composer installed, you can run:

```bash
composer require strtob/yii2-traits
```

Example of use as yii2 gridview column:

```bash
    [
            'attribute' => 'status',                
            'label' => yii::t('app', 'Status'), 
            'format' => 'raw',
            'value' => function ($model) {
                $v = $model->validityStage;

                $r = '<div class="d-flex"><div>';
                $r .= '<i class="' . $v->icon . ' me-2"></i>';
                $r .= '</div>';
                $r .= '<div>';
                $r .= $v->message;
                $r .= '<div><small>' . $v->relative_time . '</small></div>';
                $r .= '</div></div>';
                return $r;
            },
        ],
```
