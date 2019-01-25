<?php

require_once(__DIR__.'/../../../php/utilities/general.php');

final class get_param_test extends \PHPUnit\Framework\TestCase
{
    const NON_EXISTING_PARAM = 'non_existing_param';
    const EXISTING_PARAM = 'existing_param';
    const FAMILY_UNIT_PARAM = 'uf_id';
    const USER_ID_PARAM = 'user_id';
    const MEMBER_ID_PARAM = 'member_id';
    const INVALID_TRANSFORMATION = 'invalid-transformation';

    /** @test */
    public function should_throw_exception_for_non_existing_param_without_default_value()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf('get_param: Missing or wrong parameter name: %s in URL', self::NON_EXISTING_PARAM));

        get_param(self::NON_EXISTING_PARAM);
    }

    /** @test */
    public function should_return_default_value_for_non_existing_param()
    {
        $stringResult = get_param(self::NON_EXISTING_PARAM, 'default value');
        $intResult = get_param(self::NON_EXISTING_PARAM, 100);

        $this->assertSame('default value', $stringResult);
        $this->assertSame(100, $intResult);
    }

    /** @test */
    public function should_return_request_param_value()
    {
        $_REQUEST[self::EXISTING_PARAM] = 'value';

        $result = get_param(self::EXISTING_PARAM);

        $this->assertSame('value', $result);
    }

    /** @test */
    public function should_return_default_value_for_defined_empty_request_param()
    {
        $_REQUEST[self::EXISTING_PARAM] = '';

        $result = get_param(self::EXISTING_PARAM, 'default value');

        $this->assertSame('default value', $result);
    }

    /** @test */
    public function should_return_default_value_for_defined_undefined_request_param()
    {
        $_REQUEST[self::EXISTING_PARAM] = 'undefined';

        $result = get_param(self::EXISTING_PARAM, 'default value');

        $this->assertSame('default value', $result);
    }

    /** @test */
    public function should_throw_exception_for_defined_empty_request_param_without_default_value()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf('get_param: Parameter: %s has no value and no default value', self::EXISTING_PARAM));

        $_REQUEST[self::EXISTING_PARAM] = '';

        get_param(self::EXISTING_PARAM);
    }

    /** @test */
    public function should_throw_exception_for_defined_undefined_request_param_without_default_value()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf('get_param: Parameter: %s has no value and no default value', self::EXISTING_PARAM));

        $_REQUEST[self::EXISTING_PARAM] = 'undefined';

        get_param(self::EXISTING_PARAM);
    }

    /** @test */
    public function should_get_family_unit_id_from_session_when_param_value_is_minus_one()
    {
        $_REQUEST[self::FAMILY_UNIT_PARAM] = '-1';
        $_SESSION['userdata']['uf_id'] = '5';

        $result = get_param(self::FAMILY_UNIT_PARAM);

        $this->assertSame('5', $result);
    }

    /** @test */
    public function should_throw_exception_when_family_unit_id_param_is_minus_one_and_session_value_is_equal_or_less_than_zero()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('$_Session data uf_id is not set!! ');

        $_REQUEST[self::FAMILY_UNIT_PARAM] = '-1';
        $_SESSION['userdata']['uf_id'] = '0';

        get_param(self::FAMILY_UNIT_PARAM);
    }

    /** @test */
    public function should_throw_exception_when_family_unit_id_param_is_minus_one_and_session_is_empty()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('$_Session data uf_id is not set!! ');

        $_REQUEST[self::FAMILY_UNIT_PARAM] = '-1';

        get_param(self::FAMILY_UNIT_PARAM);
    }

    /** @test */
    public function should_get_user_id_from_session_when_param_value_is_minus_one()
    {
        $_REQUEST[self::USER_ID_PARAM] = '-1';
        $_SESSION['userdata'][self::USER_ID_PARAM] = '5';

        $result = get_param(self::USER_ID_PARAM);

        $this->assertSame('5', $result);
    }

    /** @test */
    public function should_throw_exception_when_user_id_param_is_minus_one_and_session_value_is_equal_or_less_than_zero()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('$_Session data user_id is not set!! ');

        $_REQUEST[self::USER_ID_PARAM] = '-1';
        $_SESSION['userdata'][self::USER_ID_PARAM] = '0';

        get_param(self::USER_ID_PARAM);
    }

    /** @test */
    public function should_throw_exception_when_user_id_param_is_minus_one_and_session_is_empty()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('$_Session data user_id is not set!! ');

        $_REQUEST[self::USER_ID_PARAM] = '-1';

        get_param(self::USER_ID_PARAM);
    }

    /** @test */
    public function should_get_member_id_from_session_when_param_value_is_minus_one()
    {
        $_REQUEST[self::MEMBER_ID_PARAM] = '-1';
        $_SESSION['userdata'][self::MEMBER_ID_PARAM] = '5';

        $result = get_param(self::MEMBER_ID_PARAM);

        $this->assertSame('5', $result);
    }

    /** @test */
    public function should_throw_exception_when_member_id_param_is_minus_one_and_session_value_is_equal_or_less_than_zero()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('$_Session data member_id is not set!! ');

        $_REQUEST[self::MEMBER_ID_PARAM] = '-1';
        $_SESSION['userdata'][self::MEMBER_ID_PARAM] = '0';

        get_param(self::MEMBER_ID_PARAM);
    }

    /** @test */
    public function should_throw_exception_when_member_id_param_is_minus_one_and_session_is_empty()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('$_Session data member_id is not set!! ');

        $_REQUEST[self::MEMBER_ID_PARAM] = '-1';

        get_param(self::MEMBER_ID_PARAM);
    }

    /** @test */
    public function should_get_and_transform_param_value_to_lowercase()
    {
        $_REQUEST[self::EXISTING_PARAM] = 'PARAMETER WITH UPPERCASE ChArAcTeRs';

        $result = get_param(self::EXISTING_PARAM, null, 'lowercase');

        $this->assertSame('parameter with uppercase characters', $result);
    }

    /** @test */
    public function should_get_and_transform_array_param_to_string()
    {
        $_REQUEST[self::EXISTING_PARAM] = ['an', 'array', 'param'];

        $result = get_param(self::EXISTING_PARAM, null, 'array2String');

        $this->assertSame('an,array,param', $result);

    }

    /** @test */
    public function should_throw_an_exception_for_an_invalid_transform_value()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf('get_param: transform \'%s\' on URL parameter not supported. ', self::INVALID_TRANSFORMATION));

        $_REQUEST[self::EXISTING_PARAM] = 'value';

        get_param(self::EXISTING_PARAM, null, self::INVALID_TRANSFORMATION);
    }
}