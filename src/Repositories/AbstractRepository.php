<?php


namespace Mvdstam\Oauth2ServerLaravel\Repositories;


use Czim\Repository\BaseRepository;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository extends BaseRepository
{

    /**
     * @param array $data
     * @return Model
     */
    public function forceCreate(array $data = [])
    {
        $model = $this->makeModel(false)->forceFill($data);
        $model->save();

        return $model;
    }

    public function findWhere($where, $columns = ['*'], $or = false)
    {
        // Ensure a collection is returned
        return parent::findWhere($where, $columns, $or) ?: collect();
    }

    public function findMany($ids, $columns = ['*'])
    {
        return $this->query()->findMany($ids, $columns);
    }

}
