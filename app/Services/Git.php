<?php

namespace App\Services;

/**
 * Класса (объект) ответа, при приведении к строке - преобразуется в JSON
 *
 * Class Result
 * @package App\Services
 */
class Result
{
    public function __toString()
    {
        return json_encode($this);
    }
}

/**
 * Класс для работы с репозиториями
 *
 * Class Git
 * @package App\Services
 */
class Git
{
    /**
     * Функция рекурсивно преобразующая массив в объект (для удобства работы и преобразования в JSON)
     *
     * @param $array
     * @param $object
     * @return object
     */
    public static function setObjectProperties($array, $object): object
    {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $object->{$k} = self::setObjectProperties($v, !empty($object->{$k}) ? $object->{$k} : new \stdClass());
            } else {
                $object->{$k} = $v;
            }
        }

        return $object;
    }

    /**
     * Функция сортировки файлов/директорий в результатах выдачи, в алфавитном порядке (директории в начале)
     *
     * @param mixed ...$args
     * @return array
     */
    public static function multiSort(...$args): array
    {
        $numargs = func_num_args();

        if ($numargs < 2) {
            return [];
        }

        //get the array to sort
        $array = array_splice($args, 0, 1);
        $array = $array[0];

        //sort with an anoymous function using args
        usort($array, function ($a, $b) use ($args, $numargs) {
            $i = 0;
            $cmp = 0;

            while ($cmp == 0 && $i < ($numargs - 1)) {
                $cmp = strcmp($a[$args[$i]], $b[$args[$i]]);
                $i++;
            }

            return $cmp;
        });

        return $array;
    }

    /**
     * Возвращает текущую (активную) ветку репозитория
     *
     * @param $repository Название репозитория
     * @param bool $formatJson флаг, возвращать ли данные в формате JSON (по умолчанию объект)
     * @return Result
     */
    public static function branchCurrent($repository): Result
    {
        $gitRepositoryDir = env('GIT_REP_DIR');

        exec("git --git-dir {$gitRepositoryDir}/{$repository}.git rev-parse --abbrev-ref HEAD", $output, $exitCode);

        if ($exitCode || !count($output)) {
            $data = [
                'error' => true,
                'error_code' => $exitCode,
                'data' => null,
            ];
        } else {
            $data = [
                'error' => false,
                'error_code' => $exitCode,
                'data' => $output[0],
            ];
        }

        $result = new Result();
        self::setObjectProperties($data, $result);

        return $result;
    }

    /**
     * Возвращает список веток в репозитории
     *
     * @param $repositoryName
     * @return mixed
     */
    public static function branchList($repositoryName): Result
    {
        $gitRepositoryDir = env('GIT_REP_DIR');

        exec("git --git-dir {$gitRepositoryDir}/{$repositoryName}.git branch | cut -c 3-", $output, $exitCode);

        if ($exitCode || !count($output)) {
            $data = [
                'error' => true,
                'error_code' => $exitCode,
                'data' => null,
            ];
        } else {
            $data = [
                'error' => false,
                'error_code' => $exitCode,
                'data' => $output,
            ];
        }

        $result = new Result();
        self::setObjectProperties($data, $result);

        return $result;
    }

    /**
     * Возвращает список файлов/директорий в указанном коммите
     * @TODO добавить в список возвращаемых данных - размер файлов
     *
     * @param $repositoryName
     * @param string $commitHash
     * @param bool $recursive
     * @return array|bool
     */
    public static function commitLs($repositoryName, $commitHash = 'HEAD', $recursive = false): Result
    {
        $gitRepositoryDir = env('GIT_REP_DIR');
        $params = '';

        if ($recursive) {
            $params .= '-r'; //-r делает рекурсивный обход
        }

        exec("git --git-dir {$gitRepositoryDir}/{$repositoryName}.git ls-tree {$params} $commitHash", $output, $exitCode);

        if ($exitCode || !count($output)) { //Если возникла ошибка
            $data = [
                'error' => true,
                'error_code' => $exitCode,
                'data' => null,
            ];
        } else {
            $data = [
                'error' => false,
                'error_code' => $exitCode,
                'data' => null,
            ];

            foreach ($output as $v) {
                $arr1 = explode(' ', $v);
                $arr2 = explode("\t", $arr1[2], 2);

                $data['data'][] = [
                    'mode' => $arr1[0],
                    'type' => $arr1[1],
                    'order' => $arr1[1] == 'blob' ? 2 : 1,
                    'hash' => $arr2[0],
                    'path' => $arr2[1],
                    'name' => basename($arr2[1]),
                ];
            }

            $data['data'] = self::multiSort($data['data'], 'order', 'name');
        }

        $result = new Result();
        self::setObjectProperties($data, $result);

        return $result;
    }
}