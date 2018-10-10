<?php

namespace luya\news\frontend\controllers;

use Yii;
use luya\news\models\Article;
use luya\news\models\Cat;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/**
 * News Module Default Controller contains actions to display and render views with predefined data.
 *
 * @author Basil Suter <basil@nadar.io>
 */
class DefaultController extends \luya\web\Controller
{
    /**
     * Get Article overview.
     *
     * The index action will return an active data provider object inside the $provider variable:
     *
     * ```php
     * foreach ($provider->models as $item) {
     *     var_dump($item);
     * }
     * ```
     *
     * @return string
     */
    public function actionIndex()
    {
        $provider = new ActiveDataProvider([
            'query' => Article::find()->andWhere(['is_deleted' => false]),
            'sort' => [
                'defaultOrder' => $this->module->articleDefaultOrder,
            ],
            'pagination' => [
                'route' => $this->module->id,
                'params' => ['page' => Yii::$app->request->get('page')],
                'defaultPageSize' => $this->module->articleDefaultPageSize,
            ],
        ]);
        
        return $this->render('index', [
            'model' => Article::className(),
            'provider' => $provider,
        ]);
    }
    
    /**
     * Get all articles for a given categorie ids string seperated by command.
     *
     * @param string $ids The categorie ids: `1,2,3`
     * @return \yii\web\Response|string
     */
    public function actionCategories($ids)
    {
        $ids = explode(",", Html::encode($ids));
        
        if (!is_array($ids)) {
            return $this->goHome();
        }
        
        $provider = new ActiveDataProvider([
            'query' => Article::find()->where(['in', 'cat_id', $ids])->andWhere(['is_deleted' => false]),
            'sort' => [
                'defaultOrder' => $this->module->articleDefaultOrder,
            ],
            'pagination' => [
                'route' => $this->module->id,
                'params' => ['page' => Yii::$app->request->get('page')],
                'defaultPageSize' => $this->module->articleDefaultPageSize,
            ],
        ]);
        
        return $this->render('categories', [
            'provider' => $provider,
        ]);
    }

    /**
     * Get the category Model for a specific ID.
     *
     * The most common way is to use the active data provider object inside the $provider variable:
     *
     * ```php
     * foreach ($provider->getModels() as $cat) {
     *     var_dump($cat);
     * }
     * ```
     *
     * Inside the Cat Object you can then retrieve its articles:
     *
     * ```php
     * foreach ($model->articles as $item) {
     *
     * }
     * ```
     *
     * or customize the where query:
     *
     * ```php
     * foreach ($model->getArticles()->where(['timestamp', time())->all() as $item) {
     *
     * }
     * ```
     *
     * @param integer $categoryId
     * @return \yii\web\Response|string
     */
    public function actionCategory($categoryId = null, $slug = null)
    {
        echo "cat" . $categoryId . " " . $slug;
        if ($categoryId === null && $slug === null) {
            return $this->goHome();
        }
        if ($categoryId !== null) {
            $model = Cat::findOne($categoryId);
        } else {
            $model = Cat::find()->i18nWhere('slug', $slug)->one();
        }
        
        if (!$model) {
            return $this->goHome();
        }
        
        $provider = new ActiveDataProvider([
            'query' => $model->getArticles(),
            'sort' => [
                'defaultOrder' => $this->module->categoryArticleDefaultOrder,
            ],
            'pagination' => [
                'route' => $this->module->id,
                'params' => ['page' => Yii::$app->request->get('page')],
                'defaultPageSize' => $this->module->categoryArticleDefaultPageSize,
            ],
        ]);
        
        return $this->render('category', [
            'model' => $model,
            'provider' => $provider,
        ]);
    }
    
    /**
     * Detail Action of an article by Id.
     *
     * @param integer $id
     * @param string $title
     * @return \yii\web\Response|string
     */
    public function actionDetail($id = null, $title = null, $category = null, $slug = null)
    {
        echo "det" . $id . " " . $slug;
        if ($id === null && $slug === null) {
            return $this->goHome();
        }
        if ($id !== null) {
            $model = Article::findOne(['id' => $id, 'is_deleted' => false]);
        } else {
            $model = Article::find()->where(['is_deleted' => false])->i18nWhere('slug', $slug)->one();
        }
        
        if (!$model) {
            return $this->goHome();
        }
        
        return $this->render('detail', [
            'model' => $model,
        ]);
    }
}
