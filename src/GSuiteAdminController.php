<?php
namespace GoogleApiProc;

use Google_Service_Directory_User;
use Google_Service_Directory_UserName;
use GoogleApiProc\Params\GoogleUsersParam;

class GSuiteAdminController extends GSuiteAdmin
{
    protected $service;

    /**
     * GSuiteAdminController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->service = new \Google_Service_Directory($this->client);
    }

    /**
     * 신규 사용자 생성
     */
    public function insertUser()
    {
        try {
            $param = $this->paramSet();
            $userInfo = $this->userInfoSet($param);

            $results = $this->service->users->insert($userInfo);
            return var_dump($results);
        } catch (Exception $e) {
            echo 'An error occurred: ' . $e->getMessage();
        }
    }

    /**
     * 신규 사용자 param 정보 세팅
     * @return GoogleUsersParam
     */
    public function paramSet()
    {
        try {
            $gUserParam = new GoogleUsersParam();

            $gUserParam->setFamilyName(request('familyName'));
            $gUserParam->setGivenName(request('givenName'));
            $gUserParam->setFullName(request('fullName'));
            $gUserParam->setPassword(request('password'));
            $gUserParam->setPrimaryEmail(request('primaryEmail'));

            return $gUserParam;
        } catch (Exception $e) {
            echo 'An error occurred: ' . $e->getMessage();
        }
    }

    /**
     * 신규 사용자 google api 등록 정보 세팅
     * @param GoogleUsersParam $param
     * @return Google_Service_Directory_User
     */
    public function userInfoSet(GoogleUsersParam $param)
    {
        try {
            $userName = new Google_Service_Directory_UserName();
            $userInfo = new Google_Service_Directory_User();

            $userName->setFamilyName($param->getFamilyName());
            $userName->setGivenName($param->getGivenName());
            $userName->setFullName($param->getFullName());

            $userInfo->setPrimaryEmail($param->getPrimaryEmail());
            $userInfo->setName($userName);
            $userInfo->setHashFunction($param->getHashFunction());
            $userInfo->setPassword($param->getPassword());

            return $userInfo;
        } catch (Exception $e) {
            echo 'An error occurred: ' . $e->getMessage();
        }
    }


    /**
     *
     */
    public function  deleteUser()
    {
        try {
            $userEmail = request('primaryEmail');

            $results = $this->service->users->delete($userEmail);

            return var_dump($results);
        } catch (Exception $e) {
            echo 'An error occurred: ' . $e->getMessage();
        }
    }

}
