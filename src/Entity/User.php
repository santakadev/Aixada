<?php

namespace Aixada\Entity;

final class User
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $password;

    /**
     * @var int
     */
    private $familyUnitId;

    /**
     * @var int
     */
    private $memberId;

    /**
     * @var string
     */
    private $language;

    /**
     * @var string
     */
    private $guiTheme;

    /**
     * @var string
     */
    private $email;

    /**
     * @var \DateTimeImmutable
     */
    private $createdOn;

    /**
     * User constructor.
     * @param int $id
     * @param string $login
     * @param string $password
     * @param int $familyUnitId
     * @param int $memberId
     * @param string $language
     * @param string $guiTheme
     * @param string $email
     * @param \DateTimeImmutable $createdOn
     */
    public function __construct($id, $login, $password, $familyUnitId, $memberId, $language, $guiTheme, $email, \DateTimeImmutable $createdOn)
    {
        $this->id = $id;
        $this->login = $login;
        $this->password = $password;
        $this->familyUnitId = $familyUnitId;
        $this->memberId = $memberId;
        $this->language = $language;
        $this->guiTheme = $guiTheme;
        $this->email = $email;
        $this->createdOn = $createdOn;
    }

    /**
     * @return int
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function login()
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function password()
    {
        return $this->password;
    }

    /**
     * @return int
     */
    public function familyUnitId()
    {
        return $this->familyUnitId;
    }

    /**
     * @return int
     */
    public function memberId()
    {
        return $this->memberId;
    }

    /**
     * @return string
     */
    public function language()
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function guiTheme()
    {
        return $this->guiTheme;
    }

    /**
     * @return string
     */
    public function email()
    {
        return $this->email;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function createdOn()
    {
        return $this->createdOn;
    }
}