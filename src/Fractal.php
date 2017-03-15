<?php
namespace CroudTech\Repositories;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class Fractal
{

    /**
     * Fractal manager
     * @var Manager
     */
    public $manager;

    /**
     * Construct method
     *
     * @param Request $request Request
     */
    public function __construct(Request $request = null)
    {
        $this->manager = new Manager();
        //$this->manager->setSerializer(new JsonApiSerializer());

        if ($request && !empty($request->include)) {
            $this->manager->parseIncludes($request->include);
        }
    }

    /**
     * Fractal transform a single item
     *
     * @param object $item $item object
     * @param string $transformer Namespaced Transformer
     * @param string $model_name Model Name
     * @return array
     */
    public function item($item, $transformer, $model_name = '')
    {
        $resource = new Item($item, $transformer, $model_name);
        return $this->manager->createData($resource)->toArray();
    }

    /**
     * Fractal transform a collection
     *
     * @param object $items $items
     * @param string $transformer Namespaced Transformer
     * @param string $model_name Model Name
     * @return array
     */
    public function collection($items, $transformer, $model_name = '', $meta = [])
    {
        $resource = new Collection($items, $transformer, $model_name);

        $resource->setMeta($meta);
        if ($items instanceof LengthAwarePaginator) {
            $resource->setPaginator(new IlluminatePaginatorAdapter($items));
        }

        return $this->manager->createData($resource)->toArray();
    }

    /**
     * Transforms api request data to schema data
     *
     * @param array $data request data
     * @param string $transformer Namespaced Transformer
     * @return array
     */
    public static function request($data, $transformer)
    {
        return $transformer::request($data);
    }

    /**
     * Static fractal transform a single item
     *
     * @param \Eloquent $record Record to transform
     * @param array [$includes=[]] Includes
     * @return array
     */
    public static function transform($record, $includes = [])
    {
        $model_name = class_basename(get_class($record));
        $transformer = sprintf('\App\Transformers\%sTransformer', $model_name);

        $fractal = new self();

        if ($includes) {
            $fractal->manager->parseIncludes($includes);
        }

        try {
            return $fractal->item($record, new $transformer, $model_name);
        } catch (\Exception $e) {
            //Transformer class doesn't exist
            return [];
        }
    }

    /**
     * Static fractal transform a single item
     *
     * @param \Eloquent $record Record to transform
     * @param array [$includes=[]] Includes
     * @return array
     */
    public static function transformExport($record, $includes = [], $transformer = null)
    {
        $model_name = class_basename(get_class($record));
        $transformer = is_null($transformer) ? sprintf('\App\Transformers\Export\%sTransformer', $model_name) : $transformer;

        $fractal = new self();

        if ($includes) {
            $fractal->manager->parseIncludes($includes);
        }

        try {
            return $fractal->item($record, new $transformer($includes), $model_name);
        } catch (\Exception $e) {
            //Export class doesn't exist
            return [];
        }
    }
}
