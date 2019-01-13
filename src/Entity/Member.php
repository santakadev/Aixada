<?php

namespace Aixada\Entity;

final class Member
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $familyUnitId;

    /**
     * @var string
     */
    private $customMemberRef;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $nif;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $zip;

    /**
     * @var string
     */
    private $phone1;

    /**
     * @var string
     */
    private $phone2;

    /**
     * @var string
     */
    private $web;

    /**
     * @var string
     */
    private $notes;

    /**
     * @var bool
     */
    private $active;

    /**
     * @var bool
     */
    private $adult;

    /**
     * @var bool
     */
    private $participant;

    /**
     * Member constructor.
     * @param int $id
     * @param int $familyUnitId
     * @param string $customMemberRef
     * @param string $name
     * @param string $nif
     * @param string $address
     * @param string $city
     * @param string $zip
     * @param string $phone1
     * @param string $phone2
     * @param string $web
     * @param string $notes
     * @param bool $active
     * @param bool $adult
     * @param bool $participant
     */
    public function __construct($id, $familyUnitId, $customMemberRef, $name, $nif, $address, $city, $zip, $phone1, $phone2, $web, $notes, $active, $adult, $participant)
    {
        $this->id = $id;
        $this->familyUnitId = $familyUnitId;
        $this->customMemberRef = $customMemberRef;
        $this->name = $name;
        $this->nif = $nif;
        $this->address = $address;
        $this->city = $city;
        $this->zip = $zip;
        $this->phone1 = $phone1;
        $this->phone2 = $phone2;
        $this->web = $web;
        $this->notes = $notes;
        $this->active = $active;
        $this->adult = $adult;
        $this->participant = $participant;
    }

    /**
     * @return int
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function familyUnitId()
    {
        return $this->familyUnitId;
    }

    /**
     * @return string
     */
    public function customMemberRef()
    {
        return $this->customMemberRef;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function nif()
    {
        return $this->nif;
    }

    /**
     * @return string
     */
    public function address()
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function city()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function zip()
    {
        return $this->zip;
    }

    /**
     * @return string
     */
    public function phone1()
    {
        return $this->phone1;
    }

    /**
     * @return string
     */
    public function phone2()
    {
        return $this->phone2;
    }

    /**
     * @return string
     */
    public function web()
    {
        return $this->web;
    }

    /**
     * @return string
     */
    public function notes()
    {
        return $this->notes;
    }

    /**
     * @return bool
     */
    public function active()
    {
        return $this->active;
    }

    /**
     * @return bool
     */
    public function adult()
    {
        return $this->adult;
    }

    /**
     * @return bool
     */
    public function participant()
    {
        return $this->participant;
    }
}