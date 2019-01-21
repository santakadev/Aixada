<?php

require_once(__ROOT__ . 'src/Repository/MySqlMemberRepository.php');
require_once(__ROOT__ . 'src/Repository/MySqlUserRepository.php');
require_once __ROOT__ . 'src/UseCase/CreateMember.php';

use Aixada\Repository\MySqlMemberRepository;
use Aixada\Repository\MySqlUserRepository;
use Aixada\UseCase\CreateMember;

/**
 * Execute a stored query
 * @param array $args the arguments to be passed to the stored query; possibly empty
 * @return the result set
 */
function do_stored_query()
{
    $functionCallArguments = func_get_args();
    $storedQueryArguments = prepare_stored_query_arguments($functionCallArguments);

    switch ($storedQueryArguments[0]) {
        case 'new_user_member':
            (new CreateMember(new MySqlMemberRepository(), new MySqlUserRepository()))->__invoke($storedQueryArguments);
            break;
        default:
            $strSQL = prepare_stored_query($storedQueryArguments);
            return DBWrap::get_instance()->do_stored_query($strSQL);
    }
}

/**
 * @param array $args
 * @return array
 */
function prepare_stored_query_arguments(array $args)
{
    if (is_array($args[0])) {
        $args = $args[0];
    }
    for ($i = 1; $i < count($args); ++$i) {
        if (is_array($args[$i])) {
            $args[$i] = $args[$i][0];
        }
    }
    return $args;
}

/**
 * @param array $args
 * @return string
 * @throws DataException
 */
function prepare_stored_query(array $args)
{
    $sql_func = array_shift($args);

    $strSQL = 'CALL ' . $sql_func . '(';
    foreach ($args as $arg) {
        if (strpos($arg, "'") !== false) {
            if (strpos($arg, '"') !== false)
                throw new DataException('Cannot use both symbols \' and " in text');
            $strSQL .= '"' . $arg . '",';
        } else
            $strSQL .= "'" . $arg . "',";
    }
    if (count($args))
        $strSQL = rtrim($strSQL, ',');
    $strSQL .= ')';
    return $strSQL;
}
