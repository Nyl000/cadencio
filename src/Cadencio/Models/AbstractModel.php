<?php

namespace Cadencio\Models;

use Cadencio\Adapters\MysqlAdapter;
use Cadencio\Services\HookHandler;

abstract class AbstractModel
{

    protected static $adapter = false;

    protected $modelName;
    protected $resourceName;
    private $tableProperties = false;
    private $from;
    private $group;
    private $having;
    private $query_parameters = [];
    private $where = [];
    protected $identifier = 'id';
    private $paging = true;
    protected $publicFields = false;


    public function __construct()
    {
        if (!self::$adapter) {
            self::$adapter = new MysqlAdapter();
        }
        if (!isset($this->modelName)) {
            throw new \Exception('missing modelName');
        }
        $this->from = $this->modelName;
        $this->setGroup('');
        $this->setHaving('');
        $this->init();
    }


    public function isPaging()
    {
        return $this->paging;
    }


    public function setPaging($paging)
    {
        $this->paging = $paging;
    }

    public function where($condition, array $parameters) {

        $this->where[] = ['condition' => $condition,'parameters' => $parameters];

    }

    public function addQueryParameter($parameter) {
        $this->query_parameters[] = $parameter;
    }

    protected function addRelation($modelName, $idProperty, $idDistant = 'id' ,$type = 'INNER JOIN',$alias = false , $moreCondition = '',$linkTo = '') {
        $this->from .=' '.$type.' '.$modelName.' '.($alias ? ' AS '.$alias : '').' ON `'.($alias ? $alias:$modelName).'`.`'.$idDistant.'` = `'.(empty($linkTo) ? $this->modelName : $linkTo).'`.`'.$idProperty.'` '.$moreCondition;
    }

    protected function getDefaultOrder() {
        return '`'.$this->modelName.'`.`'.$this->identifier.'`';
    }

    public function massEdit($idArray, $key,$value) {
        foreach ($idArray as $id) {
            $object = [$key => $value];
            $this->patch($id,$object);
        }
    }

    public function getFrom() {
        return $this->from;
    }
    public function getGroup() {
        return $this->group;
    }

    public function setGroup($groupBy) {
        $this->group = $groupBy;
    }

    public function getHaving() {
        return $this->having;
    }

    public function setHaving($having) {
        $this->having = $having;
    }

    public function getResourceName() {
        return $this->resourceName;
    }

    public function getPublicFields() {
        if(!$this->publicFields) {
            return [$this->modelName.'.*'];
        }
        return $this->publicFields;
    }

    public function setPublicFields($fields) {
        $this->publicFields = $fields;
    }

    public function getExportFields() {
        return [$this->modelName.'.*'];
    }

    public function getOrderFields()
    {
        $fields = $this->getTableProperties();
        $out = [];
        foreach($fields as $field) {
            $out[$field] = $this->modelName.'.'.$field;
        }
        return $out;
    }

    protected function getAdapter()
    {
        return self::$adapter;
    }


    public function init()
    {

    }

    public function getTableProperties()
    {
        if (!$this->tableProperties) {
            $this->tableProperties = $this->getAdapter()->fetchColumn('DESCRIBE ' . $this->modelName, array());
        }

        return $this->tableProperties;
    }

    public function getEmpty()
    {
        $array = [];
        foreach ($this->getTableProperties() as $property) {
            $array[$property] = '';
        }
        return $array;
    }

    public function createOrUpdate($datas)
    {
        if (is_object($datas)) {
            $datas = (array) $datas;
        }

        if (empty($datas['id'])) {
            unset($datas['id']);
        }
        $fields = $this->getTableProperties();
        $query = 'INSERT INTO ' . $this->modelName;

        $queryParts1 = array();
        $queryParts2 = array();
        $queryParts3 = array();
        $params = array();
        foreach ($datas as $key => $property) {
            $key = strtolower($key);

            if (in_array($key, $fields)) {
                $queryParts1[] = '`' . $key . '`';
                $queryParts2[] = '?';
                $queryParts3[] = '`' . $key . '`=VALUES(`' . $key . '`)';
                $params[] = $property;
            }
        }
        $query .= '(' . implode(',', $queryParts1) . ') VALUES(' . implode(',', $queryParts2) . ') ON DUPLICATE KEY UPDATE ' . implode(',', $queryParts3);

        try {
            $this->getAdapter()->query($query, $params);
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        $id_saved = isset($datas['id']) && $datas['id'] != 0 ? $datas['id'] : $this->getAdapter()->getLastId();
        $hooks = HookHandler::getInstance()->getHook((isset($datas['id']) && $datas['id'] != 0  ? 'do_after_update_':'do_after_insert_').$this->getModelName());
        foreach($hooks as $hook) { $hook($id_saved); }
        return $id_saved;

    }

    public function getModelName() {
        return $this->modelName;
    }

    public function patch($id, $datas, $uniqueFieldname = 'id')
    {
        if (is_object($datas)) {
            $datas = (array) $datas;
        }

        $fields = $this->getTableProperties();
        $query = 'UPDATE ' . $this->modelName . ' SET ';
        $queryPart1 = array();
        $params = array();
        foreach ($datas as $key => $property) {
            $key = strtolower($key);
            if (in_array($key, $fields)) {
                $queryPart1[] = '`' . $key . '`=?';
                $params[] = $property;
            }
        }
        $query .= implode(',', $queryPart1);

        if (!is_object($id)) {
            $query .= ' WHERE `' . $uniqueFieldname . '`= ?';
            $params[] = $id;
        } else {
            $id = (array)$id;
            $query .= ' WHERE 1=1 ';
            foreach ($id as $fieldWhere => $valWhere) {
                $query .= ' AND `' . $fieldWhere . '`= ? ';
                $params[] = $valWhere;
            }
        }
        $q = $this->getAdapter()->query($query, $params);

        $hooks = HookHandler::getInstance()->getHook('do_after_update_'.$this->getModelName());
        foreach($hooks as $hook) { $hook($id); }

    }

    public function idExists($id, $field = 'id')
    {
        if (!in_array($field, $this->getTableProperties())) {
            throw new \Exception('unknown filter error');
        }
        $row = $this->getAdapter()->fetchRow('SELECT '.$this->identifier.' FROM ' . $this->modelName . ' WHERE ' . $field . ' = ?', array($id));
        return !$row ? false : true;
    }

    public function delete($id)
    {
        $res = $this->getAdapter()->query('DELETE FROM ' . $this->modelName . ' WHERE id = ?', array($id));
        $hooks = HookHandler::getInstance()->getHook('do_after_delete_'.$this->getModelName());
        return $res;
    }

    public function findBy($properties)
    {
        $query = 'SELECT '. implode(',',$this->getPublicFields()) .' FROM ' . $this->modelName . ' WHERE 1=1 ';
        $params = [];
        foreach ($properties as $key => $val) {
            if (!in_array($key, $this->getTableProperties())) {
                throw new \Exception('unknown filter error');
            }
            $query .= ' AND ' . $key . ' = ?';
            $params[] = $val;
        }

        return $this->getAdapter()->fetchAll($query, $params) ?: [];
    }

    public function findOneBy($properties)
    {
        $datas = $this->findBy($properties);
        return isset($datas[0]) ? $datas[0] : false;
    }

    public function getOne($id, $field = 'id',$ignoreCase = false)
    {
        if (!in_array($field, $this->getTableProperties())) {
            throw new \Exception('unknown filter "'.$field.'". Filter are '.implode(',',$this->getTableProperties()));
        }

        $params = $this->query_parameters;

        $filter = $this->modelName.'.'.$field . ' = ?';
        if ($ignoreCase) {
            $filter = 'LOWER('.$this->modelName.'.'.$field . ') = LOWER(?)';
        }

        $params[] = $id;
        return $this->getAdapter()->fetchRow('SELECT  ' . implode(',',$this->getPublicFields()) . ' FROM ' . $this->from . ' WHERE ' .$filter . ' '. $this->getGroup().' '.$this->getHaving() ,$params);
    }

    public function getAll($options = [])
    {
        $query = $this->getAllQuery($options);
        return $this->getAdapter()->fetchAll($query['query'], $query['params']);
    }

    public function getByIds(array $arrayValues, $field = 'id')
    {
        if (!in_array($field, $this->getTableProperties())) {
            throw new \Exception('unknown filter error');
        }
        $params = array();
        $queryPart = array();
        foreach ($arrayValues as $val) {
            $params[] = $val;
            $queryPart[] = '?';
        }

        return $this->getAdapter()->fetchAll('SELECT  ' . implode(',',$this->getPublicFields()) . ' FROM ' . $this->modelName . ' WHERE `' . $field . '` IN (' . implode(',', $queryPart) . ')', $params);

    }

    public function getSearchField()
    {
        return [];
    }

    public function prepareSearchQuery($search)
    {
        $params = [];
        $query = '';
        $fields = $this->getSearchField();
        if (!empty($fields) && !empty($search)) {
            $query .= ' AND ( 0=1 ';
            foreach ($fields as $field) {
                $query .= ' OR ' . $field . ' LIKE ?';
                $params[] = '%' . $search . '%';
            }
            $query .= ')';
        }

        return [
            'query' => $query,
            'params' => $params
        ];
    }

    public function getFilters() {
        return [];
    }

    public function prepareQuery($options, $countOnly = false)
    {

        if ($countOnly) {
            $select = 'SELECT '.$this->modelName.'.'.$this->identifier;
        } else {
            $select = 'SELECT  ' . implode(',',$this->getPublicFields());
        }

        $from = ' FROM ' . $this->from;

        $where = ' WHERE 1=1';

        $parameters = $this->query_parameters;
        if (isset($options['search'])) {
            $s = $this->prepareSearchQuery($options['search']);
            $where .= $s['query'];
            $parameters = array_merge($parameters, $s['params']);
        }

        //Order
        $order = '';
        if (isset($options['order']) && in_array($options['order'], array_keys($this->getOrderFields()))) {
            $order .= ' ORDER BY ' .$this->getOrderFields()[$options['order']].' IS NULL, ';
            $order .=  $this->getOrderFields()[$options['order']];
            $order .= isset($options['orderDirection']) && $options['orderDirection'] == 'DESC' ? ' DESC' : ' ASC';
        }
        else {
            $order .= 'ORDER BY '.$this->getDefaultOrder();
        }

        $paging = '';
        //Paging
        if (!$countOnly && $this->paging) {
            $nbItems = isset($options['nbItems']) ? $options['nbItems'] : DEFAULT_PAGINATION;
            $activePage = isset($options['page']) ? $options['page'] : 1;

            $paging .= ' LIMIT ' . ($activePage - 1) * $nbItems . ',' . $nbItems;
        }

        foreach ($this->getFilters() as $key => $val) {
            if (isset($options[$key])) {

                if (is_array($options[$key])) {
                    $subwhere = ' AND ( 1=0 ';
                    foreach ($options[$key] as $option) {
                        $subwhere .= ' OR '.$val. '= ? ';
                        $parameters[] = $option;

                    }
                    $subwhere .= ')';
                    $where .= $subwhere;
                }
                else {
                    $where.= ' AND '.$val.' = ?';
                    $parameters[] = $options[$key];
                }
            }
        }

        foreach($this->where as $whereOption) {
            $where.= ' AND ' .$whereOption['condition'];
            $parameters = array_merge($parameters,$whereOption['parameters']);
        }

        return [
            'select' => $select,
            'from' => $from,
            'where' => $where,
            'order' => $order,
            'paging' => $paging,
            'params' => $parameters
        ];
    }

    public function getAllQuery($options, $countOnly = false)
    {

        $queryParts = $this->prepareQuery($options, $countOnly);

        $query = $queryParts['select'] . $queryParts['from'] . $queryParts['where'] . ' '.  $this->getGroup(). ' ' . $this->getHaving().' '. $queryParts['order'] . $queryParts['paging'];
        if($countOnly) {
            $query = 'SELECT COUNT(*) FROM ('.$query.') as countable';
        }
        return [
            'query' => $query,
            'params' => $queryParts['params']
        ];
    }

    public function getPaginator($options,$methodName = false)
    {
        $nbItems = isset($options['nbItems']) ? $options['nbItems'] : DEFAULT_PAGINATION;
        $activePage = isset($options['page']) ? $options['page'] : 1;

        if ($methodName) {
            if (!method_exists($this,$methodName)) { throw new \RuntimeException('Method '.$methodName.' does not exists');}
            $query =  $this->$methodName($options,true);
        }
        else {
            $query = $this->getAllQuery($options, true);
        }

        $totalRows = $this->getAdapter()->fetchOne($query['query'], $query['params']);

        return [
            'totalPages' => ceil($totalRows / $nbItems),
            'activePage' => $activePage,
            'itemsPerPage' => $nbItems
        ];
    }


    public function buildPaginatedQuery($options,$resourceName = false,$methodName = false) {

        $resourceName = $resourceName ?: $this->modelName;

        if ($methodName) {
            if (!method_exists($this,$methodName)) { throw new \RuntimeException('Method '.$methodName.' does not exists');}
            $request =  $this->$methodName($options);
        }
        else {
            $request = $this->getAllQuery($options);
        }
        return [
            $resourceName => $this->getAdapter()->fetchAll($request['query'],$request['params']),
            'paginator' => $this->getPaginator($options,$methodName)
        ];
    }


}