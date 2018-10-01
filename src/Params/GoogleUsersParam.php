<?php
namespace GoogleApiProc\Params;

class GoogleUsersParam
{
    private $familyName;
    private $givenName;
    private $fullName;
    private $name;
    private $hashFunction = 'MD5';
    private $password;
    private $primaryEmail;

    /**
     * @return mixed
     */
    public function getFamilyName()
    {
        return $this->familyName;
    }

    /**
     * @param mixed $familyName
     */
    public function setFamilyName($familyName)
    {
        $this->familyName = $familyName;
    }

    /**
     * @return mixed
     */
    public function getGivenName()
    {
        return $this->givenName;
    }

    /**
     * @param mixed $givenName
     */
    public function setGivenName($givenName)
    {
        $this->givenName = $givenName;
    }

    /**
     * @return mixed
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param mixed $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getHashFunction()
    {
        return $this->hashFunction;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password = 'password')
    {
        $this->password = hash('MD5', $password);
    }

    /**
     * @return mixed
     */
    public function getPrimaryEmail()
    {
        return $this->primaryEmail;
    }

    /**
     * @param mixed $primaryEmail
     */
    public function setPrimaryEmail($primaryEmail)
    {
        $this->primaryEmail = $primaryEmail;
    }

}
