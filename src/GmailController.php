<?php
namespace GoogleApiProc;

use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Swift_Message;

class GmailController extends Gmail
{
    protected $service;
    protected $userId = 'me';//인증 gmail 계정으로 세팅되는 특수 고정값

    public function __construct()
    {
        parent::__construct();
        $this->service = new Google_Service_Gmail($this->client);
    }

    /**
     * Gmail 전송
     * @return mixed
     */
    public function sendMessage()
    {
        try {

            $param = $this->paramSet();
            $message = $this->messageSet($param);

            $results = $this->service->users_messages->send($this->userId, $message);
            if($results){
                $msg = $this->service->users_messages->get($this->userId, $results->id);
                // Collect headers
                $headers = collect($msg->getPayload()->headers);
                return [
                    'id' => $results->id,
                    'Message-Id' => $headers
                ];
            }
            return ['msg'=> '전송 실패'];
        } catch (Exception $e) {
            echo 'An error occurred: ' . $e->getMessage();
        }
    }

    /**
     * 메일 전송 param 정보 세팅
     * @return Swift_Message
     */
    public function paramSet()
    {
        try {
            $msg = new Swift_Message();

            $msg->setTo(request('mailTo'),request('mailToName'));
            $msg->setSubject(request('mailSubject'). date('M d, Y h:i:s A'));
            $msg->setBody(request('mailContents'), 'text/html', 'utf-8');

            return $msg;
        } catch (Exception $e) {
            echo 'An error occurred: ' . $e->getMessage();
        }
    }

    /**
     * 메일 전송 데이터 세팅
     * @param Swift_Message $param
     * @return Google_Service_Gmail_Message
     */
    public function messageSet(Swift_Message $param)
    {
        try {
            $g_message = new Google_Service_Gmail_Message();
            $mime = rtrim(strtr(base64_encode($param), '+/', '-_'), '=');
            $g_message->setRaw($mime);

            return $g_message;
        } catch (Exception $e) {
            echo 'An error occurred: ' . $e->getMessage();
        }
    }

    /**
     * 에러에 의해 전송되지 못한 메일 조회
     * @return array
     */
    public function listMessages()
    {
        try {
            $errList = $this->sendErrMessageList();
            $result = $this->getMessage($errList);

            return $result;
        }
        catch (Exception $e)
        {
            print 'An error occurred: ' . $e->getMessage();
        }
    }

    /**
     * 에러에 의해 수신된 메일 목록
     * @return array
     */
    public function sendErrMessageList()
    {
        try {
            $pageToken = NULL;
            $messages = array();
            $opt_param = array();
            do {
                try {
                    $opt_param['labelIds'] = ['INBOX', 'UNREAD']; //받은메일함
                    $opt_param['q'] = 'from=mailer-daemon@googlemail.com'; //송신자
                    if ($pageToken) {
                        $opt_param['pageToken'] = $pageToken;
                    }
                    $messagesResponse = $this->service->users_messages->listUsersMessages($this->userId, $opt_param);
                    if ($messagesResponse->getMessages()) {
                        $messages = array_merge($messages, $messagesResponse->getMessages());
                        $pageToken = $messagesResponse->getNextPageToken();
                    }
                } catch (Exception $e) {
                    print 'An error occurred: ' . $e->getMessage();
                }
            } while ($pageToken);

            $errList = array();
            foreach ($messages as $message) {
                $errList[] = [
                    'errId' => $message->id,
                    'parantId' => $message->threadId
                ];
            }
            return ['msgList'=>$errList];
        }
        catch (Exception $e)
        {
            print 'An error occurred: ' . $e->getMessage();
        }
    }

    /**
     * 에러에 의해 수신된 메일의 내용
     * @param null $errList
     * @return array
     */
    public function getMessage($errList = null)
    {
        try {
            $messageList = array();
            foreach ($errList['msgList'] as $errMsg){
                $message = $this->service->users_messages->get($this->userId, $errMsg['errId']);
                $messageList[] = [
                                  'errMsg' => $message->getSnippet()
                ];
            }

            return $messageList;
        } catch (Exception $e) {
            print 'An error occurred: ' . $e->getMessage();
        }
    }
}
