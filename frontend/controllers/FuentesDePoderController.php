<?php
namespace frontend\controllers;

use Yii;
use frontend\models\FuentesDePoder;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class FuentesDePoderController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'eliminar-multiple' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $model = new FuentesDePoder();
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => FuentesDePoder::find(),
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new FuentesDePoder();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->idFuentePoder]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->idFuentePoder]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    public function actionEliminarMultiple()
    {
        $ids = Yii::$app->request->post('ids');
        if (!empty($ids) && is_array($ids)) {
            foreach ($ids as $id) {
                $model = FuentesDePoder::findOne($id);
                if ($model !== null) {
                    $model->delete();
                }
            }
            Yii::$app->session->setFlash('success', 'Las fuentes de poder seleccionadas han sido eliminadas.');
        } else {
            Yii::$app->session->setFlash('error', 'No se seleccionaron fuentes de poder para eliminar.');
        }
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = FuentesDePoder::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('La fuente de poder no existe.');
    }
}
