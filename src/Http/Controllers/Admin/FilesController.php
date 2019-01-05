<?php

namespace Railken\Amethyst\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Railken\Amethyst\Api\Http\Controllers\RestManagerController;
use Railken\Amethyst\Api\Http\Controllers\Traits as RestTraits;
use Railken\Amethyst\Managers\FileManager;

class FilesController extends RestManagerController
{
    use RestTraits\RestIndexTrait;
    use RestTraits\RestCreateTrait;
    use RestTraits\RestShowTrait;
    use RestTraits\RestUpdateTrait;
    use RestTraits\RestRemoveTrait;

    /**
     * The class of the manager.
     *
     * @var string
     */
    public $class = FileManager::class;

    /**
     * The attributes that are fillable.
     *
     * @param mixed   $id
     * @param Request $request
     */
    public function upload($id, Request $request)
    {
        $entity = $this->getQuery()->where('id', $id)->first();

        if (!$entity) {
            return $this->response(null, Response::HTTP_NOT_FOUND);
        }

        /**
         * @var FileManager
         */
        $manager = $this->getManager();

        if ($request->file('file') === null) {
            return $this->error(['errors' => ['message' => 'Missing file']]);
        }

        $result = $manager->uploadFileByContent(
            $entity,
            $request->file('file')
        );

        if (!$result->ok()) {
            return $this->error(['errors' => $result->getSimpleErrors()]);
        }

        return $this->success(['data' => $this->getManager()->getSerializer()->serialize($result->getResource())->toArray()], 201);
    }
}
