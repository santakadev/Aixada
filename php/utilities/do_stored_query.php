<?php

/**
 * Execute a stored query
 * @param array $args the arguments to be passed to the stored query; possibly empty
 * @return the result set
 */
function do_stored_query()
{
    $functionCallArguments = func_get_args();
    $storedQueryArguments = prepare_stored_query_arguments($functionCallArguments);

    if ($storedQueryArguments[0] === 'new_user_member') {

        $database = DBWrap::get_instance();
        $database->start_transaction();

        $result = DBWrap::get_instance()->Select('max(id)+1 AS newId', 'aixada_user', 'id < 1000', '');
        $newId = $result->fetch_object()->newId;

        $database->Insert([
            'table' => 'aixada_member',
            'id' => $newId,
            'uf_id' => $storedQueryArguments[3],
            'custom_member_ref' => $storedQueryArguments[4],
            'name' => $storedQueryArguments[5],
            'nif' => $storedQueryArguments[6],
            'address' => $storedQueryArguments[7],
            'city' => $storedQueryArguments[8],
            'zip' => $storedQueryArguments[9],
            'phone1' => $storedQueryArguments[10],
            'phone2' => $storedQueryArguments[11],
            'web' => $storedQueryArguments[12],
            'notes' => $storedQueryArguments[13],
            'active' => $storedQueryArguments[14],
            'participant' => $storedQueryArguments[15],
            'adult' => $storedQueryArguments[16],
        ]);

        $database->Insert([
            'table' => 'aixada_user',
            'id' => $newId,
            'login' => $storedQueryArguments[1],
            'password' => $storedQueryArguments[2],
            'uf_id' => $storedQueryArguments[3],
            'member_id' => $newId,
            'language' => $storedQueryArguments[17],
            'gui_theme' => $storedQueryArguments[18],
            'email' => $storedQueryArguments[19],
            'created_on' => (new \DateTimeImmutable())->format('Y-m-d H:i:s')
        ]);

        $database->Insert([
            'table' => 'aixada_user_role',
            'user_id' => $newId,
            'role' => 'Checkout',
        ]);

        $database->Insert([
            'table' => 'aixada_user_role',
            'user_id' => $newId,
            'role' => 'Consumer',
        ]);

        $database->commit();
        return null;
    }


    $strSQL = prepare_stored_query($storedQueryArguments);
    return DBWrap::get_instance()->do_stored_query($strSQL);
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
